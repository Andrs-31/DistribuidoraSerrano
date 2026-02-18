<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\GrupoCliente;
use App\Models\ListaPrecio;
use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Foundation\Testing\WithFaker;

class FacturaEquivalenciaTest extends TestCase
{
    use WithFaker;

    protected $admin;
    protected $cliente;
    protected $clienteInactivo;
    protected $producto;
    protected $productoInactivo;
    protected $grupo;
    protected $provincia;
    protected $distrito;

    protected function setUp(): void
    {
        parent::setUp();

        // Get user with facturacion permission
        $this->admin = User::with('permiso')
            ->where('email', 'factura@demo.com')
            ->orWhere('email', 'admin@demo.com')
            ->first();

        if (!$this->admin) {
            $this->admin = User::with('permiso')->first();
        }

        if (!$this->admin) {
            $this->markTestSkipped('No hay usuarios disponibles en la base de datos para autenticarse.');
        }

        // Ensure group exists
        $this->grupo = GrupoCliente::first();
        if (!$this->grupo) {
            $this->grupo = GrupoCliente::create(['nombre' => 'GRUPO_CE_FACTURA_TEST', 'estado' => 1]);
        }

        // Ensure province and district exist
        $this->provincia = Provincia::first();
        if (!$this->provincia) {
            $this->provincia = Provincia::create([
                'codigo_provincia' => 'TEST_CE',
                'nombre_provincia' => 'Provincia Test CE Factura'
            ]);
        }

        $this->distrito = Distrito::where('codigo_provincia', $this->provincia->codigo_provincia)->first();
        if (!$this->distrito) {
            $this->distrito = Distrito::create([
                'codigo_distrito' => 'DIST_CE',
                'codigo_provincia' => $this->provincia->codigo_provincia,
                'nombre_distrito' => 'Distrito Test CE Factura'
            ]);
        }

        // Create active client with unique RUC
        $this->cliente = Cliente::where('estado', 1)->first();
        if (!$this->cliente) {
            $ruacUnico = '20' . str_pad(mt_rand(1000000, 9999999), 10, '0', STR_PAD_LEFT);
            $this->cliente = Cliente::create([
                'ruc' => $ruacUnico,
                'nombre' => 'Cliente CE Factura Test',
                'email' => 'cliente-ce-' . uniqid() . '@example.com',
                'telefono' => '900900900',
                'direccion' => 'Calle Test',
                'estado' => 1,
                'id_grupo_fk' => $this->grupo->id_grupo,
            ]);
        }

        // Get or create inactive client (search existing first to avoid duplicates)
        $this->clienteInactivo = Cliente::where('estado', 0)->first();
        if (!$this->clienteInactivo) {
            $rucInactivo = '20' . str_pad(mt_rand(1000000, 9999999), 10, '0', STR_PAD_LEFT);
            $this->clienteInactivo = Cliente::create([
                'ruc' => $rucInactivo,
                'nombre' => 'Cliente Inactivo CE Test',
                'email' => 'cliente-inactivo-ce-' . uniqid() . '@example.com',
                'telefono' => '900900900',
                'direccion' => 'Calle Test Inactivo',
                'estado' => 0, // Inactive
                'id_grupo_fk' => $this->grupo->id_grupo,
            ]);
        }

        // Create active product
        $this->producto = Producto::where('estado', 1)->first();
        if (!$this->producto) {
            $this->producto = Producto::create([
                'nombre' => 'Producto CE Factura Test ' . uniqid(),
                'estado' => 1,
            ]);

            ListaPrecio::create([
                'id_producto_fk' => $this->producto->id_producto,
                'id_grupo_fk' => $this->grupo->id_grupo,
                'precio_unitario' => 99.99,
                'unidad' => 'Unidad',
                'estado' => 1,
            ]);
        }

        // Get or create inactive product (search existing first)
        $this->productoInactivo = Producto::where('estado', 0)->first();
        if (!$this->productoInactivo) {
            $this->productoInactivo = Producto::create([
                'nombre' => 'Producto Inactivo CE Test ' . uniqid(),
                'estado' => 0,
            ]);
        }
    }

    // ============================================================================
    // CE-F1: REGISTRAR FACTURA - Sin cliente
    // ============================================================================

    public function test_ce_f1_invoice_without_client_returns_error()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $payload = [
            'cliente_id' => '',
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => $this->producto->id_producto,
                    'cantidad' => 1,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 99.99,
                    'subtotal' => 99.99,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        $this->assertTrue(
            in_array($status, [302, 422, 400]),
            "Expected validation error, got: $status"
        );

        if ($status === 302) {
            $response->assertSessionHasErrors('cliente_id');
        }

        $this->assertDatabaseMissing('facturas', ['num_factura' => $numeroFactura]);
    }

    // ============================================================================
    // CE-F2: REGISTRAR FACTURA - Cliente inactivo
    // ============================================================================

    public function test_ce_f2_invoice_with_inactive_client_not_permitted()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $payload = [
            'cliente_id' => $this->clienteInactivo->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => $this->producto->id_producto,
                    'cantidad' => 1,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 99.99,
                    'subtotal' => 99.99,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        if (in_array($status, [200, 201, 302])) {
            $this->assertDatabaseMissing('facturas', [
                'num_factura' => $numeroFactura,
                'ruc_cliente_fk' => $this->clienteInactivo->ruc,
            ]);
        } else {
            $this->assertTrue(
                in_array($status, [302, 400, 422, 404]),
                "Expected error for inactive client, got: $status"
            );
        }
    }

    // ============================================================================
    // CE-F3: REGISTRAR FACTURA - Producto inexistente
    // ============================================================================

    public function test_ce_f3_invoice_with_nonexistent_product_returns_error()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $payload = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => 999999,
                    'cantidad' => 1,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 99.99,
                    'subtotal' => 99.99,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        $this->assertTrue(
            in_array($status, [302, 422, 400]),
            "Expected validation error for non-existent product, got: $status"
        );

        $this->assertDatabaseMissing('facturas', ['num_factura' => $numeroFactura]);
    }

    // ============================================================================
    // CE-F4: REGISTRAR FACTURA - Producto inactivo
    // ============================================================================

    public function test_ce_f4_invoice_with_inactive_product_not_permitted()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $payload = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => $this->productoInactivo->id_producto,
                    'cantidad' => 1,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 99.99,
                    'subtotal' => 99.99,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        if (in_array($status, [200, 201, 302])) {
            $this->assertDatabaseMissing('detalle_factura', [
                'num_factura_fk' => $numeroFactura,
                'id_producto_fk' => $this->productoInactivo->id_producto,
            ]);
        } else {
            $this->assertTrue(
                in_array($status, [302, 422, 400]),
                "Expected error for inactive product, got: $status"
            );
        }
    }

    // ============================================================================
    // CE-F5: REGISTRAR FACTURA - Cantidad válida
    // ============================================================================

    public function test_ce_f5_invoice_with_valid_quantity_accepted()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;
        $cantidad = 5;
        $subtotalEsperado = $cantidad * 99.99;

        $payload = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => $this->producto->id_producto,
                    'cantidad' => $cantidad,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 99.99,
                    'subtotal' => $subtotalEsperado,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        $this->assertTrue(
            in_array($status, [200, 201, 302]),
            "Expected successful creation, got: $status"
        );

        $this->assertDatabaseHas('facturas', [
            'num_factura' => $numeroFactura,
            'total' => $subtotalEsperado,
        ]);

        $this->assertDatabaseHas('detalle_factura', [
            'num_factura_fk' => $numeroFactura,
            'id_producto_fk' => $this->producto->id_producto,
            'cantidad' => $cantidad,
        ]);
    }

    // ============================================================================
    // CE-F6: REGISTRAR FACTURA - Cantidad 0
    // ============================================================================

    public function test_ce_f6_invoice_with_zero_quantity_validation_error()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $payload = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => $this->producto->id_producto,
                    'cantidad' => 0,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 99.99,
                    'subtotal' => 0,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        $this->assertTrue(
            in_array($status, [302, 422, 400]),
            "Expected validation error for zero quantity, got: $status"
        );

        if ($status === 302) {
            $response->assertSessionHasErrors();
        }

        $this->assertDatabaseMissing('facturas', ['num_factura' => $numeroFactura]);
    }

    // ============================================================================
    // CE-F7: REGISTRAR FACTURA - Cantidad negativa
    // ============================================================================

    public function test_ce_f7_invoice_with_negative_quantity_validation_error()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $payload = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => $this->producto->id_producto,
                    'cantidad' => -1,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 99.99,
                    'subtotal' => -99.99,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        $this->assertTrue(
            in_array($status, [302, 422, 400]),
            "Expected validation error for negative quantity, got: $status"
        );

        if ($status === 302) {
            $response->assertSessionHasErrors();
        }

        $this->assertDatabaseMissing('facturas', ['num_factura' => $numeroFactura]);
    }

    // ============================================================================
    // CE-F8: REGISTRAR FACTURA - Factura sin productos
    // ============================================================================

    public function test_ce_f8_invoice_without_products_returns_error()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $payload = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        $this->assertTrue(
            in_array($status, [302, 422, 400]),
            "Expected validation error for no products, got: $status"
        );

        if ($status === 302) {
            $response->assertSessionHasErrors('detalles');
        }

        $this->assertDatabaseMissing('facturas', ['num_factura' => $numeroFactura]);
    }
}
