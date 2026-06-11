<style>
    .mobile-menu-scroll {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 12px;
        -webkit-overflow-scrolling: touch;
        margin-top: 24px;
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
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border: 1px solid #1abc9c;
        color: #1abc9c;
        border-radius: 6px;
        margin-right: 10px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        background: #fff;
    }
    .mobile-menu-btn i {
        font-size: 17px;
    }
    .mobile-menu-btn.active {
        background: #1abc9c;
        color: #fff;
    }
    .menu-counter {
        display: inline-block;
        min-width: 22px;
        padding: 1px 7px;
        background: #e91e63;
        color: #fff;
        border-radius: 11px;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        line-height: 18px;
    }
    .mobile-menu-btn.active .menu-counter {
        background: #fff;
        color: #1abc9c;
    }
</style>

<div class="mobile-menu-scroll">
    <a href="{{ route('vendor.orders.index', 'all') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">
        <i class="fe-list"></i> অর্ডার
        <span class="menu-counter" data-counter="orders">{{ $vendorMenuCounts['orders'] ?? 0 }}</span>
    </a>
    <a href="{{ route('vendor.collection') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.collection') ? 'active' : '' }}">
        <i class="fe-shopping-bag"></i> কালেকশন
        <span class="menu-counter" data-counter="collection">{{ $vendorMenuCounts['collection'] ?? 0 }}</span>
    </a>
    <a href="{{ route('vendor.pending_summary') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.pending_summary') ? 'active' : '' }}">
        <i class="fe-refresh-cw"></i> পেন্ডিং সামারি
        <span class="menu-counter" data-counter="pending">{{ $vendorMenuCounts['pending'] ?? 0 }}</span>
    </a>
    <a href="{{ route('vendor.collected') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.collected') ? 'active' : '' }}">
        <i class="fe-check-square"></i> কালেক্টেড
        <span class="menu-counter" data-counter="collected">{{ $vendorMenuCounts['collected'] ?? 0 }}</span>
    </a>
    <a href="{{ route('vendor.returns') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.returns') ? 'active' : '' }}">
        <i class="fe-corner-down-left"></i> রিটার্ন
        <span class="menu-counter" data-counter="returns">{{ $vendorMenuCounts['returns'] ?? 0 }}</span>
    </a>
    <a href="{{ route('vendor.payment') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.payment') ? 'active' : '' }}">
        <i class="fe-credit-card"></i> পেমেন্ট
    </a>
    <a href="{{ route('vendor.products.index') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">
        <i class="fe-folder"></i> প্রোডাক্ট
        <span class="menu-counter" data-counter="products">{{ $vendorMenuCounts['products'] ?? 0 }}</span>
    </a>
    <a href="{{ route('vendor.recent_post') }}" class="mobile-menu-btn {{ request()->routeIs('vendor.recent_post') ? 'active' : '' }}">
        <i class="fe-plus-square"></i> রিসেন্ট পোস্ট
    </a>
</div>
