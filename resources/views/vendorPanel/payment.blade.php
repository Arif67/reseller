@extends('vendorPanel.layouts.master')
@section('title', 'Payment & Withdraw')

@section('content')
    @include('vendorPanel.layouts.mobile_menu')

    <div class="row">
        <div class="col-md-3 col-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <h5 class="text-muted fw-normal mt-0">Lifetime Earned</h5>
                    <h3 class="mt-2 mb-0">৳ {{ number_format($summary['lifetime'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <h5 class="text-muted fw-normal mt-0">Withdraw Pending</h5>
                    <h3 class="mt-2 mb-0 text-warning">৳ {{ number_format($summary['withdraw_pending'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <h5 class="text-muted fw-normal mt-0">Paid</h5>
                    <h3 class="mt-2 mb-0 text-secondary">৳ {{ number_format($summary['paid'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card widget-flat {{ $summary['available'] < 0 ? 'bg-danger' : 'bg-success' }} text-white">
                <div class="card-body">
                    <h5 class="text-white fw-normal mt-0">Available</h5>
                    <h3 class="mt-2 mb-0 text-white">৳ {{ number_format($summary['available'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            {{-- Saved payout methods --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Payout Methods</h4>

                    @forelse($payoutMethods as $pm)
                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                            <div>
                                <strong>{{ $pm->method }}</strong> — {{ $pm->account }}
                                @if($pm->holder_name)<br><small class="text-muted">{{ $pm->holder_name }}</small>@endif
                                @if($pm->is_default)<span class="badge bg-success ms-1">Default</span>@endif
                            </div>
                            <div class="d-flex gap-1">
                                @unless($pm->is_default)
                                    <form action="{{ route('vendor.payout.default') }}" method="POST">
                                        @csrf<input type="hidden" name="id" value="{{ $pm->id }}">
                                        <button class="btn btn-xs btn-light" title="Make default"><i class="fe-star"></i></button>
                                    </form>
                                @endunless
                                <form action="{{ route('vendor.payout.delete') }}" method="POST" onsubmit="return confirm('Remove this method?')">
                                    @csrf<input type="hidden" name="id" value="{{ $pm->id }}">
                                    <button class="btn btn-xs btn-outline-danger" title="Remove"><i class="fe-trash-2"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No payout method saved yet. Add one below.</p>
                    @endforelse

                    <hr>
                    <h6 class="mb-2">Add a method</h6>
                    <form action="{{ route('vendor.payout.store') }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-5">
                                <select name="method" class="form-control" required>
                                    <option value="">Method</option>
                                    @foreach(['bKash','Nagad','Rocket','Bank'] as $m)
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-7">
                                <input type="text" name="account" class="form-control" placeholder="Account number" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="holder_name" class="form-control" placeholder="Account holder name (optional)">
                            </div>
                            <div class="col-12 form-check ms-1">
                                <input type="checkbox" name="is_default" value="1" class="form-check-input" id="pm_default">
                                <label class="form-check-label" for="pm_default">Set as default</label>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm w-100"><i class="fe-plus"></i> Add Method</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Request withdraw --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Request Withdraw</h4>

                    @if($summary['available'] <= 0)
                        <p class="text-muted">No balance available to withdraw right now.</p>
                    @elseif($payoutMethods->isEmpty())
                        <p class="text-muted">Add a payout method first to request a withdrawal.</p>
                    @else
                        <form action="{{ route('vendor.withdraw.request') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label">Amount (৳)</label>
                                <input type="number" step="0.01" min="1" max="{{ $summary['available'] }}"
                                    name="amount" class="form-control" placeholder="Max {{ number_format($summary['available'], 2) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Send to</label>
                                <select name="payout_method_id" class="form-control" required>
                                    @foreach($payoutMethods as $pm)
                                        <option value="{{ $pm->id }}" @selected($pm->is_default)>{{ $pm->method }} — {{ $pm->account }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-success w-100"><i class="fe-send"></i> Submit Request</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Withdraw History</h4>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th><th>Amount</th><th>Method</th><th>Account</th><th>Status</th><th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdraws as $w)
                                    <tr>
                                        <td>{{ $w->created_at->format('d M, Y') }}</td>
                                        <td><strong>৳ {{ number_format($w->amount, 2) }}</strong></td>
                                        <td>{{ $w->method }}</td>
                                        <td>{{ $w->account }}</td>
                                        <td>
                                            @if($w->status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($w->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $w->admin_note ?? '—' }}</small></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No withdraw requests yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $withdraws->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
