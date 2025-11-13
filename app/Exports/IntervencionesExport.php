<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\{
    FromQuery,
    WithMapping,
    WithHeadings,
    WithChunkReading,
    Exportable
};

class IntervencionesExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
{
    use Exportable;

    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Categoría',
            'Tipo',
            'Modalidad',
            'Fecha inicio',
            'Fecha fin',
            'Unidad productiva',
            'Participantes',
            'Asesor',
            'Soporte',
            'Descripción',
            'Conclusiones',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->categoria,
            $row->tipo,
            $row->modalidad,
            $row->fecha_inicio,
            $row->fecha_fin,
            $row->unidad,
            $row->participantes,
            $row->asesor,
            $row->soporte,
            $row->descripcion,
            $row->conclusiones,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

