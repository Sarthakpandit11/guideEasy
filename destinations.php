<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations - Nepal Travel Guide</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <style>
        body {
            background: #e0e3ea !important;
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
        .sidebar-3d {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(60,60,90,0.12), 0 1.5px 4px 0 rgba(60,60,90,0.10);
            padding: 2rem 1.5rem 2rem 1.5rem;
            margin-top: 2.5rem;
            margin-bottom: 2.5rem;
            transition: box-shadow 0.2s;
        }
        .sidebar-3d h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.1rem;
            color: #232323;
        }
        .sidebar-3d ul {
            margin-bottom: 1.7rem;
        }
        .sidebar-3d ul li {
            margin-bottom: 0.7rem;
        }
        .sidebar-3d ul li:last-child {
            margin-bottom: 0;
        }
        .sidebar-3d a {
            color: #444;
            font-weight: 500;
            font-size: 1.08rem;
            text-decoration: none;
            border-radius: 6px;
            padding: 2px 6px;
            transition: background 0.18s, color 0.18s;
        }
        .sidebar-3d a.active, .sidebar-3d a:focus, .sidebar-3d a:hover {
            color: #FF6B4A;
            background: #f7f7fa;
            text-decoration: none;
        }
        .sidebar-3d hr {
            border: none;
            border-top: 2px solid #f0f0f0;
            margin: 0.7rem 0 1.1rem 0;
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
    <!-- END NAVBAR REPLACEMENT -->

    <div class="container mt-5 pt-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <h1 class="mb-4">Popular Destinations in Nepal</h1>
                
                <!-- Kathmandu -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="kathmandu/thamel.png" alt="Kathmandu" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Kathmandu</h2>
                            <p>The capital city of Nepal, Kathmandu is a vibrant metropolis that beautifully blends ancient traditions with modern life. It's home to numerous UNESCO World Heritage Sites, including the famous Pashupatinath Temple and Boudhanath Stupa.</p>
                            <a href="kathmandu.php" class="btn btn-primary">Explore Kathmandu</a>
                        </div>
                    </div>
                </div>

                <!-- Pokhara -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="pokhara/pokhara.png" alt="Pokhara" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Pokhara</h2>
                            <p>Known as the gateway to the Annapurna Circuit, Pokhara is famous for its stunning lakes, breathtaking mountain views, and adventure sports. It's a perfect blend of natural beauty and modern amenities.</p>
                            <a href="pokhara.php" class="btn btn-primary">Explore Pokhara</a>
                        </div>
                    </div>
                </div>

                <!-- Everest Region -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="Everest/EverestRegion.png" alt="Everest Region" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Everest Region</h2>
                            <p>Home to the world's highest peak, Mount Everest, this region offers some of the most spectacular trekking routes in the world. The Everest Base Camp trek is a dream for many adventure enthusiasts.</p>
                            <a href="everest.php" class="btn btn-primary">Explore Everest Region</a>
                        </div>
                    </div>
                </div>

                <!-- Chitwan -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="Everest/chitwan.png" alt="Chitwan" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Chitwan</h2>
                            <p>Chitwan National Park is a UNESCO World Heritage Site known for its rich wildlife, including the endangered one-horned rhinoceros and Bengal tigers. It offers excellent jungle safari experiences.</p>
                            <a href="chitwan.php" class="btn btn-primary">Explore Chitwan</a>
                        </div>
                    </div>
                </div>

                <!-- Lumbini -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="Everest/lumbini.png" alt="Lumbini" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Lumbini</h2>
                            <p>The birthplace of Lord Buddha, Lumbini is a sacred pilgrimage site for Buddhists worldwide. The Maya Devi Temple and numerous monasteries make it a spiritual and cultural hub.</p>
                            <a href="lumbini.php" class="btn btn-primary">Explore Lumbini</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar sidebar-3d">
                    <h3>Quick Links</h3>
                    <ul class="list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#about">About Us</a></li>
                        <li><a href="destinations.php" class="active">Destinations</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                    </ul>
                    <hr>
                    <h3>Popular Activities</h3>
                    <ul class="list-unstyled">
                        <li><a href="#">Trekking</a></li>
                        <li><a href="#">Jungle Safari</a></li>
                        <li><a href="#">Cultural Tours</a></li>
                        <li><a href="#">Adventure Sports</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 