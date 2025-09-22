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

class DiagnosticoRespuestasResultadoExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Respuesta',
            'Porcentaje'
        ];
    }

    public function map($row): array
    {
        return [
            $row->diagnosticorespuesta_id,
            $row->fecha_creacion,
            $row->pregunta_titulo,
            $row->diagnosticorespuesta_valor,
            $row->pregunta_porcentaje
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
