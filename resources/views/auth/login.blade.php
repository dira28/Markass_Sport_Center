<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- CSS -->
    @vite('resources/css/auth/login.css')
</head>

<body>

    <div class="login-container">

        <div class="logo">
            <img src="/images/logo-markass.png" alt="Markass Logo">
        </div>

        <h2 class="login-title">Welcome Back</h2>
        <p class="login-subtitle">Sign in to continue</p>

        <!-- ERROR MESSAGE -->
        @if(session('error'))
            <p style="color:red; text-align:center;">
                {{ session('error') }}
            </p>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="form-group">
                <label class="form-label">Email</label>
                <input 
                    type="email" 
                    name="email"
                    class="form-input" 
                    placeholder="Enter your email" 
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input 
                    type="password" 
                    name="password"
                    class="form-input" 
                    placeholder="Enter your password" 
                    required
                >
            </div>

            <button type="submit" class="btn-login">LOGIN</button>
        </form>

        <!-- OR -->
        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">OR</span>
            <div class="divider-line"></div>
        </div>

        <!-- GOOGLE -->
        <button class="btn-google">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg">
            Continue with Google
        </button>

        <div class="login-footer">
            Don't have an account? <a href="/register">Sign up</a>
        </div>

    </div>

</body>

</html>