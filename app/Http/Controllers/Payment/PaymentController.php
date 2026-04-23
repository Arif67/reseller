<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Frontend\LegacyPaymentService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly LegacyPaymentService $legacyPaymentService)
    {
    }

    public function success(Request $request): RedirectResponse
    {
        $verification = $this->legacyPaymentService->verifyPayment((string) $request->order_id);
        $payment = $verification['payment'];

        if (! $verification['success'] || ! $payment) {
            Toastr::error('Your payment failed, try again', 'Oops!');
            return redirect()->route('home');
        }

        if (($payment->value1 ?? null) === 'customer_payment') {
            $this->legacyPaymentService->completeCustomerPayment($payment);
            Toastr::success('Thanks, Your payment send successfully', 'Success!');
            return redirect()->route('home');
        }

        Toastr::error('Something wrong, please try agian', 'Error!');
        return redirect()->route('home');
    }

    public function cancel(Request $request): RedirectResponse
    {
        Toastr::error('Your payment cancelled', 'Cancelled!');
        return redirect()->route('home');
    }
}
