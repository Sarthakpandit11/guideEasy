<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: url('/Guide%20Final%20fyp/guideEasy/Everest/trekking.png') no-repeat center center fixed !important;
            background-size: cover;
            font-family: 'Segoe UI', 'Arial', sans-serif;
        }
        .overlay {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(20, 30, 40, 0.7);
            z-index: 0;
        }
        .main-content {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(30, 35, 45, 0.92);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            max-width: 400px;
            width: 100%;
            color: #fff;
        }
        .login-card .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid #FF6B4A;
            color: #fff;
        }
        .login-card .form-control:focus {
            border-color: #FF6B4A;
            box-shadow: 0 0 0 0.2rem rgba(255,107,74,0.15);
            background: rgba(255,255,255,0.13);
            color: #fff;
        }
        .login-card .btn-success {
            background: #FF6B4A;
            border: none;
            font-weight: 600;
            letter-spacing: 1px;
            border-radius: 8px;
        }
        .login-card .btn-success:hover {
            background: #ff3c00;
        }
        .login-card .form-check-label, .login-card a {
            color: #FF6B4A;
        }
        .headline-section {
            color: #fff;
            margin-left: 60px;
            max-width: 600px;
        }
        .headline-section h1 {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: 2px;
        }
        .headline-section h2 {
            color: #FF6B4A;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .headline-section p {
            font-size: 1.1rem;
            color: #e0e0e0;
        }
        @media (max-width: 991px) {
            .main-content { flex-direction: column; }
            .headline-section { margin-left: 0; margin-top: 40px; text-align: center; }
        }
        .curvy-navbar-wrapper {
            position: relative;
            z-index: 10;
        }
        .curvy-navbar-bg {
            position: absolute;
            left: 0; right: 0; top: 0;
            width: 100%;
            height: 110px;
            pointer-events: none;
        }
        .custom-navbar {
            background: rgba(20, 30, 40, 0.7);
            backdrop-filter: blur(8px);
            border: none;
            box-shadow: none;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .custom-navbar {
            position: relative;
            z-index: 2;
        }
        .custom-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.7rem;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
        }
        .custom-navbar .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }
        .custom-navbar .navbar-brand .site-name {
            color: #fff;
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 1.5px;
        }
        .custom-navbar .navbar-nav .nav-link {
            color: #fff;
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 1.5px;
            margin-left: 1.2rem;
            margin-right: 1.2rem;
            font-size: 1.05rem;
            transition: color 0.2s;
        }
        .custom-navbar .navbar-nav .nav-link.active,
        .custom-navbar .navbar-nav .nav-link:focus,
        .custom-navbar .navbar-nav .nav-link:hover {
            color: #FF6B4A;
        }
        .custom-navbar .navbar-nav .nav-link:last-child {
            margin-right: 0;
        }
        .custom-navbar .navbar-toggler {
            border: none;
        }
        .custom-navbar .navbar-toggler:focus {
            box-shadow: none;
        }
        .input-icon-wrapper {
            position: relative;
        }
        .input-icon-wrapper .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #FF6B4A;
            z-index: 2;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <!-- Curvy Navbar Wrapper -->
    <div class="curvy-navbar-wrapper">
        <nav class="navbar navbar-expand-lg custom-navbar fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="images/logo.png" alt="Guide Easy Logo">
                    <span class="site-name">Guide Easy</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#about">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="destinations.php">Destinations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#contact">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Sign In</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- SVG for curvy bottom -->
        <svg class="curvy-navbar-bg" viewBox="0 0 1440 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,0 H1440 V60 Q1200,110 720,80 Q240,50 0,110 Z" fill="rgba(20,30,40,0.7)"/>
        </svg>
    </div>
    <div class="main-content container-fluid" style="padding-top: 80px;">
        <div class="row w-100 justify-content-center align-items-center">
            <div class="col-lg-5 col-md-8">
                <div class="login-card">
                    <h2 class="text-center mb-4" style="color:#FF6B4A;">Welcome Back</h2>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger text-center">
                            <?php
                            switch ($_GET['error']) {
                                case 'not_approved':
                                    echo 'Your account is pending admin approval. Please wait until you are approved to log in.';
                                    break;
                                case 'invalid_credentials':
                                    echo 'Invalid email or password.';
                                    break;
                                case 'user_not_found':
                                    echo 'No user found with that email address.';
                                    break;
                                default:
                                    echo 'Login error. Please try again.';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <form action="process_login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3 password-container">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-icon-wrapper">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Login</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php">Register</a></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center headline-section">
                <div>
                    <h1>LET'S GO TRAVEL!</h1>
                    <h2 style="color:#FF6B4A;">EXPLORE NEPAL</h2>
                    <p>Nepal, nestled in the heart of the Himalayas, is a land of breathtaking natural beauty and rich cultural heritage. From towering peaks like Everest to serene temples and vibrant festivals, it offers a truly unforgettable experience for every traveler.</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html> 