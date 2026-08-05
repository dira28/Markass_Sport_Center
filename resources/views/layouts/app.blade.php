<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Markass')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- BOOTSTRAP (HARUS DI ATAS) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- VITE (TAILWIND & CUSTOM CSS) --}}
    @vite('resources/css/app.css')

    {{-- CSS KHUSUS PER HALAMAN --}}
    @stack('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Override SweetAlert2 button style conflicts -->
    <style>
        body div.swal2-container div.swal2-actions button.swal2-styled.swal2-confirm,
        body div.swal2-container div.swal2-actions button.swal2-styled.swal2-confirm * {
            background-color: #dc3545 !important;
            color: #ffffff !important;
            border: none !important;
            padding: 10px 24px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            font-size: 15px !important;
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
            box-shadow: 0 2px 6px rgba(220, 53, 69, 0.4) !important;
        }

        body div.swal2-container div.swal2-actions button.swal2-styled.swal2-cancel,
        body div.swal2-container div.swal2-actions button.swal2-styled.swal2-cancel * {
            background-color: #6c757d !important;
            color: #ffffff !important;
            border: none !important;
            padding: 10px 24px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            font-size: 15px !important;
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
        }

        body div.swal2-container div.swal2-actions button.swal2-styled.swal2-confirm:hover {
            background-color: #bb2d3b !important;
            color: #ffffff !important;
        }

        body div.swal2-container div.swal2-actions button.swal2-styled.swal2-cancel:hover {
            background-color: #5c636a !important;
            color: #ffffff !important;
        }
    </style>
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

    <script>
        setInterval(() => {
            fetch('/check-expired')
                .then(res => res.json())
                .then(data => console.log('Expired checked'))
                .catch(err => console.error(err));
        }, 60000); // every 1 minute
    </script>

    {{-- JS BOOTSTRAP --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>