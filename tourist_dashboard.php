<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a tourist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
    header("Location: login.php");
    exit();
}

// Fetch featured guides from the database
$featured_guides_query = "SELECT u.*, gs.availability_status, gs.specialization, gs.rate_per_hour, gs.bio,
                         (SELECT AVG(rating) FROM reviews WHERE guide_id = u.id) as avg_rating,
                         (SELECT COUNT(*) FROM reviews WHERE guide_id = u.id) as total_reviews
                         FROM users u
                         LEFT JOIN guide_settings gs ON u.id = gs.user_id
                         WHERE u.role = 'guide' AND gs.availability_status = 'available'
                         ORDER BY avg_rating DESC
                         LIMIT 2";
$featured_guides_result = $conn->query($featured_guides_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Dashboard - Guide Easy</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="tourist_dashboard.php">
                <img src="images/logo.png" alt="Guide Easy Logo" height="40" class="d-inline-block align-text-top me-2">
                Guide Easy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="tourist_dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tourist_destinations.php">Destinations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tourist_messages.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tourist_settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Carousel -->
    <div class="hero-section">
        <div id="destinationCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#destinationCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#destinationCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#destinationCarousel" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="main_photo.jpg" class="d-block w-100" alt="Nepal Landscape">
                </div>
                <div class="carousel-item">
                    <img src="images/pokhara.jpg" class="d-block w-100" alt="Pokhara">
                </div>
                <div class="carousel-item">
                    <img src="images/chitwan.jpg" class="d-block w-100" alt="Chitwan">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#destinationCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#destinationCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <!-- Search Box -->
        <div class="search-container">
            <div class="search-box">
                <h3 class="text-center mb-4">Find Your Perfect Guide</h3>
                <form action="search.php" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control search-input" name="location" placeholder="Where do you want to go?">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 

//baki cha 