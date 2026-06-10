<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Reseller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::guard('reseller')->check()) {
            return redirect()->route('reseller.dashboard');
        }
        return view('resellerPanel.auth.login');
    }

    public function signin(Request $request)
    {
        $this->validate($request, [
            'phone'    => 'required',
            'password' => 'required',
        ]);

        $reseller = Reseller::where('phone', $request->phone)->first();

        if (!$reseller) {
            Toastr::error('No account found with this phone', 'Opps!');
            return redirect()->back();
        }

        if ($reseller->status !== 'active') {
            Toastr::warning('Your account is ' . $reseller->status . '. Please wait for admin approval.', 'Not active');
            return redirect()->back();
        }

        if (Auth::guard('reseller')->attempt(['phone' => $request->phone, 'password' => $request->password])) {
            Toastr::success('You are logged in successfully', 'Success!');
            return redirect()->intended(route('reseller.dashboard'));
        }

        Toastr::error('Your phone or password is wrong', 'Opps!');
        return redirect()->back();
    }

    public function register()
    {
        if (Auth::guard('reseller')->check()) {
            return redirect()->route('reseller.dashboard');
        }
        return view('resellerPanel.auth.register');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'phone'    => 'required|unique:resellers',
            'password' => 'required|min:6|confirmed',
        ]);

        $reseller                = new Reseller();
        $reseller->name          = $request->name;
        $reseller->business_name = $request->business_name;
        $reseller->phone         = $request->phone;
        $reseller->email         = $request->email;
        $reseller->address       = $request->address;
        $reseller->password      = bcrypt($request->password);
        $reseller->status        = 'pending'; // admin approve na kora porjonto
        $reseller->save();

        Toastr::success('Account created. Please wait for admin approval before login.', 'Success');
        return redirect()->route('reseller.login');
    }

    public function logout(Request $request)
    {
        Auth::guard('reseller')->logout();
        Toastr::success('Logged out successfully', 'Success');
        return redirect()->route('reseller.login');
    }
}
