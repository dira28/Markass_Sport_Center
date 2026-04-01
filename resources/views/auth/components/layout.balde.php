    ni layout
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Gunakan variabel title jika ada, jika tidak tampilkan default --}}
    <title>{{ $title ?? 'Halaman Utama' }}</title>
    
    {{-- Font dan Tailwind --}}
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    
    {{-- Tambahkan file CSS dari Vite --}}
    @vite('resources/css/app.css')
</head>

<body class="h-full">
    <div class="min-h-full bg-gray-100">

        {{-- Navbar --}}
        <x-navbar></x-navbar>

        {{-- Header --}}
        <x-header>{{ $title ?? 'Profile' }}</x-header>

        {{-- Konten utama halaman --}}
        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>