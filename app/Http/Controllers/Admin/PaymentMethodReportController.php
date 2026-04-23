<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentMethodReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query();
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $payments = $query->latest()->paginate(30)->withQueryString();
        $summary = $query->get()
            ->groupBy(fn ($payment) => $payment->payment_method ?: 'Unknown')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'amount' => (float) $group->sum('amount'),
            ]);

        return view('backEnd.accounts.report.payment_method', compact('payments', 'summary'));
    }
}
