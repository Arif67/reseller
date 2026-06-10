@extends('landing.master')

@section('title', 'যোগাযোগ | ' . ($generalsetting?->name ?? 'ShopBase BD'))

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" />
    <style>
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.3fr;
            gap: 28px;
        }

        .contact-info .info-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 22px;
        }

        .contact-info .info-item .ic {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--brand-soft);
            color: var(--brand);
            display: grid;
            place-items: center;
            font-size: 20px;
            flex: 0 0 auto;
        }

        .contact-info .info-item b {
            display: block;
            margin-bottom: 2px;
        }

        .contact-info .info-item span,
        .contact-info .info-item a {
            color: var(--muted);
        }

        .contact-form {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
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

        @media (max-width: 800px) {

            .contact-grid,
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @include('landing.partials._banner', [
        'crumb' => 'Contact',
        'title' => 'যোগাযোগ করুন',
        'text' => 'যেকোনো প্রশ্ন বা সহযোগিতার জন্য আমাদের সাথে যোগাযোগ করুন।',
    ])

    <section class="section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="info-item">
                        <div class="ic"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <b>হটলাইন</b>
                            <a href="tel:{{ $contact?->hotline ?? '09647300100' }}">{{ $contact?->hotline ?? '09647300100' }}</a>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="ic"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <b>ইমেইল</b>
                            <a href="mailto:{{ $contact?->hotmail ?? 'support@shopbasebd.com' }}">{{ $contact?->hotmail ?? 'support@shopbasebd.com' }}</a>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="ic"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <b>ঠিকানা</b>
                            <span>{{ $contact?->address ?? 'Mohammadpur, Dhaka-1207' }}</span>
                        </div>
                    </div>
                </div>

                <form class="contact-form" method="POST" action="{{ route('landing.contact.submit') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>আপনার নাম</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>ফোন</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="form-control @error('phone') is-invalid @enderror" required>
                            @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label>ইমেইল <small style="color:var(--muted)">(ঐচ্ছিক)</small></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>মেসেজ</label>
                        <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror"
                            required>{{ old('message') }}</textarea>
                        @error('message')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> মেসেজ পাঠান</button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    {!! Toastr::message() !!}
@endpush
