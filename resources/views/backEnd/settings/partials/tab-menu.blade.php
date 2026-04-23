<div class="card mb-3">
    <div class="card-body py-2">
        <ul class="nav nav-pills flex-wrap gap-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                    General Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('socialmedias.*') ? 'active' : '' }}" href="{{ route('socialmedias.index') }}">
                    Social Media
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">
                    Contact
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pages.*') ? 'active' : '' }}" href="{{ route('pages.index') }}">
                    Create Page
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('shippingcharges.*') ? 'active' : '' }}" href="{{ route('shippingcharges.index') }}">
                    Shipping Charge
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('orderstatus.*') ? 'active' : '' }}" href="{{ route('orderstatus.index') }}">
                    Order Status
                </a>
            </li>
            <li class="nav-item ms-auto">
                <a class="nav-link" href="{{ url('/cc') }}"><i data-feather="refresh-cw"></i> Clear Cache</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/optimize-clear') }}"><i data-feather="refresh-ccw"></i> Optimize Clear</a>
            </li>
        </ul>
    </div>
</div>
