<?php

namespace App\Http\Controllers\Soporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaginasSoporte extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('en-mantenimiento', ['pageConfigs' => $pageConfigs]);
  }

  
  public function construccion()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('en-construccion', ['pageConfigs' => $pageConfigs]);
  }

}
