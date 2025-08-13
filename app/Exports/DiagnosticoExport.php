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

class DiagnosticoExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Nombre',
            'Etapa',
            'Con ventas'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->diagnostico_nombre,
            $row->etapa,
            $row->diagnostico_conventas ?  'SI' : 'NO'
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

