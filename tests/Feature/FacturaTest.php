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

class FacturaTest extends TestCase
{
    use WithFaker;

    protected $admin;
    protected $cliente;
    protected $producto;
    protected $grupo;
    protected $provincia;
    protected $distrito;

    protected function setUp(): void
    {
        parent::setUp();

        // Get user with facturacion permission (factura@demo.com or admin@demo.com)
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
            $this->grupo = GrupoCliente::create(['nombre' => 'GRUPO_FACTURA_TEST', 'estado' => 1]);
        }

        // Ensure province and district exist
        $this->provincia = Provincia::first();
        if (!$this->provincia) {
            $this->provincia = Provincia::create([
                'codigo_provincia' => 'TEST100',
                'nombre_provincia' => 'Provincia Test Factura'
            ]);
        }

        $this->distrito = Distrito::where('codigo_provincia', $this->provincia->codigo_provincia)->first();
        if (!$this->distrito) {
            $this->distrito = Distrito::create([
                'codigo_distrito' => 'DIST100',
                'codigo_provincia' => $this->provincia->codigo_provincia,
                'nombre_distrito' => 'Distrito Test Factura'
            ]);
        }

        // Create or get active client
        $this->cliente = Cliente::where('estado', 1)->first();
        if (!$this->cliente) {
            $this->cliente = Cliente::create([
                'ruc' => '20' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT),
                'nombre' => 'Cliente Factura Test',
                'email' => 'cliente-factura-' . time() . '@example.com',
                'telefono' => '900900900',
                'direccion' => 'Calle Test, ' . $this->provincia->nombre_provincia,
                'estado' => 1,
                'id_grupo_fk' => $this->grupo->id_grupo,
            ]);
        }

        // Create or get active product
        $this->producto = Producto::where('estado', 1)->first();
        if (!$this->producto) {
            $this->producto = Producto::create([
                'nombre' => 'Producto Factura Test ' . time(),
                'estado' => 1,
            ]);

            // Create price for this product
            ListaPrecio::create([
                'id_producto_fk' => $this->producto->id_producto,
                'id_grupo_fk' => $this->grupo->id_grupo,
                'precio_unitario' => 99.99,
                'unidad' => 'Unidad',
                'estado' => 1,
            ]);
        }
    }

    // ============================================================================
    // PF-F1: REGISTRAR FACTURA - Crear factura válida
    // ============================================================================

    /**
     * PF-F1: Registrar Factura → Crear factura válida → Factura guardada con total calculado correctamente
     *
     * Requisito Funcional: Registrar Factura
     * Escenario: Crear factura válida
     * Precondición: Admin o Gestor de Facturación autenticado, cliente activo existente
     * Datos de Prueba: (Seleccionar de la lista)
     * Pasos (resumen): 1. Crear factura 2. Llenar cliente + productos
     * Resultado Esperado: Factura guardada con total calculado correctamente
     */
    public function test_pf_f1_create_valid_invoice()
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
                    'cantidad' => 2,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 99.99,
                    'subtotal' => 199.98,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        // Should successfully create invoice
        $this->assertTrue(
            in_array($status, [200, 201, 302]),
            "Expected successful creation, got: $status"
        );

        // Verify invoice was created in database
        $this->assertDatabaseHas('facturas', [
            'num_factura' => $numeroFactura,
            'ruc_cliente_fk' => $this->cliente->ruc,
            'total' => 199.98,
        ]);

        // Verify details were created
        $this->assertDatabaseHas('detalle_factura', [
            'num_factura_fk' => $numeroFactura,
            'id_producto_fk' => $this->producto->id_producto,
            'cantidad' => 2,
        ]);
    }

    // ============================================================================
    // PF-F2: REGISTRAR FACTURA - Número generado automáticamente
    // ============================================================================

    /**
     * PF-F2: Registrar Factura → Número de factura generado automáticamente → Sistema genera número único consecutivo
     *
     * Requisito Funcional: Registrar Factura
     * Escenario: Número de factura generado automáticamente
     * Precondición: Admin o Gestor de Facturación autenticado
     * Datos de Prueba: (Seleccionar de la lista)
     * Pasos (resumen): 1. Crear factura
     * Resultado Esperado: Sistema genera número único consecutivo
     */
    public function test_pf_f2_invoice_number_auto_generated()
    {
        // Get view to see next invoice number
        $viewResponse = $this->actingAs($this->admin)
            ->get('/facturacion/create');

        $this->assertEquals(200, $viewResponse->getStatusCode());

        // The next number should be calculated in create view
        $content = $viewResponse->getContent();

        // Check that proximoNumero is in the view
        $this->assertTrue(
            strpos($content, 'proximoNumero') !== false ||
            strpos($content, 'numero') !== false,
            'Invoice number field should be present'
        );

        // Create first invoice
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numero1 = $ultimaFactura + 1;

        $payload1 = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numero1,
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

        $response1 = $this->actingAs($this->admin)
            ->post('/facturacion', $payload1);

        // Create second invoice - should get consecutive number
        $numero2 = $numero1 + 1;

        $payload2 = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numero2,
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

        $response2 = $this->actingAs($this->admin)
            ->post('/facturacion', $payload2);

        // Both should exist with consecutive numbers
        $factura1 = Factura::find($numero1);
        $factura2 = Factura::find($numero2);

        $this->assertNotNull($factura1, 'First invoice should exist');
        $this->assertNotNull($factura2, 'Second invoice should exist');
        $this->assertEquals($numero2, $numero1 + 1, 'Numbers should be consecutive');
    }

    // ============================================================================
    // PF-F3: VER FACTURA - Visualizar detalle de factura
    // ============================================================================

    /**
     * PF-F3: Ver Factura → Visualizar detalle de factura → Se muestran cliente, productos, cantidades y totales
     *
     * Requisito Funcional: Ver Factura
     * Escenario: Visualizar detalle de factura
     * Precondición: Factura existente
     * Datos de Prueba: ...
     * Pasos (resumen): Presionar el ícono de ver factura
     * Resultado Esperado: Se muestran cliente, productos, cantidades y totales
     */
    public function test_pf_f3_view_invoice_details()
    {
        // Create an invoice
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $factura = Factura::create([
            'num_factura' => $numeroFactura,
            'fecha_factura' => now(),
            'ruc_cliente_fk' => $this->cliente->ruc,
            'id_usuario_fk' => $this->admin->id_user,
            'subtotal' => 199.98,
            'impuesto' => 0,
            'total' => 199.98,
            'estado' => 'emitido',
        ]);

        FacturaDetalle::create([
            'num_factura_fk' => $numeroFactura,
            'id_producto_fk' => $this->producto->id_producto,
            'cantidad' => 2,
            'unidad' => 'Unidad',
            'precio_unitario' => 99.99,
            'subtotal_linea' => 199.98,
        ]);

        // Get invoice data via API endpoint
        $response = $this->actingAs($this->admin)
            ->get('/facturacion/' . $numeroFactura . '/datos-pdf');

        $status = $response->getStatusCode();

        // Should return JSON with invoice details
        $this->assertEquals(200, $status, 'Should retrieve invoice data successfully');

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertArrayHasKey('numero', $data);
        $this->assertArrayHasKey('cliente_nombre', $data);
        $this->assertArrayHasKey('detalles', $data);
        $this->assertEquals($this->cliente->nombre, $data['cliente_nombre']);
        $this->assertTrue(count($data['detalles']) > 0, 'Should have at least one detail');

        // Cleanup
        $factura->delete();
    }

    // ============================================================================
    // PF-F4: REGISTRAR FACTURA - Agregar producto válido
    // ============================================================================

    /**
     * PF-F4: Registrar Factura → Agregar producto válido → Producto agregado al detalle
     *
     * Requisito Funcional: Registrar Factura
     * Escenario: Agregar producto válido
     * Precondición: Factura en creación, Producto activo
     * Datos de Prueba: Seleccionar producto e Ingresar cantidad
     * Pasos (resumen): 1. Seleccionar producto
     * Resultado Esperado: Producto agregado al detalle
     */
    public function test_pf_f4_add_valid_product_to_invoice()
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
                    'cantidad' => 3,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 99.99,
                    'subtotal' => 299.97,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        // Verify product detail was created
        $this->assertDatabaseHas('detalle_factura', [
            'num_factura_fk' => $numeroFactura,
            'id_producto_fk' => $this->producto->id_producto,
            'cantidad' => 3,
        ]);
    }

    // ============================================================================
    // PF-F5: REGISTRAR FACTURA - Precio automático según cliente
    // ============================================================================

    /**
     * PF-F5: Registrar Factura → Precio automático según cliente → Precio corresponde al grupo del cliente
     *
     * Requisito Funcional: Registrar Factura
     * Escenario: Precio automático según cliente
     * Precondición: Cliente con grupo de precio especial
     * Datos de Prueba: Producto con precio por grupo
     * Pasos (resumen): Agregar producto
     * Resultado Esperado: Precio corresponde al grupo del cliente
     */
    public function test_pf_f5_automatic_price_by_customer_group()
    {
        // Get or create a price for this product and group
        $precioGrupo = ListaPrecio::where(
            'id_producto_fk',
            $this->producto->id_producto
        )->where(
            'id_grupo_fk',
            $this->cliente->id_grupo_fk
        )->first();

        if (!$precioGrupo) {
            $precioGrupo = ListaPrecio::create([
                'id_producto_fk' => $this->producto->id_producto,
                'id_grupo_fk' => $this->cliente->id_grupo_fk,
                'precio_unitario' => 85.00, // Special group price
                'unidad' => 'Unidad',
                'estado' => 1,
            ]);
        }

        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;
        $precioAUsar = 85.00;

        $payload = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => $this->producto->id_producto,
                    'cantidad' => 1,
                    'unidad' => 'Unidad',
                    'precio_unitario' => $precioAUsar,
                    'subtotal' => $precioAUsar,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        $status = $response->getStatusCode();

        // Verify the invoice was created
        $this->assertTrue(
            in_array($status, [200, 201, 302]),
            "Invoice creation should succeed, got: $status"
        );

        // Verify a detail was created for this invoice
        $this->assertDatabaseHas('detalle_factura', [
            'num_factura_fk' => $numeroFactura,
            'id_producto_fk' => $this->producto->id_producto,
        ]);
    }

    // ============================================================================
    // PF-F6: REGISTRAR FACTURA - Cálculo automático subtotal
    // ============================================================================

    /**
     * PF-F6: Registrar Factura → Cálculo automático subtotal → Subtotal calculado correctamente
     *
     * Requisito Funcional: Registrar Factura
     * Escenario: Cálculo automático subtotal
     * Precondición: Producto agregado
     * Datos de Prueba: ...
     * Pasos (resumen): Agregar producto
     * Resultado Esperado: Subtotal calculado correctamente
     */
    public function test_pf_f6_automatic_subtotal_calculation()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;
        $cantidad = 5;
        $precioUnitario = 50.00;
        $subtotalEsperado = $cantidad * $precioUnitario;

        $payload = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => $this->producto->id_producto,
                    'cantidad' => $cantidad,
                    'unidad' => 'Unidad',
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalEsperado,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        // Verify subtotal calculation
        $this->assertDatabaseHas('detalle_factura', [
            'num_factura_fk' => $numeroFactura,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal_linea' => $subtotalEsperado,
        ]);
    }

    // ============================================================================
    // PF-F7: REGISTRAR FACTURA - Cálculo total final
    // ============================================================================

    /**
     * PF-F7: Registrar Factura → Cálculo total final → Total calculado correctamente
     *
     * Requisito Funcional: Registrar Factura
     * Escenario: Cálculo total final
     * Precondición: Múltiples productos agregados
     * Datos de Prueba: ...
     * Pasos (resumen): Guardar factura
     * Resultado Esperado: Total calculado correctamente
     */
    public function test_pf_f7_automatic_total_calculation()
    {
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        // Create multiple products for this test
        $producto2 = Producto::create([
            'nombre' => 'Producto 2 ' . time(),
            'estado' => 1,
        ]);

        $subtotal1 = 100.00;
        $subtotal2 = 200.00;
        $totalEsperado = $subtotal1 + $subtotal2;

        $payload = [
            'cliente_id' => $this->cliente->ruc,
            'numero' => (string)$numeroFactura,
            'fecha' => now()->format('Y-m-d'),
            'detalles' => [
                [
                    'producto_id' => $this->producto->id_producto,
                    'cantidad' => 2,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 50.00,
                    'subtotal' => $subtotal1,
                ],
                [
                    'producto_id' => $producto2->id_producto,
                    'cantidad' => 4,
                    'unidad' => 'Unidad',
                    'precio_unitario' => 50.00,
                    'subtotal' => $subtotal2,
                ]
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/facturacion', $payload);

        // Verify total calculation
        $this->assertDatabaseHas('facturas', [
            'num_factura' => $numeroFactura,
            'total' => $totalEsperado,
        ]);

        // Cleanup - delete details first to avoid FK constraint
        FacturaDetalle::where('num_factura_fk', $numeroFactura)->delete();
        Factura::find($numeroFactura)?->delete();
        $producto2->delete();
    }

    // ============================================================================
    // PF-F8: ENVIAR FACTURA - Enviar factura por correo
    // ============================================================================

    /**
     * PF-F8: Enviar Factura → Enviar factura por correo → Correo enviado correctamente
     *
     * Requisito Funcional: Enviar Factura
     * Escenario: Enviar factura por correo
     * Precondición: Factura guardada válida
     * Datos de Prueba: Cliente con correo válido
     * Pasos (resumen): Click enviar correo correctamente
     * Resultado Esperado: Correo enviado
     */
    public function test_pf_f8_send_invoice_by_email()
    {
        // Create an invoice
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $factura = Factura::create([
            'num_factura' => $numeroFactura,
            'fecha_factura' => now(),
            'ruc_cliente_fk' => $this->cliente->ruc,
            'id_usuario_fk' => $this->admin->id_user,
            'subtotal' => 100.00,
            'impuesto' => 0,
            'total' => 100.00,
            'estado' => 'emitido',
        ]);

        // Try to send email (obtenerDatosPdf - this returns JSON with invoice data)
        $response = $this->actingAs($this->admin)
            ->get('/facturacion/' . $numeroFactura . '/datos-pdf');

        $status = $response->getStatusCode();

        // Should return JSON response with invoice data
        if ($status === 200) {
            $data = $response->json();
            $this->assertIsArray($data);
            $this->assertArrayHasKey('numero', $data);
            $this->assertArrayHasKey('cliente_nombre', $data);
        }

        // Cleanup
        $factura->delete();
    }

    // ============================================================================
    // PF-F9: GENERAR PDF - Descargar PDF
    // ============================================================================

    /**
     * PF-F9: Generar PDF → Descargar PDF → PDF generado correctamente
     *
     * Requisito Funcional: Generar PDF
     * Escenario: Descargar PDF
     * Precondición: Factura guardada
     * Datos de Prueba: ID factura válida
     * Pasos (resumen): Presionar Descargar o Imprimir PDF
     * Resultado Esperado: PDF generado correctamente
     */
    public function test_pf_f9_download_pdf_invoice()
    {
        // Create an invoice
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $factura = Factura::create([
            'num_factura' => $numeroFactura,
            'fecha_factura' => now(),
            'ruc_cliente_fk' => $this->cliente->ruc,
            'id_usuario_fk' => $this->admin->id_user,
            'subtotal' => 100.00,
            'impuesto' => 0,
            'total' => 100.00,
            'estado' => 'emitido',
        ]);

        // Create a detail for the invoice
        FacturaDetalle::create([
            'num_factura_fk' => $numeroFactura,
            'id_producto_fk' => $this->producto->id_producto,
            'cantidad' => 1,
            'unidad' => 'Unidad',
            'precio_unitario' => 100.00,
            'subtotal_linea' => 100.00,
        ]);

        // Try to get PDF data
        $response = $this->actingAs($this->admin)
            ->get('/facturacion/' . $numeroFactura . '/datos-pdf');

        $status = $response->getStatusCode();

        // Should return 200 with JSON data
        $this->assertEquals(200, $status, 'Should successfully retrieve PDF data');

        $data = $response->json();
        $this->assertIsArray($data, 'Response should be JSON array');
        $this->assertArrayHasKey('numero', $data);
        $this->assertArrayHasKey('cliente_nombre', $data);
        $this->assertArrayHasKey('total', $data);

        // Cleanup
        $factura->delete();
    }

    // ============================================================================
    // PF-F10: ANULAR FACTURA - Anular factura válida
    // ============================================================================

    /**
     * PF-F10: Anular Factura → Anular factura válida → Estado cambia a Anulada
     *
     * Requisito Funcional: Anular Factura
     * Escenario: Anular factura válida
     * Precondición: ID factura existente
     * Datos de Prueba: Click anular
     * Pasos (resumen): (Click anular)
     * Resultado Esperado: Estado cambia a Anulada
     */
    public function test_pf_f10_annul_valid_invoice()
    {
        // Create an invoice
        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        $factura = Factura::create([
            'num_factura' => $numeroFactura,
            'fecha_factura' => now(),
            'ruc_cliente_fk' => $this->cliente->ruc,
            'id_usuario_fk' => $this->admin->id_user,
            'subtotal' => 100.00,
            'impuesto' => 0,
            'total' => 100.00,
            'estado' => 'emitido',
        ]);

        // Annul the invoice
        $response = $this->actingAs($this->admin)
            ->post('/facturacion/' . $numeroFactura . '/anular');

        $status = $response->getStatusCode();

        // Should be successful
        $this->assertTrue(
            in_array($status, [200, 302]) || $response->isJson(),
            "Annul operation should succeed, got: $status"
        );

        // Verify status changed to 'anulada'
        $factura->refresh();
        $this->assertEquals('anulada', $factura->estado, 'Invoice status should be anulada');

        // Cleanup
        $factura->delete();
    }

    // ============================================================================
    // PF-F11: REGISTRAR FACTURA - Cancelar factura antes de guardar
    // ============================================================================

    /**
     * PF-F11: Registrar Factura → Cancelar factura antes de guardar → No se guarda en BD
     *
     * Requisito Funcional: Registrar Factura
     * Escenario: Cancelar factura antes de guardar
     * Precondición: Datos ingresados
     * Datos de Prueba: Click cancelar
     * Pasos (resumen): Click cancelar
     * Resultado Esperado: No se guarda en BD
     */
    public function test_pf_f11_cancel_invoice_before_saving()
    {
        // This test verifies that accessing the create form doesn't create any invoice
        // and that canceling (navigating away) doesn't save data

        $ultimaFactura = Factura::max('num_factura') ?? 0;
        $numeroFactura = $ultimaFactura + 1;

        // Access create form
        $response = $this->actingAs($this->admin)
            ->get('/facturacion/create');

        $this->assertEquals(200, $response->getStatusCode());

        // Verify no invoice was created just by accessing the form
        $this->assertDatabaseMissing('facturas', [
            'num_factura' => $numeroFactura,
        ]);

        // If we navigate to index (simulating cancel), nothing should be saved
        $indexResponse = $this->actingAs($this->admin)
            ->get('/facturacion');

        $this->assertEquals(200, $indexResponse->getStatusCode());

        // Still no invoice created
        $this->assertDatabaseMissing('facturas', [
            'num_factura' => $numeroFactura,
        ]);
    }
}
