@extends('layouts.app')

@section('title', 'Detalle de Usuario')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('usuarios.index') }}"
               class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detalle de Usuario</h1>
                <p class="mt-1 text-sm text-gray-500">Información completa del usuario</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('usuarios.edit', $usuario->id ?? 0) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información Personal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Información Personal
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $usuario->nombre ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Correo Electrónico</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $usuario->email ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $usuario->telefono ?? 'No registrado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">DNI</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $usuario->dni ?? 'No registrado' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Actividad Reciente
                    </h3>
                </div>
                <div class="p-6">
                    @if(isset($actividades) && count($actividades) > 0)
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach($actividades as $index => $actividad)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-600">{{ $actividad->descripcion }}</p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        {{ $actividad->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500">No hay actividad registrada</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Facturas Realizadas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Últimas Facturas
                    </h3>
                    <span class="text-xs text-gray-500">Últimas 5 facturas</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Factura</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($facturas ?? [] as $factura)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('facturacion.show', $factura->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ $factura->numero }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $factura->cliente->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">S/ {{ number_format($factura->total, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $factura->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay facturas registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="space-y-6">
            <!-- Tarjeta de Perfil -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-2xl mx-auto mb-4">
                        {{ strtoupper(substr($usuario->nombre ?? 'U', 0, 2)) }}
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $usuario->nombre ?? 'N/A' }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $usuario->email ?? 'N/A' }}</p>

                    @php
                        $rolColors = [
                            'admin' => 'bg-red-100 text-red-700',
                            'vendedor' => 'bg-blue-100 text-blue-700',
                            'cajero' => 'bg-green-100 text-green-700',
                            'almacen' => 'bg-amber-100 text-amber-700',
                        ];
                        $rolLabels = [
                            'admin' => 'Administrador',
                            'vendedor' => 'Vendedor',
                            'cajero' => 'Cajero',
                            'almacen' => 'Almacén',
                        ];
                        $rol = $usuario->rol ?? 'vendedor';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-3 {{ $rolColors[$rol] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $rolLabels[$rol] ?? ucfirst($rol) }}
                    </span>
                </div>
                <div class="border-t border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Estado</span>
                        @if(($usuario->estado ?? 'activo') == 'activo')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Activo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Estadísticas del Usuario -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">Estadísticas</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Facturas realizadas</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $totalFacturas ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Ventas del mes</span>
                        <span class="text-sm font-semibold text-gray-900">S/ {{ number_format($ventasMes ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Clientes atendidos</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $clientesAtendidos ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Información de Cuenta -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">Información de Cuenta</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase">Último acceso</span>
                        <p class="text-sm text-gray-900 mt-1">
                            {{ $usuario->ultimo_acceso ? $usuario->ultimo_acceso->format('d/m/Y H:i') : 'Nunca' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase">Fecha de registro</span>
                        <p class="text-sm text-gray-900 mt-1">
                            {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase">Última actualización</span>
                        <p class="text-sm text-gray-900 mt-1">
                            {{ $usuario->updated_at ? $usuario->updated_at->format('d/m/Y H:i') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">Acciones Rápidas</h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('usuarios.edit', $usuario->id ?? 0) }}"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar información
                    </a>
                    <button type="button"
                            onclick="document.getElementById('reset-password-modal').classList.remove('hidden')"
                            class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Restablecer contraseña
                    </button>
                    @if(auth()->id() != ($usuario->id ?? 0))
                        <button type="button"
                                onclick="if(confirm('¿Está seguro de {{ ($usuario->estado ?? 'activo') == 'activo' ? 'desactivar' : 'activar' }} este usuario?')) document.getElementById('toggle-status-form').submit()"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm {{ ($usuario->estado ?? 'activo') == 'activo' ? 'text-amber-700 hover:bg-amber-50' : 'text-green-700 hover:bg-green-50' }} rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if(($usuario->estado ?? 'activo') == 'activo')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @endif
                            </svg>
                            {{ ($usuario->estado ?? 'activo') == 'activo' ? 'Desactivar usuario' : 'Activar usuario' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Restablecer Contraseña -->
    <div id="reset-password-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('reset-password-modal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Restablecer Contraseña</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Se enviará un enlace de restablecimiento de contraseña al correo electrónico del usuario.
                </p>
                <form action="{{ route('usuarios.reset-password', $usuario->id ?? 0) }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-end gap-3">
                        <button type="button"
                                onclick="document.getElementById('reset-password-modal').classList.add('hidden')"
                                class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Enviar enlace
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Formulario Toggle Estado -->
    <form id="toggle-status-form" action="{{ route('usuarios.toggle-status', $usuario->id ?? 0) }}" method="POST" class="hidden">
        @csrf
        @method('PATCH')
    </form>
@endsection
