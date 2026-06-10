<style>
    .mobile-menu-scroll {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 10px;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 15px;
    }
    .mobile-menu-scroll::-webkit-scrollbar {
        height: 4px;
    }
    .mobile-menu-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    .mobile-menu-btn {
        display: inline-block;
        padding: 5px 12px;
        border: 1px solid #1abc9c;
        color: #1abc9c;
        border-radius: 4px;
        margin-right: 8px;
        font-size: 14px;
        text-decoration: none;
        background: #fff;
    }
    .mobile-menu-btn i {
        margin-right: 4px;
    }
    .mobile-menu-btn.active {
        background: #1abc9c;
        color: #fff;
    }
</style>

<div class="mobile-menu-scroll">
    <a href="{{ route('vendor.orders.index', 'all') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">
        <i class="fe-list"></i> অর্ডার
    </a>
    <a href="{{ route('vendor.collection') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.collection') ? 'active' : '' }}">
        <i class="fe-shopping-bag"></i> কালেকশন
    </a>
    <a href="{{ route('vendor.pending_summary') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.pending_summary') ? 'active' : '' }}">
        <i class="fe-refresh-cw"></i> পেন্ডিং সামারি
    </a>
    <a href="{{ route('vendor.collected') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.collected') ? 'active' : '' }}">
        <i class="fe-check-square"></i> কালেক্টেড
    </a>
    <a href="{{ route('vendor.payment') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.payment') ? 'active' : '' }}">
        <i class="fe-credit-card"></i> পেমেন্ট
    </a>
    <a href="{{ route('vendor.products.index') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">
        <i class="fe-folder"></i> প্রোডাক্ট
    </a>
    <a href="{{ route('vendor.recent_post') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.recent_post') ? 'active' : '' }}">
        <i class="fe-plus-square"></i> রিসেন্ট পোস্ট
    </a>
</div>
