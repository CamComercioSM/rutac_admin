<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessageLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsappLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = WhatsappMessageLog::with(['template', 'user']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        if ($request->filled('template_name')) {
            $query->where('template_name', 'like', '%' . $request->template_name . '%');
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('whatsapp.logs.index', compact('logs'));
    }

    public function show(WhatsappMessageLog $log): View
    {
        $log->load(['template', 'user']);
        return view('whatsapp.logs.show', compact('log'));
    }
}
