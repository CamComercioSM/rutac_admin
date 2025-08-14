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

class BannerExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
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
            'Titulo',
            'Descripcion',
            'Botón',
            'Imagen',
            'URL',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->title,
            $row->description,
            $row->text_button,
            $row->image,
            $row->url,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

