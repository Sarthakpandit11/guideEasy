<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a tourist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
    header("Location: login.php");
    exit();
}

// Get user's bookings
$sql = "SELECT b.*, u.name as guide_name, u.email as guide_email, u.phone as guide_phone
        FROM bookings b
        JOIN users u ON b.guide_id = u.id
        WHERE b.tourist_id = ?
        ORDER BY b.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);

// Get success message if exists
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : null;
unset($_SESSION['success']); // Clear the message after displaying
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .booking-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .booking-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-completed {
            background-color: #e2e3e5;
            color: #383d41;
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
                        <a class="nav-link active" href="my_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 80px;">
        <h1 class="mb-4">My Bookings</h1>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">
                You haven't made any bookings yet. <a href="tourist_destinations.php">Find a guide</a> to get started!
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($bookings as $booking): ?>
                    <div class="col-md-6">
                        <div class="card booking-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Booking #<?php echo $booking['id']; ?></h5>
                                    <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </div>
                                
                                <p class="card-text">
                                    <strong>Guide:</strong> <?php echo htmlspecialchars($booking['guide_name']); ?><br>
                                    <strong>Destination:</strong> <?php echo htmlspecialchars($booking['destination']); ?><br>
                                    <strong>Dates:</strong> <?php echo date('M j, Y', strtotime($booking['start_date'])); ?> - 
                                    <?php echo date('M j, Y', strtotime($booking['end_date'])); ?><br>
                                    <strong>People:</strong> <?php echo $booking['number_of_people']; ?><br>
                                    <strong>Total Cost:</strong> $<?php echo number_format($booking['total_cost'], 2); ?>
                                </p>

                                <div class="mt-3">
                                    <a href="guide_profile.php?id=<?php echo $booking['guide_id']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-user"></i> View Guide
                                    </a>
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <button class="btn btn-outline-danger btn-sm" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                window.location.href = 'cancel_booking.php?id=' + bookingId;
            }
        }
    </script>
</body>
</html> 