<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /** Mostrar formulario de login */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /** Procesar intento de login */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
            // IMPLEMENTACION DE CAPTCHA
            'g-recaptcha-response' => ['required', 'captcha'],
        ]);

         // EXTRAER SOLO email y password PARA EVALUACION SIN EL CAPTACH A LA BD
        $credentials = $request->only('email', 'password');

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard.index'))->with('success', 'Inicio de sesión exitoso.');
        }

        return back()->withInput($request->only('email', 'remember'))->with('error', 'Credenciales incorrectas.');
    }

    /** Cerrar sesión */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada.');
    }
}
