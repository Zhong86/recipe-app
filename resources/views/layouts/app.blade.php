<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'RecipeBook') }} — @yield('title', 'Home')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>

<body>

    <nav class="nav">
        <a href="{{ url('/') }}" class="nav-logo">Recipe<span>book</span></a>

        <ul class="nav-links">
            <li><a href="{{ url('/recipes') }}">Browse</a></li>
            <li><a href="{{ url('/recipes?category=breakfast') }}">Breakfast</a></li>
            <li><a href="{{ url('/recipes?category=dinner') }}">Dinner</a></li>
            <li><a href="{{ url('/recipes?category=dessert') }}">Dessert</a></li>
        </ul>

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

                <form action="{{ route('logout') }}" method="POST">

                    <button class="btn-nav btn-fill">Logout</button>
                </form>
            @endguest
        </div>
    </nav>

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

    @stack('scripts')
</body>

</html>
