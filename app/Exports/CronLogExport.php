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

class CronLogExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Tarea',
            'Fecha inicio',
            'Fecha fin',
            'Estado',
            'Mensaje'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nombre_tarea,
            $row->inicio_ejecucion,
            $row->fin_ejecucion,
            $row->estado,
            $row->mensaje,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

