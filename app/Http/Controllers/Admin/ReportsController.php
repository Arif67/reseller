<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCartLead;
use App\Models\MarketingEventLog;
use App\Models\Order;
use App\Models\OrderTrackingEvent;
use App\Models\VisitorAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function incompleteOrders()
    {
        $incompleteOrders = collect();

        if (Schema::hasTable('abandoned_cart_leads')) {
            $incompleteOrders = AbandonedCartLead::query()
                ->where('status', 'active')
                ->where('cart_count', '>', 0)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->latest('last_activity_at')
                ->paginate(50);
        }

        return view('backEnd.reports.incomplete_orders', compact('incompleteOrders'));
    }

    public function utmCampaigns()
    {
        $campaigns = collect();
        $recentLeads = collect();

        if (Schema::hasTable('abandoned_cart_leads')) {
            $campaigns = AbandonedCartLead::query()
                ->selectRaw('COALESCE(utm_source, "") as utm_source, COALESCE(utm_medium, "") as utm_medium, COALESCE(utm_campaign, "") as utm_campaign, COUNT(*) as lead_count, SUM(cart_total) as total_value')
                ->groupBy('utm_source', 'utm_medium', 'utm_campaign')
                ->orderByDesc('lead_count')
                ->get();

            $recentLeads = AbandonedCartLead::latest('id')->limit(50)->get();
        }

        return view('backEnd.reports.utm_campaigns', compact('campaigns', 'recentLeads'));
    }

    public function conversionDashboard(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $providerSummary = collect();
        $purchaseSummary = collect();
        $trackingSummary = collect();
        $trackingTrend = collect();
        $recentTrackingEvents = collect();
        $orderMetrics = [
            'total_orders' => 0,
            'total_revenue' => 0,
            'delivered_orders' => 0,
            'cancelled_orders' => 0,
            'delivered_rate' => 0,
            'cancelled_rate' => 0,
        ];

        if (Schema::hasTable('marketing_event_logs')) {
            $marketingQuery = MarketingEventLog::query();

            if ($dateFrom) {
                $marketingQuery->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $marketingQuery->whereDate('created_at', '<=', $dateTo);
            }

            $providerSummary = (clone $marketingQuery)
                ->selectRaw('provider, event_name, COUNT(*) as total_events, SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful_events, SUM(COALESCE(value, 0)) as total_value')
                ->groupBy('provider', 'event_name')
                ->orderBy('provider')
                ->get();

            $purchaseSummary = (clone $marketingQuery)
                ->where('event_name', 'Purchase')
                ->selectRaw('provider, COUNT(*) as purchase_count, SUM(COALESCE(value, 0)) as purchase_value')
                ->groupBy('provider')
                ->get();
        }

        if (Schema::hasTable('orders')) {
            $ordersQuery = Order::query();

            if ($dateFrom) {
                $ordersQuery->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $ordersQuery->whereDate('created_at', '<=', $dateTo);
            }

            $orderMetrics['total_orders'] = (clone $ordersQuery)->count();
            $orderMetrics['total_revenue'] = (float) (clone $ordersQuery)->sum('amount');
        }

        if (Schema::hasTable('order_tracking_events')) {
            $trackingQuery = OrderTrackingEvent::query();

            if ($dateFrom) {
                $trackingQuery->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $trackingQuery->whereDate('created_at', '<=', $dateTo);
            }

            if ($request->input('export') === 'csv') {
                return $this->exportTrackingEventsCsv(clone $trackingQuery, $dateFrom, $dateTo);
            }

            $trackingSummary = (clone $trackingQuery)
                ->whereIn('event_name', ['order_delivered', 'order_cancelled'])
                ->selectRaw('event_name, COUNT(*) as total_events')
                ->groupBy('event_name')
                ->orderBy('event_name')
                ->get();

            $trackingTrend = (clone $trackingQuery)
                ->whereIn('event_name', ['order_delivered', 'order_cancelled'])
                ->selectRaw('DATE(created_at) as event_date')
                ->selectRaw('SUM(CASE WHEN event_name = "order_delivered" THEN 1 ELSE 0 END) as delivered_count')
                ->selectRaw('SUM(CASE WHEN event_name = "order_cancelled" THEN 1 ELSE 0 END) as cancelled_count')
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('event_date')
                ->get();

            $recentTrackingEvents = (clone $trackingQuery)
                ->whereIn('event_name', ['order_status_changed', 'order_delivered', 'order_cancelled'])
                ->with('order:id,invoice_id')
                ->latest('id')
                ->limit(20)
                ->get();

            $orderMetrics['delivered_orders'] = (int) $trackingSummary
                ->firstWhere('event_name', 'order_delivered')
                ?->total_events;
            $orderMetrics['cancelled_orders'] = (int) $trackingSummary
                ->firstWhere('event_name', 'order_cancelled')
                ?->total_events;

            $trackedOutcomes = max($orderMetrics['delivered_orders'] + $orderMetrics['cancelled_orders'], 1);
            $orderMetrics['delivered_rate'] = round(($orderMetrics['delivered_orders'] / $trackedOutcomes) * 100, 2);
            $orderMetrics['cancelled_rate'] = round(($orderMetrics['cancelled_orders'] / $trackedOutcomes) * 100, 2);
        }

        return view('backEnd.reports.conversion_dashboard', compact(
            'providerSummary',
            'purchaseSummary',
            'trackingSummary',
            'trackingTrend',
            'recentTrackingEvents',
            'orderMetrics',
            'dateFrom',
            'dateTo'
        ));
    }

    public function visitorAnalytics(Request $request)
    {
        $districtSummary = collect();
        $recentVisits = collect();
        $selectedDistrict = $request->input('district');
        $totals = [
            'visitors' => 0,
            'page_views' => 0,
            'districts' => 0,
        ];

        if (Schema::hasTable('visitor_analytics')) {
            $query = VisitorAnalytic::query();

            if ($request->filled('date_from')) {
                $query->whereDate('visit_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('visit_date', '<=', $request->date_to);
            }

            $totals['visitors'] = (clone $query)->distinct('session_id')->count('session_id');
            $totals['page_views'] = (int) (clone $query)->sum('view_count');

            $districtSummary = (clone $query)
                ->selectRaw("COALESCE(NULLIF(TRIM(district), ''), 'Unknown') as district_name")
                ->selectRaw('COUNT(DISTINCT session_id) as visitors')
                ->selectRaw('SUM(view_count) as page_views')
                ->groupBy('district_name')
                ->orderByDesc('visitors')
                ->get();

            $totals['districts'] = $districtSummary->where('district_name', '!=', 'Unknown')->count();

            if ($selectedDistrict) {
                $query->where(function ($builder) use ($selectedDistrict) {
                    if ($selectedDistrict === 'Unknown') {
                        $builder->whereNull('district')->orWhere('district', '');
                        return;
                    }

                    $builder->where('district', $selectedDistrict);
                });
            }

            $recentVisits = $query->latest('last_seen_at')->paginate(50)->withQueryString();
        }

        return view('backEnd.reports.visitor_analytics', compact(
            'districtSummary',
            'recentVisits',
            'selectedDistrict',
            'totals'
        ));
    }

    protected function exportTrackingEventsCsv($trackingQuery, ?string $dateFrom, ?string $dateTo): StreamedResponse
    {
        $events = $trackingQuery
            ->whereIn('event_name', ['order_status_changed', 'order_delivered', 'order_cancelled'])
            ->with('order:id,invoice_id')
            ->orderBy('created_at')
            ->get([
                'created_at',
                'invoice_id',
                'event_name',
                'previous_status_name',
                'current_status_name',
                'source',
                'payload',
                'order_id',
            ]);

        $suffix = trim(($dateFrom ?: 'all') . '_to_' . ($dateTo ?: 'all'));
        $filename = 'order-lifecycle-report-' . $suffix . '.csv';

        return response()->streamDownload(function () use ($events) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Time', 'Invoice', 'Event', 'Previous Status', 'Current Status', 'Source', 'Order Amount', 'Customer ID', 'Marketing Source']);

            foreach ($events as $event) {
                $payload = is_array($event->payload) ? $event->payload : [];

                fputcsv($handle, [
                    optional($event->created_at)->format('Y-m-d H:i:s'),
                    $event->invoice_id ?: optional($event->order)->invoice_id ?: '',
                    $event->event_name,
                    $event->previous_status_name,
                    $event->current_status_name,
                    $event->source,
                    $payload['order_amount'] ?? '',
                    $payload['customer_id'] ?? '',
                    $payload['marketing_source'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
