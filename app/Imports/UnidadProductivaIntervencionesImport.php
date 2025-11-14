<?php

namespace App\Imports;

use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\TablasReferencias\CategoriasIntervenciones;
use App\Models\TablasReferencias\TiposIntervenciones;
use App\Models\User;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnidadProductivaIntervencionesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    { 
        return new UnidadProductivaIntervenciones([
            'asesor_id'          => $this->buscarAsesor($row['asesor']),
            'unidadproductiva_id'=> $this->buscarUP($row['unidad_productiva']),
            'descripcion'        => $row['descripcion'],
            'fecha_inicio'       => $this->parseFecha($row['fecha_inicio']),
            'fecha_fin'          => $this->parseFecha($row['fecha_fin']),

            'categoria_id'       => $this->buscarCategoria($row['categoria']),
            'tipo_id'            => $this->buscarTipo($row['tipo']),
            'modalidad'          => $row['modalidad'],
            'participantes'      => $this->toNumber($row['participantes']),
            'conclusiones'       => $row['conclusiones'],
        ]);
    }

    private function toNumber($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return floor(floatval($value));
    }

    private function parseFecha($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Si es un número -> Excel date
            if (is_numeric($value)) {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                );
            }

            // Si es string -> intentar parsear normal
            return Carbon::parse($value);

        } catch (\Throwable $e) {
            // Si no se puede parsear, devolver null o registrar el error
            return null;
        }
    }


    private function buscarAsesor($documento)
    {
        return User::where('identification', $documento)->value('id') ?? 0;
    }

    private function buscarUP($nombre)
    {
        return UnidadProductiva::where('business_name', 'like', $nombre)->value('unidadproductiva_id') ?? 0;
    }

    private function buscarCategoria($nombre)
    {
        return CategoriasIntervenciones::where('nombre', 'like', $nombre)->value('id') ?? 0;
    }

    private function buscarTipo($nombre)
    {
        return TiposIntervenciones::where('nombre', 'like', $nombre)->value('id') ?? 0;
    }
}
