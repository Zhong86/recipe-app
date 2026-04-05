<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    @auth
        <h1>HELO</h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @else
        <h1>HELLOO</h1>
        <a href="{{ route('register') }}">Register</a>
        <a href="{{ route('login') }}">Login</a>

    @endauth

    @foreach ($recipes as $recipe)
        <div>
            <h2>{{ $recipe->title }}</h2>
            <p>{{ $recipe->description }}</p>
            <span>{{ $recipe->category }}</span>
            <span>{{ $recipe->cook_time }}</span>
            <span>{{ $recipe->serving }}</span>
            <p>By {{ $recipe->user->name }}</p>
        </div>
    @endforeach
</body>

</html>
