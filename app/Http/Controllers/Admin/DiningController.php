<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiningSession;
use App\Models\Table;
use Illuminate\Http\Request;

class DiningController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('table_number')->get();

        $activeSessions = DiningSession::where('status', DiningSession::STATUS_ACTIVE)
            ->withCount('orders')
            ->get()
            ->keyBy('table_number');

        return view('admin.dining.index', compact('tables', 'activeSessions'));
    }

    public function close(DiningSession $session)
    {
        if ($session->status !== DiningSession::STATUS_ACTIVE) {
            return back()->with('error', 'This table is already closed.');
        }

        $session->update([
            'status' => DiningSession::STATUS_CLOSED,
            'end_time' => now(),
        ]);

        Table::where('table_number', $session->table_number)
            ->update(['status' => Table::STATUS_AVAILABLE]);

        return back()->with('success', 'Dining session closed.');
    }
}
