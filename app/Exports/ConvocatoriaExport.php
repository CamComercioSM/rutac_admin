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

class ConvocatoriaExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Programa',
            'Nombre',
            'Encragado',
            'Email',
            'Teléfono',
            'Fecha inicio',
            'Fecha finalizacion'         
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nombre_programa,
            $row->nombre_convocatoria,
            $row->persona_encargada,
            $row->correo_contacto,
            $row->telefono,
            $row->fecha_apertura_convocatoria,
            $row->fecha_cierre_convocatoria,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
