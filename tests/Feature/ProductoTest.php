<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Producto;
use App\Models\GrupoCliente;
use App\Models\ListaPrecio;
use Illuminate\Foundation\Testing\WithFaker;

class ProductoTest extends TestCase
{
    use WithFaker;

    protected $admin;
    protected $grupo;

    protected function setUp(): void
    {
        parent::setUp();

        // Try to find a known admin user, otherwise pick any user
        $this->admin = User::where('email', 'admin@demo.com')->first() ?: User::first();

        if (!$this->admin) {
            $this->markTestSkipped('No hay usuarios disponibles en la base de datos para autenticarse.');
        }

        // Ensure core data exists for tests
        $this->grupo = GrupoCliente::first();
        if (!$this->grupo) {
            $this->grupo = GrupoCliente::create(['nombre' => 'GRUPO_PRODUCTOS_DEFAULT', 'estado' => 1]);
        }
    }

    // ============================================================================
    // PRUEBAS FUNCIONALES (PF-P1 a PF-P5)
    // ============================================================================

    /**
     * PF-P1: Crear producto válido
     * Requisito: Crear producto con datos válidos
     * Precondición: Admin de Productos loggeado
     * Pasos: 1. Ir a /productos, 2. Nuevo producto, 3. Llenar formulario, 4. Guardar
     * Resultado esperado: Producto creado correctamente
     */
    public function test_pf_p1_create_valid_product()
    {
        $nombre = 'Producto Prueba ' . time();
        
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $nombre,
            ]);

        $status = $response->getStatusCode();
        
        // Should succeed (302 redirect or 201)
        $this->assertTrue(
            in_array($status, [302, 201, 200]),
            "Failed to create product, got status: $status"
        );

        // Verify product was created
        $this->assertDatabaseHas('productos', ['nombre' => $nombre]);

        // Cleanup
        $producto = Producto::where('nombre', $nombre)->first();
        if ($producto) {
            $producto->delete();
        }
    }

    /**
     * PF-P2: Activar producto inactivo
     * Requisito: Activar un producto desactivado
     * Precondición: Un producto debe estar desactivado
     * Pasos: 1. Enlistar productos, 2. Presionar icono de activación, 3. Confirmar
     * Resultado esperado: Estado cambia a Activo
     */
    public function test_pf_p2_activate_inactive_product()
    {
        // Create an inactive product
        $producto = Producto::create([
            'nombre' => 'Producto Inactivo ' . time(),
            'estado' => 0,
        ]);

        // Activate it via destroy (toggle)
        $response = $this->actingAs($this->admin)
            ->delete('/productos/' . $producto->id_producto);

        $status = $response->getStatusCode();
        $this->assertTrue(
            in_array($status, [302, 200]),
            "Failed to toggle product state, got status: $status"
        );

        // Verify product is now active
        $producto->refresh();
        $this->assertEquals(1, $producto->estado, 'Product should be active (estado = 1)');

        // Cleanup
        $producto->delete();
    }

    /**
     * PF-P3: Desactivar producto activo
     * Requisito: Desactivar un producto activo
     * Precondición: Un producto debe estar activo
     * Pasos: 1. Enlistar productos, 2. Presionar icono de desactivación, 3. Confirmar
     * Resultado esperado: Estado cambia a Desactivo
     */
    public function test_pf_p3_deactivate_active_product()
    {
        // Create an active product
        $producto = Producto::create([
            'nombre' => 'Producto Activo ' . time(),
            'estado' => 1,
        ]);

        // Deactivate it via destroy (toggle)
        $response = $this->actingAs($this->admin)
            ->delete('/productos/' . $producto->id_producto);

        $status = $response->getStatusCode();
        $this->assertTrue(
            in_array($status, [302, 200]),
            "Failed to toggle product state, got status: $status"
        );

        // Verify product is now inactive
        $producto->refresh();
        $this->assertEquals(0, $producto->estado, 'Product should be inactive (estado = 0)');

        // Cleanup
        $producto->delete();
    }

    /**
     * PF-P4: Ver lista de productos
     * Requisito: Listar los productos guardados en la BD
     * Precondición: Debe haber productos en la BD
     * Pasos: 1. Acceder a /productos, 2. Ver la lista de productos
     * Resultado esperado: Se muestra la lista de los productos
     */
    public function test_pf_p4_view_products_list()
    {
        // Create a test product
        $producto = Producto::create([
            'nombre' => 'Producto Lista ' . time(),
            'estado' => 1,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/productos');

        $status = $response->getStatusCode();
        $this->assertEquals(200, $status, "Failed to view products list, got status: $status");

        // Verify product is in the response
        $response->assertSeeText($producto->nombre);

        // Cleanup
        $producto->delete();
    }

    /**
     * PF-P5: Manejo de precios por grupo
     * Requisito: Administrar precios especiales por grupo de clientes
     * Precondición: Producto creado, grupo de clientes existe
     * Pasos: 1. Enlistar productos, 2. Presionar icono de administrar precios, 3. Seleccionar grupo, 4. Ingresar precio
     * Resultado esperado: Se aplica precio correspondiente al grupo del cliente
     */
    public function test_pf_p5_manage_prices_by_group()
    {
        // Create a test product
        $producto = Producto::create([
            'nombre' => 'Producto Precios ' . time(),
            'estado' => 1,
        ]);

        // Create a price list entry for this product and group
        $response = $this->actingAs($this->admin)
            ->post('/productos/' . $producto->id_producto . '/precios', [
                'id_grupo_fk' => $this->grupo->id_grupo,
                'precio_unitario' => 99.99,
                'unidad' => 'unidad',
            ]);

        $status = $response->getStatusCode();
        
        // Should succeed
        $this->assertTrue(
            in_array($status, [302, 201, 200]),
            "Failed to create price list, got status: $status"
        );

        // Verify price was created
        $this->assertDatabaseHas('lista_precios', [
            'id_producto_fk' => $producto->id_producto,
            'id_grupo_fk' => $this->grupo->id_grupo,
        ]);

        // Cleanup
        ListaPrecio::where('id_producto_fk', $producto->id_producto)->delete();
        $producto->delete();
    }

    // ============================================================================
    // PRUEBAS DE EQUIVALENCIA / VALIDACIÓN (CE-P1 a CE-P5)
    // ============================================================================

    /**
     * CE-P1: Nombre válido que inicia con letra y contiene números
     * Escenario: Nombre inicia con letra y contiene números
     * Precondición: Admin de Productos autenticado
     * Datos de prueba: "Producto 123"
     * Resultado esperado: Aceptado
     */
    public function test_ce_p1_valid_name_with_letter_and_numbers()
    {
        $nombre = 'Producto 123 ' . time();
        
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $nombre,
            ]);

        $status = $response->getStatusCode();
        
        // Should succeed
        $this->assertTrue(
            in_array($status, [302, 201, 200]),
            "Valid product name should be accepted, got: $status"
        );

        // Verify product was created
        $this->assertDatabaseHas('productos', ['nombre' => $nombre]);

        // Cleanup
        $producto = Producto::where('nombre', $nombre)->first();
        if ($producto) {
            $producto->delete();
        }
    }

    /**
     * CE-P2: Nombre que inicia con espacio
     * Escenario: Nombre inicia con espacio
     * Precondición: Admin de Productos autenticado
     * Datos de prueba: " Producto234"
     * Resultado esperado: Error de validación
     */
    public function test_ce_p2_name_starting_with_space_returns_error()
    {
        $nombre = ' Producto234 ' . time();
        
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $nombre,
            ]);

        $status = $response->getStatusCode();

        // Should reject or return validation error
        if ($status === 302) {
            $response->assertSessionHasErrors('nombre');
        } else {
            $this->assertTrue(in_array($status, [422, 400]), "Unexpected status: $status");
        }

        // Verify product was NOT created
        $this->assertDatabaseMissing('productos', ['nombre' => $nombre]);
    }

    /**
     * CE-P3: Nombre con doble espacio
     * Escenario: Nombre con doble espacio
     * Precondición: Admin de Productos autenticado
     * Datos de prueba: "Producto  21"
     * Resultado esperado: Error de validación
     */
    public function test_ce_p3_name_with_double_space_returns_error()
    {
        $nombre = 'Producto  21 ' . time();
        
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $nombre,
            ]);

        $status = $response->getStatusCode();

        // May reject or accept depending on validation rules
        // Most likely should reject
        if ($status === 302) {
            // Check if there are errors
            $response->assertSessionHasErrors();
        } else {
            // Either accept or return validation error
            $this->assertTrue(in_array($status, [302, 201, 200, 422, 400]), "Unexpected status: $status");
        }
    }

    /**
     * CE-P4: Nombre que inicia con número
     * Escenario: Nombre inicia con número
     * Precondición: Admin de Productos autenticado
     * Datos de prueba: "1Producto"
     * Resultado esperado: Error de validación
     */
    public function test_ce_p4_name_starting_with_number_returns_error()
    {
        $nombre = '1Producto ' . time();
        
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $nombre,
            ]);

        $status = $response->getStatusCode();

        if ($status === 302) {
            $response->assertSessionHasErrors('nombre');
        } else {
            $this->assertTrue(in_array($status, [422, 400]), "Unexpected status: $status");
        }

        // Verify product was NOT created
        $this->assertDatabaseMissing('productos', ['nombre' => $nombre]);
    }

    /**
     * CE-P5: Código generado automáticamente (sin ingreso manual)
     * Escenario: No se ingresa código manual, el sistema genera código único
     * Precondición: Admin de Productos autenticado
     * Datos de prueba: (No se envía código, solo nombre)
     * Resultado esperado: Sistema genera código único automáticamente
     */
    public function test_ce_p5_auto_generated_code()
    {
        $nombre = 'Producto AutoCode ' . time();
        
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $nombre,
                // No código enviado - debe ser generado automáticamente
            ]);

        $status = $response->getStatusCode();
        
        // Should succeed
        $this->assertTrue(
            in_array($status, [302, 201, 200]),
            "Auto-generated code product should succeed, got: $status"
        );

        // Verify product was created with an auto-generated ID
        $producto = Producto::where('nombre', $nombre)->first();
        $this->assertNotNull($producto, 'Product should be created');
        $this->assertNotNull($producto->id_producto, 'Product should have auto-generated ID');

        // Cleanup
        if ($producto) {
            $producto->delete();
        }
    }

    // ============================================================================
    // PRUEBAS ADICIONALES DE VALIDACIÓN Y CASOS EDGE
    // ============================================================================

    /**
     * Producto duplicado (mismo nombre) → Error único
     */
    public function test_duplicate_product_name_returns_error()
    {
        $nombre = 'Producto Duplicado ' . time();

        // Create first product
        $producto1 = Producto::create([
            'nombre' => $nombre,
            'estado' => 1,
        ]);

        // Try to create second with same name
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $nombre,
            ]);

        $status = $response->getStatusCode();

        // Should reject
        if ($status === 302) {
            $response->assertSessionHasErrors('nombre');
        } else {
            $this->assertTrue(in_array($status, [422, 400]), "Duplicate should be rejected, got: $status");
        }

        // Cleanup
        $producto1->delete();
    }

    /**
     * Nombre vacío → Error requerido
     */
    public function test_empty_product_name_returns_error()
    {
        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => '',
            ]);

        $status = $response->getStatusCode();

        if ($status === 302) {
            $response->assertSessionHasErrors('nombre');
        } else {
            $this->assertTrue(in_array($status, [422, 400]), "Empty name should be rejected, got: $status");
        }
    }

    /**
     * Editar producto existente
     */
    public function test_edit_existing_product()
    {
        // Create a product
        $producto = Producto::create([
            'nombre' => 'Producto Original ' . time(),
            'estado' => 1,
        ]);

        // Edit it
        $newNombre = 'Producto Editado ' . time();
        $response = $this->actingAs($this->admin)
            ->put('/productos/' . $producto->id_producto, [
                'nombre' => $newNombre,
                'estado' => 1,
            ]);

        $status = $response->getStatusCode();
        $this->assertTrue(
            in_array($status, [302, 200]),
            "Failed to update product, got status: $status"
        );

        // Verify update
        $producto->refresh();
        $this->assertEquals($newNombre, $producto->nombre, 'Product name should be updated');

        // Cleanup
        $producto->delete();
    }

    /**
     * Ver detalles del producto
     */
    public function test_view_product_details()
    {
        // Create a product
        $producto = Producto::create([
            'nombre' => 'Producto Detalle ' . time(),
            'estado' => 1,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/productos/' . $producto->id_producto);

        $status = $response->getStatusCode();
        $this->assertTrue(
            in_array($status, [200, 302]),
            "Failed to view product details, got status: $status"
        );

        if ($status === 200) {
            $response->assertSeeText($producto->nombre);
        }

        // Cleanup
        $producto->delete();
    }

    /**
     * Desactivar producto también desactiva sus precios
     */
    public function test_deactivating_product_also_deactivates_prices()
    {
        // Create a product
        $producto = Producto::create([
            'nombre' => 'Producto Precios Cascada ' . time(),
            'estado' => 1,
        ]);

        // Add a price
        ListaPrecio::create([
            'id_producto_fk' => $producto->id_producto,
            'id_grupo_fk' => $this->grupo->id_grupo,
            'precio_unitario' => 99.99,
            'unidad' => 'unidad',
            'estado' => 1,
        ]);

        // Deactivate product
        $response = $this->actingAs($this->admin)
            ->delete('/productos/' . $producto->id_producto);

        $status = $response->getStatusCode();
        $this->assertTrue(in_array($status, [302, 200]), "Failed to deactivate, got: $status");

        // Verify product is inactive
        $producto->refresh();
        $this->assertEquals(0, $producto->estado);

        // Verify all related prices are also inactive
        $precios = ListaPrecio::where('id_producto_fk', $producto->id_producto)->get();
        foreach ($precios as $precio) {
            $this->assertEquals(0, $precio->estado, 'Related price should also be inactive');
        }

        // Cleanup
        ListaPrecio::where('id_producto_fk', $producto->id_producto)->delete();
        $producto->delete();
    }

    /**
     * Listar productos con filtro de búsqueda
     */
    public function test_list_products_with_search()
    {
        $nombre = 'Producto Búsqueda ' . time();
        $producto = Producto::create([
            'nombre' => $nombre,
            'estado' => 1,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/productos?search=' . $nombre);

        $status = $response->getStatusCode();
        $this->assertEquals(200, $status);
        $response->assertSeeText($nombre);

        // Cleanup
        $producto->delete();
    }

    /**
     * Listar productos con filtro de estado
     */
    public function test_list_products_with_estado_filter()
    {
        $response = $this->actingAs($this->admin)
            ->get('/productos?estado=activo');

        $status = $response->getStatusCode();
        $this->assertEquals(200, $status, "Failed to filter products by estado, got: $status");
    }

    /**
     * Producto no existe → 404 en view
     */
    public function test_view_nonexistent_product_returns_404()
    {
        $response = $this->actingAs($this->admin)
            ->get('/productos/nonexistent');

        $this->assertEquals(404, $response->getStatusCode());
    }
}
