<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Lista os registros de atividade mais recentes (somente leitura).
     * Eager loading do usuário para evitar N+1.
     */
    public function index(): View
    {
        $logs = ActivityLog::with('user')->latest()->paginate(20);

        return view('activity-logs.index', compact('logs'));
    }
}
