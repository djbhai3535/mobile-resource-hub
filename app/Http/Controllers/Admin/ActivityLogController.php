<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('action', 'like', $keyword)
                    ->orWhere('subject_type', 'like', $keyword)
                    ->orWhere('ip_address', 'like', $keyword);
            });
        }

        $logs = $query->latest('created_at')->paginate(25)->withQueryString();

        return view('admin.activity.index', compact('logs'));
    }
}
