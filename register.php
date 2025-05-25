<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Guide Easy</title>
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
        .register-card {
            background: rgba(30, 35, 45, 0.92);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            max-width: 500px;
            width: 100%;
            color: #fff;
        }
        .register-card .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid #FF6B4A;
            color: #fff;
        }
        .register-card .form-control:focus {
            border-color: #FF6B4A;
            box-shadow: 0 0 0 0.2rem rgba(255,107,74,0.15);
            background: rgba(255,255,255,0.13);
            color: #fff;
        }
        .register-card .btn-success {
            background: #FF6B4A;
            border: none;
            font-weight: 600;
            letter-spacing: 1px;
            border-radius: 8px;
        }
        .register-card .btn-success:hover {
            background: #ff3c00;
        }
        .register-card .form-check-label, .register-card a {
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
                            <a class="nav-link" href="login.php">Login</a>
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
                <div class="register-card">
                    <h2 class="text-center mb-4" style="color:#FF6B4A;">Create an Account</h2>
                    <form action="process_register.php" method="POST" enctype="multipart/form-data" id="registerForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="tourist" value="tourist" checked>
                                    <label class="form-check-label" for="tourist">Tourist</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="guide" value="guide">
                                    <label class="form-check-label" for="guide">Guide</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" id="photo-upload-group">
                            <label for="profile_photo" class="form-label">Profile Photo</label>
                            <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                        </div>
                        <div class="mb-3 password-container">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-icon-wrapper">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                            </div>
                        </div>
                        <div class="mb-3 password-container">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-icon-wrapper">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>.
                            </label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Register</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="login.php">Login</a></p>
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
    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Terms and Conditions for Tourists</h6>
                    <ul style="padding-left: 1.2em;">
                        <li><strong>Account Registration:</strong><br>
                            Tourists must provide accurate and complete personal details during registration.<br>
                            Uploading a valid photo is mandatory for identity verification and account approval.
                        </li>
                        <li><strong>Use of Services:</strong><br>
                            Tourists may browse, book, and communicate with verified tour guides through this platform.<br>
                            All bookings must be made in good faith. Fraudulent or misleading activity will lead to account suspension.
                        </li>
                        <li><strong>Photo Verification:</strong><br>
                            The uploaded photo will be used solely by the admin team for identity verification.<br>
                            Inappropriate, unclear, or falsified photos will result in rejection of the account.
                        </li>
                        <li><strong>Privacy:</strong><br>
                            All personal data, including your photo, will be securely stored and used only for account verification and booking management.<br>
                            We will never sell, rent, or share your data with third parties without your explicit consent.
                        </li>
                        <li><strong>Admin Approval:</strong><br>
                            All new tourist registrations undergo manual review by the admin.<br>
                            Only after approval will the account become active.<br>
                            You will be notified via email if your account is accepted or rejected.
                        </li>
                        <li><strong>Code of Conduct:</strong><br>
                            Tourists must act respectfully and professionally when communicating with guides.<br>
                            Any form of harassment, abuse, or inappropriate behavior will result in permanent account termination.
                        </li>
                        <li><strong>Platform Limitations:</strong><br>
                            Guide Easy is a platform to connect tourists with guides and does not directly organize or oversee the tours.<br>
                            We are not responsible for disputes, injuries, damages, or any issues arising from your interaction with the guide or during your tour.
                        </li>
                        <li><strong>Booking and Cancellation:</strong><br>
                            Each guide may set their own cancellation and refund policies. Tourists are responsible for reviewing and complying with these terms.<br>
                            Late cancellations or repeated no-shows may lead to a warning or temporary ban.
                        </li>
                        <li><strong>Liability Disclaimer:</strong><br>
                            Guide Easy holds no liability for loss, injury, accidents, theft, or damage that occurs during or as a result of a booked tour.<br>
                            Tourists are advised to take appropriate precautions and use their own judgment when selecting and interacting with guides.
                        </li>
                        <li><strong>Modifications to Terms:</strong><br>
                            We reserve the right to update these Terms and Conditions at any time.<br>
                            Continued use of the platform after changes implies acceptance of the revised terms.
                        </li>
                        <li><strong>Termination of Access:</strong><br>
                            We may suspend or terminate your access without prior notice if you violate these terms or misuse the platform.
                        </li>
                        <li><strong>Acceptance:</strong><br>
                            By selecting the checkbox and submitting the registration form, you acknowledge that you have read, understood, and agree to comply with all terms stated above.
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        // Show/hide photo upload based on role
        function updatePhotoField() {
            const isGuide = document.getElementById('guide').checked;
            const photoGroup = document.getElementById('photo-upload-group');
            const photoInput = document.getElementById('profile_photo');
            if (isGuide) {
                photoGroup.style.display = '';
                photoInput.required = true;
            } else {
                photoGroup.style.display = 'none';
                photoInput.required = false;
            }
        }
        document.getElementById('tourist').addEventListener('change', updatePhotoField);
        document.getElementById('guide').addEventListener('change', updatePhotoField);
        window.onload = updatePhotoField;
        // Prevent form submission unless terms are checked
        const form = document.getElementById('registerForm');
        form.addEventListener('submit', function(e) {
            if (!document.getElementById('terms').checked) {
                e.preventDefault();
                alert('You must agree to the Terms and Conditions.');
            }
        });
    </script>
</body>
</html> 