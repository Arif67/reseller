<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            ->all();
    };

    $statusMap = [
        'pending' => $resolveStatusIds(['pending']),
        'processing' => $resolveStatusIds(['processing', 'approved']),
        'on_the_way' => $resolveStatusIds(['on-the-way', 'on the way', 'packed', 'shipped']),
        'on_hold' => $resolveStatusIds(['on-hold', 'on hold']),
        'in_courier' => $resolveStatusIds(['in-courier', 'in courier', 'out-for-delivery']),
        'completed' => $resolveStatusIds(['completed', 'delivered', 'complete', 'deliveryed']),
        'cancelled' => $resolveStatusIds(['cancelled', 'canceled', 'returned']),
        'didnt_receive' => $resolveStatusIds(['didnt-receive-call', 'didn-t-receive-call', 'didnt receive call', 'did not receive call']),
    ];

    // Total order count and amount
    $total_order_count  = Order::count();
    $total_order_amount = Order::sum('amount');

    // Status-wise counts and amounts
    $pending_count  = empty($statusMap['pending']) ? 0 : Order::whereIn('order_status', $statusMap['pending'])->count();
    $pending_amount = empty($statusMap['pending']) ? 0 : Order::whereIn('order_status', $statusMap['pending'])->sum('amount');

    $processing_count  = empty($statusMap['processing']) ? 0 : Order::whereIn('order_status', $statusMap['processing'])->count();
    $processing_amount = empty($statusMap['processing']) ? 0 : Order::whereIn('order_status', $statusMap['processing'])->sum('amount');

    $on_the_way_count  = empty($statusMap['on_the_way']) ? 0 : Order::whereIn('order_status', $statusMap['on_the_way'])->count();
    $on_the_way_amount = empty($statusMap['on_the_way']) ? 0 : Order::whereIn('order_status', $statusMap['on_the_way'])->sum('amount');

    $on_hold_count  = empty($statusMap['on_hold']) ? 0 : Order::whereIn('order_status', $statusMap['on_hold'])->count();
    $on_hold_amount = empty($statusMap['on_hold']) ? 0 : Order::whereIn('order_status', $statusMap['on_hold'])->sum('amount');

    $in_courier_count  = empty($statusMap['in_courier']) ? 0 : Order::whereIn('order_status', $statusMap['in_courier'])->count();
    $in_courier_amount = empty($statusMap['in_courier']) ? 0 : Order::whereIn('order_status', $statusMap['in_courier'])->sum('amount');

    $completed_count  = empty($statusMap['completed']) ? 0 : Order::whereIn('order_status', $statusMap['completed'])->count();
    $completed_amount = empty($statusMap['completed']) ? 0 : Order::whereIn('order_status', $statusMap['completed'])->sum('amount');

    $cancelled_count  = empty($statusMap['cancelled']) ? 0 : Order::whereIn('order_status', $statusMap['cancelled'])->count();
    $cancelled_amount = empty($statusMap['cancelled']) ? 0 : Order::whereIn('order_status', $statusMap['cancelled'])->sum('amount');

    $didnt_receive_count  = empty($statusMap['didnt_receive']) ? 0 : Order::whereIn('order_status', $statusMap['didnt_receive'])->count();
    $didnt_receive_amount = empty($statusMap['didnt_receive']) ? 0 : Order::whereIn('order_status', $statusMap['didnt_receive'])->sum('amount');

    // Today’s orders
    $today_order_count  = Order::whereDate('created_at', Carbon::today())->count();
    $today_order_amount = Order::whereDate('created_at', Carbon::today())->sum('amount');
    $today_order_list   = Order::whereDate('created_at', Carbon::today())->get();

    // Total products and customers
    $total_product  = Product::count();
    $total_customer = Customer::count();

    // Latest orders and customers
    $latest_order    = Order::latest()->limit(5)->with('customer', 'product', 'product.image', 'status')->get();
    $latest_customer = Customer::latest()->limit(5)->get();

    // Deliveries
    $today_delivery = empty($statusMap['in_courier'])
        ? 0
        : Order::whereIn('order_status', $statusMap['in_courier'])->whereDate('created_at', Carbon::today())->count();
    $total_delivery = empty($statusMap['in_courier'])
        ? 0
        : Order::whereIn('order_status', $statusMap['in_courier'])->count();

    // Last week and last month deliveries
    $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
    $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
    $lastMonthDate = Carbon::now()->subMonth();

    $last_week = empty($statusMap['in_courier'])
        ? 0
        : Order::whereIn('order_status', $statusMap['in_courier'])
        ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
        ->count();

    $last_month = empty($statusMap['in_courier'])
        ? 0
        : Order::whereIn('order_status', $statusMap['in_courier'])
        ->whereYear('created_at', $lastMonthDate->year)
        ->whereMonth('created_at', $lastMonthDate->month)
        ->count();

    // Monthly sales
    $monthly_sale = empty($statusMap['in_courier'])
        ? collect()
        : Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as amount'))
        ->whereIn('order_status', $statusMap['in_courier'])
        ->groupBy('date')
        ->orderByDesc('date')
        ->limit(30)
        ->get()
        ->sortBy('date')
        ->values();

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

    return view('backEnd.admin.dashboard', compact(
        'total_order_count', 'total_order_amount',
        'pending_count', 'pending_amount', 
        'processing_count', 'processing_amount', 
        'on_the_way_count', 'on_the_way_amount', 
        'on_hold_count', 'on_hold_amount', 
        'in_courier_count', 'in_courier_amount', 
        'completed_count', 'completed_amount', 
        'cancelled_count', 'cancelled_amount', 
        'didnt_receive_count', 'didnt_receive_amount', 
        'today_order_count', 'today_order_amount', 'today_order_list', 
        'total_product', 'total_customer', 
        'latest_order', 'latest_customer', 
        'today_delivery', 'total_delivery', 
        'last_week', 'last_month', 'monthly_sale', 'topBestSellingProduct'
    ));
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
