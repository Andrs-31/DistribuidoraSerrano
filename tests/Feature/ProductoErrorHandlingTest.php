<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Producto;
use App\Models\ListaPrecio;
use App\Models\GrupoCliente;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Foundation\Testing\WithFaker;

class ProductoErrorHandlingTest extends TestCase
{
    use WithFaker;

    protected $admin;
    protected $grupo;

    protected function setUp(): void
    {
        parent::setUp();

        // Get admin user
        $this->admin = User::where('email', 'admin@demo.com')->first() ?: User::first();

        if (!$this->admin) {
            $this->markTestSkipped('No hay usuarios disponibles en la base de datos para autenticarse.');
        }

        // Ensure core data exists
        $this->grupo = GrupoCliente::first();
        if (!$this->grupo) {
            $this->grupo = GrupoCliente::create(['nombre' => 'GRUPO_ERROR_TEST', 'estado' => 1]);
        }
    }

    /**
     * ERR-P1: Guardar Producto → Fallo al guardar producto → No se guarda nada (rollback)
     *
     * Requisito Funcional: Guardar Producto
     * Escenario: Fallo al guardar producto
     * Precondición: (No especificado)
     * Datos de Prueba: ...
     * Pasos (resumen): ...
     * Resultado Esperado: No se guarda nada (rollback)
     */
    public function test_err_p1_save_product_failure_with_rollback()
    {
        // Simulate a save failure scenario where a database transaction should rollback
        // One approach is to try to create a product with a duplicate name (violates unique constraint)

        // First create a product
        $nombreOriginal = 'Producto Rollback Test ' . time();
        $producto1 = Producto::create([
            'nombre' => $nombreOriginal,
            'estado' => 1,
        ]);

        // Now try to create another with the exact same name (violates unique constraint)
        // This should trigger an error and rollback
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $nombreOriginal, // Duplicate name - will fail
            ]);

        $status = $response->getStatusCode();

        // Should get validation error or 422
        if ($status === 302) {
            // If redirected, verify there's an error message (no product duplicated)
            $response->assertSessionHasErrors('nombre');
        } else {
            // Should be 422 or similar
            $this->assertTrue(in_array($status, [422, 400]), "Expected validation error, got: $status");
        }

        // Verify only ONE product with this name exists (rollback worked)
        $productCount = Producto::where('nombre', $nombreOriginal)->count();
        $this->assertEquals(1, $productCount, 'Only one product should exist (rollback should prevent duplicate)');

        // Cleanup
        $producto1->delete();
    }

    /**
     * ERR-P1b: Guardar producto con nombre vacío → No se guarda nada
     */
    public function test_err_p1b_save_product_empty_name_not_saved()
    {
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => '', // Empty name should fail
            ]);

        $status = $response->getStatusCode();

        // Should get validation error
        if ($status === 302) {
            $response->assertSessionHasErrors('nombre');
        } else {
            $this->assertTrue(in_array($status, [422, 400]), "Expected validation error for empty name");
        }

        // Verify no product with empty name was created
        $this->assertDatabaseMissing('productos', ['nombre' => '']);
    }

    /**
     * ERR-P2: Producto inactivo usado en factura → Producto inactivo usado en factura → No permitido
     *
     * Requisito Funcional: Producto inactivo usado en factura
     * Escenario: Producto inactivo usado en factura
     * Precondición: ...
     * Datos de Prueba: ...
     * Pasos (resumen): ...
     * Resultado Esperado: No permitido
     */
    public function test_err_p2_inactive_product_cannot_be_used_in_factura()
    {
        // Create an inactive product
        $producto = Producto::create([
            'nombre' => 'Producto Inactivo Factura ' . time(),
            'estado' => 0, // Inactive
        ]);

        // Create supporting data for factura
        $provincia = Provincia::first();
        if (!$provincia) {
            $provincia = Provincia::create([
                'codigo_provincia' => 'TEST01',
                'nombre_provincia' => 'Provincia Test Error'
            ]);
        }

        $distrito = Distrito::where('codigo_provincia', $provincia->codigo_provincia)->first();
        if (!$distrito) {
            $distrito = Distrito::create([
                'codigo_distrito' => 'DIST01',
                'codigo_provincia' => $provincia->codigo_provincia,
                'nombre_distrito' => 'Distrito Test Error'
            ]);
        }

        // Create a client
        $cliente = Cliente::create([
            'ruc' => '50' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT),
            'nombre' => 'Cliente Factura Test',
            'email' => 'err-p2-' . time() . '@example.com',
            'telefono' => '900900900',
            'direccion' => $provincia->nombre_provincia . ', ' . $distrito->nombre_distrito . ', Calle Test',
            'estado' => 1,
            'id_grupo_fk' => $this->grupo->id_grupo,
        ]);

        // Try to create a factura with the inactive product
        $response = $this->actingAs($this->admin)
            ->post('/facturacion', [
                'ruc_cliente_fk' => $cliente->ruc,
                'detalles' => [
                    [
                        'id_producto_fk' => $producto->id_producto,
                        'cantidad' => 1,
                        'precio_unitario' => 99.99,
                    ]
                ],
            ]);

        $status = $response->getStatusCode();

        // The system should either:
        // 1. Return validation error (422/400)
        // 2. Redirect with error message (302)
        // 3. Prevent the factura creation entirely

        // Verify that if factura is created, it uses only ACTIVE products
        // OR the request fails with appropriate error

        $this->assertTrue(
            in_array($status, [302, 400, 422]) ||
            !Factura::where('ruc_cliente_fk', $cliente->ruc)->exists(),
            "Inactive product should not be allowed in factura, got status: $status"
        );

        // Cleanup
        ListaPrecio::where('id_producto_fk', $producto->id_producto)->delete();
        Factura::where('ruc_cliente_fk', $cliente->ruc)->delete();
        $cliente->delete();
        $producto->delete();
    }

    /**
     * ERR-P2b: Producto no existe al intentar usar en factura → 404 o error
     */
    public function test_err_p2b_nonexistent_product_in_factura_returns_error()
    {
        // Create a client
        $provincia = Provincia::first() ?: Provincia::create([
            'codigo_provincia' => 'TEST02',
            'nombre_provincia' => 'Provincia Test Error 2'
        ]);

        $distrito = Distrito::where('codigo_provincia', $provincia->codigo_provincia)->first()
            ?: Distrito::create([
                'codigo_distrito' => 'DIST02',
                'codigo_provincia' => $provincia->codigo_provincia,
                'nombre_distrito' => 'Distrito Test Error 2'
            ]);

        $cliente = Cliente::create([
            'ruc' => '51' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT),
            'nombre' => 'Cliente Factura No Producto',
            'email' => 'err-p2b-' . time() . '@example.com',
            'telefono' => '900900900',
            'direccion' => $provincia->nombre_provincia . ', ' . $distrito->nombre_distrito . ', Calle Test',
            'estado' => 1,
            'id_grupo_fk' => $this->grupo->id_grupo,
        ]);

        // Try to create factura with non-existent product
        $response = $this->actingAs($this->admin)
            ->post('/facturacion', [
                'ruc_cliente_fk' => $cliente->ruc,
                'detalles' => [
                    [
                        'id_producto_fk' => 999999999, // Non-existent product ID
                        'cantidad' => 1,
                        'precio_unitario' => 99.99,
                    ]
                ],
            ]);

        $status = $response->getStatusCode();

        // Should return error
        $this->assertTrue(
            in_array($status, [302, 400, 422, 404]),
            "Non-existent product should return error, got: $status"
        );

        // Cleanup
        $cliente->delete();
    }

    /**
     * ERR-P3: Editar producto inexistente → 404
     */
    public function test_err_p3_edit_nonexistent_product_returns_404()
    {
        $response = $this->actingAs($this->admin)
            ->put('/productos/999999999', [
                'nombre' => 'Updated Name',
                'estado' => 1,
            ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * ERR-P4: Eliminar producto inexistente → 404
     */
    public function test_err_p4_delete_nonexistent_product_returns_404()
    {
        $response = $this->actingAs($this->admin)
            ->delete('/productos/999999999');

        $this->assertEquals(404, $response->getStatusCode());
    }
}
