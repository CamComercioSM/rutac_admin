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

class InscripcionesRespuestasExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Pregunta',
            'Respuesta'
        ];
    }

    public function map($row): array
    {
        return [
            $row->convocatoriarespuesta_id,
            $row->fecha_creacion,
            $row->requisito_titulo,
            $row->value
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
