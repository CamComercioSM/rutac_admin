<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class AdminViewController extends Controller
{
    function dashboard()
    { 
        return View("dashboard");
    }

    function consultaExpedienteMercantil()
    { 
        return View("consultaExpedienteMercantil");
    }

}
