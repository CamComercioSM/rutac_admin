<?php

namespace App\Http\Controllers;

use App\Exports\UsuariosExport;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    function list()
    { 
        $data = [ 'roles'=> Role::get() ];

        return View("users.index", $data);
    }
    
    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new UsuariosExport($query), 'usuarios.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = User::findOrFail($id);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        } else {
            unset($data['password']);
        }

        if ($request->filled('id')) 
        {
            $entity = User::findOrFail($request->id);
            $entity->update($data);
        } 
        else {
            $entity = User::create($data);
        }

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    public function destroy($id)
    {
        $entity = User::findOrFail($id);
        $entity->status = false;
        $entity->save();

        return response()->json(['message' => 'Removed']);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = User::where('rol_id', '>', 0);

        if(!empty($search))
        {
            $filterts = ['identification', 'name', 'lastname', 'position', 'email', 'email_cargo'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
