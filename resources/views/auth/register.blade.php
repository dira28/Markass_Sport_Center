<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>

    @vite('resources/css/auth/login.css')
</head>

<body>

    <div class="login-container">

        <div class="logo">
            <img src="/images/logo-markass.png" alt="Markass Logo">
        </div>

        <h2 class="login-title">Create Account</h2>
        <p class="login-subtitle">Sign up to get started</p>

        <!-- ERROR -->
        @if(session('error'))
            <p style="color:red; text-align:center;">
                {{ session('error') }}
            </p>
        @endif

        <!-- SUCCESS -->
        @if(session('success'))
            <p style="color:green; text-align:center;">
                {{ session('success') }}
            </p>
        @endif

        <form method="POST" action="/register">
            @csrf

            <div class="form-group">
                <label class="form-label">Username</label>
                <input 
                    type="text" 
                    name="nama"
                    class="form-input" 
                    placeholder="Enter your name" 
                    required
                >
            </div>

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
                    placeholder="Create a password" 
                    required
                >
            </div>

            <button type="submit" class="btn-login">REGISTER</button>
        </form>

        <div class="login-footer">
            Already have an account? <a href="/login">Sign in</a>
        </div>

    </div>

</body>

</html>