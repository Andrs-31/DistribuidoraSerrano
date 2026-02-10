<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Distrito;

class UbicacionController extends Controller
{
    public function distritos($codigoProvincia)
    {
        return Distrito::where('codigo_provincia', $codigoProvincia)
            ->orderBy('nombre_distrito')
            ->get();
    }
}
