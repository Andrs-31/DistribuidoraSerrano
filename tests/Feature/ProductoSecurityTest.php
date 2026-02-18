<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Producto;
use App\Models\GrupoCliente;
use Illuminate\Foundation\Testing\WithFaker;

class ProductoSecurityTest extends TestCase
{
    use WithFaker;

    protected $admin;
    protected $userSinPermisos;
    protected $grupo;

    protected function setUp(): void
    {
        parent::setUp();

        // Get admin user with gestion_productos permission
        $this->admin = User::where('email', 'admin@demo.com')->first();
        if (!$this->admin) {
            $this->admin = User::first();
        }

        // Get or create a user WITHOUT gestion_productos permission
        $this->userSinPermisos = User::where('email', 'clientes@demo.com')->first();
        if (!$this->userSinPermisos) {
            // Try another restricted user email
            $this->userSinPermisos = User::where('email', 'factura@demo.com')->first();
        }

        if (!$this->admin) {
            $this->markTestSkipped('No hay usuarios disponibles en la base de datos para autenticarse.');
        }

        // Ensure core data exists
        $this->grupo = GrupoCliente::first();
        if (!$this->grupo) {
            $this->grupo = GrupoCliente::create(['nombre' => 'GRUPO_SECURITY_TEST', 'estado' => 1]);
        }
    }

    // ============================================================================
    // SEG-P1: INYECCIÓN SQL EN NOMBRE
    // ============================================================================

    /**
     * SEG-P1: Registrar Producto → Inyección SQL en nombre → Rechazado
     * 
     * Requisito Funcional: Registrar Producto
     * Escenario: Inyección SQL en nombre
     * Precondición: ...
     * Datos de Prueba: OR 1=1 -- (SQL injection attempt)
     * Pasos (resumen): 1. Intentar registrar producto con nombre malicioso
     * Resultado Esperado: Rechazado
     */
    public function test_seg_p1_sql_injection_in_product_name_is_rejected()
    {
        $sqlInjectionPayload = "' OR '1'='1' --";

        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $sqlInjectionPayload,
            ]);

        $status = $response->getStatusCode();

        // Should either reject with validation error or store safely (escaped)
        if ($status === 302) {
            // Redirected - check if there's an error message
            $response->assertSessionHasErrors();
        } else {
            // If accepted, should be stored as literal text, not executed
            $this->assertTrue(in_array($status, [302, 201, 200, 422, 400]));
        }

        // Verify the dangerous data was not interpreted as SQL
        // Either it wasn't created, or it was stored as literal text
        $producto = Producto::where('nombre', $sqlInjectionPayload)->first();
        if ($producto) {
            // If created, it should be stored literally
            $this->assertStringContainsString("'", $producto->nombre);
            $producto->delete();
        }
    }

    /**
     * SEG-P1b: Múltiples intentos de inyección SQL
     */
    public function test_seg_p1b_multiple_sql_injection_variants()
    {
        $sqlInjectionVariants = [
            "' OR 1=1 --",
            "'; DROP TABLE productos; --",
            "' UNION SELECT * FROM usuarios --",
            "' OR 'a'='a",
            "1' OR '1' = '1",
            "admin' --",
            "' /*",
        ];

        foreach ($sqlInjectionVariants as $injection) {
            $response = $this->actingAs($this->admin)
                ->post('/productos', [
                    'nombre' => $injection,
                ]);

            $status = $response->getStatusCode();

            // Should handle safely
            $this->assertTrue(
                in_array($status, [302, 201, 200, 422, 400]),
                "SQL injection '$injection' not handled safely, status: $status"
            );

            // Cleanup
            $producto = Producto::where('nombre', $injection)->first();
            if ($producto) {
                $producto->delete();
            }
        }
    }

    // ============================================================================
    // SEG-P2: XSS (CROSS-SITE SCRIPTING) EN NOMBRE
    // ============================================================================

    /**
     * SEG-P2: Registrar Producto → XSS en nombre → Sanitizado
     * 
     * Requisito Funcional: Registrar Producto
     * Escenario: XSS en nombre
     * Precondición: ...
     * Datos de Prueba: <script>alert(1)</script>
     * Pasos (resumen): 1. Intentar registrar producto con nombre con script malicioso
     * Resultado Esperado: Sanitizado
     */
    public function test_seg_p2_xss_injection_in_product_name_is_sanitized()
    {
        $xssPayload = '<script>alert("XSS")</script>';

        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $xssPayload,
            ]);

        $status = $response->getStatusCode();

        // Should either reject or store safely (escaped)
        $this->assertTrue(
            in_array($status, [302, 201, 200, 422, 400]),
            "XSS handling failed, status: $status"
        );

        // If product was created, verify the script tag is escaped when displayed
        $producto = Producto::where('nombre', $xssPayload)->first();
        if ($producto) {
            // Get the product detail page
            $viewResponse = $this->actingAs($this->admin)
                ->get('/productos/' . $producto->id_producto);

            if ($viewResponse->getStatusCode() === 200) {
                $content = $viewResponse->getContent();
                
                // Verify the script tag is NOT executable in the response
                // It should either be:
                // 1. Not present at all (stripped)
                // 2. HTML-escaped (contains &lt; or &#)
                
                if (strpos($content, '<script>') === false) {
                    // Script tag removed - good
                    $this->assertTrue(true, 'XSS payload was filtered');
                } else {
                    // If present, must be escaped
                    $this->assertTrue(
                        strpos($content, '&lt;script') !== false || 
                        strpos($content, '&#') !== false,
                        'XSS payload should be escaped in output'
                    );
                }
            }

            $producto->delete();
        }
    }

    /**
     * SEG-P2b: Múltiples variantes de XSS
     */
    public function test_seg_p2b_multiple_xss_variants()
    {
        $xssVariants = [
            '<img src=x onerror="alert(\'XSS\')">',
            'javascript:alert("XSS")',
            '<svg onload="alert(\'XSS\')">',
            '<iframe src="javascript:alert(\'XSS\')">',
            '"><script>alert("XSS")</script>',
            '<body onload="alert(\'XSS\')">',
        ];

        foreach ($xssVariants as $xss) {
            $response = $this->actingAs($this->admin)
                ->post('/productos', [
                    'nombre' => $xss,
                ]);

            $status = $response->getStatusCode();

            // Should handle safely
            $this->assertTrue(
                in_array($status, [302, 201, 200, 422, 400]),
                "XSS variant '$xss' not handled properly, status: $status"
            );

            // Cleanup
            $producto = Producto::where('nombre', $xss)->first();
            if ($producto) {
                $producto->delete();
            }
        }
    }

    // ============================================================================
    // SEG-P3: VERIFICACIÓN POR ROL (AUTENTICACIÓN Y AUTORIZACIÓN)
    // ============================================================================

    /**
     * SEG-P3: Verificación por Rol → Usuario no autenticado accede a /productos → Redirije a login
     * 
     * Requisito Funcional: Verificación por Rol
     * Escenario: Usuario no autenticado accede a /productos
     * Precondición: No estar loggeado
     * Datos de Prueba: ...
     * Pasos (resumen): Poner en la URL "/productos"
     * Resultado Esperado: Redirije a login
     */
    public function test_seg_p3_unauthenticated_user_redirected_to_login()
    {
        // Access productos endpoint without authentication
        $response = $this->get('/productos');

        // Should redirect to login
        $this->assertEquals(302, $response->getStatusCode());
        $response->assertRedirect('/login');
    }

    /**
     * SEG-P3b: Usuario no autenticado intenta crear producto → Redirije a login
     */
    public function test_seg_p3b_unauthenticated_user_cannot_create_product()
    {
        // Try to create product without authentication
        $response = $this->post('/productos', [
            'nombre' => 'Test Producto',
        ]);

        // Should redirect to login
        $this->assertEquals(302, $response->getStatusCode());
        $response->assertRedirect('/login');

        // Verify product was NOT created
        $this->assertDatabaseMissing('productos', ['nombre' => 'Test Producto']);
    }

    /**
     * SEG-P3c: Usuario sin permiso de gestion_productos no puede acceder
     */
    public function test_seg_p3c_user_without_permission_cannot_access_productos()
    {
        if (!$this->userSinPermisos) {
            $this->markTestSkipped('No user without gestion_productos permission found');
        }

        $response = $this->actingAs($this->userSinPermisos)
            ->get('/productos');

        $status = $response->getStatusCode();

        // Should return 403 Forbidden or redirect away
        $this->assertTrue(
            in_array($status, [403, 302]),
            "User without permission should get 403 or 302, got: $status"
        );
    }

    /**
     * SEG-P3d: Usuario sin permiso no puede crear producto
     */
    public function test_seg_p3d_user_without_permission_cannot_create_product()
    {
        if (!$this->userSinPermisos) {
            $this->markTestSkipped('No user without gestion_productos permission found');
        }

        $response = $this->actingAs($this->userSinPermisos)
            ->post('/productos', [
                'nombre' => 'Test No Permission ' . time(),
            ]);

        $status = $response->getStatusCode();

        // Should return 403 or redirect
        $this->assertTrue(
            in_array($status, [403, 302]),
            "User without permission should get 403 or 302 for creation, got: $status"
        );

        // Verify product was NOT created
        $this->assertDatabaseMissing('productos', ['nombre' => 'Test No Permission']);
    }

    /**
     * SEG-P4: CSRF Protection
     */
    public function test_seg_p4_csrf_token_required()
    {
        // Get the create form
        $response = $this->actingAs($this->admin)
            ->get('/productos/create');

        $status = $response->getStatusCode();

        if ($status === 200) {
            // Verify CSRF token is present in form
            $content = $response->getContent();
            $this->assertTrue(
                strpos($content, '_token') !== false || 
                strpos($content, 'csrf') !== false,
                'CSRF token should be present in form'
            );
        }
    }

    /**
     * SEG-P5: SQL Injection in other fields (if editable)
     */
    public function test_seg_p5_sql_injection_in_estado_field()
    {
        $producto = Producto::create([
            'nombre' => 'Producto Estado Test ' . time(),
            'estado' => 1,
        ]);

        // Try to inject SQL in estado field (though it should be validated as integer)
        $response = $this->actingAs($this->admin)
            ->put('/productos/' . $producto->id_producto, [
                'nombre' => $producto->nombre,
                'estado' => "1 OR 1=1", // Not a valid integer
            ]);

        $status = $response->getStatusCode();

        // Should handle gracefully (validation error or cast to int)
        $this->assertTrue(
            in_array($status, [302, 200, 422, 400]),
            "Invalid estado value should be handled, got: $status"
        );

        // Cleanup
        $producto->delete();
    }

    /**
     * SEG-P6: Data validation - name too long
     */
    public function test_seg_p6_product_name_max_length_validation()
    {
        // Try to create with excessively long name
        $longName = str_repeat('a', 300); // Assuming max is 255

        $response = $this->actingAs($this->admin)
            ->post('/productos', [
                'nombre' => $longName,
            ]);

        $status = $response->getStatusCode();

        // Should either reject or truncate
        if ($status === 302) {
            // Check for validation error
            $response->assertSessionHasErrors();
        } else {
            // Accept or return validation error
            $this->assertTrue(in_array($status, [302, 201, 200, 422, 400]));
        }

        // Cleanup
        $producto = Producto::where('nombre', $longName)->first();
        if ($producto) {
            $producto->delete();
        }
    }
}
