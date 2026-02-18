<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class LoginSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Durante los tests aceptamos cualquier valor para la regla 'captcha'
        Validator::extend('captcha', function () {
            return true;
        });
    }

    /** SEG-1: Intento de SQL Injection en usuario */
    public function test_sql_injection_in_username()
    {
        $response = $this->post('/login', [
            'email' => "OR '1'='1",
            'password' => 'cualquier_pass',
            'g-recaptcha-response' => 'ok',
        ]);

        // El sistema debe rechazar la solicitud (no debe permitir acceso)
        $this->assertGuest();
        $this->assertTrue(
            $response->getSession()->has('error') || $response->getSession()->has('errors')
        );
    }

    /** SEG-2: Intento de SQL Injection en contraseña */
    public function test_sql_injection_in_password()
    {
        $response = $this->post('/login', [
            'email' => 'admin@demo.com',
            'password' => "OR '1'='1",
            'g-recaptcha-response' => 'ok',
        ]);

        // El sistema debe rechazar la solicitud (no debe permitir acceso)
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Credenciales incorrectas.');
        $this->assertGuest();
    }

    /** SEG-3: Caracteres prohibidos en usuario (script injection) */
    public function test_script_injection_in_username()
    {
        $response = $this->post('/login', [
            'email' => '<script>alert("xss")</script>@demo.com',
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        // El sistema debe validar y rechazar
        $response->assertSessionHasErrors(['email']);
    }

    /** SEG-4: POST vacío (sin datos) */
    public function test_empty_post_request()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
            'g-recaptcha-response' => '',
        ]);

        // El sistema debe validar campos requeridos
        $response->assertSessionHasErrors(['email', 'password', 'g-recaptcha-response']);
    }

    /** SEG-5: Modificar request válido con dev tools (manipular campos) */
    public function test_request_manipulation_with_extra_fields()
    {
        $response = $this->post('/login', [
            'email' => 'admin@demo.com',
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
            'role_override' => 'admin', // Campo adicional malicioso
            'bypass_captcha' => '1',     // Intento de evadir captcha
        ]);

        // El sistema debe ignorar campos no esperados y procesar normalmente
        // Si las credenciales son válidas, debe autenticar
        $response->assertRedirect();
        // Los campos adicionales no deben afectar la autenticación
    }

    /** SEG-6: Intento de fuerza bruta (múltiples intentos fallidos) */
    public function test_multiple_failed_login_attempts()
    {
        // Realizar 5 intentos fallidos
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'admin@demo.com',
                'password' => 'contraseña_incorrecta_' . $i,
                'g-recaptcha-response' => 'ok',
            ]);

            $response->assertSessionHas('error', 'Credenciales incorrectas.');
        }

        // Todos los intentos deben ser rechazados
        $this->assertGuest();
    }

    /** SEG-7: Validación de Email válido (RFC 5322) */
    public function test_invalid_email_format_variations()
    {
        $invalidEmails = [
            'usuario_sin_arroba',
            '@nodomain.com',
            'usuario@',
            'usuario@.com',
            'usuario@domain.',
            'usuario @domain.com', // espacio en el email
        ];

        foreach ($invalidEmails as $email) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => '123456',
                'g-recaptcha-response' => 'ok',
            ]);

            // Debe no autenticarse y presentar error/validación
            $this->assertGuest();
            $this->assertTrue(
                $response->getSession()->has('errors') || $response->getSession()->has('error')
            );
        }
    }

    /** SEG-8: Header injection attempt */
    public function test_header_injection_attempt()
    {
        $response = $this->post('/login', [
            'email' => "admin@demo.com\r\nSet-Cookie: admin=1",
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        // Email con newlines debe ser considerado inválido
        $response->assertSessionHasErrors(['email']);
    }

    /** SEG-9: Null byte injection */
    public function test_null_byte_injection()
    {
        $response = $this->post('/login', [
            'email' => "admin@demo.com\x00",
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        // Null bytes deben ser sanitizados/rechazados (no autenticación)
        $this->assertGuest();
        $this->assertTrue(
            $response->getSession()->has('errors') || $response->getSession()->has('error')
        );
    }

    /** SEG-10: Intento de enumeración de usuarios */
    public function test_user_enumeration_attack()
    {
        // Intento 1: usuario que existe
        $response1 = $this->post('/login', [
            'email' => 'admin@demo.com',
            'password' => 'contraseña_incorrecta',
            'g-recaptcha-response' => 'ok',
        ]);

        // Intento 2: usuario que no existe
        $response2 = $this->post('/login', [
            'email' => 'usuario_inexistente@demo.com',
            'password' => 'cualquier_contraseña',
            'g-recaptcha-response' => 'ok',
        ]);

        // Ambos deben mostrar el MISMO mensaje de error
        // para no revelar si el usuario existe o no
        $this->assertStringContainsString(
            'Credenciales incorrectas',
            $response1->getSession()->get('error')
        );
        $this->assertStringContainsString(
            'Credenciales incorrectas',
            $response2->getSession()->get('error')
        );
    }

    /** SEG-11: Bypass de validación con array en email */
    public function test_array_injection_in_email()
    {
        $response = $this->post('/login', [
            'email' => ['admin@demo.com', 'otro@demo.com'],
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        // Debe validar que email es string, no array
        $response->assertSessionHasErrors(['email']);
    }

    /** SEG-12: CAPTCHA bypass attempt */
    public function test_missing_captcha_validation()
    {
        $response = $this->post('/login', [
            'email' => 'admin@demo.com',
            'password' => '123456',
            // Omitir g-recaptcha-response
        ]);

        // Sin captcha válido, debe rechazar (aunque nuestro seeder lo acepta todo)
        $response->assertSessionHasErrors(['g-recaptcha-response']);
    }

    /** SEG-13: Unicode normalization attack */
    public function test_unicode_normalization_attack()
    {
        // Intento de usar caracteres Unicode que se parecen a caracteres ASCII
        $response = $this->post('/login', [
            'email' => 'ａｄｍｉｎ@demo.com', // caracteres fullwidth
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        // Debe no encontrar el usuario (no debe autenticarse)
        $this->assertGuest();
        $this->assertTrue(
            $response->getSession()->has('error') || $response->getSession()->has('errors')
        );
    }

    /** SEG-14: Session fixation prevention */
    public function test_session_regeneration_on_login()
    {
        // Obtener session ID antes de login
        $responseBefore = $this->get('/login');
        $sessionIdBefore = session()->getId();

        // Hacer login válido
        $response = $this->post('/login', [
            'email' => 'admin@demo.com',
            'password' => '123456',
            'g-recaptcha-response' => 'ok',
        ]);

        // Session ID debe cambiar después del login (regeneración)
        $sessionIdAfter = session()->getId();
        $this->assertNotEquals($sessionIdBefore, $sessionIdAfter);
    }
}
