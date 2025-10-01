@extends('layouts.list', ['titulo'=> 'Log de ejecuón de tareas', 'tituloModal'=> ''])

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/cronLog',
            sortName: 'id',
            acciones: "",
            columns: [
                { data: 'nombre_tarea', title: 'Tarea', orderable: true },
                { data: 'inicio_ejecucion', title: 'Fecha inicio', orderable: true },
                { data: 'fin_ejecucion', title: 'Fecha fin', orderable: true },
                { data: 'estado', title: 'Estado', orderable: true },
                { data: 'mensaje', title: 'Mensaje', orderable: true }
            ],
        };
    </script>
@endsection