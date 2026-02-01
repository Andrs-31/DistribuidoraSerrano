@extends('layouts.app')

@section('content')
<div style="text-align:center; margin-top:80px">
    <h1>🚫 Acceso denegado</h1>
    <p>No tienes permiso para acceder a esta sección del sistema.</p>

    <a href="{{ route('dashboard.index') }}" class="btn btn-primary">
        Volver al Dashboard
    </a>
</div>
@endsection
