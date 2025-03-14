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
    <style>
        .hero-section {
            position: relative;
            height: 100vh;
            overflow: hidden;
            margin-top: -80px; /* Compensate for fixed navbar */
        }
        .carousel {
            height: 100%;
        }
        .carousel-inner {
            height: 100%;
        }
        .carousel-item {
            height: 100%;
        }
        .carousel-item img {
            object-fit: cover;
            height: 100%;
            width: 100%;
            min-height: 100vh;
        }
        .search-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 600px;
            z-index: 10;
        }
        .search-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        .search-input {
            border: none;
            border-bottom: 2px solid #007bff;
            border-radius: 0;
            padding: 10px 0;
            background: transparent;
        }
        .search-input:focus {
            box-shadow: none;
            border-bottom: 2px solid #0056b3;
        }
        .featured-guides {
            background-color: #f8f9fa;
            padding: 4rem 0;
        }
        .guide-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .guide-card:hover {
            transform: translateY(-5px);
        }
        .guide-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .guide-card h4 {
            margin-bottom: 10px;
        }
        .guide-card .rating {
            color: #ffc107;
        }
        .popular-destinations {
            padding: 4rem 0;
        }
        .destination-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .destination-card:hover {
            transform: translateY(-5px);
        }
        .destination-card img {
            height: 200px;
            object-fit: cover;
        }
    </style>
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
                    <img src="images/kathmandu.png" class="d-block w-100" alt="Kathmandu">
                </div>
                <div class="carousel-item">
                    <img src="images/nepal map.png" class="d-block w-100" alt="Nepal Map">
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

    <!-- Featured Guides Section -->
    <section class="featured-guides">
        <div class="container">
            <h2 class="text-center mb-5">Featured Guides</h2>
            <div class="row">
                <?php if ($featured_guides_result->num_rows > 0): ?>
                    <?php while ($guide = $featured_guides_result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="guide-card">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="<?php echo !empty($guide['profile_image']) ? $guide['profile_image'] : 'images/default_guide.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($guide['name']); ?>" class="img-fluid rounded-circle">
                                </div>
                                <div class="col-md-8">
                                    <h4><?php echo htmlspecialchars($guide['name']); ?></h4>
                                    <div class="rating mb-2">
                                        <?php
                                        $avg_rating = round($guide['avg_rating'] ?? 0, 1);
                                        $full_stars = floor($avg_rating);
                                        $half_star = $avg_rating - $full_stars >= 0.5;
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $full_stars) {
                                                echo '<i class="fas fa-star text-warning"></i>';
                                            } elseif ($i == $full_stars + 1 && $half_star) {
                                                echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                            } else {
                                                echo '<i class="far fa-star text-warning"></i>';
                                            }
                                        }
                                        ?>
                                        <span class="ms-2"><?php echo $avg_rating; ?> (<?php echo $guide['total_reviews'] ?? 0; ?> reviews)</span>
                                    </div>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-map-marker-alt"></i> 
                                        <?php echo htmlspecialchars($guide['specialization'] ?? 'Not specified'); ?>
                                    </p>
                                    <p class="mb-3"><?php echo htmlspecialchars($guide['bio'] ?? ''); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-success">$<?php echo number_format($guide['rate_per_hour'] ?? 0, 2); ?>/hour</span>
                                        <a href="book_guide.php?id=<?php echo $guide['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-calendar-check"></i> Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No featured guides available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="all_guides.php" class="btn btn-outline-primary">View All Guides</a>
            </div>
        </div>
    </section>

    <!-- Popular Destinations Section -->
    <section class="popular-destinations">
        <div class="container">
            <h2 class="text-center mb-5">Popular Destinations</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="destination-card">
                        <img src="images/kathmandu.jpg" class="card-img-top" alt="Kathmandu">
                        <div class="card-body">
                            <h5 class="card-title">Kathmandu</h5>
                            <p class="card-text">Explore the cultural heart of Nepal with its ancient temples and vibrant streets.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="destination-card">
                        <img src="images/pokhara.jpg" class="card-img-top" alt="Pokhara">
                        <div class="card-body">
                            <h5 class="card-title">Pokhara</h5>
                            <p class="card-text">Discover the beauty of lakes and mountains in Nepal's adventure capital.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="destination-card">
                        <img src="images/everest.jpg" class="card-img-top" alt="Everest Region">
                        <div class="card-body">
                            <h5 class="card-title">Everest Region</h5>
                            <p class="card-text">Experience the majesty of the world's highest peak and its surrounding beauty.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
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
</body>
</html> 