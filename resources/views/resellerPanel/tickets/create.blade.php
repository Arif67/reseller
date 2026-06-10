@extends('resellerPanel.layouts.master')
@section('title', 'New Ticket')

@section('content')
    <div class="row align-items-center mb-2">
        <div class="col"><div class="page-title-box"><h4 class="page-title">New Support Ticket</h4></div></div>
        <div class="col-auto">
            <a href="{{ route('reseller.tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('reseller.tickets.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Order (kon order niye somossa?)</label>
                            <select name="order_id" class="form-control">
                                <option value="">— General (kono nirdishto order na) —</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}" @selected((string) $selectedOrderId === (string) $order->id)>
                                        #{{ $order->invoice_id }} — {{ $order->created_at->format('d M Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" value="{{ old('subject') }}"
                                class="form-control @error('subject') is-invalid @enderror"
                                placeholder="Somossar choto sironam" required>
                            @error('subject')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror"
                                placeholder="Somossata bistarito likhun..." required>{{ old('message') }}</textarea>
                            @error('message')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-send"></i> Submit Ticket
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
