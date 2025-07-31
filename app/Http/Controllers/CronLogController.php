<?php

namespace App\Http\Controllers;

use App\Exports\CronLogExport;
use App\Http\Controllers\Controller;
use App\Models\Crons\CronLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CronLogController extends Controller
{
    function list()
    { 
        return View("crons.indexLog");
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new CronLogExport($query), 'cron_log.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }


    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');

        $query = CronLog::query();

        if(!empty($search))
        {
            $filterts = ['nombre_tarea'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
