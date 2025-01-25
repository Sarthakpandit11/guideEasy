<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a guide
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guide') {
    header("Location: login.php");
    exit();
}

$guide_id = $_SESSION['user_id'];

// Get unread notifications count
$count_query = "SELECT COUNT(*) as unread_count FROM notifications WHERE guide_id = ? AND is_read = 0";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$count_result = $stmt->get_result();
$unread_count = $count_result->fetch_assoc()['unread_count'];

// Get recent notifications
$notifications_query = "SELECT n.*, u.name as tourist_name, b.destination 
                       FROM notifications n 
                       JOIN users u ON n.tourist_id = u.id 
                       JOIN bookings b ON n.booking_id = b.id 
                       WHERE n.guide_id = ? 
                       ORDER BY n.created_at DESC 
                       LIMIT 5";
$stmt = $conn->prepare($notifications_query);
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$notifications_result = $stmt->get_result();

// Fetch guide's data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$result = $stmt->get_result();
$guide = $result->fetch_assoc();

// Fetch guide's bookings
$bookings_query = "SELECT b.*, u.name as tourist_name, u.email as tourist_email 
                  FROM bookings b 
                  JOIN users u ON b.tourist_id = u.id 
                  WHERE b.guide_id = ? 
                  ORDER BY b.start_date DESC 
                  LIMIT 5";
$bookings_stmt = $conn->prepare($bookings_query);
$bookings_stmt->bind_param("i", $guide_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();

// Fetch guide's messages
$messages_query = "SELECT m.*, u.name as sender_name, u.email as sender_email 
                  FROM messages m 
                  JOIN users u ON m.sender_id = u.id 
                  WHERE m.receiver_id = ? 
                  ORDER BY m.created_at DESC 
                  LIMIT 5";
$messages_stmt = $conn->prepare($messages_query);
$messages_stmt->bind_param("i", $guide_id);
$messages_stmt->execute();
$messages_result = $messages_stmt->get_result();

// Fetch guide's ratings
$ratings_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings 
                 FROM reviews 
                 WHERE guide_id = ?";
$ratings_stmt = $conn->prepare($ratings_query);
$ratings_stmt->bind_param("i", $guide_id);
$ratings_stmt->execute();
$ratings_result = $ratings_stmt->get_result();
$ratings = $ratings_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Dashboard - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-section {
            padding: 100px 0 50px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .booking-card, .message-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .booking-status, .message-time {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-completed {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .rating-stars {
            color: #ffc107;
            font-size: 1.2em;
        }
        .notification-dropdown {
            width: 300px;
        }
        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-item.unread {
            background: #f8f9fa;
        }
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="guide_dashboard.php">
                <img src="images/logo.png" alt="Guide Easy Logo" height="40" class="d-inline-block align-text-top me-2">
                Guide Easy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="guide_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_messages.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_settings.php">Settings</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <?php if ($unread_count > 0): ?>
                                <span class="badge bg-danger notification-badge"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <?php if ($notifications_result->num_rows > 0): ?>
                                <?php while ($notification = $notifications_result->fetch_assoc()): ?>
                                    <li>
                                        <a class="dropdown-item notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>" 
                                           href="notifications.php">
                                            <div class="d-flex justify-content-between">
                                                <strong><?php echo htmlspecialchars($notification['tourist_name']); ?></strong>
                                                <small class="text-muted">
                                                    <?php echo date('H:i', strtotime($notification['created_at'])); ?>
                                                </small>
                                            </div>
                                            <p class="mb-0"><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <small class="text-muted"><?php echo htmlspecialchars($notification['destination']); ?></small>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="notifications.php">View All Notifications</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item text-center" href="#">No notifications</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Section -->
    <section class="dashboard-section">
        <div class="container">
            <div class="row">
                <!-- Profile Card -->
                <div class="col-md-4">
                    <div class="profile-card">
                        <div class="text-center">
                            <img src="<?php echo !empty($guide['profile_image']) ? $guide['profile_image'] : 'images/default_profile.jpg'; ?>" 
                                 alt="Profile Image" class="profile-image">
                            <h3><?php echo htmlspecialchars($guide['name']); ?></h3>
                            <p class="text-muted">Professional Tour Guide</p>
                            <div class="rating mb-3">
                                <?php
                                $avg_rating = round($ratings['avg_rating'] ?? 0, 1);
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
                                <span class="ms-2"><?php echo $avg_rating; ?> (<?php echo $ratings['total_ratings'] ?? 0; ?> reviews)</span>
                            </div>
                            <a href="guide_settings.php" class="btn btn-outline-primary">Edit Profile</a>
                        </div>
                    </div>
                </div>

                <!-- Stats and Content -->
                <div class="col-md-8">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <h3><?php echo $bookings_result->num_rows; ?></h3>
                                <p class="text-muted">Total Bookings</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <h3><?php echo $avg_rating; ?></h3>
                                <p class="text-muted">Average Rating</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <h3><?php echo $ratings['total_ratings'] ?? 0; ?></h3>
                                <p class="text-muted">Total Reviews</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Bookings</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($bookings_result->num_rows > 0): ?>
                                <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                                    <div class="booking-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($booking['tourist_name']); ?></h6>
                                                <p class="mb-1 text-muted">
                                                    <i class="fas fa-calendar"></i> 
                                                    <?php echo date('M d, Y', strtotime($booking['start_date'])); ?> - 
                                                    <?php echo date('M d, Y', strtotime($booking['end_date'])); ?>
                                                </p>
                                                <p class="mb-0 text-muted">
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    <?php echo htmlspecialchars($booking['destination']); ?>
                                                </p>
                                                <p class="mb-0 text-muted">
                                                    <i class="fas fa-users"></i> 
                                                    <?php echo $booking['number_of_people']; ?> people
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                                <p class="mb-0 mt-2">
                                                    <a href="booking_details.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        View Details
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-center text-muted">No bookings yet.</p>
                            <?php endif; ?>
                            <div class="text-center mt-3">
                                <a href="guide_bookings.php" class="btn btn-outline-primary">View All Bookings</a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Messages -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Messages</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($messages_result->num_rows > 0): ?>
                                <?php while ($message = $messages_result->fetch_assoc()): ?>
                                    <div class="message-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($message['sender_name']); ?></h6>
                                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($message['message']); ?></p>
                                            </div>
                                            <div class="text-end">
                                                <span class="message-time">
                                                    <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-center text-muted">No messages yet.</p>
                            <?php endif; ?>
                            <div class="text-center mt-3">
                                <a href="guide_messages.php" class="btn btn-outline-primary">View All Messages</a>
                            </div>
                        </div>
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
</body>
</html>
<?php
$conn->close();
?> 