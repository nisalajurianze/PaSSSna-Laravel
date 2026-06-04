<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Report;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    /**
     * Generate order PDF
     */
    public function order(Order $order)
    {
        // Check authorization
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();

        // Customers can only view their own orders
        if ($user->role !== 'admin' && $order->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $order->load(['items', 'user', 'payment']);

        $pdf = Pdf::loadView('pdf.order', compact('order'));

        return $pdf->download("order-{$order->order_number}.pdf");
    }

    /**
     * Generate invoice PDF
     */
    public function invoice(Order $order)
    {
        // Check authorization
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();

        // Customers can only view their own orders
        if ($user->role !== 'admin' && $order->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $order->load(['items', 'user', 'payment']);

        $pdf = Pdf::loadView('pdf.invoice', compact('order'));

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    /**
     * Generate report PDF
     */
    public function report(Request $request, $type)
    {
        // Only admins can access reports
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $report = new Report();
        $data = [];

        switch ($type) {
            case 'sales':
                $data = $report->getSalesReport($startDate, $endDate);
                break;
            case 'inventory':
                $data = $report->getInventoryReport();
                break;
            case 'staff':
                $data = $report->getStaffReport();
                break;
            case 'orders':
                $data = $report->getOrdersReport($startDate, $endDate);
                break;
            default:
                abort(404, 'Report type not found');
        }

        $pdf = Pdf::loadView("pdf.{$type}-report", array_merge($data, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]));

        return $pdf->download("{$type}-report-{$startDate}-to-{$endDate}.pdf");
    }

    /**
     * Generate daily report (admin only)
     */
    public function dailyReport(Request $request)
    {
        // Only admins can access
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $date = $request->get('date', today()->toDateString());

        $report = new Report();
        $data = $report->getDailyReport($date);

        $pdf = Pdf::loadView('pdf.daily-report', array_merge($data, ['date' => $date]));

        return $pdf->download("daily-report-{$date}.pdf");
    }
}
