<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\HubStock;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Support\VendorEarnings;
use App\Support\VendorMenuCounts;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private function vendorId(): int
    {
        return Auth::guard('vendor')->id();
    }

    public function index($slug, Request $request)
    {
        $vendorId = $this->vendorId();

        $query = Order::query()
            ->whereHas('orderdetails', function ($q) use ($vendorId) {
                $q->whereHas('product', function ($pq) use ($vendorId) {
                    $pq->where('vendor_id', $vendorId);
                });
            })
            ->with([
                'status',
                'shipping',
                // Only this vendor's items, with their images for the thumbnails.
                'orderdetails' => function ($q) use ($vendorId) {
                    $q->whereHas('product', function ($pq) use ($vendorId) {
                        $pq->where('vendor_id', $vendorId);
                    })->with(['image', 'product.image']);
                },
            ]);

        if ($slug !== 'all') {
            // Grouped filters (take precedence over a single matching status).
            $groups = [
                'returned' => ['returned', 'refunded'],
            ];

            if (isset($groups[$slug])) {
                $statusIds = OrderStatus::whereIn('slug', $groups[$slug])->pluck('id');
                $query->whereIn('order_status', $statusIds);
            } elseif ($status = OrderStatus::where('slug', $slug)->first()) {
                $query->where('order_status', $status->id);
            } else {
                $virtual_map = [
                    'processing' => ['processing', 'approved'],
                    'on-the-way' => ['on-the-way', 'on the way', 'packed', 'shipped'],
                    'completed' => ['completed', 'delivered', 'complete', 'deliveryed'],
                    'cancelled' => ['cancelled', 'canceled', 'failed'],
                    'in-courier' => ['in-courier', 'in courier', 'out-for-delivery'],
                ];

                if (isset($virtual_map[$slug])) {
                    $candidates = $virtual_map[$slug];
                    $statusIds = OrderStatus::whereIn('slug', $candidates)
                        ->orWhereIn('name', $candidates)
                        ->pluck('id');
                    $query->whereIn('order_status', $statusIds);
                }
            }
        }

        if ($request->keyword) {
            $vendorId = $this->vendorId();
            $query->where(function ($q) use ($request, $vendorId) {
                $q->where('invoice_id', 'like', "%{$request->keyword}%")
                  ->orWhereHas('orderdetails', function ($dq) use ($request, $vendorId) {
                      $dq->where('product_name', 'like', "%{$request->keyword}%")
                         ->whereHas('product', fn ($pq) => $pq->where('vendor_id', $vendorId));
                  });
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $order_statuses = OrderStatus::where('status', 1)->get();

        return view('vendorPanel.order.index', compact('orders', 'order_statuses', 'slug'));
    }

    public function show($id)
    {
        $vendorId = $this->vendorId();

        $order = Order::with(['shipping', 'status', 'orderdetails' => function ($q) use ($vendorId) {
            $q->whereHas('product', function ($pq) use ($vendorId) {
                $pq->where('vendor_id', $vendorId);
            })->with(['product', 'productVariable']);
        }])
        ->whereHas('orderdetails', function ($q) use ($vendorId) {
            $q->whereHas('product', function ($pq) use ($vendorId) {
                $pq->where('vendor_id', $vendorId);
            });
        })
        ->findOrFail($id);

        return view('vendorPanel.order.show', compact('order'));
    }

    // Only this vendor's own order line items (404 otherwise).
    private function ownOrderDetail($id): OrderDetails
    {
        return OrderDetails::where('id', $id)
            ->whereHas('product', fn ($q) => $q->where('vendor_id', $this->vendorId()))
            ->firstOrFail();
    }

    public function collection()
    {
        $vendorId = $this->vendorId();

        // Pending items this vendor has NOT collected yet.
        $orderDetails = OrderDetails::whereHas('product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
        ->where('vendor_collected', 0)
        ->whereHas('order', function ($q) {
            $q->whereHas('status', function($sq) {
                $sq->whereIn('name', ['Pending', 'Processing', 'Approved']);
            });
        })
        ->with(['product.image', 'image', 'order'])
        ->latest()
        ->get();

        return view('vendorPanel.collection.index', compact('orderDetails'));
    }

    // Vendor marks an item as collected -> it moves to the Collected page.
    public function markCollected(Request $request)
    {
        $this->validate($request, ['id' => 'required|integer']);

        $detail = $this->ownOrderDetail($request->id);
        $detail->vendor_collected = 1;
        $detail->vendor_collected_at = now();
        $detail->save();

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Item marked as collected',
                'counts'  => VendorMenuCounts::get($this->vendorId()),
            ]);
        }

        Toastr::success('Item marked as collected', 'Success');
        return back();
    }

    // Undo: move a collected item back to the Collection page.
    public function unmarkCollected(Request $request)
    {
        $this->validate($request, ['id' => 'required|integer']);

        $detail = $this->ownOrderDetail($request->id);
        $detail->vendor_collected = 0;
        $detail->vendor_collected_at = null;
        $detail->save();

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Item moved back to collection',
                'counts'  => VendorMenuCounts::get($this->vendorId()),
            ]);
        }

        Toastr::success('Item moved back to collection', 'Success');
        return back();
    }

    public function pendingSummary()
    {
        $vendorId = $this->vendorId();

        // All pending line items for this vendor...
        $orderDetails = OrderDetails::whereHas('product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
        ->where('vendor_collected', 0)
        ->whereHas('order', function ($q) {
            $q->whereHas('status', function ($sq) {
                $sq->whereIn('name', ['Pending', 'Processing', 'Approved']);
            });
        })
        ->with(['product.image', 'image'])
        ->get();

        // ...rolled up product-wise: total qty + how many orders + size breakdown.
        $summary = $orderDetails
            ->groupBy('product_id')
            ->map(function ($items) {
                $first = $items->first();

                return (object) [
                    'product_id'   => $first->product_id,
                    'product_name' => $first->product_name,
                    'image'        => $first->image->image
                        ?? optional(optional($first->product)->image)->image
                        ?? 'uploads/logo.png',
                    'total_qty'    => $items->sum('qty'),
                    'order_count'  => $items->pluck('order_id')->unique()->count(),
                    'sizes'        => $items->groupBy(fn ($i) => $i->product_size ?: 'N/A')
                                            ->map->sum('qty'),
                ];
            })
            ->sortByDesc('total_qty')
            ->values();

        return view('vendorPanel.collection.pending_summary', compact('summary'));
    }

    public function collected()
    {
        $vendorId = $this->vendorId();

        // Items the vendor has marked as collected.
        $orderDetails = OrderDetails::whereHas('product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
        ->where('vendor_collected', 1)
        ->with(['product.image', 'image', 'order'])
        ->latest('vendor_collected_at')
        ->get();

        return view('vendorPanel.collection.collected', compact('orderDetails'));
    }

    // Returned items for this vendor: products from returned/refunded orders.
    public function returns()
    {
        $vendorId = $this->vendorId();

        $base = OrderDetails::whereHas('product', fn ($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order.status', fn ($q) => $q->whereIn('slug', VendorEarnings::RETURNED_SLUGS))
            ->with(['product.image', 'image', 'order']);

        // Waiting for the vendor to confirm they got the product back.
        $toReceive = (clone $base)->where('vendor_return_received', 0)->latest()->get();
        // Already confirmed -> vendor's return list.
        $received = (clone $base)->where('vendor_return_received', 1)->latest('vendor_return_received_at')->get();

        return view('vendorPanel.collection.returns', compact('toReceive', 'received'));
    }

    // Vendor confirms the returned product is back in their hand.
    public function markReturnReceived(Request $request)
    {
        $this->validate($request, ['id' => 'required|integer']);

        $detail = $this->ownOrderDetail($request->id);

        DB::transaction(function () use ($detail) {
            $detail->vendor_return_received = 1;
            $detail->vendor_return_received_at = now();
            $detail->save();

            // Product leaves the hub back to the vendor: drop hub stock if it
            // had been received at the hub earlier.
            if ($detail->admin_received) {
                $stock = HubStock::where('product_id', $detail->product_id)
                    ->where('product_variable_id', $detail->product_variable_id ?? 0)
                    ->first();
                if ($stock) {
                    $stock->qty = max(0, (int) $stock->qty - (int) $detail->qty);
                    $stock->save();
                }
            }
        });

        Toastr::success('Return received — back in your stock', 'Success');
        return back();
    }
}
