<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get all available guides
$guides_query = "SELECT 
                    u.*,
                    gs.availability_status,
                    gs.location,
                    COALESCE(gs.rate_per_hour, 0) as rate_per_hour,
                    COALESCE(gs.bio, 'No bio available') as bio,
                    COALESCE(gs.specialization, 'General Guide') as specialization,
                    COALESCE((SELECT AVG(rating) FROM reviews WHERE guide_id = u.id), 0) as avg_rating,
                    COALESCE((SELECT COUNT(*) FROM reviews WHERE guide_id = u.id), 0) as total_reviews
                FROM users u
                LEFT JOIN guide_settings gs ON u.id = gs.user_id
                WHERE u.role = 'guide' 
                AND gs.availability_status = 'available'
                ORDER BY avg_rating DESC, total_reviews DESC";

$guides_result = $conn->query($guides_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Available Guides - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .guide-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #eee;
            margin-bottom: 20px;
        }
        .guide-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        .guide-card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        .guide-name {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.25rem;
        }
        .location {
            color: #666;
            font-size: 0.95rem;
        }
        .bio {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .rating {
            margin: 10px 0;
        }
        .rate-badge {
            font-size: 1.1rem;
            padding: 8px 12px;
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
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container" style="margin-top: 80px;">
        <h2 class="mb-4">All Available Guides</h2>
        <p class="text-muted mb-4">Browse through our list of professional guides and find the perfect match for your journey.</p>

        <div class="row">
            <?php if ($guides_result->num_rows > 0): ?>
                <?php while ($guide = $guides_result->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="guide-card">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="images/default_guide.jpg" 
                                         alt="<?php echo htmlspecialchars($guide['name']); ?>" class="img-fluid mb-2">
                                    <div class="rating">
                                        <?php
                                        $avg_rating = round($guide['avg_rating'], 1);
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
                                        <div class="text-muted small mt-1">
                                            <?php echo $avg_rating; ?> (<?php echo $guide['total_reviews']; ?> reviews)
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h4 class="guide-name"><?php echo htmlspecialchars($guide['name']); ?></h4>
                                    <p class="location mb-2">
                                        <i class="fas fa-map-marker-alt text-danger"></i> 
                                        <?php echo htmlspecialchars($guide['location'] ?? 'Location not specified'); ?>
                                    </p>
                                    <div class="rate mb-3">
                                        <span class="badge bg-success rate-badge">
                                            <i class="fas fa-dollar-sign"></i> 
                                            <?php echo number_format($guide['rate_per_hour'], 2); ?>/hour
                                        </span>
                                    </div>
                                    <div class="specialization mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-certificate"></i> 
                                            <?php echo htmlspecialchars($guide['specialization']); ?>
                                        </small>
                                    </div>
                                    <div class="bio mb-3">
                                        <?php 
                                        $bio = $guide['bio'];
                                        echo strlen($bio) > 150 ? htmlspecialchars(substr($bio, 0, 150)) . '...' : htmlspecialchars($bio);
                                        ?>
                                    </div>
                                    <div class="d-grid">
                                        <a href="guide_profile.php?id=<?php echo $guide['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-user-circle"></i> View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        No guides are currently available. Please check back later.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?> 