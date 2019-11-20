@extends('layouts.app')

@section('content')
    <main class="account account-signup">
        <div class="container">
            <h2>{{ __('Reset Password') }}</h2>
            <div class="row justify-content-center">
                <div class="col-md-12 box">

                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="email_addon"><i
                                                        class="fas fa-user"></i></span>
                                            </div>
                                            <input id="email" type="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   name="email"
                                                   value="{{ $email ?? old('email') }}"
                                                   required
                                                   autocomplete="email"
                                                   autofocus
                                                   placeholder="{{ __('E-Mail Address') }}"
                                                   aria-label="{{ __('E-Mail Address') }}"
                                                   aria-describedby="email-addon"
                                            >
                                        </div>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="password_addon"><i
                                                        class="fas fa-lock"></i></span>
                                            </div>
                                            <input id="password" type="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   name="password"
                                                   required
                                                   autocomplete="new-password"
                                                   placeholder="{{ __('Password') }}"
                                                   aria-label="{{ __('Password') }}"
                                                   aria-describedby="password_addon"
                                            >
                                        </div>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="password_confirmation_addon"><i
                                                        class="fas fa-lock"></i></span>
                                            </div>
                                            <input id="password-confirm" type="password"
                                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                                   name="password_confirmation"
                                                   required
                                                   autocomplete="new-password"
                                                   placeholder="{{ __('Confirm Password') }}"
                                                   aria-label="{{ __('Confirm Password') }}"
                                                   aria-describedby="password_confirmation_addon"
                                            >
                                        </div>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Reset Password') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
