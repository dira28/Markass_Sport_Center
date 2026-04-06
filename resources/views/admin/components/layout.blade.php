<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- DATE PICKER --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- VITE --}}
    @vite('resources/css/app.css')
</head>

<body>

    {{-- SIDEBAR --}}
    @include('admin.components.sidebar')

    {{-- MAIN --}}
    <div class="admin-main">

        {{-- NAVBAR --}}
        @include('admin.components.navbar')

        {{-- CONTENT --}}
        <div class="content-wrapper mt-4">
            @yield('content')
        </div>

    </div>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const dateInput = document.querySelector("#dateRange");
            const dateBox = document.querySelector("#dateRangeBox");
            const dateText = document.querySelector("#dateText");

            console.log("JS jalan");

            if (dateInput) {

                flatpickr(dateInput, {
                    mode: "range",
                    dateFormat: "d M Y",
                    onChange: function (selectedDates, dateStr) {
                        if (dateText) {
                            dateText.innerText = dateStr || "Pilih tanggal";
                        }
                    }
                });

                if (dateBox) {
                    dateBox.addEventListener("click", function () {
                        if (dateInput._flatpickr) {
                            dateInput._flatpickr.open();
                        }
                    });
                }
            }

        });
    </script>

</body>

</html>