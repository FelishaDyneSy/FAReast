async function sendOTP() {
    const email = document.getElementById("email").value;

    const response = await fetch("http://localhost/concept/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "send_otp", email })
    });

    const result = await response.json();
    document.getElementById("message").innerText = result.message;
}

async function verifyOTP() {
    const email = document.getElementById("email").value;
    const otp = document.getElementById("otp").value;

    const response = await fetch("http://localhost/concept/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "verify_otp", email, otp })
    });

    const result = await response.json();
    document.getElementById("message").innerText = result.message;

    if (result.status === "success") {
        window.location.href = "dashboard.html";
    }
}

async function login() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    const response = await fetch("http://localhost/concept/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "login", email, password })
    });

    const result = await response.json();
    document.getElementById("message").innerText = result.message;

    if (result.status === "success") {
        sendOTP(); // Send OTP after login success
    }
}
