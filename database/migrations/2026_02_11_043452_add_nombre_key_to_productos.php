<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Crear columna solo si NO existe
        if (!Schema::hasColumn('productos', 'nombre_key')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->string('nombre_key')->nullable()->after('nombre');
            });
        }

        // 2) Llenar nombre_key normalizado
        $productos = DB::table('productos')->select('id_producto', 'nombre')->get();

        foreach ($productos as $p) {
            $key = Str::of($p->nombre)->ascii()->lower()->trim();
            $key = preg_replace('/\s+/', ' ', (string)$key);

            DB::table('productos')
                ->where('id_producto', $p->id_producto)
                ->update(['nombre_key' => $key]);
        }

        // 3) Resolver duplicados automáticamente (si existen)
        //    (si hay 2 productos que al normalizar quedan igual, se les agrega -ID)
        $dups = DB::table('productos')
            ->select('nombre_key', DB::raw('COUNT(*) as c'))
            ->whereNotNull('nombre_key')
            ->groupBy('nombre_key')
            ->having('c', '>', 1)
            ->get();

        foreach ($dups as $d) {
            $rows = DB::table('productos')
                ->select('id_producto', 'nombre_key')
                ->where('nombre_key', $d->nombre_key)
                ->orderBy('id_producto')
                ->get();

            $first = true;
            foreach ($rows as $r) {
                if ($first) { // deja el primero igual
                    $first = false;
                    continue;
                }
                DB::table('productos')
                    ->where('id_producto', $r->id_producto)
                    ->update(['nombre_key' => $r->nombre_key . '-' . $r->id_producto]);
            }
        }

        // 4) Crear índice UNIQUE si no existe
        //    (evita error si ya lo intentaste antes)
        $indexes = DB::select("SHOW INDEX FROM productos WHERE Key_name = 'productos_nombre_key_unique'");
        if (count($indexes) === 0) {
            Schema::table('productos', function (Blueprint $table) {
                $table->unique('nombre_key', 'productos_nombre_key_unique');
            });
        }
    }

    public function down(): void
    {
        // Borrar índice si existe
        $indexes = DB::select("SHOW INDEX FROM productos WHERE Key_name = 'productos_nombre_key_unique'");
        if (count($indexes) > 0) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropUnique('productos_nombre_key_unique');
            });
        }

        // Borrar columna si existe
        if (Schema::hasColumn('productos', 'nombre_key')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('nombre_key');
            });
        }
    }
};
