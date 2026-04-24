<?php

namespace App\Services\OrderTracking;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderTrackingEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderStatusTrackingService
{
    public function trackPlacement(Order $order, ?string $source = null): void
    {
        $this->storeEvent($order, [
            'event_name' => 'order_placed',
            'previous_status_id' => null,
            'current_status_id' => $order->order_status,
            'source' => $source ?: $this->detectSource(),
        ]);
    }

    public function trackStatusChange(Order $order, mixed $previousStatusId = null, ?string $source = null): void
    {
        $currentStatusId = $order->order_status;

        if ((string) $previousStatusId === (string) $currentStatusId) {
            return;
        }

        $payload = [
            'event_name' => 'order_status_changed',
            'previous_status_id' => $previousStatusId,
            'current_status_id' => $currentStatusId,
            'source' => $source ?: $this->detectSource(),
        ];

        $this->storeEvent($order, $payload);

        $lifecycleEvent = $this->resolveLifecycleEvent($currentStatusId);

        if ($lifecycleEvent) {
            $payload['event_name'] = $lifecycleEvent;
            $this->storeEvent($order, $payload);
        }
    }

    protected function storeEvent(Order $order, array $data): void
    {
        if (! Schema::hasTable('order_tracking_events')) {
            Log::info('Order tracking event skipped because table is missing.', [
                'order_id' => $order->id,
                'event_name' => $data['event_name'] ?? 'unknown',
            ]);

            return;
        }

        $previousStatus = $this->resolveStatusMeta($data['previous_status_id'] ?? null);
        $currentStatus = $this->resolveStatusMeta($data['current_status_id'] ?? null);

        OrderTrackingEvent::create([
            'order_id' => $order->id,
            'invoice_id' => $order->invoice_id,
            'event_name' => $data['event_name'],
            'previous_status_id' => $previousStatus['id'],
            'previous_status_name' => $previousStatus['name'],
            'current_status_id' => $currentStatus['id'],
            'current_status_name' => $currentStatus['name'],
            'source' => $data['source'] ?? $this->detectSource(),
            'payload' => [
                'order_amount' => (float) ($order->amount ?? 0),
                'customer_id' => $order->customer_id,
                'marketing_source' => $order->marketing_source,
                'status_slug' => $currentStatus['slug'],
            ],
        ]);
    }

    protected function resolveLifecycleEvent(mixed $statusId): ?string
    {
        $meta = $this->resolveStatusMeta($statusId);
        $name = strtolower(trim(($meta['slug'] ?: $meta['name']) ?? ''));

        if ($name === '') {
            return null;
        }

        if (in_array($name, ['delivered', 'deliveryed', 'complete', 'completed'], true)) {
            return 'order_delivered';
        }

        if (in_array($name, ['cancelled', 'canceled', 'returned'], true)) {
            return 'order_cancelled';
        }

        return null;
    }

    protected function resolveStatusMeta(mixed $statusId): array
    {
        if (empty($statusId) || ! Schema::hasTable('order_statuses')) {
            return ['id' => $statusId ?: null, 'name' => null, 'slug' => null];
        }

        $status = OrderStatus::query()->select('id', 'name', 'slug')->find($statusId);

        return [
            'id' => $status?->id ?: ($statusId ?: null),
            'name' => $status?->name,
            'slug' => $status?->slug,
        ];
    }

    protected function detectSource(): string
    {
        if (app()->runningInConsole()) {
            return 'console';
        }

        $routeName = request()->route()?->getName();

        return $routeName ?: 'app';
    }
}
