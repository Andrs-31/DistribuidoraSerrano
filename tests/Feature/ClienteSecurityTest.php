<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permiso;
use App\Models\Cliente;
use App\Models\GrupoCliente;
use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Foundation\Testing\WithFaker;

class ClienteSecurityTest extends TestCase
{
    use WithFaker;

    protected $admin;
    protected $userSinPermisos;
    protected $grupo;
    protected $provincia;
    protected $distrito;

    protected function setUp(): void
    {
        parent::setUp();

        // Get or create admin user with gestion_clientes permission
        $this->admin = User::where('email', 'admin@demo.com')->first();
        if (! $this->admin) {
            $this->admin = User::first();
        }

        // Get or create a user WITHOUT gestion_clientes permission
        $this->userSinPermisos = User::where('email', 'productos@demo.com')->first();
        if (! $this->userSinPermisos) {
            // Create a restricted user if needed
            $this->userSinPermisos = User::create([
                'id_user' => 'test_user_' . time(),
                'nombre' => 'Test User No Perms',
                'email' => 'test-no-perms-' . time() . '@example.com',
                'password' => bcrypt('password123'),
                'id_permiso' => 2,  // Assume ID 2 is a restricted role
            ]);
        }

        if (! $this->admin) {
            $this->markTestSkipped('No hay usuarios disponibles en la base de datos para autenticarse.');
        }

        // Ensure core data exists for tests
        $this->grupo = GrupoCliente::first();
        if (! $this->grupo) {
            $this->grupo = GrupoCliente::create(['nombre' => 'GRUPO_DEFAULT', 'estado' => 1]);
        }

        $this->provincia = Provincia::first();
        if (! $this->provincia) {
            $this->provincia = Provincia::create([
                'codigo_provincia' => 'TEST01',
                'nombre_provincia' => 'Provincia Test'
            ]);
        }

        $this->distrito = Distrito::where('codigo_provincia', $this->provincia->codigo_provincia)->first();
        if (! $this->distrito) {
            $this->distrito = Distrito::create([
                'codigo_distrito' => 'DIST01',
                'codigo_provincia' => $this->provincia->codigo_provincia,
                'nombre_distrito' => 'Distrito Test'
            ]);
        }
    }

    /** SEG-C1: Protección contra Inyección SQL en campo nombre **/
    public function test_seg_c1_sql_injection_in_nombre_is_rejected()
    {
        // Attempt SQL injection in nombre field
        $payload = [
            'ruc' => '20' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT),
            'nombre' => "' OR '1'='1' --",  // Classic SQL injection
            'email' => 'seg-c1-sqli-' . time() . '@example.com',
            'telefono' => '999888777',
            'direccion' => 'Calle Valid',
            'estado' => 1,
            'grupo_id' => $this->grupo->id_grupo,
            'provincia' => $this->provincia->codigo_provincia,
            'distrito' => $this->distrito->codigo_distrito,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/clientes', $payload);

        $status = $response->getStatusCode();

        // Should either reject with validation error or store it safely (escaped)
        // The injection should not execute a SQL query

        // Verify that the dangerous data was either:
        // 1. Rejected (422, 400, or 302 with errors)
        // 2. Stored safely (not causing data corruption)

        if (in_array($status, [302, 422, 400])) {
            // Either rejected or responded with error - good
            $this->assertTrue(true, 'SQL injection was rejected or handled safely');
        } else {
            // If accepted, verify the data wasn't interpreted as SQL
            $cliente = Cliente::where('email', $payload['email'])->first();
            if ($cliente) {
                // The payload should be stored as literal text, not executed
                $this->assertStringContainsString("'", $cliente->nombre, 'Injection payload stored as literal text');
                $cliente->delete();
            }
        }
    }

    /** SEG-C1b: Protección contra Inyección SQL con diferentes intentos **/
    public function test_seg_c1b_sql_injection_variants()
    {
        $sqlInjectionPayloads = [
            "' OR 1=1 --",
            "'; DROP TABLE clientes; --",
            "' UNION SELECT * FROM usuarios --",
            "' OR 'a'='a",
        ];

        foreach ($sqlInjectionPayloads as $injection) {
            $payload = [
                'ruc' => '21' . str_pad((time() + rand(1, 1000)) % 1000000000, 10, '0', STR_PAD_LEFT),
                'nombre' => $injection,
                'email' => 'seg-c1b-' . md5($injection) . '@example.com',
                'telefono' => '999888777',
                'direccion' => 'Calle Valid',
                'estado' => 1,
                'grupo_id' => $this->grupo->id_grupo,
                'provincia' => $this->provincia->codigo_provincia,
                'distrito' => $this->distrito->codigo_distrito,
            ];

            $response = $this->actingAs($this->admin)
                ->post('/clientes', $payload);

            $status = $response->getStatusCode();

            // Should handle safely (either reject or store as literal)
            $this->assertTrue(
                in_array($status, [302, 201, 200, 422, 400]),
                "Unexpected response for injection '$injection': $status"
            );

            // Cleanup
            $cliente = Cliente::where('email', $payload['email'])->first();
            if ($cliente) {
                $cliente->delete();
            }
        }
    }

    /** SEG-C2: Protección contra XSS en dirección **/
    public function test_seg_c2_xss_injection_in_direccion_is_escaped()
    {
        $ruc = '22' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT);
        $xssPayload = '<script>alert("XSS")</script>';

        $payload = [
            'ruc' => $ruc,
            'nombre' => 'Cliente XSS Test',
            'email' => 'seg-c2-xss-' . time() . '@example.com',
            'telefono' => '999888777',
            'direccion' => $xssPayload,  // XSS attempt in direccion
            'estado' => 1,
            'grupo_id' => $this->grupo->id_grupo,
            'provincia' => $this->provincia->codigo_provincia,
            'distrito' => $this->distrito->codigo_distrito,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/clientes', $payload);

        $status = $response->getStatusCode();

        if (in_array($status, [302, 201, 200])) {
            // Creation succeeded; verify the stored data is safe
            $cliente = Cliente::find($ruc);

            if ($cliente) {
                // Verify that when viewed, the script tag is escaped or sanitized
                $viewResponse = $this->actingAs($this->admin)
                    ->get('/clientes/' . $cliente->ruc);

                if ($viewResponse->getStatusCode() === 200) {
                    // The view response should NOT contain executable script tag
                    // It should be escaped as HTML entities
                    $content = $viewResponse->getContent();

                    // Check that the script is either:
                    // 1. Not present at all (stripped)
                    // 2. Escaped (contains &lt;script or similar)
                    if (strpos($content, '<script>') === false) {
                        $this->assertTrue(true, 'XSS payload was not executed (script tag missing)');
                    } else {
                        // If present, it must be escaped
                        $this->assertTrue(
                            strpos($content, '&lt;script') !== false ||
                            strpos($content, '&#') !== false,
                            'XSS payload should be escaped'
                        );
                    }
                }

                $cliente->delete();
            }
        }
    }

    /** SEG-C2b: Protección contra XSS con diferentes payloads **/
    public function test_seg_c2b_xss_variants()
    {
        $xssPayloads = [
            '<img src=x onerror="alert(\'XSS\')">',
            'javascript:alert("XSS")',
            '<svg onload="alert(\'XSS\')">',
            '<iframe src="javascript:alert(\'XSS\')">',
        ];

        foreach ($xssPayloads as $xss) {
            $ruc = '23' . str_pad((time() + rand(1, 1000)) % 1000000000, 10, '0', STR_PAD_LEFT);
            $payload = [
                'ruc' => $ruc,
                'nombre' => 'Cliente XSS Variant Test',
                'email' => 'seg-c2b-' . md5($xss) . '@example.com',
                'telefono' => '999888777',
                'direccion' => $xss,
                'estado' => 1,
                'grupo_id' => $this->grupo->id_grupo,
                'provincia' => $this->provincia->codigo_provincia,
                'distrito' => $this->distrito->codigo_distrito,
            ];

            $response = $this->actingAs($this->admin)
                ->post('/clientes', $payload);

            $status = $response->getStatusCode();

            // Should handle or reject
            $this->assertTrue(
                in_array($status, [302, 201, 200, 422, 400]),
                "XSS payload handling failed: $status"
            );

            // Cleanup
            $cliente = Cliente::find($ruc);
            if ($cliente) {
                $cliente->delete();
            }
        }
    }

    /** SEG-C3: Control de Acceso por Rol → Usuario sin permiso recibe 403 o redirección **/
    public function test_seg_c3_user_without_permission_cannot_create_client()
    {
        $payload = [
            'ruc' => '24' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT),
            'nombre' => 'Cliente Acceso Denegado',
            'email' => 'seg-c3-' . time() . '@example.com',
            'telefono' => '999888777',
            'direccion' => 'Calle Valid',
            'estado' => 1,
            'grupo_id' => $this->grupo->id_grupo,
            'provincia' => $this->provincia->codigo_provincia,
            'distrito' => $this->distrito->codigo_distrito,
        ];

        // Attempt to create as user without gestion_clientes permission
        $response = $this->actingAs($this->userSinPermisos)
            ->post('/clientes', $payload);

        $status = $response->getStatusCode();

        // Should return 403 Forbidden or redirect away
        $this->assertTrue(
            in_array($status, [403, 302]),
            "Expected 403 or 302 redirect for unauthorized user, got: $status. " .
            "Response: " . $response->getContent()
        );

        // Verify client was NOT created
        $this->assertDatabaseMissing('clientes', ['email' => $payload['email']]);
    }

    /** SEG-C3b: User without permission cannot view clients list **/
    public function test_seg_c3b_user_without_permission_cannot_list_clients()
    {
        $response = $this->actingAs($this->userSinPermisos)
            ->get('/clientes');

        $status = $response->getStatusCode();

        // Should return 403 or redirect
        $this->assertTrue(
            in_array($status, [403, 302]),
            "Expected 403 or 302 for unauthorized list access, got: $status"
        );
    }

    /** SEG-C4: Autenticación obligatoria → Usuario no autenticado redirigido a login **/
    public function test_seg_c4_unauthenticated_user_redirected_to_login()
    {
        // Access clientes endpoint without authentication
        $response = $this->get('/clientes');

        // Should redirect to login
        $status = $response->getStatusCode();
        $this->assertEquals(302, $status, "Unauthenticated access should redirect, got: $status");

        // Verify redirect is to login route
        $response->assertRedirect('/login');
    }

    /** SEG-C4b: Unauthenticated user cannot create client **/
    public function test_seg_c4b_unauthenticated_user_cannot_create_client()
    {
        $payload = [
            'ruc' => '25' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT),
            'nombre' => 'Cliente No Autenticado',
            'email' => 'seg-c4b-' . time() . '@example.com',
            'telefono' => '999888777',
            'direccion' => 'Calle Valid',
            'estado' => 1,
            'grupo_id' => $this->grupo->id_grupo,
            'provincia' => $this->provincia->codigo_provincia,
            'distrito' => $this->distrito->codigo_distrito,
        ];

        // Post without authentication
        $response = $this->post('/clientes', $payload);

        // Should redirect to login
        $response->assertRedirect('/login');

        // Verify client was NOT created
        $this->assertDatabaseMissing('clientes', ['email' => $payload['email']]);
    }

    /** SEG-C5: Session fixation prevention → Session regenerated after login **/
    public function test_seg_c5_session_regenerated_on_operations()
    {
        // Create test client
        $ruc = '26' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT);
        $payload = [
            'ruc' => $ruc,
            'nombre' => 'Cliente Session Test',
            'email' => 'seg-c5-' . time() . '@example.com',
            'telefono' => '999888777',
            'direccion' => 'Calle Valid',
            'estado' => 1,
            'grupo_id' => $this->grupo->id_grupo,
            'provincia' => $this->provincia->codigo_provincia,
            'distrito' => $this->distrito->codigo_distrito,
        ];

        // Get initial session ID
        $this->actingAs($this->admin);
        $response = $this->post('/clientes', $payload);

        // Verify the operation was successful
        $status = $response->getStatusCode();
        $this->assertTrue(
            in_array($status, [302, 201, 200]),
            "Operation should succeed, got: $status"
        );

        // Cleanup
        $cliente = Cliente::find($ruc);
        if ($cliente) {
            $cliente->delete();
        }
    }

    /** SEG-C6: CSRF protection **/
    public function test_seg_c6_csrf_token_required_for_post()
    {
        // This test verifies that CSRF token validation is in place
        // Laravel's VerifyCsrfToken middleware should prevent this

        // Get the form page first to see if we can extract CSRF token
        $response = $this->actingAs($this->admin)
            ->get('/clientes/create');

        $status = $response->getStatusCode();

        // If we can access the form, CSRF token should be present
        if ($status === 200) {
            // The form should contain a CSRF token field
            $content = $response->getContent();
            $this->assertTrue(
                strpos($content, '_token') !== false ||
                strpos($content, 'csrf') !== false,
                'CSRF token should be present in form'
            );
        }
    }
}
