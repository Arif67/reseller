@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh; background: linear-gradient(to right, #e3f2fd, #ffffff);">
    <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header text-white text-center fw-bold" style="background: linear-gradient(to right, #1976d2, #2196f3); font-size: 1.5rem;">
                {{ __('Login') }}
            </div>

            <div class="card-body bg-light rounded-bottom-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="row mb-4">
                        <label for="email" class="col-md-4 col-form-label text-md-end fw-semibold text-primary">
                            {{ __('Email Address') }}
                        </label>

                        <div class="col-md-6">
                            <input id="email" type="email"
                                class="form-control rounded-pill shadow-sm @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label for="password" class="col-md-4 col-form-label text-md-end fw-semibold text-primary">
                            {{ __('Password') }}
                        </label>

                        <div class="col-md-6">
                            <input id="password" type="password"
                                class="form-control rounded-pill shadow-sm @error('password') is-invalid @enderror"
                                name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit"
                                class="btn btn-primary rounded-pill px-4 py-2 fw-bold"
                                style="background: linear-gradient(to right, #42a5f5, #1e88e5); border: none;">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection