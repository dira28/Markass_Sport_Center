<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Markass')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- BOOTSTRAP (HARUS DI ATAS) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- VITE (TAILWIND & CUSTOM CSS) --}}
    @vite('resources/css/app.css')

    {{-- CSS KHUSUS PER HALAMAN --}}
    @stack('styles')

</head>

<body>

    {{-- Navbar --}}
    @include('user.components.navbar')

    {{-- Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('user.components.footer')

    {{-- JS BOOTSTRAP --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>