@extends('layouts.list', ['titulo'=> 'Log de ejecuón de tareas', 'tituloModal'=> ''])

@section('script')
    <script> 
        const TABLA = {
            urlApi: '/cronLog',
            sortName: 'id',
            acciones: "",
            columns: [
                { field: 'nombre_tarea', title: 'Tarea', sortable: true },
                { field: 'inicio_ejecucion', title: 'Fecha inicio', sortable: true },
                { field: 'fin_ejecucion', title: 'Fecha fin', sortable: true },
                { field: 'estado', title: 'Estado', sortable: true },
                { field: 'mensaje', title: 'Mensaje', sortable: true }
            ],
        };
    </script>
@endsection