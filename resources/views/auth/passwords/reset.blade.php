@extends('layouts.app')

@section('content')
	<main>		
        <section class="position-relative py-8">
            <div class="container">
                <div class="row justify-content-center align-items-center">
					<div class="col-md-2 text-center mb-5 mb-md-0">
                        <img src="frontend/img/hirecrg-logo.png" alt="Hire CRG" />
                    </div>

                    <div class="col-md-6 col-lg-5 offset-md-1 text-center text-md-start">
                        <h1 class="h3 mb-4">{{ __('Reset Password') }}</h1>
                        <form method="POST" action="{{ route('password.update') }}">
                        @csrf
						<input type="hidden" name="token" value="{{ $token }}">
						<div class="mb-4">
							<!--<label for="email" class="form-control-label">{{ __('Email Address') }}</label>-->
							<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('Email Address') }}" required autocomplete="email" autofocus>

							@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>
						
						<div class="mb-4">
							<!--<label for="email" class="form-control-label">{{ __('Password') }}</label>-->
							<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

							@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>
						
						<div class="mb-4">
							<!--<label for="email" class="form-control-label">{{ __('Confirm Password') }}</label>-->
							<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
						</div>
						<div class="mb-4">
							<input type="password" name="password" id="password" class="form-control form-control-lg text-center" placeholder="Password" />
						</div>
						<div>
							<button type="submit" class="btn btn-primary">
								{{ __('Reset Password') }}
							</button>
						</div>
					</form>
				</div>
	</main>

<?php /*
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
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
*/ ?>

@endsection
