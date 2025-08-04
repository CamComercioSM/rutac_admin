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

class DiagnosticoResultadoExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'NIT',
            'Unidad Productiva',
            'Etapa',
            'Puntaje',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->fecha_creacion,
            $row->nit,
            $row->business_name,
            $row->etapa,
            $row->resultado_puntaje
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
