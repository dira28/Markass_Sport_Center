<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Markass Sport Center</title>
    @vite('resources/css/auth/login.css')
</head>

<body>

    <div class="auth-wrapper">

        <!-- SISI KIRI: BANNER MERAH -->
        <div class="banner-side">
            <img src="/images/logo-markass.png" alt="Markass Logo" class="banner-logo">
            <h1 class="banner-title">Welcome Back!</h1>
            <p class="banner-text">To keep connected with us please login with your personal info.</p>
            <a href="/login" class="btn-outline-banner">SIGN IN</a>
        </div>

        <!-- SISI KANAN: FORM REGISTER -->
        <div class="form-side">
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Fill in the details to get started</p>

            <!-- ERROR -->
            @if(session('error'))
                <div class="alert-msg alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="/register" autocomplete="off">
                @csrf

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="nama" class="form-input" placeholder="Enter your name" required
                        autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" placeholder="Enter your email" required
                        autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="register-password" class="form-input"
                            placeholder="Create a password" required autocomplete="new-password">
                        <span class="toggle-password" onclick="togglePassword('register-password')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn-login">REGISTER</button>
            </form>

            <div class="login-footer">
                Already have an account? <a href="/login">Sign in</a>
            </div>
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