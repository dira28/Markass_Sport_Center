<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @vite('resources/css/app.css')
</head>
<body>

<div class="d-flex">

    {{-- SIDEBAR --}}
    @include('admin.components.sidebar')

    {{-- MAIN CONTENT --}}
    <div class="admin-main flex-grow-1 p-4">

        {{-- NAVBAR --}}
        @include('admin.components.navbar')

        {{-- PAGE CONTENT --}}
        <div class="mt-4">
            {{ $slot }}
        </div>

    </div>

</div>

</body>
</html>