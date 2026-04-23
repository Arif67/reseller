<?php

namespace App\Services\Admin;

use App\Models\AccountTransaction;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Order;

class DailyClosingService
{
    public function summarize(?string $startDate, ?string $endDate): array
    {
        $startDate = $startDate ?: now()->toDateString();
        $endDate = $endDate ?: now()->toDateString();

        $orderRevenue = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->sum('amount');
        $income = Income::whereBetween('received_at', [$startDate, $endDate])->where('status', 1)->sum('amount');
        $expense = Expense::whereBetween('transaction_date', [$startDate, $endDate])->where('status', 1)->sum('amount');
        $cashIn = AccountTransaction::whereBetween('transaction_date', [$startDate, $endDate])->where('status', 1)->where('direction', 'in')->sum('amount');
        $cashOut = AccountTransaction::whereBetween('transaction_date', [$startDate, $endDate])->where('status', 1)->where('direction', 'out')->sum('amount');

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'order_revenue' => (float) $orderRevenue,
            'other_income' => (float) $income,
            'expense' => (float) $expense,
            'cash_in' => (float) $cashIn,
            'cash_out' => (float) $cashOut,
            'net_cash_flow' => (float) $cashIn - (float) $cashOut,
            'net_operating' => ((float) $orderRevenue + (float) $income) - (float) $expense,
        ];
    }
}
