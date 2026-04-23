<?php

namespace App\Exports;

use App\Models\Programas\FasePrograma;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FaseProgramaExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query ?? FasePrograma::query();
    }

    public function query()
    {
        return $this->query->orderBy('orden', 'asc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Descripción',
            'Orden',
            'Activa',
            'Fecha Creación',
            'Fecha Actualización',
            'Creado Por',
            'Actualizado Por',
        ];
    }

    public function map($fase): array
    {
        return [
            $fase->fase_id,
            $fase->nombre,
            $fase->descripcion,
            $fase->orden,
            $fase->activa ? 'Sí' : 'No',
            $fase->fecha_creacion?->format('Y-m-d H:i:s'),
            $fase->fecha_actualizacion?->format('Y-m-d H:i:s'),
            $fase->usuario_creo,
            $fase->usuario_actualizo,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']],
            ],
        ];
    }
}
