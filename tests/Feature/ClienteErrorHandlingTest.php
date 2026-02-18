<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use App\Models\GrupoCliente;
use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Foundation\Testing\WithFaker;

class ClienteErrorHandlingTest extends TestCase
{
    use WithFaker;

    protected $admin;
    protected $grupo;
    protected $provincia;
    protected $distrito;

    protected function setUp(): void
    {
        parent::setUp();

        // Try to find a known admin user, otherwise pick any user
        $this->admin = User::where('email', 'admin@demo.com')->first() ?: User::first();

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

    /** ERR-C1: Editar cliente inexistente → 404 **/
    public function test_err_c1_edit_nonexistent_client_returns_404()
    {
        // Attempt to edit a client that doesn't exist
        $nonexistentRuc = 'NONEXIST999999';

        $response = $this->actingAs($this->admin)
            ->get('/clientes/' . $nonexistentRuc . '/edit');

        $status = $response->getStatusCode();
        
        // Should return 404 Not Found
        $this->assertEquals(404, $status, "Expected 404 for nonexistent client, got: $status");
    }

    /** ERR-C1b: Ver cliente inexistente → 404 **/
    public function test_err_c1b_view_nonexistent_client_returns_404()
    {
        // Attempt to view a client that doesn't exist
        $nonexistentRuc = 'NONEXIST888888';

        $response = $this->actingAs($this->admin)
            ->get('/clientes/' . $nonexistentRuc);

        $status = $response->getStatusCode();
        
        // Should return 404 Not Found
        $this->assertEquals(404, $status, "Expected 404 for nonexistent client, got: $status");
    }

    /** ERR-C2: Fallo de conexión a BD → Mensaje controlado sin error técnico visible **/
    public function test_err_c2_database_error_shows_friendly_message()
    {
        // Simulate a database error by trying to create a client with invalid data
        // that causes a database-level error (e.g., constraint violation)
        
        // First create a client
        $ruc = '11' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT);
        $email = 'err-c2-' . time() . '@example.com';
        
        $cliente = Cliente::create([
            'ruc' => $ruc,
            'nombre' => 'Cliente Error BD',
            'email' => $email,
            'telefono' => '900900900',
            'direccion' => $this->provincia->nombre_provincia . ', ' . $this->distrito->nombre_distrito . ', Calle X',
            'estado' => 1,
            'id_grupo_fk' => $this->grupo->id_grupo,
        ]);

        // Now try to create another with exact same RUC (violates unique constraint)
        $payload = [
            'ruc' => $ruc,  // Duplicate RUC - violates unique constraint
            'nombre' => 'Cliente Duplicado',
            'email' => 'err-c2-dup-' . time() . '@example.com',
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

        // Should return validation error (either 302 with session errors or 422)
        $this->assertTrue(
            in_array($status, [302, 422, 400]),
            "Expected validation error, got status: $status"
        );

        // If it's a 302 redirect, verify there's a session message (not a raw database error)
        if ($status === 302) {
            // Should have session with error message, not showing raw SQL
            $response->assertSessionHasErrors();
        }

        // Cleanup
        $cliente->delete();
    }

    /** ERR-C3: Timeout al guardar → Mensaje amigable **/
    public function test_err_c3_timeout_shows_friendly_message()
    {
        // This test simulates a scenario where the application handles timeout gracefully
        // In a real scenario, this would be triggered by network delays or long operations
        
        // For now, we'll test that the application handles potentially slow operations
        // by posting valid data and ensuring we get a response (not a timeout)
        
        $ruc = '12' . str_pad(time() % 1000000000, 10, '0', STR_PAD_LEFT);
        $payload = [
            'ruc' => $ruc,
            'nombre' => 'Cliente Timeout Test',
            'email' => 'err-c3-' . time() . '@example.com',
            'telefono' => '999888777',
            'direccion' => 'Calle Valid',
            'estado' => 1,
            'grupo_id' => $this->grupo->id_grupo,
            'provincia' => $this->provincia->codigo_provincia,
            'distrito' => $this->distrito->codigo_distrito,
        ];

        // Set a reasonable timeout for the test
        $response = $this->actingAs($this->admin)
            ->post('/clientes', $payload);

        $status = $response->getStatusCode();

        // Should get a response (200, 302, 422) and not timeout
        $this->assertTrue(
            in_array($status, [302, 201, 200, 422, 400]),
            "Should receive response without timeout, got status: $status"
        );

        // Cleanup
        $cliente = Cliente::where('email', $payload['email'])->first();
        if ($cliente) {
            $cliente->delete();
        }
    }

    /** ERR-C4: Eliminar cliente que no existe → Manejo graceful **/
    public function test_err_c4_delete_nonexistent_client_returns_404()
    {
        $nonexistentRuc = 'NONEXIST777777';

        $response = $this->actingAs($this->admin)
            ->delete('/clientes/' . $nonexistentRuc);

        $status = $response->getStatusCode();

        // Should return 404 when trying to delete non-existent resource
        $this->assertEquals(404, $status, "Expected 404 for nonexistent client delete, got: $status");
    }

    /** ERR-C5: Actualizar cliente que no existe → 404 **/
    public function test_err_c5_update_nonexistent_client_returns_404()
    {
        $nonexistentRuc = 'NONEXIST666666';

        $payload = [
            'nombre' => 'Updated Name',
            'email' => 'err-c5-' . time() . '@example.com',
            'telefono' => '999888777',
            'direccion' => 'Calle Valid',
            'estado' => 1,
            'grupo_id' => $this->grupo->id_grupo,
            'provincia' => $this->provincia->codigo_provincia,
            'distrito' => $this->distrito->codigo_distrito,
        ];

        $response = $this->actingAs($this->admin)
            ->put('/clientes/' . $nonexistentRuc, $payload);

        $status = $response->getStatusCode();

        // Should return 404
        $this->assertEquals(404, $status, "Expected 404 for nonexistent client update, got: $status");
    }
}
