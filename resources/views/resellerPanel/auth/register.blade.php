@extends('landing.master')

@section('title', 'রিসেলার রেজিস্ট্রেশন | ' . ($generalsetting?->name ?? 'ShopBase BD'))

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" />
    <style>
        .auth-section {
            background: var(--brand-soft);
            padding: 56px 0;
        }

        .auth-card {
            max-width: 600px;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
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

        .form-group label small {
            color: var(--muted);
            font-weight: 400;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #dfe3e8;
            border-radius: 10px;
            font-family: inherit;
            font-size: 15px;
            transition: border-color .15s ease;
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

        @media (max-width: 560px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="auth-card">
                @include('landing.partials._auth_tabs', ['active' => 'reseller', 'type' => 'register'])
                <h2>রিসেলার রেজিস্ট্রেশন</h2>
                <p class="sub">অ্যাকাউন্ট তৈরির পর অ্যাডমিন অ্যাপ্রুভাল লাগবে।</p>

                <form method="POST" action="{{ route('reseller.store') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>আপনার নাম</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="form-group">
                            <label>ব্যবসার নাম <small>(ঐচ্ছিক)</small></label>
                            <input type="text" name="business_name" value="{{ old('business_name') }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>ফোন</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="form-control @error('phone') is-invalid @enderror" required>
                            @error('phone')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="form-group">
                            <label>ইমেইল <small>(ঐচ্ছিক)</small></label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>ঠিকানা <small>(ঐচ্ছিক)</small></label>
                        <input type="text" name="address" value="{{ old('address') }}" class="form-control">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>পাসওয়ার্ড</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="form-group">
                            <label>পাসওয়ার্ড নিশ্চিত করুন</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <button class="btn btn-primary btn-block" type="submit">
                        <i class="fa-solid fa-user-plus"></i> রেজিস্ট্রেশন করুন
                    </button>
                </form>

                <p class="auth-foot">আগে থেকেই অ্যাকাউন্ট আছে?
                    <a href="{{ route('reseller.login') }}">লগইন করুন</a>
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
