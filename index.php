<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact_submit'])) {
    $name = htmlspecialchars($_POST['contact_name']);
    $email = htmlspecialchars($_POST['contact_email']);
    $topic = htmlspecialchars($_POST['contact_topic']);
    $message = htmlspecialchars($_POST['contact_message']);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'guideeasy1@gmail.com'; // your Gmail
        $mail->Password = 'umkh blec wjbu kgkb';    // your Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('guideeasy1@gmail.com', 'Guide Easy Contact');
        $mail->addAddress('guideeasy1@gmail.com', 'Guide Easy');
        $mail->addReplyTo($email, $name);

        $mail->Subject = 'New Contact Message from Guide Easy';
        $mail->Body    = "Name: $name\nEmail: $email\nTopic: $topic\n\nMessage:\n$message";

        $mail->send();
        $contact_success = "Thank you for contacting us! We'll get back to you soon.";
    } catch (Exception $e) {
        $contact_error = "Sorry, your message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Easy</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
        /* Contact Section Modern Redesign */
        .contact-section {
            background: linear-gradient(120deg, #e0e3ea 0%, #f6f7fb 100%);
            position: relative;
            overflow: hidden;
            padding-top: 70px;
            padding-bottom: 70px;
        }
        .contact-section .section-title {
            font-size: 2.3rem;
            font-weight: 700;
            color: #232323;
            margin-bottom: 0.7rem;
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
        }
        .contact-section .section-title:before {
            content: '\f0e0'; /* envelope icon */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #FF6B4A;
            font-size: 2.1rem;
            margin-right: 0.6rem;
            vertical-align: middle;
            position: relative;
            top: -2px;
        }
        .contact-form {
            background: rgba(255,255,255,0.85);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(60,60,90,0.13), 0 1.5px 4px 0 rgba(60,60,90,0.10);
            padding: 2.5rem 2rem 2rem 2rem;
            backdrop-filter: blur(6px);
            border: 1.5px solid #f0f0f0;
            margin-top: 2.2rem;
            margin-bottom: 2.2rem;
            transition: box-shadow 0.2s;
        }
        .contact-form input.form-control,
        .contact-form textarea.form-control {
            background: #f6f7fb;
            border-radius: 8px;
            border: 1.5px solid #e0e3ea;
            font-size: 1.08rem;
            padding: 0.9rem 1.1rem;
            margin-bottom: 1.1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .contact-form input.form-control:focus,
        .contact-form textarea.form-control:focus {
            border-color: #FF6B4A;
            box-shadow: 0 0 0 2px #ff6b4a22;
            background: #fff;
        }
        .contact-form textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        .contact-form .btn-primary {
            background: linear-gradient(90deg, #FF6B4A 0%, #ff914d 100%);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.7rem 2.2rem;
            box-shadow: 0 2px 8px 0 #ff6b4a33;
            transition: background 0.18s, box-shadow 0.18s, transform 0.12s;
        }
        .contact-form .btn-primary:hover, .contact-form .btn-primary:focus {
            background: linear-gradient(90deg, #ff914d 0%, #FF6B4A 100%);
            box-shadow: 0 4px 16px 0 #ff6b4a44;
            transform: translateY(-2px) scale(1.03);
        }
        .contact-form .alert {
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 1.2rem;
        }
        @media (max-width: 768px) {
            .contact-form {
                padding: 1.2rem 0.7rem;
            }
            .contact-section {
                padding-top: 40px;
                padding-bottom: 40px;
            }
        }
    </style>
</head>
<body>
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
    <!-- Aeroplane Path and Icon -->
    <!-- Remove the SVG path line, only keep the plane icon -->
    <div id="aeroplane-icon" style="position:fixed; left:120px; top:110px; z-index:9; transition: left 0.5s, top 0.5s;">
        <!-- Cartoon-style SVG Aeroplane -->
        <svg width="140" height="80" viewBox="0 0 180 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g>
                <!-- Main body -->
                <path d="M20 70 Q10 60 30 50 Q80 20 160 40 Q175 45 170 60 Q165 80 120 85 Q60 90 20 70 Z" fill="#f5f5dc" stroke="#222" stroke-width="5"/>
                <!-- Blue underside -->
                <path d="M40 80 Q60 90 120 85 Q165 80 170 60 Q170 70 120 80 Q60 85 40 80 Z" fill="#2196f3" stroke="#222" stroke-width="3"/>
                <!-- Top wing -->
                <path d="M60 40 Q80 20 120 35 Q100 45 60 40 Z" fill="#e0e0e0" stroke="#222" stroke-width="4"/>
                <!-- Bottom wing -->
                <path d="M70 80 Q90 95 130 90 Q110 80 70 80 Z" fill="#e0e0e0" stroke="#222" stroke-width="4"/>
                <!-- Tail -->
                <path d="M30 50 Q25 40 40 35 Q45 45 30 50 Z" fill="#e0e0e0" stroke="#222" stroke-width="4"/>
                <!-- Windows -->
                <circle cx="70" cy="60" r="4" fill="#222"/>
                <circle cx="85" cy="58" r="4" fill="#222"/>
                <circle cx="100" cy="57" r="4" fill="#222"/>
                <circle cx="115" cy="58" r="4" fill="#222"/>
                <circle cx="130" cy="60" r="4" fill="#222"/>
                <!-- Nose -->
                <ellipse cx="165" cy="55" rx="7" ry="10" fill="#f5f5dc" stroke="#222" stroke-width="4"/>
            </g>
        </svg>
    </div>

    <!-- Home Section -->
    <section id="home" class="hero-section">
        <img src="main_photo.jpg" alt="Nepal Landscape" class="hero-image">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome to Nepal</h1>
                <p>Discover the beauty of the Himalayas</p>
                <a href="destinations.php" class="btn btn-primary">Explore Destinations</a>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="about-section py-5">
        <div class="container">
            <h2 class="section-title text-center">About Nepal</h2>
            <div class="row">
                <div class="col-lg-6">
                    <p class="section-text">Nepal, nestled in the heart of the Himalayas, is a land of breathtaking landscapes, rich cultural heritage, and warm hospitality. From the majestic peaks of Mount Everest to the ancient temples of Kathmandu, Nepal offers a unique blend of natural beauty and cultural richness.</p>
                    <div class="features">
                        <div class="feature-item">
                            <i class="fas fa-mountain"></i>
                            <span>8 of the world's 10 highest peaks</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-umbrella-beach"></i>
                            <span>Diverse landscapes</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-utensils"></i>
                            <span>Rich cultural heritage</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="map-container">
                        <img src="Everest/map.png" alt="Nepal Map" class="img-fluid map-image">
                        <div class="map-pin" style="top: 35%; left: 20%;" data-destination="Kathmandu">
                            <i class="fas fa-map-marker-alt" style="color: #003366;"></i>
                            <div class="pin-info">Kathmandu</div>
                        </div>
                        <div class="map-pin" style="top: 35%; left: 35%;" data-destination="Pokhara">
                            <i class="fas fa-map-marker-alt" style="color: #003366;"></i>
                            <div class="pin-info">Pokhara</div>
                        </div>
                        <div class="map-pin" style="top: 45%; left: 50%;" data-destination="Everest Base Camp">
                            <i class="fas fa-map-marker-alt" style="color: #003366;"></i>
                            <div class="pin-info">Everest Base Camp</div>
                        </div>
                        <div class="map-pin" style="top: 45%; left: 65%;" data-destination="Chitwan">
                            <i class="fas fa-map-marker-alt" style="color: #003366;"></i>
                            <div class="pin-info">Chitwan</div>
                        </div>
                        <div class="map-pin" style="top: 45%; left: 80%;" data-destination="Lumbini">
                            <i class="fas fa-map-marker-alt" style="color: #003366;"></i>
                            <div class="pin-info">Lumbini</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section py-5">
        <div class="container">
            <h2 class="section-title text-center">Contact Us</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-form">
                        <?php if (isset($contact_success)): ?>
                            <div class="alert alert-success"><?php echo $contact_success; ?></div>
                        <?php elseif (isset($contact_error)): ?>
                            <div class="alert alert-danger"><?php echo $contact_error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="#contact">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="contact_name" placeholder="Your Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="contact_email" placeholder="Your Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="contact_topic" placeholder="Topic" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="contact_message" rows="5" placeholder="Your Message" required></textarea>
                            </div>
                            <button type="submit" name="contact_submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Guide Easy</h5>
                    <p>Your trusted companion for exploring Nepal's wonders.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 Guide Easy. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
    // Aeroplane scroll animation with invisible path and curvy/circular movement, now with smooth and slower movement
    (function() {
        const plane = document.getElementById('aeroplane-icon');
        let current = { x: 120, y: 130 };
        let target = { x: 120, y: 130 };
        let animating = false;

        function getTargetPosition() {
            const scrollY = window.scrollY;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            let progress = docHeight > 0 ? scrollY / docHeight : 0;
            const w = window.innerWidth;
            const h = window.innerHeight;
            // Path geometry
            const startX = 120, startY = 130;
            const loopCX = w / 2, loopCY = h / 2;
            const loopR = Math.min(w, h) * 0.12;
            const endX = w - 80, endY = h - 80;
            const beforeLoopX = w * 0.35, beforeLoopY = h * 0.25;
            const afterLoopX = w * 0.65, afterLoopY = h * 0.75;

            let pos = {x: startX, y: startY};
            if (progress < 0.4) {
                // Start to loop start (quadratic Bezier)
                const t = progress / 0.4;
                const x = (1-t)*(1-t)*startX + 2*(1-t)*t*beforeLoopX + t*t*(loopCX-loopR);
                const y = (1-t)*(1-t)*startY + 2*(1-t)*t*beforeLoopY + t*t*loopCY;
                pos = {x, y};
            } else if (progress < 0.6) {
                // Loop (circle)
                const theta = 2 * Math.PI * ((progress-0.4)/0.2); // 0 to 2PI
                const x = loopCX + loopR * Math.cos(theta - Math.PI/2); // start at left
                const y = loopCY + loopR * Math.sin(theta - Math.PI/2);
                pos = {x, y};
            } else {
                // Loop end to finish (quadratic Bezier)
                const t = (progress-0.6)/0.4;
                const x = (1-t)*(1-t)*(loopCX+loopR) + 2*(1-t)*t*afterLoopX + t*t*endX;
                const y = (1-t)*(1-t)*loopCY + 2*(1-t)*t*afterLoopY + t*t*endY;
                pos = {x, y};
            }
            return pos;
        }

        function animatePlane() {
            // Easing factor (smaller = slower, smoother)
            const ease = 0.08;
            current.x += (target.x - current.x) * ease;
            current.y += (target.y - current.y) * ease;
            plane.style.left = (current.x - 70) + 'px';
            plane.style.top = (current.y - 40) + 'px';
            if (Math.abs(current.x - target.x) > 0.5 || Math.abs(current.y - target.y) > 0.5) {
                requestAnimationFrame(animatePlane);
            } else {
                current.x = target.x;
                current.y = target.y;
                plane.style.left = (current.x - 70) + 'px';
                plane.style.top = (current.y - 40) + 'px';
                animating = false;
            }
        }

        function updateTargetAndAnimate() {
            target = getTargetPosition();
            if (!animating) {
                animating = true;
                animatePlane();
            }
        }

        window.addEventListener('scroll', updateTargetAndAnimate);
        window.addEventListener('resize', updateTargetAndAnimate);
        document.addEventListener('DOMContentLoaded', function() {
            current = getTargetPosition();
            target = current;
            plane.style.left = (current.x - 70) + 'px';
            plane.style.top = (current.y - 40) + 'px';
        });
    })();
    </script>
</body>
</html>
