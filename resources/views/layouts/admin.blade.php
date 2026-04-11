<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Markas Sport</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- VITE --}}
    @vite('resources/css/app.css')

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    {{-- ICON (WAJIB BUAT KPI) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    const fp = flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "d M Y",
        onChange: function (selectedDates, dateStr) {
            document.getElementById("dateText").innerText = dateStr || "Pilih tanggal";
        }
    });

    document.getElementById("dateRangeBox").addEventListener("click", function () {
        fp.open();
    });
</script>

<body>

    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('admin.components.sidebar')

        {{-- MAIN --}}
        <div class="admin-main flex-grow-1 p-4">

            {{-- NAVBAR --}}
            @include('admin.components.navbar')

            {{-- CONTENT WRAPPER --}}
            <div class="content-wrapper mt-4">
                @yield('content')
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>