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

class UsuariosExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'N° documento',
            'Nombre (s)',
            'Apellido (s)',
            'Cargo',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->identification,
            $row->name,
            $row->lastname,
            $row->position,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

