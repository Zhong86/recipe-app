<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="/register" method="POST">
        @csrf
        <input name="name" placeholder="New username">

        <input type="email" name="email" placeholder="Enter email" class="@error('password') is-invalid @enderror">
        @error('email')
            <div class='invalid-feedback'>{{ $message }}</div>
        @enderror

        <input name="password" placeholder="Enter password" class="@error('password') is-invalid @enderror">
        @error('password')
            <div class='invalid-feedback'>{{ $message }}</div>
        @enderror
        <button>Register</button>
    </form>
</body>

</html>
