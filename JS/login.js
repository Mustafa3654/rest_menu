document.addEventListener("DOMContentLoaded", () => {
    const errorBox = document.getElementById("login-error-box");
    if (errorBox) {
        setTimeout(function() {
            window.location.assign("login");
        }, 3000);
    }
});
