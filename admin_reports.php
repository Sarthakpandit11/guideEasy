<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get booking statistics
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'] ?? 0;
$completed_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'completed'")->fetch_assoc()['count'] ?? 0;
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")->fetch_assoc()['count'] ?? 0;

// Get guide availability statistics
$available_guides = $conn->query("SELECT COUNT(*) as count FROM guide_settings WHERE availability_status = 'available'")->fetch_assoc()['count'] ?? 0;
$busy_guides = $conn->query("SELECT COUNT(*) as count FROM guide_settings WHERE availability_status = 'busy'")->fetch_assoc()['count'] ?? 0;
$away_guides = $conn->query("SELECT COUNT(*) as count FROM guide_settings WHERE availability_status = 'away'")->fetch_assoc()['count'] ?? 0;

// Get all bookings with guide and tourist information
$bookings = [];
$bookings_query = "SELECT b.*, 
                  t.name as tourist_name, t.email as tourist_email,
                  g.name as guide_name, g.email as guide_email,
                  b.start_date as booking_date
                  FROM bookings b
                  LEFT JOIN users t ON b.tourist_id = t.id
                  LEFT JOIN users g ON b.guide_id = g.id
                  ORDER BY b.created_at DESC";
$result = $conn->query($bookings_query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
} else {
    die("Error fetching bookings: " . $conn->error);
}

// Get all guides with their availability status
$guides_query = "SELECT u.id, u.name, u.email, u.phone, 
                gs.availability_status, gs.specialization, gs.rate_per_hour
                FROM users u
                LEFT JOIN guide_settings gs ON u.id = gs.user_id
                WHERE u.role = 'guide'
                ORDER BY u.name ASC";
$guides_result = $conn->query($guides_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reports - Guide Easy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-container {
            margin-top: 80px;
            padding: 20px;
        }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Guide Easy" height="40" class="me-2">
                Guide Easy
            </a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="admin_reports.php">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_messages.php">Messages</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_settings.php">Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Reports Content -->
    <div class="admin-wrapper">
        <div class="dashboard-container">
            <div class="container">
                <h2 class="mb-4">Reports & Analytics</h2>

                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card bg-primary text-white text-center">
                            <i class="fas fa-calendar-check"></i>
                            <h3><?php echo $total_bookings; ?></h3>
                            <p>Total Bookings</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-success text-white text-center">
                            <i class="fas fa-check-circle"></i>
                            <h3><?php echo $completed_bookings; ?></h3>
                            <p>Completed Bookings</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-warning text-white text-center">
                            <i class="fas fa-clock"></i>
                            <h3><?php echo $pending_bookings; ?></h3>
                            <p>Pending Bookings</p>
                        </div>
                    </div>
                </div>

                <!-- Guide Availability Section -->
                <div class="row mt-5">
                    <div class="col-md-4">
                        <div class="stat-card bg-success text-white text-center">
                            <i class="fas fa-user-check"></i>
                            <h3><?php echo $available_guides; ?></h3>
                            <p>Available Guides</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-warning text-white text-center">
                            <i class="fas fa-user-clock"></i>
                            <h3><?php echo $busy_guides; ?></h3>
                            <p>Busy Guides</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-secondary text-white text-center">
                            <i class="fas fa-user-slash"></i>
                            <h3><?php echo $away_guides; ?></h3>
                            <p>Away Guides</p>
                        </div>
                    </div>
                </div>

                <!-- Guides Table -->
                <div class="table-responsive mt-5">
                    <h4>Guide Availability Status</h4>
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Specialization</th>
                                <th>Rate/Hour</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($guide = $guides_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($guide['name']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($guide['email']); ?><br>
                                    <small><?php echo htmlspecialchars($guide['phone']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($guide['specialization'] ?? 'Not specified'); ?></td>
                                <td>$<?php echo number_format($guide['rate_per_hour'] ?? 0, 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $guide['availability_status'] === 'available' ? 'success' : 
                                            ($guide['availability_status'] === 'busy' ? 'warning' : 'secondary'); 
                                    ?>">
                                        <?php echo ucfirst($guide['availability_status'] ?? 'unknown'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-5">
                    <h4>All Bookings</h4>
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tourist</th>
                                <th>Guide</th>
                                <th>Destination</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($booking['tourist_name'] ?? 'N/A'); ?><br>
                                    <small><?php echo htmlspecialchars($booking['tourist_email'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($booking['guide_name'] ?? 'N/A'); ?><br>
                                    <small><?php echo htmlspecialchars($booking['guide_email'] ?? ''); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                                <td>
                                    <?php
                                    // Use start_date for booking date display
                                    $booking_date = !empty($booking['start_date']) ? $booking['start_date'] : $booking['created_at'];
                                    echo date('M d, Y', strtotime($booking_date));
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $booking['status'] === 'completed' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fixed Footer -->
        <footer class="fixed-footer">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
