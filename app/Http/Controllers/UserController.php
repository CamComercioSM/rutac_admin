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
        $result->roles = $result->roles()->pluck('role_id')->toArray();

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

            $entity->roles()->detach();
        } 
        else {
            $entity = User::create($data);
        }

        $entity->roles()->attach( $data['roles'] ?? [] );

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
        $search = $request->get('searchText');

        $query = User::query();

        if(!empty($search))
        {
            $filterts = ['identification', 'name', 'lastname', 'position', 'email'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
