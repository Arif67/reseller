<?php

namespace App\Support;

use App\Models\OrderDetails;
use App\Models\VendorWithdraw;

class VendorEarnings
{
    // In-progress: order placed, not yet delivered/cancelled/returned.
    public const PROGRESS_SLUGS = ['pending', 'processing', 'on-hold', 'confirmed', 'shipped', 'out_for_delivery'];
    // Earned: counts toward withdrawable balance.
    public const EARNED_SLUGS = ['delivered', 'completed'];
    // Cancelled / failed.
    public const CANCELLED_SLUGS = ['cancelled', 'failed'];
    // Returned / refunded.
    public const RETURNED_SLUGS = ['returned', 'refunded'];

    /** Backwards-compat alias used by the dashboard. */
    public const COMPLETED_SLUGS = self::EARNED_SLUGS;

    /**
     * Sum of vendor rate (purchase_price × qty) for this vendor's items
     * in orders whose current status slug is in $slugs.
     */
    public static function bucket(int $vendorId, array $slugs): float
    {
        return (float) OrderDetails::whereHas('product', fn ($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order.status', fn ($q) => $q->whereIn('slug', $slugs))
            ->selectRaw('COALESCE(SUM(purchase_price * qty), 0) AS total')
            ->value('total');
    }

    /** Lifetime earned (delivered) — kept for existing callers. */
    public static function lifetime(int $vendorId): float
    {
        return self::bucket($vendorId, self::EARNED_SLUGS);
    }

    /**
     * Full money summary: the 4 order-state buckets + withdraw figures.
     * available = earned − (pending + paid withdraws); may go negative if a
     * delivered order is later returned after the money was withdrawn.
     */
    public static function summary(int $vendorId): array
    {
        $pending   = self::bucket($vendorId, self::PROGRESS_SLUGS);
        $earned    = self::bucket($vendorId, self::EARNED_SLUGS);
        $cancelled = self::bucket($vendorId, self::CANCELLED_SLUGS);
        $returned  = self::bucket($vendorId, self::RETURNED_SLUGS);

        $withdrawPending = (float) VendorWithdraw::where('vendor_id', $vendorId)
            ->where('status', 'pending')->sum('amount');
        $withdrawPaid = (float) VendorWithdraw::where('vendor_id', $vendorId)
            ->where('status', 'paid')->sum('amount');

        $available = round($earned - $withdrawPending - $withdrawPaid, 2);

        return [
            // order-state buckets (vendor rate)
            'pending'   => round($pending, 2),
            'earned'    => round($earned, 2),
            'cancelled' => round($cancelled, 2),
            'returned'  => round($returned, 2),
            // withdraw figures
            'withdraw_pending' => round($withdrawPending, 2),
            'paid'             => round($withdrawPaid, 2),
            'available'        => $available,           // can be negative
            'withdrawable'     => max(0, $available),   // capped for the form
            // alias kept for older view code
            'lifetime'  => round($earned, 2),
        ];
    }
}
