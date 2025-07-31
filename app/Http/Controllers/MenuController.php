<?php

namespace App\Http\Controllers;

use App\Exports\MenuExport;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MenuController extends Controller
{
    function list()
    { 
        $data = [ 'roles'=> Role::get() ];

        return View("menu.index", $data);
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new MenuExport($query), 'usuarios.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        $data['rows'] = array_map(function ($item) {
            $array = $item->toArray();
            $array['roles'] = collect($array['roles'])->pluck('id')->toArray();
            return $array;
        }, $data['rows']);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = Menu::findOrFail($id);
        $result->roles = $result->roles()->pluck('role_id')->toArray();

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        
        if ($request->filled('id')) 
        {
            $entity = Menu::findOrFail($request->id);
            $entity->update($data);

            $entity->roles()->detach();
        } 
        else {
            $entity = Menu::create($data);
        }

        $entity->roles()->attach( $data['roles'] ?? [] );

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    public function destroy($id)
    {
        $entity = Menu::findOrFail($id);
        $entity->delete();

        return response()->json(['message' => 'Removed']);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');

        $query = Menu::with('roles:id');

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
