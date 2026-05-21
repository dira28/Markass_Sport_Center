window.toggleLoginPassword = function () {
    const password = document.getElementById("loginPassword");
    const eye = document.getElementById("loginEye");

    if (password.type === "password") {
        password.type = "text";

        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");

    } else {
        password.type = "password";

        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
    }
}

window.toggleRegisterPassword = function () {
    const password = document.getElementById("registerPassword");
    const eye = document.getElementById("registerEye");

    if (password.type === "password") {
        password.type = "text";

        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");

    } else {
        password.type = "password";

        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
    }
}