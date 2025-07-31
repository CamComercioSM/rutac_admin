<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

abstract class Controller
{
    protected function paginate($query, Request $request)
    {
        $page = $request->input('pageNumber', 1);
        $perPage = $request->input('pageSize', 10);
        $sort = $request->get('sortName');
        $order = $request->get('sortOrder', 'asc');

        Paginator::currentPageResolver(function () use ($page) { return $page; });

        if (!empty($sort)) {
            $query->orderBy($sort, $order);
        }

        $rows = $query->paginate($perPage);

        return [ 'total' => $rows->total(), 'rows' => $rows->items() ];
    }
}
