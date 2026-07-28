<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Markass Sport Center</title>
    @vite('resources/css/auth/login.css')
</head>

<body>

    <div class="auth-wrapper">

        <!-- SISI KIRI: FORM LOGIN -->
        <div class="form-side">
            <h2 class="auth-title">Sign In</h2>
            <p class="auth-subtitle">Sign in to access your account</p>

            <!-- ERROR MESSAGE -->
            @if(session('error'))
                <div class="alert-msg alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <!-- SUCCESS MESSAGE -->
            @if(session('success'))
                <div class="alert-msg alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="/login" autocomplete="off">
                @csrf

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" placeholder="Enter your email" required
                        autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="login-password" class="form-input"
                            placeholder="Enter your password" required autocomplete="new-password">
                        <span class="toggle-password" onclick="togglePassword('login-password')">
                            <!-- Icon Eye SVG -->
                            <svg id="eye-login-password" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn-login">SIGN IN</button>
            </form>

            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">OR</span>
                <div class="divider-line"></div>
            </div>

            <!-- GOOGLE -->
            <a href="/auth/google" style="text-decoration: none;">
                <button type="button" class="btn-google">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo">
                    Continue with Google
                </button>
            </a>

            <div class="login-footer">
                Don't have an account? <a href="/register">Sign up</a>
            </div>
        </div>

        <!-- SISI KANAN: BANNER MERAH PERHATIKAN GAMBAR 2 & 3 -->
        <div class="banner-side">
            <img src="/images/logo-markass.png" alt="Markass Logo" class="banner-logo">
            <h1 class="banner-title">Hello, Friend!</h1>
            <p class="banner-text">Sign up now and start booking your favorite sport venues with us.</p>
            <a href="/register" class="btn-outline-banner">SIGN UP</a>
        </div>

    </div>

    <!-- SCRIPT TOGGLE PASSWORD -->
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>
</body>

</html>