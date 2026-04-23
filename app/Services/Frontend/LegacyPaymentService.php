<?php

namespace App\Services\Frontend;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Payment;
use Auth;
use Cart;
use shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers\ShurjopayController;

class LegacyPaymentService
{
    public function verifyPayment(string $orderId): array
    {
        $shurjopayService = new ShurjopayController();
        $decodedResponse = json_decode($shurjopayService->verify($orderId));

        return [
            'raw' => $decodedResponse,
            'payment' => $decodedResponse[0] ?? null,
            'success' => isset($decodedResponse[0]) && (int) $decodedResponse[0]->sp_code === 1000,
        ];
    }

    public function completeCustomerPayment(object $paymentData): void
    {
        Customer::query()->find(Auth::guard('customer')->user()->id);

        $order = new Order();
        $order->invoice_id = $paymentData->id;
        $order->amount = $paymentData->amount;
        $order->customer_id = Auth::guard('customer')->user()->id;
        $order->order_status = $paymentData->bank_status;
        $order->save();

        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->customer_id = Auth::guard('customer')->user()->id;
        $payment->payment_method = 'shurjopay';
        $payment->amount = $order->amount;
        $payment->trx_id = $paymentData->bank_trx_id;
        $payment->sender_number = $paymentData->phone_no;
        $payment->payment_status = 'paid';
        $payment->save();

        foreach (Cart::instance('shopping')->content() as $cartItem) {
            $orderDetails = new OrderDetails();
            $orderDetails->order_id = $order->id;
            $orderDetails->product_id = $cartItem->id;
            $orderDetails->product_name = $cartItem->name;
            $orderDetails->product_variable_id = $cartItem->options->product_variable_id ?? null;
            $orderDetails->purchase_price = $cartItem->options->purchase_price;
            $orderDetails->product_color = $cartItem->options->product_color ?? null;
            $orderDetails->product_size = $cartItem->options->product_size ?? null;
            $orderDetails->product_model = $cartItem->options->product_model ?? null;
            $orderDetails->product_weight = $cartItem->options->product_weight ?? null;
            $orderDetails->selected_attributes = $cartItem->options->selected_attributes ?? [];
            $orderDetails->sale_price = $cartItem->price;
            $orderDetails->qty = $cartItem->qty;
            $orderDetails->save();
        }

        Cart::instance('shopping')->destroy();
    }
}
