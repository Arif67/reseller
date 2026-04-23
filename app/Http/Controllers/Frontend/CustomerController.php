<?php

namespace App\Http\Controllers\Frontend;
use shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers\ShurjopayController;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Customer;
use App\Models\District;
use App\Models\Order;
use App\Models\ShippingCharge;
use App\Models\OrderDetails;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\Review;
use App\Models\Product;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use App\Models\CouponCode;
use App\Models\AbandonedCartLead;
use App\Services\AppService\FileUploadService;
use App\Services\AppService\ProfitLossService;
use Illuminate\Http\Request;
use Session;
use Hash;
use Auth;
use Cart;
use Mail;
use Str;
use DB;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function __construct(
        private readonly ProfitLossService $profitLossService,
        private readonly FileUploadService $fileUploadService,
    ) {
        $this->middleware('customer', ['except' => ['register','customer_coupon','coupon_remove','store','verify','resendotp','account_verify','login','signin','logout','checkout','forgot_password','forgot_verify','forgot_reset','forgot_store','forgot_resend','order_save','order_success','order_track','order_track_result']]);
    }

    private function normalizeMarketingSource(string $source): string
    {
        $normalized = Str::lower(trim($source));

        if ($normalized === '') {
            return 'direct';
        }

        if (Str::contains($normalized, ['facebook', 'fb', 'instagram', 'ig', 'meta', 'messenger'])) {
            return 'facebook';
        }

        if (Str::contains($normalized, ['tiktok', 'tik tok'])) {
            return 'tiktok';
        }

        if (Str::contains($normalized, ['google', 'gads', 'adwords', 'youtube'])) {
            return 'google';
        }

        if (Str::contains($normalized, ['organic', 'seo'])) {
            return 'organic';
        }

        if (Str::contains($normalized, ['direct'])) {
            return 'direct';
        }

        return $normalized;
    }

    private function detectMarketingSource(Request $request): array
    {
        $utmSource = trim((string) $request->input('utm_source'));
        $utmMedium = trim((string) $request->input('utm_medium'));
        $utmCampaign = trim((string) $request->input('utm_campaign'));
        $utmTerm = trim((string) $request->input('utm_term'));
        $utmContent = trim((string) $request->input('utm_content'));
        $landingUrl = trim((string) $request->input('landing_url'));
        $referrerUrl = trim((string) $request->input('referrer_url'));
        $marketingSource = trim((string) $request->input('marketing_source'));
        $landingQuery = [];

        if ($landingUrl !== '') {
            parse_str((string) parse_url($landingUrl, PHP_URL_QUERY), $landingQuery);
        }

        if ($marketingSource !== '') {
            $marketingSource = $this->normalizeMarketingSource($marketingSource);
        } else {
            $combinedUtm = Str::lower(trim($utmSource . ' ' . $utmMedium));
            $referrerHost = Str::lower((string) parse_url($referrerUrl, PHP_URL_HOST));
            $appHost = Str::lower((string) parse_url(config('app.url'), PHP_URL_HOST));

            if (! empty($landingQuery['fbclid'])) {
                $marketingSource = 'facebook';
            } elseif (! empty($landingQuery['ttclid'])) {
                $marketingSource = 'tiktok';
            } elseif (! empty($landingQuery['gclid']) || ! empty($landingQuery['wbraid']) || ! empty($landingQuery['gbraid'])) {
                $marketingSource = 'google';
            } elseif (Str::contains($combinedUtm, ['facebook', 'fb', 'instagram', 'ig', 'meta', 'messenger'])) {
                $marketingSource = 'facebook';
            } elseif (Str::contains($combinedUtm, ['tiktok', 'tik tok'])) {
                $marketingSource = 'tiktok';
            } elseif (Str::contains($combinedUtm, ['google', 'gads', 'adwords', 'youtube'])) {
                $marketingSource = 'google';
            } elseif ($referrerHost !== '' && $appHost !== '' && $referrerHost !== $appHost) {
                if (Str::contains($referrerHost, ['facebook.com', 'fb.com', 'instagram.com', 'messenger.com'])) {
                    $marketingSource = 'facebook';
                } elseif (Str::contains($referrerHost, ['tiktok.com'])) {
                    $marketingSource = 'tiktok';
                } elseif (Str::contains($referrerHost, ['google.', 'bing.com', 'yahoo.com', 'duckduckgo.com'])) {
                    $marketingSource = 'organic';
                } else {
                    $marketingSource = 'referral';
                }
            } else {
                $marketingSource = 'direct';
            }
        }

        return [
            'marketing_source' => $marketingSource,
            'utm_source' => $utmSource ?: null,
            'utm_medium' => $utmMedium ?: null,
            'utm_campaign' => $utmCampaign ?: null,
            'utm_term' => $utmTerm ?: null,
            'utm_content' => $utmContent ?: null,
            'landing_url' => $landingUrl ?: null,
            'referrer_url' => $referrerUrl ?: null,
        ];
    }

     public function customer_coupon(Request $request){
        $findcoupon = CouponCode::where('coupon_code',$request->coupon_code)->first();
        if($findcoupon==NULL){
            Toastr::error('Opps! your entered promo code is not valid');
            return back();
        }else{
            $currentdata = date('Y-m-d');
            $expiry_date=$findcoupon->expiry_date;
            if($currentdata <= $expiry_date){
                $totalcart = Cart::instance('shopping')->subtotal();
                $totalcart = str_replace('.00','',$totalcart);
                $totalcart = str_replace(',','',$totalcart);
                if($totalcart >= $findcoupon->buy_amount){
                        if($totalcart >= $findcoupon->buy_amount){
                            if($findcoupon->offer_type==1){
                                $discountammount =  (($totalcart*$findcoupon->amount)/100);
                                Session::forget('coupon_amount');
                                 Session::put('coupon_amount',$discountammount);

                                Session::put('coupon_used',$findcoupon->coupon_code);
                            }else{
                                Session::put('coupon_amount',$findcoupon->amount);
                                Session::put('coupon_used',$findcoupon->coupon_code);
                            }
                            Toastr::success('Success! your promo code accepted');
                            return back();
                        }

                }else{
                    Toastr::error('You need to buy a minimum of ' . $findcoupon->buy_amount . ' Taka to get the offer');
                    return back();
                }
            }else{
                Toastr::error('Opps! Sorry your promo code date expaire');
                return back();
            }
        }
    }
    public function coupon_remove(Request $request){
        Session::forget('coupon_amount');
        Session::forget('coupon_used');
        Session::forget('discount');
        Toastr::success('Success','Your coupon remove successfully');
        return back();

    }

    public function review(Request $request){
        $this->validate($request,[
            'product_id' => 'required|integer|exists:products,id',
            'ratting'=>'required',
            'review'=>'required',
        ]);

        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            Toastr::error('Please login first to submit a review', 'Failed!');
            return redirect()->back();
        }

        $product = Product::query()->select('id')->findOrFail($request->product_id);

        if (! $product->hasCompletedPurchaseBy($customer->id)) {
            Toastr::error('Only verified buyers can review this product', 'Failed!');
            return redirect()->back();
        }

        // data save
        $review              =   new Review();
        $review->name        =   $customer->name ? $customer->name : 'N / A';
        $review->email       =   $customer->email ? $customer->email : 'N / A';
        $review->product_id  =   $request->product_id;
        $review->review      =   $request->review;
        $review->ratting     =   $request->ratting;
        $review->customer_id =   $customer->id;
        $review->status      =   'pending';
        $review->save();

        Toastr::success('Thanks, Your review send successfully', 'Success!');
        return redirect()->back();
    }

    public function login(){
        return view('frontEnd.pages.customer.login');
    }

    public function signin(Request $request){
        $auth_check = Customer::where('phone',$request->phone)->first();
        if($auth_check){
            if (Auth::guard('customer')->attempt(['phone' => $request->phone, 'password' => $request->password])) {
                Toastr::success('You are login successfully', 'success!');
                if(Cart::instance('shopping')->count() > 0){
                    return redirect()->route('customer.checkout');
                }
                return redirect()->intended('customer/account');
            }
            Toastr::error('message', 'Opps! your phone or password wrong');
            return redirect()->back();
        }else{
            Toastr::error('message', 'Sorry! You have no account');
            return redirect()->back();
        }
    }

    public function register(){
        return view('frontEnd.pages.customer.register');
    }

    public function store(Request $request){
        $this->validate($request, [
            'name'    => 'required',
            'phone'    => 'required|unique:customers',
            'password' => 'required|min:6'
        ]);

        $last_id = Customer::orderBy('id', 'desc')->first();
        $last_id = $last_id?$last_id->id+1:1;
        $store              = new Customer();
        $store->name        = $request->name;
        $store->slug        = strtolower(Str::slug($request->name.'-'.$last_id));
        $store->phone       = $request->phone;
        $store->email       = $request->email;
        $store->password    = bcrypt($request->password);
        $store->verify      = 1;
        $store->status      = 'active';
        $store->save();

        Toastr::success('Success','Account Create Successfully');
        return redirect()->route('customer.login');
    }
    public function verify(){
        return view('frontEnd.pages.customer.verify');
    }
    public function resendotp(Request $request){
        $customer_info = Customer::where('phone',session::get('verify_phone'))->first();
        $customer_info->verify = rand(1111,9999);
        $customer_info->save();
        $site_setting = GeneralSetting::where('status', 1)->first();
        $sms_gateway = SmsGateway::where('status', 1)->first();
        if($sms_gateway) {
            $url = "$sms_gateway->url";
            $data = [
                "api_key" => "$sms_gateway->api_key",
                "number" => $customer_info->phone,
                "type" => 'text',
                "senderid" => "$sms_gateway->serderid",
                "message" => "Dear $customer_info->name!\r\nYour account verify OTP is $customer_info->verify \r\nThank you for using $site_setting->name"
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

        }
        Toastr::success('Success','Resend code send successfully');
        return redirect()->back();
    }
    public function account_verify(Request $request){
        $this->validate($request,[
            'otp' => 'required',
        ]);
        $customer_info = Customer::where('phone',session::get('verify_phone'))->first();
        if($customer_info->verify != $request->otp){
            Toastr::error('Success','Your OTP not match');
            return redirect()->back();
        }

        $customer_info->verify = 1;
        $customer_info->status = 'active';
        $customer_info->save();
        Auth::guard('customer')->loginUsingId($customer_info->id);
        return redirect()->route('customer.account');
    }
    public function forgot_password(){
        return view('frontEnd.pages.customer.forgot_password');
    }

    public function forgot_verify(Request $request){
        $customer_info = Customer::where('phone',$request->phone)->first();
        if(!$customer_info){
            Toastr::error('Your phone number not found');
            return back();
        }
        $customer_info->forgot = rand(1111,9999);
        $customer_info->save();
        $site_setting = GeneralSetting::where('status', 1)->first();
        $sms_gateway = SmsGateway::where(['status'=> 1, 'forget_pass'=>1])->first();
        if($sms_gateway) {
            $url = "$sms_gateway->url";
            $data = [
                "api_key" => "$sms_gateway->api_key",
                "number" => $customer_info->phone,
                "type" => 'text',
                "senderid" => "$sms_gateway->serderid",
                "message" => "Dear $customer_info->name!\r\nYour forgot password verify OTP is $customer_info->forgot \r\nThank you for using $site_setting->name"
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        }

        session::put('verify_phone',$request->phone);
        Toastr::success('Your account register successfully');
        return redirect()->route('customer.forgot.reset');
    }

    public function forgot_resend(Request $request){
        $customer_info = Customer::where('phone',session::get('verify_phone'))->first();
        $customer_info->forgot = rand(1111,9999);
        $customer_info->save();
        $site_setting = GeneralSetting::where('status', 1)->first();
        $sms_gateway = SmsGateway::where(['status'=> 1])->first();
        if($sms_gateway) {
            $url = "$sms_gateway->url";
            $data = [
                "api_key" => "$sms_gateway->api_key",
                "number" => $customer_info->phone,
                "type" => 'text',
                "senderid" => "$sms_gateway->serderid",
                "message" => "Dear $customer_info->name!\r\nYour forgot password verify OTP is $customer_info->forgot \r\nThank you for using $site_setting->name"
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

        }

        Toastr::success('Success','Resend code send successfully');
        return redirect()->back();
    }
    public function forgot_reset(){
        if(!Session::get('verify_phone')){
          Toastr::error('Something wrong please try again');
          return redirect()->route('customer.forgot.password');
        };
        return view('frontEnd.pages.customer.forgot_reset');
    }
    public function forgot_store(Request $request){

        $customer_info = Customer::where('phone',session::get('verify_phone'))->first();

        if($customer_info->forgot != $request->otp){
            Toastr::error('Success','Your OTP not match');
            return redirect()->back();
        }

        $customer_info->forgot = 1;
        $customer_info->password = bcrypt($request->password);
        $customer_info->save();
        if(Auth::guard('customer')->attempt(['phone' => $customer_info->phone, 'password' => $request->password])) {
            Session::forget('verify_phone');
            Toastr::success('You are login successfully', 'success!');
                return redirect()->intended('customer/account');
        }
    }
    public function account(){
        return view('frontEnd.pages.customer.account');
    }
    public function logout(Request $request){
        Auth::guard('customer')->logout();
        Toastr::success('You are logout successfully', 'success!');
        return redirect()->route('customer.login');
    }
    public function checkout(){
        $shippingcharge = ShippingCharge::where(['status'=>1,'website'=>1])->get();
        $select_charge = ShippingCharge::where(['status'=>1,'website'=>1])->first();
        $bkash_gateway = PaymentGateway::where(['status'=> 1, 'type'=>'bkash'])->first();
        $shurjopay_gateway = PaymentGateway::where(['status'=> 1, 'type'=>'shurjopay'])->first();
        if(Session::get('free_shipping') == 1){
            Session::put('shipping',0);
        }else{
            Session::put('shipping',$select_charge->amount);
        }

       return view('frontEnd.pages.customer.checkout',compact('shippingcharge', 'bkash_gateway', 'shurjopay_gateway'));
    }
    public function order_save(Request $request){

        $this->validate($request,[
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required'
        ]);
        $cartItems = Cart::instance('shopping')->content();

       // dd($cartItems);

        if(Cart::instance('shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('shopping')->subtotal();
        $subtotal = str_replace(',','',$subtotal);
        $subtotal = str_replace('.00', '',$subtotal);
        $discount = Session::get('coupon_amount');

        $shippingfee  = Session::get('free_shipping') ? 0 :Session::get('shipping');

        $shipping_area  = ShippingCharge::where('id', $request->area)->first();
      //  dd($shipping_area);
        if(Auth::guard('customer')->user()){
            $customer_id = Auth::guard('customer')->user()->id;
        }else{
            $exits_customer = Customer::where('phone',$request->phone)->select('phone','id')->first();
            if($exits_customer){
                $customer_id = $exits_customer->id;
            }else{

            $password = rand(111111,999999);
            $store              = new Customer();
            $store->name        = $request->name;
            $store->slug        = $request->name;
            $store->phone       = $request->phone;
            $store->password    = bcrypt($password);
            $store->verify      = 1;
            $store->status      = 'active';
            $store->save();
            $customer_id = $store->id;

            }

        }

        $sourceData = $this->detectMarketingSource($request);

         // order data save
        $order                   = new Order();
        $order->invoice_id       = rand(11111,99999);
        $order->ip_address       = $request->ip();
        $order->amount           = ($subtotal + $shipping_area->amount) - $discount;
        $order->discount         = $discount ? $discount : 0;
        $order->shipping_charge  = $shipping_area->amount;
        $order->customer_id      = $customer_id;
        $order->order_status     = 1;
        $order->note             = $request->note;

        if (Schema::hasColumn('orders', 'marketing_source')) {
            $order->marketing_source = $sourceData['marketing_source'];
            $order->utm_source = $sourceData['utm_source'];
            $order->utm_medium = $sourceData['utm_medium'];
            $order->utm_campaign = $sourceData['utm_campaign'];
            $order->utm_term = $sourceData['utm_term'];
            $order->utm_content = $sourceData['utm_content'];
            $order->landing_url = $sourceData['landing_url'];
            $order->referrer_url = $sourceData['referrer_url'];
        }

        $order->save();

        // shipping data save
        $shipping              =   new Shipping();
        $shipping->order_id    =   $order->id;
        $shipping->customer_id =   $customer_id;
        $shipping->name        =   $request->name;
        $shipping->phone       =   $request->phone;
        $shipping->address     =   $request->address;
        $shipping->area        =   $shipping_area?$shipping_area->name:'Free Shipping';
        $shipping->save();

        // payment data save
        $payment                 = new Payment();
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = $request->payment_method;
        $payment->amount         = $order->amount;
        $payment->payment_status = 'pending';
        $payment->save();

      // order details data save
        foreach(Cart::instance('shopping')->content() as $cart){
            $order_details                  =   new OrderDetails();
            $order_details->order_id        =   $order->id;
            $order_details->product_id      =   $cart->id;
            $order_details->product_variable_id = $cart->options->product_variable_id ?? null;
            $order_details->product_name    =   $cart->name;
            $order_details->purchase_price  =   $cart->options->purchase_price;
            $order_details->product_color   =   $cart->options->product_color;
            $order_details->product_size    =   $cart->options->product_size;
            $order_details->type            =   $cart->options->type;
            $order_details->sale_price      =   $cart->price;
            $order_details->qty             =   $cart->qty;
            $order_details->product_weight  =   $cart->options->product_weight;
            $order_details->product_model   =   $cart->options->product_model;
            $order_details->selected_attributes = $cart->options->selected_attributes ?? [];
            $order_details->save();
        }

        $this->profitLossService->recalculateOrder($order);

        Session::forget('free_shipping');
        Cart::instance('shopping')->destroy();

        if (Schema::hasTable('abandoned_cart_leads')) {
            $leadQuery = AbandonedCartLead::query()
                ->where('session_id', $request->session()->getId());

            if (! empty($request->phone)) {
                $leadQuery->orWhere('phone', $request->phone);
            }

            if (! empty($customer_id)) {
                $leadQuery->orWhere('customer_id', $customer_id);
            }

            $leadQuery->delete();
        }

        Toastr::success('Thanks, Your order place successfully', 'Success!');
        $site_setting = GeneralSetting::where('status', 1)->first();
        $sms_gateway = SmsGateway::where(['status'=> 1, 'order'=>'1'])->first();
        if($sms_gateway) {
            $url = "$sms_gateway->url";
            $data = [
                "api_key" => "$sms_gateway->api_key",
                "number" => $request->phone,
                "type" => 'text',
                "senderid" => "$sms_gateway->serderid",
                "message" => "Dear $request->name!\r\nYour order ($order->invoice_id) has been successfully placed. Track your order tracking https://projapotishop.com/customer/order-track and Total Bill $order->amount\r\nThank you for using $site_setting->name"
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        }

        if($request->payment_method=='bkash'){
            return redirect('/bkash/checkout-url/create?order_id='.$order->id);
        }elseif($request->payment_method=='shurjopay'){
            $info = array(
                'currency' => "BDT",
                'amount' => $order->amount,
                'order_id' => uniqid(),
                'discsount_amount' =>0 ,
                'disc_percent' =>0 ,
                'client_ip' => $request->ip(),
                'customer_name' =>  $request->name,
                'customer_phone' => $request->phone,
                'email' => "customer@gmail.com",
                'customer_address' => $request->address,
                'customer_city' => $request->area,
                'customer_state' => $request->area,
                'customer_postcode' => "1212",
                'customer_country' => "BD",
                'value1' => $order->id
            );
            $shurjopay_service = new ShurjopayController();
            return $shurjopay_service->checkout($info);
        }else{
            return redirect('customer/order-success/'.$order->id);
        }

    }

    public function orders()
    {
        $orders = Order::where('customer_id',Auth::guard('customer')->user()->id)->with('status')->latest()->get();
        return view('frontEnd.pages.customer.orders',compact('orders'));
    }
    public function order_success($id) {
        $order = Order::where('id', $id)
            ->with([
                'orderdetails.image',
                'orderdetails.product.image',
                'orderdetails.product.media',
                'orderdetails.productVariable.media',
                'shipping',
            ])->firstOrFail();
        return view('frontEnd.pages.customer.order_success',compact('order'));
    }
    public function invoice(Request $request)
    {
        $order = Order::where(['id'=>$request->id,'customer_id'=>Auth::guard('customer')->user()->id])->with('orderdetails','payment','shipping','customer')->firstOrFail();
        return view('frontEnd.pages.customer.invoice',compact('order'));
    }
    public function order_note(Request $request)
    {
        $order = Order::where(['id'=>$request->id,'customer_id'=>Auth::guard('customer')->user()->id])->firstOrFail();
        return view('frontEnd.pages.customer.order_note',compact('order'));
    }
    public function profile_edit(Request $request)
    {
        $profile_edit = Customer::where(['id'=>Auth::guard('customer')->user()->id])->firstOrFail();
        $districts = District::distinct()->select('district')->get();
        $areas = District::where(['district'=>$profile_edit->district])->select('area_name','id')->get();
        return view('frontEnd.pages.customer.profile_edit',compact('profile_edit','districts','areas'));
    }
    public function profile_update(Request $request)
    {
        $update_data = Customer::where(['id'=>Auth::guard('customer')->user()->id])->firstOrFail();

        $image = $request->file('image');
        if($image){
            $imageUrl = $this->fileUploadService->processAndUploadImage(
                $image,
                'uploads/customer',
                ['width' => 120, 'height' => 120, 'prefix' => 'customer-profile']
            );
            $this->fileUploadService->deleteLocalIfNeeded($update_data->image);
        }else{
            $imageUrl = $update_data->image;
        }

        $update_data->name        =   $request->name;
        $update_data->phone       =   $request->phone;
        $update_data->email       =   $request->email;
        $update_data->address     =   $request->address;
        $update_data->district    =   $request->district;
        $update_data->area        =   $request->area;
        $update_data->image       =   $imageUrl;
        $update_data->save();

        Toastr::success('Your profile update successfully', 'Success!');
       return redirect()->route('customer.account');
    }

    public function order_track(){
        return view('frontEnd.pages.customer.order_track');
    }

     public function order_track_result(Request $request){

       $phone = $request->phone;
       $invoice_id = $request->invoice_id;

       if($phone !=null && $invoice_id==null){
        $order = DB::table('orders')
        ->join('shippings','orders.id','=','shippings.order_id')
        ->where(['shippings.phone' => $request->phone])
        ->get();

       }else if($invoice_id && $phone){
         $order = DB::table('orders')
        ->join('shippings','orders.id','=','shippings.order_id')
        ->where(['orders.invoice_id' => $request->invoice_id, 'shippings.phone'=>$request->phone])
        ->get();
       }

       if($order->count() == 0){

            Toastr::error('message', 'Something Went Wrong !');
            return redirect()->back();
       }

    //   return $order->count();



        return view('frontEnd.pages.customer.tracking_result',compact('order'));
    }


    public function change_pass(){
        return view('frontEnd.pages.customer.change_password');
    }

     public function password_update(Request $request)
    {
        $this->validate($request, [
            'old_password'=>'required',
            'new_password'=>'required',
            'confirm_password' => 'required_with:new_password|same:new_password|'
        ]);

        $customer = Customer::find(Auth::guard('customer')->user()->id);
        $hashPass = $customer->password;

        if (Hash::check($request->old_password, $hashPass)) {

            $customer->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            Toastr::success('Success', 'Password changed successfully!');
            return redirect()->route('customer.account');
        }else{
            Toastr::error('Failed', 'Old password not match!');
            return redirect()->back();
        }
    }
}
