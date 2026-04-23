<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Services\Admin\InventoryService;
use Illuminate\Http\Request;
use Toastr;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {
    }

    public function ledger(Request $request)
    {
        $data = InventoryMovement::with(['product', 'productVariable'])->latest('movement_date');

        if ($request->keyword) {
            $data->where(function ($query) use ($request) {
                $query->where('reference_no', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('note', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        if ($request->movement_type) {
            $data->where('movement_type', $request->movement_type);
        }

        if ($request->date_from) {
            $data->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $data->whereDate('movement_date', '<=', $request->date_to);
        }

        $data = $data->paginate(30)->withQueryString();

        return view('backEnd.inventory.ledger', compact('data'));
    }

    public function productMovements($id, Request $request)
    {
        $product = Product::with(['allVariables:id,product_id,size,color,stock'])->findOrFail($id);
        $data = InventoryMovement::with(['product', 'productVariable'])
            ->where('product_id', $product->id)
            ->latest('movement_date');

        if ($request->product_variable_id) {
            $data->where('product_variable_id', $request->product_variable_id);
        }

        if ($request->movement_type) {
            $data->where('movement_type', $request->movement_type);
        }

        $data = $data->paginate(30)->withQueryString();

        return view('backEnd.inventory.product_movements', compact('product', 'data'));
    }

    public function lowStock()
    {
        $products = Product::with(['allVariables:id,product_id,size,color,stock,low_stock_alert'])
            ->select('id', 'name', 'stock', 'type', 'variation_pricing_mode', 'low_stock_alert')
            ->orderBy('name')
            ->get();

        $rows = collect();

        foreach ($products as $product) {
            if ((int) $product->type === 1) {
                $threshold = (int) ($product->low_stock_alert ?? 5);
                if ((int) $product->stock <= $threshold) {
                    $rows->push([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'variant_name' => null,
                        'stock' => (int) $product->stock,
                        'threshold' => $threshold,
                    ]);
                }
                continue;
            }

            foreach ($product->allVariables as $variable) {
                $threshold = (int) ($variable->low_stock_alert ?? 5);
                if ((int) $variable->stock <= $threshold) {
                    $rows->push([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'variant_name' => trim(collect([$variable->size, $variable->color])->filter()->implode(' / ')) ?: ('Variant #' . $variable->id),
                        'stock' => (int) $variable->stock,
                        'threshold' => $threshold,
                    ]);
                }
            }
        }

        return view('backEnd.inventory.low_stock', ['rows' => $rows]);
    }

    public function adjustments(Request $request)
    {
        $data = StockAdjustment::with(['product', 'productVariable'])->latest('adjustment_date');

        if ($request->keyword) {
            $data->whereHas('product', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        $data = $data->paginate(20)->withQueryString();

        return view('backEnd.inventory.adjustment.index', compact('data'));
    }

    public function createAdjustment()
    {
        $products = Product::with(['allVariables:id,product_id,size,color,purchase_price'])
            ->select('id', 'name', 'purchase_price', 'type')
            ->orderBy('name')
            ->get();

        return view('backEnd.inventory.adjustment.create', compact('products'));
    }

    public function storeAdjustment(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id',
            'product_variable_id' => 'nullable',
            'adjustment_type' => 'required|in:adjustment_in,adjustment_out',
            'qty' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);

        $this->inventoryService->createAdjustment($request->all() + ['status' => 1]);

        Toastr::success('Success', 'Stock adjustment created successfully');

        return redirect()->route('inventory.adjustments.index');
    }
}
