<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Markass')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- VITE (CSS/TAILWIND) --}}
    @vite('resources/css/app.css')

    {{-- FONT AWESOME (hapus duplikat) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">

    {{-- Navbar --}}
    @include('user.components.navbar')

    {{-- Content --}}
    <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('user.components.footer')

    {{-- JS BOOTSTRAP --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>