@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="auth-page">

    {{-- ── Left: Visual panel ──────────────────────── --}}
    <div class="auth-panel-visual">
        <img
            src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=900&q=80"
            alt="Food"
            class="auth-panel-visual-bg"
        >
        <div class="auth-panel-visual-overlay"></div>

        <div class="auth-panel-visual-content">
            <div class="auth-panel-eyebrow">Welcome back</div>
            <h2 class="auth-panel-heading">
                Good food<br>deserves to be<br><em>shared.</em>
            </h2>
            <p class="auth-panel-sub">
                Sign in to access your saved recipes, post new ones,
                and connect with a community of food lovers.
            </p>
            <div class="auth-panel-quote">
                <p>"Cooking is one of the strongest ceremonies for life."</p>
                <cite>— Laura Esquivel</cite>
            </div>
        </div>
    </div>

    {{-- ── Right: Form panel ───────────────────────── --}}
    <div class="auth-panel-form">
        <div class="auth-form-inner">

            <a href="{{ url('/') }}" class="auth-form-logo">Recipe<span>book</span></a>

            <h1 class="auth-form-title">Sign in</h1>
            <p class="auth-form-subtitle">
                Don't have an account?
                <a href="{{ route('register') }}">Create one free</a>
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

            <form action="{{ route('login') }}" method="POST" novalidate>
                @csrf

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
                        autofocus
                    >
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="••••••••"
                        autocomplete="current-password"
                    >
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row-split">
                    <label class="form-check">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="form-check-label">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="auth-btn-submit">
                    Sign in to Recipebook
                </button>
            </form>

            <div class="auth-divider">or</div>

            <div class="auth-register-strip">
                <p>New to Recipebook?</p>
                <a href="{{ route('register') }}">Create account →</a>
            </div>

        </div>
    </div>

</div>
@endsection
