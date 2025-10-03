@extends('layouts.list', ['titulo'=> 'Empresarios', 'tituloModal'=> 'usuario'])

@section('script')
    <script> 
        window.TABLA = {
            urlApi: '/empresarios',
            sortName: 'name',
            
            menu_row: `<a class="dropdown-item" href="/empresarios/ROWID" >Ver detalles</a>`,
            
            columns: [
                { data: 'email', title: 'Email', orderable: true },
                { data: 'identification', title: 'Identificatión', orderable: true },
                { data: 'full_name', title: 'Nombre', orderable: true },
            ]
        };
    </script>
@endsection