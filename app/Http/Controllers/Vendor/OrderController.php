<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderController extends Controller
{
    private function vendorId(): int
    {
        return Auth::guard('vendor')->id();
    }

    /**
     * Demo product catalogue used to seed dummy data while the vendor
     * has no real orders yet. Returned as plain objects so the existing
     * blade views render them exactly like Eloquent models.
     */
    private function dummyCatalogue(): array
    {
        return [
            ['name' => 'Premium Cotton Panjabi', 'size' => 'L', 'qty' => 3],
            ['name' => 'Slim Fit Denim Jeans', 'size' => '32', 'qty' => 2],
            ['name' => 'Casual Cotton T-Shirt', 'size' => 'M', 'qty' => 5],
            ['name' => 'Embroidered Three Piece', 'size' => 'Free', 'qty' => 1],
            ['name' => 'Formal Office Shirt', 'size' => 'XL', 'qty' => 4],
            ['name' => 'Kids Party Frock', 'size' => '6Y', 'qty' => 2],
            ['name' => 'Leather Casual Loafer', 'size' => '42', 'qty' => 1],
            ['name' => 'Winter Hoodie Jacket', 'size' => 'L', 'qty' => 3],
            ['name' => 'Printed Cotton Saree', 'size' => 'Free', 'qty' => 2],
        ];
    }

    /**
     * Build a dummy collection of order-detail-like objects for the
     * collection / pending / collected pages when there is no real data.
     */
    private function dummyOrderDetails(): Collection
    {
        return collect($this->dummyCatalogue())->values()->map(function ($item, $index) {
            return (object) [
                'id'           => 9000 + $index,
                'order'        => (object) ['invoice_id' => 'INV-' . (10500 + $index)],
                'created_at'   => Carbon::now()->subHours($index * 5 + 1),
                'product_size' => $item['size'],
                'qty'          => $item['qty'],
                'product_name' => $item['name'],
                'product'      => null,
            ];
        });
    }

    /**
     * Build a dummy paginator of order-like objects for the orders list.
     */
    private function dummyOrders(Request $request): LengthAwarePaginator
    {
        $names    = ['Rahim Uddin', 'Karina Akter', 'Shuvo Das', 'Nusrat Jahan', 'Tanvir Hasan', 'Mitu Rahman', 'Jamal Mia', 'Ayesha Siddika'];
        $statuses = ['Pending', 'Processing', 'Approved', 'On The Way', 'Completed', 'Cancelled'];

        $items = collect(range(0, 7))->map(function ($i) use ($names, $statuses) {
            return (object) [
                'id'         => 9000 + $i,
                'invoice_id' => 'INV-' . (10500 + $i),
                'created_at' => Carbon::now()->subDays($i)->subHours($i),
                'shipping'   => (object) [
                    'name'    => $names[$i % count($names)],
                    'phone'   => '01' . rand(3, 9) . rand(10000000, 99999999),
                    'address' => 'House ' . ($i + 10) . ', Road ' . ($i + 2) . ', Dhaka',
                ],
                'status'     => (object) ['name' => $statuses[$i % count($statuses)]],
            ];
        });

        return new LengthAwarePaginator(
            $items,
            $items->count(),
            20,
            1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
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
            ->with(['status', 'shipping']);

        if ($slug !== 'all') {
            $status = OrderStatus::where('slug', $slug)->first();
            if ($status) {
                $query->where('order_status', $status->id);
            } else {
                $virtual_map = [
                    'processing' => ['processing', 'approved'],
                    'on-the-way' => ['on-the-way', 'on the way', 'packed', 'shipped'],
                    'completed' => ['completed', 'delivered', 'complete', 'deliveryed'],
                    'cancelled' => ['cancelled', 'canceled', 'returned'],
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
            $query->where(function ($q) use ($request) {
                $q->where('invoice_id', 'like', "%{$request->keyword}%")
                  ->orWhereHas('shipping', function ($sq) use ($request) {
                      $sq->where('phone', 'like', "%{$request->keyword}%")
                        ->orWhere('name', 'like', "%{$request->keyword}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        if ($orders->isEmpty() && !$request->keyword) {
            $orders = $this->dummyOrders($request);
        }

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

    public function collection()
    {
        $vendorId = $this->vendorId();
        
        $orderDetails = OrderDetails::whereHas('product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
        ->whereHas('order', function ($q) {
            $q->whereHas('status', function($sq) {
                $sq->whereIn('name', ['Pending', 'Processing', 'Approved']); 
            });
        })
        ->with(['product', 'order'])
        ->latest()
        ->get();

        if ($orderDetails->isEmpty()) {
            $orderDetails = $this->dummyOrderDetails();
        }

        return view('vendorPanel.collection.index', compact('orderDetails'));
    }

    public function pendingSummary()
    {
        $vendorId = $this->vendorId();
        
        // Similar to collection but maybe grouped or just the same view for now
        $orderDetails = OrderDetails::whereHas('product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
        ->whereHas('order', function ($q) {
            $q->whereHas('status', function($sq) {
                $sq->whereIn('name', ['Pending', 'Processing', 'Approved']); 
            });
        })
        ->with(['product', 'order'])
        ->latest()
        ->get();

        if ($orderDetails->isEmpty()) {
            $orderDetails = $this->dummyOrderDetails()->take(5);
        }

        return view('vendorPanel.collection.pending_summary', compact('orderDetails'));
    }

    public function collected()
    {
        $vendorId = $this->vendorId();
        
        $orderDetails = OrderDetails::whereHas('product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
        ->whereHas('order', function ($q) {
            $q->whereHas('status', function($sq) {
                $sq->whereNotIn('name', ['Pending', 'Processing', 'Approved', 'Cancelled', 'Canceled', 'Returned']); 
            });
        })
        ->with(['product', 'order'])
        ->latest()
        ->get();

        if ($orderDetails->isEmpty()) {
            $orderDetails = $this->dummyOrderDetails()->skip(3)->values();
        }

        return view('vendorPanel.collection.collected', compact('orderDetails'));
    }
}
