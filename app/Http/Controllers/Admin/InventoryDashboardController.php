<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\StockAdjustment;
use App\Models\Supplier;
use App\Models\SupplierLedger;

class InventoryDashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $summary = [
            'purchase_today' => (float) Purchase::whereDate('purchase_date', $today)->sum('grand_total'),
            'purchase_return_today' => (float) PurchaseReturn::whereDate('return_date', $today)->sum('total_amount'),
            'adjustment_today' => (int) StockAdjustment::whereDate('adjustment_date', $today)->count(),
            'supplier_due' => (float) SupplierLedger::sum('due_amount'),
            'low_stock_items' => (int) Product::whereColumn('stock', '<=', 'low_stock_alert')->count(),
            'movement_today' => (int) InventoryMovement::whereDate('movement_date', $today)->count(),
        ];

        $recentMovements = InventoryMovement::with(['product', 'productVariable'])->latest('movement_date')->limit(20)->get();
        $recentPurchases = Purchase::latest('purchase_date')->limit(10)->get();
        $supplierDueSummary = SupplierLedger::query()
            ->selectRaw('supplier_name, SUM(due_amount) as net_due')
            ->groupBy('supplier_name')
            ->havingRaw('SUM(due_amount) > 0')
            ->orderByDesc('net_due')
            ->limit(10)
            ->get();
        $activeSupplierCount = Supplier::query()->where('status', 1)->count();
        $movementTrend = InventoryMovement::query()
            ->selectRaw('DATE(movement_date) as day')
            ->selectRaw("SUM(CASE WHEN direction = 'in' THEN qty ELSE 0 END) as qty_in")
            ->selectRaw("SUM(CASE WHEN direction = 'out' THEN qty ELSE 0 END) as qty_out")
            ->whereDate('movement_date', '>=', now()->subDays(6)->toDateString())
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('backEnd.inventory.dashboard', compact('summary', 'recentMovements', 'recentPurchases', 'supplierDueSummary', 'activeSupplierCount', 'movementTrend'));
    }
}
