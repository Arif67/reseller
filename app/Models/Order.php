<?php

namespace App\Models;

use App\Services\OrderTracking\OrderStatusTrackingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected ?int $trackingPreviousOrderStatus = null;

    protected $fillable = [
        'amount',
        'discount',
        'invoice_id',
        'shipping_charge',
        'customer_id',
        'customer_ip',
        'admin_note',
        'order_status',
        'created_at',
        'user_id',
        'note',
        'updated_at',
        'tracking_id',
        'courier',
        'ip',
        'marketing_source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'landing_url',
        'referrer_url',
        'item_subtotal',
        'line_discount_total',
        'item_revenue',
        'product_cost',
        'shipping_revenue',
        'discount_total',
        'courier_cost',
        'packaging_cost',
        'payment_gateway_fee',
        'misc_cost',
        'additional_cost',
        'total_expense',
        'gross_profit',
        'net_profit',
        'profit_status',
    ];

    protected $casts = [
        'item_subtotal' => 'float',
        'line_discount_total' => 'float',
        'item_revenue' => 'float',
        'product_cost' => 'float',
        'shipping_revenue' => 'float',
        'discount_total' => 'float',
        'courier_cost' => 'float',
        'packaging_cost' => 'float',
        'payment_gateway_fee' => 'float',
        'misc_cost' => 'float',
        'additional_cost' => 'float',
        'total_expense' => 'float',
        'gross_profit' => 'float',
        'net_profit' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->trackingPreviousOrderStatus = null;
        });

        static::updating(function (Order $order) {
            $order->trackingPreviousOrderStatus = $order->getOriginal('order_status');
        });

        static::created(function (Order $order) {
            app(OrderStatusTrackingService::class)->trackPlacement($order);
        });

        static::updated(function (Order $order) {
            if (! $order->wasChanged('order_status')) {
                return;
            }

            app(OrderStatusTrackingService::class)->trackStatusChange(
                $order,
                $order->trackingPreviousOrderStatus
            );
        });
    }

    public function orderdetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }
    public function product()
    {
        return $this->belongsTo(OrderDetails::class, 'id', 'order_id')
            ->select('order_details.id', 'order_details.order_id', 'order_details.product_id');
    }
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status');
    }
    public function shipping()
    {
        return $this->belongsTo(Shipping::class, 'id', 'order_id');
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'id', 'order_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function reseller()
    {
        return $this->belongsTo(Reseller::class,'reseller_id');
    }

    public function getMarketingSourceLabelAttribute(): string
    {
        return match (strtolower((string) $this->marketing_source)) {
            'facebook' => 'Facebook',
            'messenger' => 'Messenger',
            'whatsapp' => 'WhatsApp',
            'tiktok' => 'TikTok',
            'google' => 'Google',
            'organic', 'direct', 'referral' => 'Organic',
            'admin' => 'Admin Panel',
            default => $this->marketing_source ? ucfirst((string) $this->marketing_source) : 'Unknown',
        };
    }

    public function getMarketingSourceBadgeClassAttribute(): string
    {
        return match (strtolower((string) $this->marketing_source)) {
            'facebook' => 'bg-primary',
            'messenger' => 'bg-primary',
            'whatsapp' => 'bg-success',
            'tiktok' => 'bg-dark',
            'google' => 'bg-success',
            'organic', 'direct', 'referral' => 'bg-info text-dark',
            'admin' => 'bg-danger',
            default => 'bg-light text-dark',
        };
    }
}
