<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCartLead;
use App\Models\MarketingEventLog;
use App\Models\Order;
use App\Models\VisitorAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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

    public function conversionDashboard()
    {
        $providerSummary = collect();
        $purchaseSummary = collect();
        $orderMetrics = [
            'total_orders' => 0,
            'total_revenue' => 0,
        ];

        if (Schema::hasTable('marketing_event_logs')) {
            $providerSummary = MarketingEventLog::query()
                ->selectRaw('provider, event_name, COUNT(*) as total_events, SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful_events, SUM(COALESCE(value, 0)) as total_value')
                ->groupBy('provider', 'event_name')
                ->orderBy('provider')
                ->get();

            $purchaseSummary = MarketingEventLog::query()
                ->where('event_name', 'Purchase')
                ->selectRaw('provider, COUNT(*) as purchase_count, SUM(COALESCE(value, 0)) as purchase_value')
                ->groupBy('provider')
                ->get();
        }

        if (Schema::hasTable('orders')) {
            $orderMetrics['total_orders'] = Order::count();
            $orderMetrics['total_revenue'] = (float) Order::sum('amount');
        }

        return view('backEnd.reports.conversion_dashboard', compact('providerSummary', 'purchaseSummary', 'orderMetrics'));
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
}
