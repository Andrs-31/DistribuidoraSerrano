<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Durante los tests aceptamos cualquier valor para la regla 'captcha'
        Validator::extend('captcha', function () {
            return true;
        });
    }

    /** CE-L1: Login (Credenciales válidas) */
    public function test_login_with_valid_credentials_redirects_to_dashboard()
    {
        Auth::shouldReceive('attempt')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn(null);

        $response = $this->post('/login', [
            'email' => 'admin@demo.com',
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        $response->assertRedirect(route('dashboard.index'));
        $response->assertSessionHas('success', 'Inicio de sesión exitoso.');
    }

    /** CE-L2: Login (Usuario inexistente) */
    public function test_login_with_nonexistent_user_shows_error()
    {
        Auth::shouldReceive('attempt')->once()->andReturn(false);
        // Cuando mockeamos Auth::attempt, Laravel internamente puede llamar a
        // Auth::guard() desde helpers como assertGuest(). Stubeamos guard
        // para evitar errores de Mockery al invocar métodos como guest()/check().
        Auth::shouldReceive('guard')->andReturn(new class {
            public function guest() { return true; }
            public function check() { return false; }
        });

        $response = $this->post('/login', [
            'email' => 'no_existe@demo.com',
            'password' => 'Probando123',
            'g-recaptcha-response' => 'ok',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Credenciales incorrectas.');
        $this->assertGuest();
    }

    /** CE-L3: Login (Contraseña incorrecta) */
    public function test_login_with_incorrect_password_shows_error()
    {
        Auth::shouldReceive('attempt')->once()->andReturn(false);
        Auth::shouldReceive('guard')->andReturn(new class {
            public function guest() { return true; }
            public function check() { return false; }
        });

        $response = $this->post('/login', [
            'email' => 'clientes@demo.com',
            'password' => 'Incorrecta999',
            'g-recaptcha-response' => 'ok',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Credenciales incorrectas.');
        $this->assertGuest();
    }

    /** CE-L4: Login (Validación campos requeridos) */
    public function test_login_required_fields_validation()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
            'g-recaptcha-response' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password', 'g-recaptcha-response']);
    }

    /** CE-L5: Login (Validación formato de email) */
    public function test_login_invalid_email_format_shows_validation_error()
    {
        $response = $this->post('/login', [
            'email' => 'facturademo.com',
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** CE-L6: Acceso por rol facturación */
    public function test_login_as_facturacion_admin_role()
    {
        // El usuario factura@demo.com ya existe en TestDataSeeder con rol facturacion
        $response = $this->post('/login', [
            'email' => 'factura@demo.com',
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Inicio de sesión exitoso.');
        $this->assertAuthenticatedAs(User::where('email', 'factura@demo.com')->first());
    }

    /** CE-L7: Acceso por rol gestor de clientes */
    public function test_login_as_gestion_clientes_admin_role()
    {
        // El usuario clientes@demo.com ya existe en TestDataSeeder con rol gestion_clientes
        $response = $this->post('/login', [
            'email' => 'clientes@demo.com',
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Inicio de sesión exitoso.');
        $this->assertAuthenticatedAs(User::where('email', 'clientes@demo.com')->first());
    }

    /** CE-L8: Acceso por rol gestor de productos */
    public function test_login_as_gestion_productos_admin_role()
    {
        // El usuario productos@demo.com ya existe en TestDataSeeder con rol gestion_productos
        $response = $this->post('/login', [
            'email' => 'productos@demo.com',
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Inicio de sesión exitoso.');
        $this->assertAuthenticatedAs(User::where('email', 'productos@demo.com')->first());
    }
}
