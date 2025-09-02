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

class InscripcionesExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Fecha de creación',
            'Convocatoria',
            'Programa',
            'NIT',
            'Unidad Productiva',
            'Sector',
            'Ventas',
            'Estado',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->fecha_creacion,
            $row->nombre_convocatoria,
            $row->nombre_programa,
            $row->nit,
            $row->business_name,
            $row->sector,
            $row->ventas,
            $row->estado,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
