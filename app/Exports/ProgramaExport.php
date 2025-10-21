<?php

namespace App\Exports;

use App\Models\Programas\Programa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\{
    FromQuery,
    WithMapping,
    WithHeadings,
    WithChunkReading,
    Exportable
};

class ProgramaExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Código PAC',
            'Nombre',
            'Duración',
            'Modalidad',
            'Etapas',     
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->codigo_pac,
            $row->nombre,
            $row->duracion,
            (Programa::$es_virtual_text[$row->es_virtual] ?? ""),
            implode(', ', $row->etapas->pluck('name')->toArray())
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
