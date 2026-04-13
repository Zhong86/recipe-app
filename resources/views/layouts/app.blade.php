<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RecipeBook') }} — @yield('title', 'Home')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* ── Hamburger button ─────────────────────── */
        .nav-hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            width: 40px;
            height: 40px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 2px;
            cursor: pointer;
            padding: 0;
            flex-shrink: 0;
            transition: border-color .2s;
        }

        .nav-hamburger:hover {
            border-color: var(--terra-light);
        }

        .nav-hamburger span {
            display: block;
            width: 18px;
            height: 1.5px;
            background: var(--cream);
            border-radius: 2px;
            transition: transform .3s ease, opacity .2s ease, width .3s ease;
            transform-origin: center;
        }

        /* Animated X state */
        .nav-hamburger.is-open span:nth-child(1) {
            transform: translateY(6.5px) rotate(45deg);
        }

        .nav-hamburger.is-open span:nth-child(2) {
            opacity: 0;
            width: 0;
        }

        .nav-hamburger.is-open span:nth-child(3) {
            transform: translateY(-6.5px) rotate(-45deg);
        }

        /* ── Mobile dropdown ──────────────────────── */
        .nav-mobile {
            display: none;
            /* shown via JS class */
            position: absolute;
            top: 64px;
            left: 0;
            right: 0;
            background: var(--espresso);
            border-top: 1px solid rgba(255, 255, 255, .08);
            z-index: 99;
            overflow: hidden;
            /* slide-down animation */
            max-height: 0;
            transition: max-height .35s cubic-bezier(.4, 0, .2, 1), opacity .25s ease;
            opacity: 0;
        }

        .nav-mobile.is-open {
            max-height: 480px;
            opacity: 1;
        }

        .nav-mobile-inner {
            padding: 1rem 1.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* Section label */
        .nav-mobile-label {
            font-family: 'DM Mono', monospace;
            font-size: .62rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(237, 227, 212, .28);
            padding: .85rem 0 .4rem;
            margin-top: .5rem;
            border-top: 1px solid rgba(255, 255, 255, .06);
        }

        .nav-mobile-label:first-child {
            margin-top: 0;
            border-top: none;
        }

        .nav-mobile a {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .7rem 0;
            color: rgba(237, 227, 212, .75);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 400;
            border-bottom: 1px solid rgba(255, 255, 255, .04);
            transition: color .15s, padding-left .15s;
        }

        .nav-mobile a:last-child {
            border-bottom: none;
        }

        .nav-mobile a:hover {
            color: var(--cream);
            padding-left: .35rem;
        }

        .nav-mobile a .link-icon {
            font-size: 1rem;
            width: 1.1rem;
            text-align: center;
        }

        /* Mobile CTA buttons */
        .nav-mobile-actions {
            display: flex;
            gap: .75rem;
            padding-top: 1.1rem;
            margin-top: .5rem;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        .nav-mobile-actions .btn-nav {
            flex: 1;
            text-align: center;
            padding: .65rem 1rem;
            font-size: .78rem;
        }

        /* ── Show hamburger on small screens ─────── */
        @media (max-width: 768px) {
            .nav-links {
                display: none !important;
            }

            .nav-actions {
                display: none !important;
            }

            .nav-hamburger {
                display: flex;
            }

            .nav-mobile {
                display: block;
            }

            .nav {
                position: relative;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <nav class="nav">
        <a href="{{ url('/') }}" class="nav-logo">Recipe<span>book</span></a>

        {{-- Desktop links --}}
        <ul class="nav-links">
            <li><a href="{{ url('/recipes') }}">Browse</a></li>
            <li><a href="{{ url('/recipes?category=breakfast') }}">Breakfast</a></li>
            <li><a href="{{ url('/recipes?category=dinner') }}">Dinner</a></li>
            <li><a href="{{ url('/recipes?category=dessert') }}">Dessert</a></li>
        </ul>

        {{-- Desktop auth actions --}}
        <div class="nav-actions">
            @guest
                <a href="{{ route('login') }}" class="btn-nav btn-outline">Sign in</a>
                <a href="{{ route('register') }}" class="btn-nav btn-fill">Join</a>
            @else
                @if (request()->routeIs('recipes/index'))
                    <a href="{{ url('/my-recipes') }}" class="btn-nav btn-outline">My Recipes</a>
                @elseif(request()->routeIs('recipes/user'))
                    <a href="{{ url('/recipes') }}" class="btn-nav btn-outline">All Recipes</a>
                @endif
                <a href="{{ url('/recipes/create') }}" class="btn-nav btn-fill">+ New Recipe</a>
                <a href="{{ url('/profile') }}" class="btn-nav btn-outline">Profile</a>

            @endguest
        </div>

        {{-- Hamburger (mobile only) --}}
        <button class="nav-hamburger" id="navHamburger" aria-label="Toggle menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    {{-- Mobile dropdown --}}
    <div class="nav-mobile" id="navMobile" aria-hidden="true">
        <div class="nav-mobile-inner">

            <div class="nav-mobile-label">Browse</div>
            <a href="{{ url('/recipes') }}">
                <span class="link-icon">🍽️</span> All Recipes
            </a>

            @auth
                <a href="{{ url('/my-recipes') }}">
                    <span class="link-icon">📖</span> My Recipes
                </a>
                <a href="{{ url('/recipes/create') }}">
                    <span class="link-icon">✏️</span> New Recipe
                </a>
                <a href="{{ url('/profile') }}">
                    <span class="link-icon">👤</span> Profile
                </a>
            @else
                <div class="nav-mobile-actions" style="width:75%">
                    <a href="{{ route('login') }}" class="btn-nav btn-outline">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-nav btn-fill">Join free</a>
                </div>
            @endguest
        </div>
    </div>

    <main>
        <div class="page">
            @if (session('success'))
                <div class="flash flash-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif
        </div>

        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-logo">Recipe<span>book</span></div>
        <p class="footer-tagline">Recipes worth sharing</p>
        <ul class="footer-links">
            <li><a href="#">Browse</a></li>
            <li><a href="#">Submit Recipe</a></li>
            <li><a href="#">About</a></li>
        </ul>
        <p class="footer-copy">© {{ date('Y') }} Recipebook. All rights reserved.</p>
    </footer>

    <script>
        const hamburger = document.getElementById('navHamburger');
        const mobileNav = document.getElementById('navMobile');

        hamburger.addEventListener('click', () => {
            const isOpen = hamburger.classList.toggle('is-open');
            mobileNav.classList.toggle('is-open', isOpen);
            hamburger.setAttribute('aria-expanded', isOpen);
            mobileNav.setAttribute('aria-hidden', !isOpen);
        });

        // Close menu when any mobile nav link is tapped
        mobileNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('is-open');
                mobileNav.classList.remove('is-open');
                hamburger.setAttribute('aria-expanded', 'false');
                mobileNav.setAttribute('aria-hidden', 'true');
            });
        });

        // Close on outside tap
        document.addEventListener('click', e => {
            if (!hamburger.contains(e.target) && !mobileNav.contains(e.target)) {
                hamburger.classList.remove('is-open');
                mobileNav.classList.remove('is-open');
                hamburger.setAttribute('aria-expanded', 'false');
                mobileNav.setAttribute('aria-hidden', 'true');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
