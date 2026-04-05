@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="auth-page">

    {{-- ── Left: Visual panel ──────────────────────── --}}
    <div class="auth-panel-visual">
        <img
            src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&q=80"
            alt="Food spread"
            class="auth-panel-visual-bg"
        >
        <div class="auth-panel-visual-overlay"></div>

        <div class="auth-panel-visual-content">
            <div class="auth-panel-eyebrow">Join us today</div>
            <h2 class="auth-panel-heading">
                Your recipes<br>deserve an<br><em>audience.</em>
            </h2>
            <p class="auth-panel-sub">
                Create your free account and start sharing the dishes
                you love with a community that cares about real food.
            </p>

            <ul class="auth-perks">
                <li class="auth-perk">
                    <div class="auth-perk-icon">📌</div>
                    <div class="auth-perk-text">
                        <strong>Save your favourites</strong>
                        <span>Bookmark recipes you love and find them instantly.</span>
                    </div>
                </li>
                <li class="auth-perk">
                    <div class="auth-perk-icon">✍️</div>
                    <div class="auth-perk-text">
                        <strong>Publish your own recipes</strong>
                        <span>Share step-by-step recipes with photos and tags.</span>
                    </div>
                </li>
                <li class="auth-perk">
                    <div class="auth-perk-icon">⭐</div>
                    <div class="auth-perk-text">
                        <strong>Leave reviews & ratings</strong>
                        <span>Help others discover the best dishes.</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    {{-- ── Right: Form panel ───────────────────────── --}}
    <div class="auth-panel-form">
        <div class="auth-form-inner">

            <a href="{{ url('/') }}" class="auth-form-logo">Recipe<span>book</span></a>

            <h1 class="auth-form-title">Create account</h1>
            <p class="auth-form-subtitle">
                Already have an account?
                <a href="{{ route('login') }}">Sign in instead</a>
            </p>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="auth-alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" novalidate>
                @csrf

                {{-- Account info --}}
                <p class="form-section-label">Account details</p>

                <div class="form-group">
                    <label for="name" class="form-label">Display name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                        value="{{ old('name') }}"
                        placeholder="E.g. Chef Marco"
                        autocomplete="name"
                        autofocus
                    >
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        autocomplete="email"
                    >
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <p class="form-section-label" style="margin-top:1.5rem">Password</p>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Minimum 8 characters"
                        autocomplete="new-password"
                    >
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @else
                        <span class="form-hint">Use at least 8 characters with a mix of letters and numbers.</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="Repeat your password"
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="auth-btn-submit" style="margin-top:0.5rem">
                    Create my account
                </button>
            </form>

            <div class="auth-divider">already cooking?</div>

            <div class="auth-login-strip">
                <p>Have an account already?</p>
                <a href="{{ route('login') }}">Sign in →</a>
            </div>

        </div>
    </div>

</div>
@endsection
