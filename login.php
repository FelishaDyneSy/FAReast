<!doctype html>
<html lang="en">
 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/css/style.css">
    <link rel="stylesheet" href="assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="assets/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="assets/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet" href="assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
    html, body {
        height: 100%;
    }

    body {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
        background-color: #2E5077;
    }

    .splash-container {
        width: 100%;
        max-width: 900px; /* More space for two columns */
        padding: 20px;
    }

    .card {
        display: flex;
        flex-direction: row; /* Make it a row layout */
        padding: 0;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .video-container {
        flex: 1;
        background-color: #000;
    }

    .video-container video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .form-container {
        flex: 1;
        padding: 30px;
        background: #fff;
    }

    .logo-img {
        max-width: 120px;
        margin: 0 auto 20px;
        display: block;
    }

    .form-group, .input-group {
        margin-bottom: 20px;
    }

    .input-group-text {
        cursor: pointer;
    }

    button {
        margin-top: 10px;
    }

    .card-footer {
        text-align: center;
        padding-top: 10px;
    }

    @media (max-width: 768px) {
        .card {
            flex-direction: column; /* Stack for mobile */
        }

        .video-container {
            height: 200px;
        }
    }
</style>


</head>

<body>
    <!-- ============================================================== -->
    <!-- login page  -->
    <!-- ============================================================== -->
  

    <div class="splash-container">
    <div class="card">
        <div class="video-container">
            <video src="./assets/images/vector.mp4" loop autoplay muted></video>
        </div>

        <div class="form-container">
            <div class="card-header text-center">
                <a href="../index.html">
                    <img class="logo-img" src="./assets/images/elogo.jpg" alt="logo">
                </a>
            </div>

            <div id="error-message" class="alert alert-danger d-none"></div>

            <div class="card-body">
                <form id="loginForm">
                    <div class="form-group">
                        <input class="form-control form-control-lg" id="username" type="text" placeholder="Username" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <input class="form-control form-control-lg" id="password" type="password" placeholder="Password">
                            <div class="input-group-append">
                                <span class="input-group-text" id="togglePassword">
                                    <i class="fa fa-eye-slash"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
                </form>
            </div>

            <div class="card-footer">
                <a href="#" class="footer-link">Forgot Password</a>
            </div>
        </div>
    </div>
</div>

  
    <!-- ============================================================== -->
    <!-- end login page  -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <!-- <script src="../assets/vendor/jquery/jquery-3.3.1.min.js"></script> -->
    <!-- <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.js"></script> -->
     <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
    
      // Toastify function
         function showToast(message, type) {
            Toastify({
                text: message,
                style: {
                    background: type === 'success' 
                        ? "linear-gradient(to right, #00b09b, #96c93d)" 
                        : "linear-gradient(to right, #ff5f6d, #ffc371)"
                },
                duration: 3000,
                close: true
            }).showToast();
        }

        document.querySelector("form").addEventListener("submit", async (e) => {
    e.preventDefault();

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    let response = await fetch("http://localhost/FAReast-cafe/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include", // Keeps session data
        body: JSON.stringify({ name: username, password: password })
    });

    let data = await response.json();
    console.log(data);

    if (data.success) {
        showToast("Login successful! Redirecting...", "success");
        localStorage.setItem("user_id", data.id);
        setTimeout(() => {
            window.location.href = data.redirect;
        }, 1500);
    } else {
        showToast(data.error, "error");
    }
});



document.getElementById("togglePassword").addEventListener("click", function () {
    let passwordField = document.getElementById("password");
    let icon = this.querySelector("i");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        passwordField.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
});


    </script>
    

  
        
</body>

 
</html>