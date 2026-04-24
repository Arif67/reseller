<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use Carbon\Carbon;
use Session;
use Toastr;
use Auth;
use DB;
use Illuminate\Support\Str;
class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth')->except(['locked','unlocked']);
    }
    public function dashboard()
    {
        $statusMap = Cache::remember('admin_dashboard_status_map_v1', 300, function () {
            $statusCollection = OrderStatus::query()->get();

            $resolveStatusIds = function (array $candidates) use ($statusCollection) {
                return $statusCollection
                    ->filter(function ($status) use ($candidates) {
                        $slug = Str::slug((string) $status->slug);
                        $name = Str::slug((string) $status->name);

                        foreach ($candidates as $candidate) {
                            $normalized = Str::slug($candidate);

                            if ($slug === $normalized || $name === $normalized) {
                                return true;
                            }
                        }

                        return false;
                    })
                    ->pluck('id')
                    ->map(fn ($id) => (string) $id)
                    ->all();
            };

            return [
                'pending' => $resolveStatusIds(['pending']),
                'processing' => $resolveStatusIds(['processing', 'approved']),
                'on_the_way' => $resolveStatusIds(['on-the-way', 'on the way', 'packed', 'shipped']),
                'on_hold' => $resolveStatusIds(['on-hold', 'on hold']),
                'in_courier' => $resolveStatusIds(['in-courier', 'in courier', 'out-for-delivery']),
                'completed' => $resolveStatusIds(['completed', 'delivered', 'complete', 'deliveryed']),
                'cancelled' => $resolveStatusIds(['cancelled', 'canceled', 'returned']),
                'didnt_receive' => $resolveStatusIds(['didnt-receive-call', 'didn-t-receive-call', 'didnt receive call', 'did not receive call']),
            ];
        });

        $dashboardData = Cache::remember('admin_dashboard_payload_v2', 30, function () use ($statusMap) {
            $today = Carbon::today();
            $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
            $lastMonthDate = Carbon::now()->subMonth();

            $statusSummary = Order::query()
                ->selectRaw('CAST(order_status AS CHAR) as status_key')
                ->selectRaw('COUNT(*) as total_count')
                ->selectRaw('COALESCE(SUM(amount), 0) as total_amount')
                ->groupBy('order_status')
                ->get()
                ->keyBy('status_key');

            $sumStatusMetrics = function (array $statusIds) use ($statusSummary): array {
                $count = 0;
                $amount = 0.0;

                foreach ($statusIds as $statusId) {
                    $row = $statusSummary->get((string) $statusId);

                    if (! $row) {
                        continue;
                    }

                    $count += (int) ($row->total_count ?? 0);
                    $amount += (float) ($row->total_amount ?? 0);
                }

                return ['count' => $count, 'amount' => $amount];
            };

            $overall = Order::query()
                ->selectRaw('COUNT(*) as total_order_count')
                ->selectRaw('COALESCE(SUM(amount), 0) as total_order_amount')
                ->first();

            $todaySummary = Order::query()
                ->whereDate('created_at', $today)
                ->selectRaw('COUNT(*) as total_count')
                ->selectRaw('COALESCE(SUM(amount), 0) as total_amount')
                ->first();

            $today_order_list = Order::query()
                ->whereDate('created_at', $today)
                ->latest('id')
                ->get();

            $inCourierIds = $statusMap['in_courier'] ?? [];
            $deliveryQuery = empty($inCourierIds) ? null : Order::query()->whereIn('order_status', $inCourierIds);

            $today_delivery = $deliveryQuery ? (clone $deliveryQuery)->whereDate('created_at', $today)->count() : 0;
            $total_delivery = $deliveryQuery ? (clone $deliveryQuery)->count() : 0;
            $last_week = $deliveryQuery ? (clone $deliveryQuery)->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count() : 0;
            $last_month = $deliveryQuery ? (clone $deliveryQuery)->whereYear('created_at', $lastMonthDate->year)->whereMonth('created_at', $lastMonthDate->month)->count() : 0;

            $monthly_sale = $deliveryQuery
                ? (clone $deliveryQuery)
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as amount'))
                    ->groupBy('date')
                    ->orderByDesc('date')
                    ->limit(30)
                    ->get()
                    ->sortBy('date')
                    ->values()
                : collect();

            $topBestSellingStats = OrderDetails::query()
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->when(! empty($statusMap['completed']), function ($query) use ($statusMap) {
                    $query->whereIn('orders.order_status', $statusMap['completed']);
                }, function ($query) {
                    $query->whereRaw('1 = 0');
                })
                ->whereNotNull('order_details.product_id')
                ->select(
                    'order_details.product_id',
                    DB::raw('SUM(order_details.qty) as sold_quantity'),
                    DB::raw('SUM(order_details.qty * order_details.sale_price) as sold_amount'),
                    DB::raw('COUNT(DISTINCT order_details.order_id) as total_orders')
                )
                ->groupBy('order_details.product_id')
                ->orderByDesc('sold_quantity')
                ->limit(5)
                ->get();

            $topBestSellingProduct = null;

            if ($topBestSellingStats->isNotEmpty()) {
                $topBestSellingProductMap = Product::where('status', 1)
                    ->whereIn('id', $topBestSellingStats->pluck('product_id'))
                    ->select('id', 'name', 'slug', 'new_price', 'old_price')
                    ->with('image')
                    ->get()
                    ->keyBy('id');

                $topBestSellingProduct = $topBestSellingStats->map(function ($stat) use ($topBestSellingProductMap) {
                    $product = $topBestSellingProductMap->get($stat->product_id);

                    if (! $product) {
                        return null;
                    }

                    $product->setAttribute('sold_quantity', (int) $stat->sold_quantity);
                    $product->setAttribute('sold_amount', (float) $stat->sold_amount);
                    $product->setAttribute('total_orders', (int) $stat->total_orders);

                    return $product;
                })->filter()->first();
            }

            return [
                'total_order_count' => (int) ($overall->total_order_count ?? 0),
                'total_order_amount' => (float) ($overall->total_order_amount ?? 0),
                'pending' => $sumStatusMetrics($statusMap['pending'] ?? []),
                'processing' => $sumStatusMetrics($statusMap['processing'] ?? []),
                'on_the_way' => $sumStatusMetrics($statusMap['on_the_way'] ?? []),
                'on_hold' => $sumStatusMetrics($statusMap['on_hold'] ?? []),
                'in_courier' => $sumStatusMetrics($statusMap['in_courier'] ?? []),
                'completed' => $sumStatusMetrics($statusMap['completed'] ?? []),
                'cancelled' => $sumStatusMetrics($statusMap['cancelled'] ?? []),
                'didnt_receive' => $sumStatusMetrics($statusMap['didnt_receive'] ?? []),
                'today_order_count' => (int) ($todaySummary->total_count ?? 0),
                'today_order_amount' => (float) ($todaySummary->total_amount ?? 0),
                'today_order_list' => $today_order_list,
                'total_product' => Product::count(),
                'total_customer' => Customer::count(),
                'latest_order' => Order::latest('id')->limit(5)->with('customer', 'product', 'product.image', 'status')->get(),
                'latest_customer' => Customer::latest('id')->limit(5)->get(),
                'today_delivery' => $today_delivery,
                'total_delivery' => $total_delivery,
                'last_week' => $last_week,
                'last_month' => $last_month,
                'monthly_sale' => $monthly_sale,
                'topBestSellingProduct' => $topBestSellingProduct,
            ];
        });

        return view('backEnd.admin.dashboard', [
            'total_order_count' => $dashboardData['total_order_count'],
            'total_order_amount' => $dashboardData['total_order_amount'],
            'pending_count' => $dashboardData['pending']['count'],
            'pending_amount' => $dashboardData['pending']['amount'],
            'processing_count' => $dashboardData['processing']['count'],
            'processing_amount' => $dashboardData['processing']['amount'],
            'on_the_way_count' => $dashboardData['on_the_way']['count'],
            'on_the_way_amount' => $dashboardData['on_the_way']['amount'],
            'on_hold_count' => $dashboardData['on_hold']['count'],
            'on_hold_amount' => $dashboardData['on_hold']['amount'],
            'in_courier_count' => $dashboardData['in_courier']['count'],
            'in_courier_amount' => $dashboardData['in_courier']['amount'],
            'completed_count' => $dashboardData['completed']['count'],
            'completed_amount' => $dashboardData['completed']['amount'],
            'cancelled_count' => $dashboardData['cancelled']['count'],
            'cancelled_amount' => $dashboardData['cancelled']['amount'],
            'didnt_receive_count' => $dashboardData['didnt_receive']['count'],
            'didnt_receive_amount' => $dashboardData['didnt_receive']['amount'],
            'today_order_count' => $dashboardData['today_order_count'],
            'today_order_amount' => $dashboardData['today_order_amount'],
            'today_order_list' => $dashboardData['today_order_list'],
            'total_product' => $dashboardData['total_product'],
            'total_customer' => $dashboardData['total_customer'],
            'latest_order' => $dashboardData['latest_order'],
            'latest_customer' => $dashboardData['latest_customer'],
            'today_delivery' => $dashboardData['today_delivery'],
            'total_delivery' => $dashboardData['total_delivery'],
            'last_week' => $dashboardData['last_week'],
            'last_month' => $dashboardData['last_month'],
            'monthly_sale' => $dashboardData['monthly_sale'],
            'topBestSellingProduct' => $dashboardData['topBestSellingProduct'],
        ]);
    }

    public function changepassword(){
        return view('backEnd.admin.changepassword');
    }
     public function newpassword(Request $request)
    {
        $this->validate($request, [
            'old_password'=>'required',
            'new_password'=>'required',
            'confirm_password' => 'required_with:new_password|same:new_password|'
        ]);

        $user = User::find(Auth::id());
        $hashPass = $user->password;

        if (Hash::check($request->old_password, $hashPass)) {

            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            Toastr::success('Success', 'Password changed successfully!');
            return redirect()->route('dashboard');
        }else{
            Toastr::error('Failed', 'Old password not match!');
            return back();
        }
    }
    public function locked(){
        // only if user is logged in
        
            Session::put('locked', true);
            return view('backEnd.auth.locked');
        

        return redirect()->route('login');
    }

    public function unlocked(Request $request)
    {
        if(!Auth::check())
            return redirect()->route('login');
        $password = $request->password;
        if(Hash::check($password,Auth::user()->password)){
            Session::forget('locked');
            Toastr::success('Success', 'You are logged in successfully!');
            return redirect()->route('dashboard');
        }
        Toastr::error('Failed', 'Your password not match!');
        return back();
    }
}
