@extends('backEnd.layouts.master')
@section('title','Reseller Withdrawals')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center">
        <div class="col"><div class="page-title-box"><h4 class="page-title">Reseller Withdraw Requests</h4></div></div>
        <div class="col-auto pt-2"><a href="{{ route('admin.resellers.index') }}" class="btn btn-light btn-sm">← Resellers</a></div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-2 mb-3" method="GET">
                        <div class="col-sm-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach(['pending','approved','paid','rejected'] as $s)
                                    <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-info">Filter</button>
                            <a href="{{ route('admin.resellers.withdrawals') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-centered">
                            <thead>
                                <tr><th>Date</th><th>Reseller</th><th>Amount</th><th>Method</th><th>Account</th><th>Status</th><th style="width:260px">Action</th></tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawals as $w)
                                    <tr>
                                        <td>{{ $w->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($w->reseller)
                                                <a href="{{ route('admin.resellers.show', $w->reseller->id) }}">{{ $w->reseller->name }}</a>
                                                <br><small class="text-muted">{{ $w->reseller->phone }}</small>
                                            @else — @endif
                                        </td>
                                        <td class="fw-bold">৳ {{ number_format($w->amount,0) }}</td>
                                        <td>{{ ucfirst($w->method) }}</td>
                                        <td>{{ $w->account }}</td>
                                        <td>
                                            @if($w->status=='paid')<span class="badge bg-success">Paid</span>
                                            @elseif($w->status=='approved')<span class="badge bg-info">Approved</span>
                                            @elseif($w->status=='rejected')<span class="badge bg-danger">Rejected</span>
                                            @else<span class="badge bg-warning">Pending</span>@endif
                                        </td>
                                        <td>
                                            @if($w->status != 'paid' && $w->status != 'rejected')
                                                @if($w->status == 'pending')
                                                    <form action="{{ route('admin.resellers.withdraw_status') }}" method="POST" class="d-inline">
                                                        @csrf<input type="hidden" name="hidden_id" value="{{ $w->id }}"><input type="hidden" name="status" value="approved">
                                                        <button class="btn btn-sm btn-info">Approve</button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.resellers.withdraw_status') }}" method="POST" class="d-inline">
                                                    @csrf<input type="hidden" name="hidden_id" value="{{ $w->id }}"><input type="hidden" name="status" value="paid">
                                                    <button class="btn btn-sm btn-success">Mark Paid</button>
                                                </form>
                                                <form action="{{ route('admin.resellers.withdraw_status') }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Reject this request?')">
                                                    @csrf<input type="hidden" name="hidden_id" value="{{ $w->id }}"><input type="hidden" name="status" value="rejected">
                                                    <button class="btn btn-sm btn-danger">Reject</button>
                                                </form>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Kono withdraw request nei.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $withdrawals->withQueryString()->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
