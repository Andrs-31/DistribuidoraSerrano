<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermisoMiddleware
{
    public function handle($request, Closure $next, $permiso)
    {
        $user = Auth::user();

        if (!$user || !$user->permiso) {
            abort(403);
        }

        // El admin puede entrar a todo
        if ($user->permiso->nombre === 'admin') {
            return $next($request);
        }

        // Validación por módulo
        if ($user->permiso->nombre !== $permiso) {
            return response()->view('errors.sin-permiso', [], 403);
        }

        return $next($request);
    }
}
