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

class EmpresariosExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Email',
            'Identificatión',
            'Nombre',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->email,
            $row->identification,
            $row->full_name,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

