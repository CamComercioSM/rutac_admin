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

class ReporteMensualExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Gestor',
            'Año',
            'Mes',
            'Intervenciones',
            'Unidades productivas',
            'Estado',
            'Observaciones',
            'Supervisor',
            'Fecha de creación',
            'Fecha de revisión',
            'Documento pdf',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->asesor_id,
            $row->anio,
            $row->mes,
            $row->total_intervenciones,
            $row->total_unidades,
            $row->estado,
            $row->observaciones_supervisor,
            $row->supervisor_id,
            $row->fecha_creacion,
            $row->fecha_revision,
            $row->informe_url,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}