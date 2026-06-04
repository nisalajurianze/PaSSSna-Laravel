<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\DiningSession;
use Symfony\Component\HttpFoundation\Response;

class DiningSessionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = Session::get('dining_session_token');
        $tableNumber = Session::get('dining_table_number');

        if (!$token || !$tableNumber) {
            return $this->deny($request, 'Please enter your table number to start dining.');
        }

        $session = DiningSession::where('session_code', $token)
            ->where('table_number', $tableNumber)
            ->where('status', DiningSession::STATUS_ACTIVE)
            ->first();

        if (!$session) {
            Session::forget(['dining_session_token', 'dining_table_number', 'dining_cart']);
            return $this->deny($request, 'Your dining session has ended. Please re-enter your table number.');
        }

        $request->attributes->set('dining_session', $session);

        return $next($request);
    }

    private function deny(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson()) {
            return response()->json(['success' => false, 'message' => $message], 401);
        }

        return redirect()->route('dining.login')->with('error', $message);
    }
}
