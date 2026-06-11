<?php

namespace App\Support;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Support\VendorEarnings;

class VendorMenuCounts
{
    // Statuses that count as "still pending / to collect".
    private const PENDING_STATUSES = ['Pending', 'Processing', 'Approved'];

    /**
     * Counters shown as badges in the vendor top menu.
     */
    public static function get(int $vendorId): array
    {
        $ownProduct = fn ($q) => $q->where('vendor_id', $vendorId);

        $orders = Order::whereHas('orderdetails.product', $ownProduct)->count();

        $toCollect = OrderDetails::whereHas('product', $ownProduct)
            ->where('vendor_collected', 0)
            ->whereHas('order.status', fn ($q) => $q->whereIn('name', self::PENDING_STATUSES));

        $collection = (clone $toCollect)->count();
        $pending    = (clone $toCollect)->distinct()->count('product_id');

        $collected = OrderDetails::whereHas('product', $ownProduct)
            ->where('vendor_collected', 1)
            ->count();

        // Returned items waiting for the vendor to confirm receipt.
        $returns = OrderDetails::whereHas('product', $ownProduct)
            ->where('vendor_return_received', 0)
            ->whereHas('order.status', fn ($q) => $q->whereIn('slug', VendorEarnings::RETURNED_SLUGS))
            ->count();

        $products = Product::where('vendor_id', $vendorId)->count();

        return [
            'orders'     => $orders,
            'collection' => $collection,
            'pending'    => $pending,
            'collected'  => $collected,
            'returns'    => $returns,
            'products'   => $products,
        ];
    }
}
