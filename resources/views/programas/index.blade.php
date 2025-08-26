@extends('layouts.list', ['titulo'=> 'Programas', 'tituloModal'=> 'programa'])


@section('script')
    <script> 
        const TABLA = {
            urlApi: '/programas',
            sortName: 'programa_id',
            columns: [
                { field: 'nombre', title: 'Nombre', sortable: true },
                { field: 'duracion', title: 'Duración', sortable: true }
            ],
        };
    </script>
@endsection