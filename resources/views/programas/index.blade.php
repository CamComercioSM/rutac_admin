@extends('layouts.list', ['titulo'=> 'Programas', 'tituloModal'=> 'programa'])


@section('script')
    <script> 
        const TABLA = {
            urlApi: '/programas',
            sortName: 'programa_id',

            menu_row: `<a class="dropdown-item" href="/programas/ROWID" >Ver detalles</a>
                       <a class="dropdown-item" href="/convocatorias/list?programa=ROWID">Convocatorias</a>`,

            columns: [
                { field: 'nombre', title: 'Nombre', sortable: true },
                { field: 'duracion', title: 'Duración', sortable: true }
            ],
        };
    </script>
@endsection