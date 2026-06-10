@extends('landing.master')

@section('title', 'ভেন্ডর লগইন | ' . ($generalsetting?->name ?? 'ShopBase BD'))

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" />
    <style>
        .auth-section {
            background: var(--brand-soft);
            padding: 56px 0;
        }

        .auth-card {
            max-width: 460px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 36px;
            box-shadow: var(--shadow);
        }

        .auth-card h2 {
            text-align: center;
            font-size: 26px;
            margin-bottom: 4px;
        }

        .auth-card .sub {
            text-align: center;
            color: var(--muted);
            margin-bottom: 26px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            font-size: 14.5px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #dfe3e8;
            border-radius: 10px;
            font-family: inherit;
            font-size: 15px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(255, 106, 0, .12);
        }

        .form-control.is-invalid {
            border-color: #e11d48;
        }

        .invalid-feedback {
            color: #e11d48;
            font-size: 13px;
            display: block;
            margin-top: 4px;
        }

        .btn-block {
            width: 100%;
            justify-content: center;
            margin-top: 8px;
        }

        .auth-foot {
            text-align: center;
            color: var(--muted);
            margin-top: 18px;
        }

        .auth-foot a {
            color: var(--brand);
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="auth-card">
                @include('landing.partials._auth_tabs', ['active' => 'vendor', 'type' => 'login'])
                <h2>ভেন্ডর লগইন</h2>
                <p class="sub">আপনার শপ ম্যানেজ করতে লগইন করুন।</p>

                <form method="POST" action="{{ route('vendor.signin') }}">
                    @csrf
                    <div class="form-group">
                        <label for="phone">ফোন</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                            class="form-control @error('phone') is-invalid @enderror" required autofocus
                            placeholder="আপনার ফোন নম্বর">
                        @error('phone')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password">পাসওয়ার্ড</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required
                            placeholder="আপনার পাসওয়ার্ড">
                        @error('password')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                    </div>

                    <button class="btn btn-primary btn-block" type="submit">
                        <i class="fa-solid fa-right-to-bracket"></i> লগইন করুন
                    </button>
                </form>

                <p class="auth-foot">নতুন ভেন্ডর?
                    <a href="{{ route('vendor.register') }}">রেজিস্ট্রেশন করুন</a>
                </p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    {!! Toastr::message() !!}
@endpush
