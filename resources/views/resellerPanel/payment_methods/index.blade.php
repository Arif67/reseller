@extends('resellerPanel.layouts.master')
@section('title', 'Payment Methods')

@section('content')
    <div class="row align-items-center">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">Payment Methods</h4></div>
        </div>
    </div>

    <div class="row">
        {{-- Saved methods --}}
        <div class="col-lg-7 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">Apnar Payment Method gulo</h4>

                    @forelse($paymentMethods as $pm)
                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                            <div>
                                <span class="badge bg-success text-uppercase">{{ $pm->type }}</span>
                                <span class="ms-1">{{ $pm->label }}</span>
                            </div>
                            <form action="{{ route('reseller.payment_method.delete') }}" method="POST"
                                onsubmit="return confirm('Delete this payment method?')">
                                @csrf<input type="hidden" name="id" value="{{ $pm->id }}">
                                <button class="btn btn-sm btn-outline-danger"><i class="mdi mdi-delete"></i></button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="mdi mdi-credit-card-outline" style="font-size:40px;"></i>
                            <p class="mt-2 mb-0">Ekhono kono payment method nei. Pashe theke add korun.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Add new method --}}
        <div class="col-lg-5 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="header-title mb-3">Notun Payment Method Add korun</h4>

                    <form action="{{ route('reseller.payment_method.add') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Type</label>
                            <select name="type" id="pm-type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        <div class="mb-2 pm-bank-field" style="display:none;">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" id="pm-num-label">Number</label>
                            <input type="text" name="account_number" value="{{ old('account_number') }}"
                                class="form-control @error('account_number') is-invalid @enderror" required>
                            @error('account_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-2 pm-bank-field" style="display:none;">
                            <label class="form-label">Account Holder Name</label>
                            <input type="text" name="account_name" value="{{ old('account_name') }}" class="form-control">
                        </div>
                        <button class="btn btn-success btn-sm mt-1">
                            <i class="mdi mdi-plus"></i> Add Payment Method
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
(function(){
    const type = document.getElementById('pm-type');
    const bankFields = document.querySelectorAll('.pm-bank-field');
    const numLabel = document.getElementById('pm-num-label');
    function toggle(){
        const isBank = type.value === 'bank';
        bankFields.forEach(f => f.style.display = isBank ? 'block' : 'none');
        numLabel.textContent = isBank ? 'Account Number' : 'Number';
    }
    type.addEventListener('change', toggle);
    toggle();
})();
</script>
@endsection
