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
            'Fecha inicio',
            'Fecha fin',
            'Unidad productiva',
            'Asesor',
            'Soporte',
            'Descripción'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->fecha_inicio,
            $row->fecha_fin,
            $row->unidad,
            $row->asesor,
            $row->soporte,
            $row->descripcion
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

