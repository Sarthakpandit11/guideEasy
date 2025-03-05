<?php
require_once 'db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get booking details
$sql = "SELECT b.*, u.username as guide_name 
        FROM bookings b 
        JOIN users u ON b.guide_id = u.id 
        WHERE b.id = ? AND b.tourist_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    header("Location: tourist_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        .booking-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="success-container">
            <i class="fas fa-check-circle success-icon"></i>
            <h1>Booking Successful!</h1>
            <p class="lead">You have successfully booked <?php echo htmlspecialchars($booking['guide_name']); ?> as your guide.</p>
            
            <div class="booking-details text-start">
                <h5 class="mb-4">Booking Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Booking ID:</strong> #<?php echo $booking['id']; ?></p>
                        <p><strong>Destination:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
                        <p><strong>Start Date:</strong> <?php echo date('F j, Y', strtotime($booking['start_date'])); ?></p>
                        <p><strong>End Date:</strong> <?php echo date('F j, Y', strtotime($booking['end_date'])); ?></p>
                        <p><strong>Number of People:</strong> <?php echo $booking['number_of_people']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Days:</strong> <?php echo $booking['total_days']; ?></p>
                        <p><strong>Total Hours:</strong> <?php echo $booking['total_hours']; ?></p>
                        <p><strong>Total Cost:</strong> $<?php echo number_format($booking['total_cost'], 2); ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-warning"><?php echo ucfirst($booking['status']); ?></span></p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="tourist_dashboard.php" class="btn btn-primary me-3">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
                <a href="my_bookings.php" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> View All Bookings
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 