<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode OTP - Markas Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-900 flex items-center justify-center min-h-screen">
    <div class="bg-slate-800 p-8 rounded-xl shadow-lg w-full max-w-md text-white border border-slate-700">
        <h2 class="text-2xl font-bold text-center mb-2">Verifikasi OTP</h2>
        <p class="text-sm text-gray-400 text-center mb-6">
            Masukkan 6 digit kode OTP yang dikirim ke email: <br>
            <b class="text-red-400">{{ session('otp_email') }}</b>
        </p>

        @if(session('error'))
            <div class="bg-red-500/20 text-red-400 p-3 rounded mb-4 text-center text-sm border border-red-500/50">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('otp.verify') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ session('otp_email') }}">

            <div>
                <input type="text" name="otp" maxlength="6" placeholder="______" required autofocus
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-center text-2xl tracking-[0.5em] font-mono text-white focus:outline-none focus:border-red-500">
            </div>

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition duration-200">
                VERIFIKASI OTP
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white underline">
                Kembali ke Login
            </a>
        </div>
    </div>
</body>

</html>