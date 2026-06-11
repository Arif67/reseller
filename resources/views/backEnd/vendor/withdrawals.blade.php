@extends('backEnd.layouts.master')
@section('title','Vendor Withdrawals')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-title-box"><h4 class="page-title">Vendor Withdrawals</h4></div>
        </div>
        <div class="col-auto">
            <span class="badge bg-warning p-2">Pending total: ৳ {{ number_format($pendingTotal, 2) }}</span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-2 mb-3" method="GET">
                        <div class="col-sm-3">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                @foreach(['pending','paid','rejected'] as $s)
                                    <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <a href="{{ route('admin.vendor.withdrawals') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-centered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Date</th><th>Vendor / Shop</th><th>Amount</th>
                                    <th>Method</th><th>Account</th><th>Status</th><th style="width:260px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdraws as $w)
                                    <tr>
                                        <td>{{ $w->created_at->format('d M, Y') }}</td>
                                        <td>{{ optional($w->vendor)->shop_name ?? '—' }}<br><small class="text-muted">{{ optional($w->vendor)->phone }}</small></td>
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
                                        <td>
                                            @if($w->status === 'pending')
                                                <form action="{{ route('admin.vendor.withdrawals.process') }}" method="POST" class="d-flex gap-1">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $w->id }}">
                                                    <input type="text" name="note" class="form-control form-control-sm" placeholder="Note (optional)" style="max-width:110px">
                                                    <button name="action" value="paid" class="btn btn-sm btn-success">Paid</button>
                                                    <button name="action" value="rejected" class="btn btn-sm btn-danger" onclick="return confirm('Reject this request?')">Reject</button>
                                                </form>
                                            @else
                                                <small class="text-muted">
                                                    {{ $w->admin_note ?? '—' }}
                                                    @if($w->processed_at)<br>{{ $w->processed_at->format('d M, Y') }}@endif
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">No withdraw requests.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $withdraws->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
