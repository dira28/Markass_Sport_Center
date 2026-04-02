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

        <form id="registerForm">

            <div class="form-group">
                <label class="form-label">Userame</label>
                <input type="text" id="name" class="form-input" placeholder="Enter your name" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" id="email" class="form-input" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" id="password" class="form-input" placeholder="Create a password" required>
            </div>

            <button type="submit" class="btn-login">REGISTER</button>

        </form>

        <div class="login-footer">
            Already have an account? <a href="/login">Sign in</a>
        </div>

    </div>

    <!-- API REGISTER -->
    <script>
        document.getElementById("registerForm").addEventListener("submit", async function (e) {
            e.preventDefault();

            const nama = document.getElementById("name").value.trim(); // 👈 ini dia
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();

            if (!nama || !email || !password) {
                alert("Please fill in all fields!");
                return;
            }

            try {
                const response = await fetch("http://localhost:5000/api/auth/register", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        nama: nama,
                        email: email,
                        password: password
                    })
                });

                const result = await response.json();
                console.log(result);

                if (result.success) {
                    alert("Registration successful! Please sign in.");
                    window.location.href = "/login";
                } else {
                    alert(result.message || "Registration failed!");
                }

            } catch (error) {
                alert("Server error! Please try again.");
                console.log(error);
            }
        });
    </script>
</body>

</html>