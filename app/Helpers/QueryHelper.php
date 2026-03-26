<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QueryHelper
{
    /**
     * Agrupa y cuenta registros dinámicamente
     */
    public static function agrupar(Builder $query, string $campo, ?string $relacion = null, string $alias = 'total')
    {
        $q = (clone $query)
            ->select($campo, DB::raw("COUNT(*) as {$alias}"))
            ->groupBy($campo);

        if ($relacion) {
            $q->with($relacion);
        }

        return $q->get();
    }
}