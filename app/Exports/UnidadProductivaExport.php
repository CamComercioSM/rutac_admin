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

class UnidadProductivaExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Fecha de registro',
            'Tipo registro',
            'NIT',
            'Razón social',
            'Represnetante legal',
            'Email',
            'Tipo persona',
            'Sector',
            'Tamaño',
            'Etapa',
            'Departamento',
            'Municipio',           
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->fecha_creacion,
            $row->tipo_registro_rutac,
            $row->nit,
            $row->business_name,
            $row->name_legal_representative,
            $row->registration_email,
            $row->tipo_persona,
            $row->sector,
            $row->tamano,
            $row->etapa,
            $row->departamento,
            $row->municipio,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
