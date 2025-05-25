<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kathmandu - Guide Easy</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
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
    </style>
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
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#about">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="destinations.php">Destinations</a>
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

    <div class="container mt-5 pt-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <h1 class="mb-4">Kathmandu</h1>
                
                <!-- Hero Image -->
                <div class="destination-hero mb-4">
                    <img src="kathmandu/kathmandumain.png" alt="Kathmandu Main" class="img-fluid rounded">
                </div>

                <!-- Overview -->
                <div class="destination-section mb-5">
                    <h2>Overview</h2>
                    <p>Kathmandu, the capital city of Nepal, is a vibrant metropolis that beautifully blends ancient traditions with modern life. It's home to numerous UNESCO World Heritage Sites, including the famous Pashupatinath Temple and Boudhanath Stupa. The city offers a unique mix of cultural heritage, religious significance, and urban development.</p>
                </div>

                <!-- Activities -->
                <div class="destination-section mb-5">
                    <h2>Things to Do</h2>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="kathmandu/ktmdurbarsquare.png" alt="Kathmandu Durbar Square" class="img-fluid rounded">
                                <h3>Visit Kathmandu Durbar Square</h3>
                                <p>Explore the ancient royal palace complex with its intricate wood carvings and traditional architecture.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="kathmandu/Boudhanath Stupa.png" alt="Boudhanath Stupa" class="img-fluid rounded">
                                <h3>Boudhanath Stupa</h3>
                                <p>Experience the spiritual atmosphere of one of the largest stupas in the world, surrounded by monasteries and prayer wheels.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="kathmandu/pashu.png" alt="Pashupatinath Temple" class="img-fluid rounded">
                                <h3>Pashupatinath Temple</h3>
                                <p>Witness Hindu rituals and ceremonies at this sacred temple complex on the banks of the Bagmati River.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="kathmandu/thamel.png" alt="Thamel" class="img-fluid rounded">
                                <h3>Explore Thamel</h3>
                                <p>Shop for souvenirs, enjoy local cuisine, and experience the vibrant nightlife in Kathmandu's tourist hub.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Travel Tips -->
                <div class="destination-section mb-5">
                    <h2>Travel Tips</h2>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Best time to visit: September to November and March to May</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Currency: Nepalese Rupee (NPR)</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Language: Nepali, but English is widely spoken</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Transportation: Taxis, rickshaws, and local buses available</li>
                    </ul>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar">
                    <h3>Quick Links</h3>
                    <ul class="list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#about">About Us</a></li>
                        <li><a href="destinations.php">Destinations</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                    </ul>

                    <h3>Nearby Destinations</h3>
                    <ul class="list-unstyled">
                        <li><a href="#">Bhaktapur</a></li>
                        <li><a href="#">Patan</a></li>
                        <li><a href="#">Nagarkot</a></li>
                        <li><a href="#">Dhulikhel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 