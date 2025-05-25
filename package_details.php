<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo '<div class="container mt-5"><div class="alert alert-danger">No package selected.</div></div>';
    exit();
}

$package_id = intval($_GET['id']);
$pkg_query = "SELECT p.*, u.name as guide_name, gs.profile_picture FROM packages p JOIN users u ON p.guide_id = u.id LEFT JOIN guide_settings gs ON u.id = gs.user_id WHERE p.id = ?";
$stmt = $conn->prepare($pkg_query);
$stmt->bind_param('i', $package_id);
$stmt->execute();
$pkg_result = $stmt->get_result();
$package = $pkg_result->fetch_assoc();

if (!$package) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Package not found.</div></div>';
    exit();
}

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    $tourist_id = $_SESSION['user_id'];
    $guide_id = $package['guide_id'];
    $destination = $package['title'];
    $number_of_people = 1;
    $price = floatval($package['price']);
    $duration_str = $package['duration'];
    // Extract number of days from duration string (e.g., '3 Days')
    if (preg_match('/(\\d+)/', $duration_str, $matches)) {
        $total_days = intval($matches[1]);
    } else {
        $total_days = 1; // fallback
    }
    $total_hours = $total_days * 8; // 8 hours per day
    $total_cost = $price;
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime("+$total_days days"));

    $insert = $conn->prepare("INSERT INTO bookings (tourist_id, guide_id, destination, start_date, end_date, number_of_people, total_hours, total_days, total_cost, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $insert->bind_param('iisssiiid', $tourist_id, $guide_id, $destination, $start_date, $end_date, $number_of_people, $total_hours, $total_days, $total_cost);
    if ($insert->execute()) {
        $success = true;
    } else {
        $error = 'Booking failed. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Details - Guide Easy</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html, body { background: #f4f8fb; }
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
        /* 3D Card Style */
        .package-details-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 10px 36px 0 rgba(31, 38, 135, 0.16), 0 2px 8px 0 rgba(60,60,60,0.10);
            padding: 2.5rem 2rem 2rem 2rem;
            margin-top: 4.5rem;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
            transition: transform 0.35s cubic-bezier(.21,1.02,.73,1), box-shadow 0.35s cubic-bezier(.21,1.02,.73,1);
            position: relative;
        }
        .package-details-card:hover {
            transform: translateY(-12px) scale(1.04) rotateX(3deg);
            box-shadow: 0 20px 56px 0 rgba(31, 38, 135, 0.22), 0 6px 20px 0 rgba(60,60,60,0.13);
            z-index: 3;
        }
        .package-details-card .guide-photo {
            width: 90px; height: 90px; object-fit: cover; border-radius: 50%; border: 2.5px solid #fff; box-shadow: 0 2px 10px 0 rgba(31, 38, 135, 0.13);
        }
        .package-details-card .badge {
            font-size: 1em;
            margin-right: 0.5em;
        }
        .package-details-card .badge.bg-success {
            font-size: 1.01em;
            padding: 0.45em 1.05em;
            border-radius: 1.1em;
            box-shadow: 0 2px 8px 0 rgba(40, 167, 69, 0.10);
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            font-weight: 600;
        }
        .package-details-card .badge.bg-info {
            background: linear-gradient(90deg, #a1c4fd 0%, #c2e9fb 100%);
            color: #222;
            font-weight: 500;
            border-radius: 1.1em;
            padding: 0.45em 1.05em;
        }
        .package-details-card .btn-primary {
            border-radius: 1.2em;
            font-weight: 600;
            box-shadow: 0 2px 8px 0 rgba(0,123,255,0.10);
            background: linear-gradient(90deg, #4e54c8 0%, #8f94fb 100%);
            border: none;
        }
        .package-details-card .btn-primary:hover {
            background: linear-gradient(90deg, #8f94fb 0%, #4e54c8 100%);
        }
        .package-details-card .btn-outline-secondary {
            border-radius: 1.2em;
            font-weight: 600;
        }
        .package-details-card .guide-info {
            margin-bottom: 1.2rem;
        }
        .package-details-card .guide-name {
            font-size: 1.25rem;
            font-weight: 700;
        }
        .package-details-card .guide-location {
            color: #ff6b4a;
            font-size: 1.05rem;
            font-weight: 500;
        }
        .package-details-card .guide-bio {
            color: #555;
            font-size: 1.01rem;
        }
        .package-details-card .guide-experience {
            color: #444;
            font-size: 0.98rem;
        }
        .package-details-card .guide-label {
            font-size: 0.98rem;
            color: #888;
            font-weight: 500;
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
        <!-- SVG for curvy bottom -->
        <svg class="curvy-navbar-bg" viewBox="0 0 1440 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,0 H1440 V60 Q1200,110 720,80 Q240,50 0,110 Z" fill="rgba(20,30,40,0.7)"/>
        </svg>
    </div>
    <div class="container">
        <div class="package-details-card mt-5 mb-5">
            <!-- Guide Card Header -->
            <div class="guide-info d-flex align-items-center mb-4 pb-2 border-bottom">
                <?php
                // Fetch more guide info
                $guide_id = $package['guide_id'];
                $guide_query = "SELECT u.*, gs.* FROM users u LEFT JOIN guide_settings gs ON u.id = gs.user_id WHERE u.id = ?";
                $stmt = $conn->prepare($guide_query);
                $stmt->bind_param("i", $guide_id);
                $stmt->execute();
                $guide = $stmt->get_result()->fetch_assoc();
                $guide_photo = !empty($guide['profile_picture']) ? $guide['profile_picture'] : (!empty($package['profile_picture']) ? $package['profile_picture'] : 'images/default_guide.jpg');
                ?>
                <img src="<?php echo htmlspecialchars($guide_photo); ?>" class="guide-photo me-4 border border-3 border-light shadow-sm" alt="<?php echo htmlspecialchars($guide['name']); ?>">
                <div>
                    <div class="guide-name mb-1"><?php echo htmlspecialchars($guide['name']); ?></div>
                    <div class="guide-label mb-2"><i class="fas fa-user-tie me-1"></i> Guide</div>
                    <div class="guide-location mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo htmlspecialchars($guide['location'] ?? ''); ?></div>
                    <div class="mb-1"><span class="badge bg-success"><i class="fas fa-dollar-sign"></i> <?php echo number_format($guide['rate_per_hour'],2); ?>/hour</span></div>
                    <div class="mb-1"><i class="fas fa-envelope me-1"></i> <span class="text-muted">Email:</span> <?php echo htmlspecialchars($guide['email']); ?></div>
                    <div class="mb-1"><i class="fas fa-phone me-1"></i> <span class="text-muted">Phone:</span> <?php echo htmlspecialchars($guide['phone']); ?></div>
                    <div class="mb-1"><i class="fas fa-language me-1"></i> <span class="text-muted">Languages:</span> <?php echo htmlspecialchars($guide['languages'] ?? 'Not specified'); ?></div>
                    <div class="mb-1"><i class="fas fa-star me-1"></i> <span class="text-muted">Specialization:</span> <?php echo htmlspecialchars($guide['specialization'] ?? ''); ?></div>
                    <div class="mb-1"><i class="fas fa-briefcase me-1"></i> <span class="text-muted">Experience:</span> <?php echo htmlspecialchars($guide['experience'] ?? ''); ?></div>
                    <div class="mb-1"><i class="fas fa-info-circle me-1"></i> <span class="text-muted">Bio:</span> <span class="guide-bio"><?php echo htmlspecialchars($guide['bio'] ?? 'No bio available'); ?></span></div>
                    <div class="mb-1"><i class="fas fa-tags me-1"></i> <span class="text-muted">Tour Categories:</span> <?php
                        $cat_query = "SELECT gc.name FROM guide_category_mappings gcm JOIN guide_categories gc ON gcm.category_id = gc.id WHERE gcm.guide_id = ?";
                        $cat_stmt = $conn->prepare($cat_query);
                        $cat_stmt->bind_param("i", $guide_id);
                        $cat_stmt->execute();
                        $cat_result = $cat_stmt->get_result();
                        $categories = [];
                        while ($row = $cat_result->fetch_assoc()) {
                            $categories[] = $row['name'];
                        }
                        $categories = array_unique($categories);
                        if (!empty($categories)) {
                            foreach ($categories as $cat) {
                                echo '<span class="badge bg-info text-dark mb-1 me-1">' . htmlspecialchars($cat) . '</span>';
                            }
                        } else {
                            echo '<span class="text-muted">No categories</span>';
                        }
                    ?></div>
                </div>
            </div>
            <!-- Package Info -->
            <div class="mb-2">
                <h3 class="fw-bold mb-2"><?php echo htmlspecialchars($package['title']); ?></h3>
                <span class="badge bg-success"><i class="fas fa-dollar-sign"></i> <?php echo number_format($package['price'],2); ?></span>
                <span class="badge bg-info text-dark"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($package['duration']); ?></span>
            </div>
            <div class="mb-3 guide-bio">
                <?php echo nl2br(htmlspecialchars($package['description'])); ?>
            </div>
            <div class="mb-3 text-muted" style="font-size:0.95em;">
                <i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($package['created_at'])); ?>
            </div>
            <?php if ($success): ?>
                <div class="alert alert-success">Booking confirmed! The guide will contact you soon.</div>
            <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php else: ?>
                <form method="post">
                    <button type="submit" name="confirm_booking" class="btn btn-primary w-100"><i class="fas fa-check-circle"></i> Confirm Booking</button>
                </form>
            <?php endif; ?>
            <a href="tourist_dashboard.php" class="btn btn-outline-secondary w-100 mt-3"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 