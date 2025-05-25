<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

try {
    session_start();
    require_once 'db_connect.php';

    // Debug output (REMOVE ALL BELOW)
    // echo "<pre>";
    // echo "POST Data:\n";
    // print_r($_POST);
    // echo "\nSession Data:\n";
    // print_r($_SESSION);
    // echo "</pre>";

    // Check if user is logged in and is a tourist
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
        throw new Exception("User not logged in or not a tourist");
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['destination']) || !isset($_POST['start_date']) || !isset($_POST['end_date']) || !isset($_POST['people'])) {
            throw new Exception("Missing required form fields");
        }

        $destination = $_POST['destination'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $people = $_POST['people'];
        
        $_SESSION['booking_details'] = [
            'destination' => $destination,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'people' => $people
        ];
    }

    if (!isset($_SESSION['booking_details'])) {
        throw new Exception("No booking details found in session");
    }

    $destination = $_SESSION['booking_details']['destination'];
    
    // Debug: Check if guide_settings table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'guide_settings'");
    if ($table_check->num_rows == 0) {
        throw new Exception("guide_settings table does not exist");
    }

    // Debug: Check if there are any guides with the specified location
    $location_check = $conn->prepare("SELECT COUNT(*) as count FROM guide_settings WHERE LOWER(TRIM(location)) = LOWER(TRIM(?))");
    $location_check->bind_param("s", $destination);
    $location_check->execute();
    $location_result = $location_check->get_result();
    $location_count = $location_result->fetch_assoc()['count'];
    
    // echo "<pre>Number of guides with location '$destination': $location_count</pre>";

    // Get available guides based on their profile location
    $guides_query = "SELECT 
                        u.*,
                        gs.availability_status,
                        gs.location,
                        gs.rate_per_hour,
                        gs.bio,
                        GROUP_CONCAT(DISTINCT gc.name) as categories,
                        COALESCE((SELECT AVG(rating) FROM reviews WHERE guide_id = u.id), 0) as avg_rating,
                        COALESCE((SELECT COUNT(*) FROM reviews WHERE guide_id = u.id), 0) as total_reviews
                    FROM users u
                    JOIN guide_settings gs ON u.id = gs.user_id
                    LEFT JOIN guide_category_mappings gcm ON u.id = gcm.guide_id AND gcm.location = gs.location
                    LEFT JOIN guide_categories gc ON gcm.category_id = gc.id
                    WHERE u.role = 'guide' 
                    AND gs.availability_status = 'available'
                    AND gs.location IS NOT NULL
                    AND LOWER(TRIM(gs.location)) = LOWER(TRIM(?))
                    GROUP BY u.id
                    ORDER BY avg_rating DESC, total_reviews DESC";

    $stmt = $conn->prepare($guides_query);
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $destination);
    if (!$stmt->execute()) {
        throw new Exception("Database execute failed: " . $stmt->error);
    }

    $guides_result = $stmt->get_result();
    
    // echo "<pre>SQL Query: " . $guides_query . "</pre>";
    // echo "<pre>Destination: " . $destination . "</pre>";
    // echo "<pre>Found " . $guides_result->num_rows . " guides for " . htmlspecialchars($destination) . "</pre>";

    // Debug: Show all available guides regardless of location
    $all_guides_query = "SELECT u.id, u.name, gs.location, gs.availability_status 
                        FROM users u 
                        JOIN guide_settings gs ON u.id = gs.user_id 
                        WHERE u.role = 'guide'";
    $all_guides_result = $conn->query($all_guides_query);
    // echo "<pre>All available guides:\n";
    // while ($guide = $all_guides_result->fetch_assoc()) {
    //     echo "Guide: " . $guide['name'] . ", Location: " . $guide['location'] . ", Status: " . $guide['availability_status'] . "\n";
    // }
    // echo "</pre>";

    // Debug: Show the exact location values in the database
    $location_values_query = "SELECT DISTINCT location FROM guide_settings WHERE location IS NOT NULL";
    $location_values_result = $conn->query($location_values_query);
    // echo "<pre>All location values in database:\n";
    // while ($row = $location_values_result->fetch_assoc()) {
    //     echo "Location: '" . $row['location'] . "'\n";
    // }
    // echo "</pre>";

} catch (Exception $e) {
    echo "<div style='color: red; padding: 20px; margin: 20px; border: 1px solid red;'>";
    echo "Error: " . htmlspecialchars($e->getMessage());
    echo "<br>File: " . htmlspecialchars($e->getFile());
    echo "<br>Line: " . $e->getLine();
    echo "</div>";
}

// Flush output buffer
ob_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Guides - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .main-card {
            background: linear-gradient(135deg, #f8fafc 80%, #e3e9f7 100%);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(20,30,40,0.10);
            padding: 2.2rem 2.2rem 1.5rem 2.2rem;
            margin-bottom: 2.5rem;
            border: 2.5px solid #FF6B4A;
        }
        .filter-card {
            background: linear-gradient(135deg, #f8fafc 80%, #e3e9f7 100%);
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(20,30,40,0.08);
            padding: 1.5rem 2rem 1.2rem 2rem;
            margin-bottom: 2.5rem;
            border: 2px solid #4f8cff22;
        }
        .filter-btn {
            border-radius: 22px !important;
            font-weight: 600;
            font-size: 1.05rem;
            padding: 10px 28px;
            border: 2px solid #4f8cff;
            background: #fff;
            color: #2563eb;
            margin-right: 12px;
            margin-bottom: 10px;
            transition: background 0.2s, color 0.2s, border 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(79, 140, 255, 0.06);
        }
        .filter-btn.active, .filter-btn:focus, .filter-btn:hover {
            background: linear-gradient(90deg, #4f8cff 60%, #6be0ff 100%);
            color: #fff;
            border: 2px solid #4f8cff;
            box-shadow: 0 4px 16px rgba(79, 140, 255, 0.12);
        }
        .guide-card {
            background: linear-gradient(135deg, #fff 80%, #e3e9f7 100%);
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(20,30,40,0.10);
            padding: 1.5rem 1.5rem 1.2rem 1.5rem;
            margin-bottom: 2rem;
            border: 2px solid #4f8cff22;
            transition: transform 0.22s cubic-bezier(.4,2,.3,1), box-shadow 0.22s cubic-bezier(.4,2,.3,1);
        }
        .guide-card:hover {
            transform: translateY(-8px) scale(1.025);
            box-shadow: 0 8px 32px rgba(20, 30, 40, 0.18), 0 1.5px 6px rgba(20, 30, 40, 0.10);
        }
        .guide-card .profile-img {
            width: 54px;
            height: 54px;
            object-fit: cover;
            border-radius: 50%;
            border: 2.5px solid #4f8cff;
            box-shadow: 0 2px 8px rgba(79, 140, 255, 0.10);
            margin-right: 14px;
        }
        .guide-card .guide-name {
            font-weight: 700;
            font-size: 1.18rem;
            color: #222;
        }
        .guide-card .guide-location {
            color: #2563eb;
            font-size: 1rem;
            font-weight: 500;
        }
        .guide-card .rate-badge {
            border-radius: 18px;
            background: linear-gradient(90deg, #22c55e 60%, #4ade80 100%);
            color: #fff;
            font-weight: 700;
            font-size: 1.08rem;
            padding: 7px 18px;
            margin-bottom: 8px;
            display: inline-block;
        }
        .guide-card .star-rating {
            color: #ffc107;
            font-size: 1.1rem;
        }
        .guide-card .reviews-count {
            color: #888;
            font-size: 0.98rem;
        }
    </style>
</head>
<body>
    <div class="curvy-navbar-wrapper">
        <nav class="navbar navbar-expand-lg custom-navbar fixed-top">
            <div class="container">
                <a class="navbar-brand" href="tourist_dashboard.php">
                    <img src="images/logo.png" alt="Guide Easy Logo">
                    <span class="site-name">Guide Easy</span>
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
        <!-- SVG for curvy bottom -->
        <svg class="curvy-navbar-bg" viewBox="0 0 1440 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,0 H1440 V60 Q1200,110 720,80 Q240,50 0,110 Z" fill="rgba(20,30,40,0.7)"/>
        </svg>
    </div>

    <!-- Main Content -->
    <div class="container" style="margin-top: 80px;">
        <?php if (isset($guides_result) && $guides_result->num_rows > 0): ?>
            <!-- Booking Summary -->
            <div class="booking-summary">
                <h3>Your Booking Details</h3>
                <p><strong>Destination:</strong> <?php echo htmlspecialchars($_SESSION['booking_details']['destination']); ?></p>
                <p><strong>Start Date:</strong> <?php echo htmlspecialchars($_SESSION['booking_details']['start_date']); ?></p>
                <p><strong>End Date:</strong> <?php echo htmlspecialchars($_SESSION['booking_details']['end_date']); ?></p>
                <p><strong>Number of People:</strong> <?php echo htmlspecialchars($_SESSION['booking_details']['people']); ?></p>
            </div>

            <!-- Category Filter Section -->
            <div class="category-filter mb-4">
                <h4 class="mb-3">Filter by Tour Category</h4>
                <div class="row g-2">
                    <?php
                    // Get all categories
                    $categories_query = "SELECT * FROM guide_categories ORDER BY name";
                    $categories_result = $conn->query($categories_query);
                    
                    // Get selected category from URL if any
                    $selected_category = isset($_GET['category']) ? $_GET['category'] : '';
                    
                    while ($category = $categories_result->fetch_assoc()):
                        $is_active = $selected_category == $category['id'];
                    ?>
                        <div class="col-md-3 col-6">
                            <a href="?destination=<?php echo urlencode($destination); ?>&category=<?php echo $category['id']; ?>" 
                               class="btn btn-outline-primary w-100 <?php echo $is_active ? 'active' : ''; ?>">
                                <?php
                                $icon = '';
                                switch($category['name']) {
                                    case 'Sightseeing Tours':
                                        $icon = 'fa-camera';
                                        break;
                                    case 'Cultural Tours':
                                        $icon = 'fa-landmark';
                                        break;
                                    case 'Hiking Tours':
                                        $icon = 'fa-hiking';
                                        break;
                                    case 'Food Tours':
                                        $icon = 'fa-utensils';
                                        break;
                                }
                                ?>
                                <i class="fas <?php echo $icon; ?> me-2"></i>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        </div>
                    <?php endwhile; ?>
                    
                    <?php if ($selected_category): ?>
                        <div class="col-12 mt-2">
                            <a href="?destination=<?php echo urlencode($destination); ?>" class="btn btn-link text-danger">
                                <i class="fas fa-times"></i> Clear Filter
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Guide Selection Section -->
            <div class="guide-selection-section">
                <h2 class="mb-4">Available Guides in <?php echo htmlspecialchars($_SESSION['booking_details']['destination']); ?></h2>
                <p class="text-muted mb-4">Please review the available guides and select one by viewing their profile.</p>

                <div class="row">
                    <?php 
                    // Modify the query to filter by category if selected
                    if ($selected_category) {
                        $guides_query = "SELECT 
                            u.*,
                            gs.availability_status,
                            gs.location,
                            gs.rate_per_hour,
                            gs.bio,
                            GROUP_CONCAT(DISTINCT gc.name) as categories,
                            COALESCE((SELECT AVG(rating) FROM reviews WHERE guide_id = u.id), 0) as avg_rating,
                            COALESCE((SELECT COUNT(*) FROM reviews WHERE guide_id = u.id), 0) as total_reviews
                        FROM users u
                        JOIN guide_settings gs ON u.id = gs.user_id
                        JOIN guide_category_mappings gcm ON u.id = gcm.guide_id AND gcm.location = gs.location
                        LEFT JOIN guide_categories gc ON gcm.category_id = gc.id
                        WHERE u.role = 'guide' 
                        AND gs.availability_status = 'available'
                        AND gs.location IS NOT NULL
                        AND LOWER(TRIM(gs.location)) = LOWER(TRIM(?))
                        AND gcm.category_id = ?
                        GROUP BY u.id
                        ORDER BY avg_rating DESC, total_reviews DESC";
                        
                        $stmt = $conn->prepare($guides_query);
                        $stmt->bind_param("si", $destination, $selected_category);
                    } else {
                        // Use the original query without category filter
                        $stmt = $conn->prepare($guides_query);
                        $stmt->bind_param("s", $destination);
                    }
                    
                    $stmt->execute();
                    $guides_result = $stmt->get_result();
                    
                    if ($guides_result->num_rows === 0): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                No guides available for the selected category. Please try another category or clear the filter.
                            </div>
                        </div>
                    <?php endif;
                    
                    while ($guide = $guides_result->fetch_assoc()): ?>
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
                                            <?php echo htmlspecialchars($guide['location']); ?>
                                        </p>
                                        <div class="rate mb-3">
                                            <span class="badge bg-success rate-badge">
                                                <i class="fas fa-dollar-sign"></i> 
                                                <?php echo number_format($guide['rate_per_hour'], 2); ?>/hour
                                            </span>
                                        </div>
                                        <div class="bio mb-3">
                                            <?php 
                                            $bio = $guide['bio'] ?? 'No bio available';
                                            echo strlen($bio) > 150 ? htmlspecialchars(substr($bio, 0, 150)) . '...' : htmlspecialchars($bio);
                                            ?>
                                        </div>
                                        
                                        <!-- Add categories display -->
                                        <?php if (!empty($guide['categories'])): ?>
                                        <div class="categories mb-3">
                                            <strong>Specialties:</strong><br>
                                            <?php
                                            $categories = explode(',', $guide['categories']);
                                            foreach ($categories as $category) {
                                                echo '<span class="badge bg-info me-1 mb-1">' . htmlspecialchars(trim($category)) . '</span>';
                                            }
                                            ?>
                                        </div>
                                        <?php endif; ?>

                                        <div class="d-grid">
                                            <a href="guide_profile.php?id=<?php echo $guide['id']; ?>&booking=true" class="btn btn-primary">
                                                <i class="fas fa-user-circle"></i> View Full Profile & Book
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                No guides are currently available in <?php echo htmlspecialchars($destination); ?>. 
                Please try another destination or check back later.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 