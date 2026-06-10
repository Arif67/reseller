@extends('resellerPanel.layouts.master')
@section('title', 'Withdraw')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">Withdraw Margin</h4></div>
        </div>
    </div>

    <div class="row">
        {{-- Request form --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-success">
                        <h4 class="mb-0">Available: ৳ {{ number_format($available, 0) }}</h4>
                        <small>Delivered order-er margin</small>
                    </div>

                    @if($available <= 0)
                        <p class="text-muted mb-0">Ekhono withdraw korar moto balance nei. Order delivered hole margin add hobe.</p>
                    @elseif($paymentMethods->isEmpty())
                        <div class="alert alert-warning mb-0">
                            Age <a href="{{ route('reseller.profile') }}">profile</a> theke ekta payment method (bKash/Nagad/Bank) add korun.
                        </div>
                    @else
                    <form action="{{ route('reseller.withdraw.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" max="{{ $available }}" min="1" step="0.01"
                                value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" required>
                            @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method_id" class="form-control @error('payment_method_id') is-invalid @enderror" required>
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}">{{ strtoupper($pm->type) }} — {{ $pm->label }}</option>
                                @endforeach
                            </select>
                            @error('payment_method_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            <small class="text-muted">Profile theke aro method add kora jay.</small>
                        </div>
                        <button type="submit" class="btn btn-success">Request Withdraw</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- History --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Withdraw History</h4>
                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead class="table-light">
                                <tr><th>Date</th><th>Amount</th><th>Method</th><th>Account</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawals as $w)
                                    <tr>
                                        <td>{{ $w->created_at->format('d M Y') }}</td>
                                        <td>৳ {{ number_format($w->amount, 0) }}</td>
                                        <td>{{ ucfirst($w->method) }}</td>
                                        <td>{{ $w->account }}</td>
                                        <td>
                                            @if($w->status=='paid')<span class="badge bg-success">Paid</span>
                                            @elseif($w->status=='approved')<span class="badge bg-info">Approved</span>
                                            @elseif($w->status=='rejected')<span class="badge bg-danger">Rejected</span>
                                            @else<span class="badge bg-warning">Pending</span>@endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">Kono withdraw request nei.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
