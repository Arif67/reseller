<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::guard('vendor')->check()) {
            return redirect()->route('vendor.dashboard');
        }
        return view('vendorPanel.auth.login');
    }

    public function signin(Request $request)
    {
        $this->validate($request, [
            'phone'    => 'required',
            'password' => 'required',
        ]);

        $vendor = Vendor::where('phone', $request->phone)->first();

        if (!$vendor) {
            Toastr::error('No account found with this phone', 'Opps!');
            return redirect()->back();
        }

        if ($vendor->status !== 'active') {
            Toastr::warning('Your account is ' . $vendor->status . '. Please wait for admin approval.', 'Not active');
            return redirect()->back();
        }

        if (Auth::guard('vendor')->attempt(['phone' => $request->phone, 'password' => $request->password])) {
            Toastr::success('You are logged in successfully', 'Success!');
            return redirect()->intended(route('vendor.dashboard'));
        }

        Toastr::error('Your phone or password is wrong', 'Opps!');
        return redirect()->back();
    }

    public function register()
    {
        if (Auth::guard('vendor')->check()) {
            return redirect()->route('vendor.dashboard');
        }
        return view('vendorPanel.auth.register');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'shop_name' => 'required',
            'phone'     => 'required|unique:vendors',
            'password'  => 'required|min:6|confirmed',
        ]);

        $last_id = Vendor::orderBy('id', 'desc')->first();
        $last_id = $last_id ? $last_id->id + 1 : 1;

        $vendor             = new Vendor();
        $vendor->name       = $request->name;
        $vendor->shop_name  = $request->shop_name;
        $vendor->shop_slug  = strtolower(Str::slug($request->shop_name . '-' . $last_id));
        $vendor->phone      = $request->phone;
        $vendor->email      = $request->email;
        $vendor->address    = $request->address;
        $vendor->password   = bcrypt($request->password);
        $vendor->status     = 'pending'; // admin approve na kora porjonto
        $vendor->save();

        Toastr::success('Account created. Please wait for admin approval before login.', 'Success');
        return redirect()->route('vendor.login');
    }

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();
        Toastr::success('Logged out successfully', 'Success');
        return redirect()->route('vendor.login');
    }
}
