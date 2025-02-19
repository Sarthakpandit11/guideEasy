<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a guide
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guide') {
    header("Location: login.php");
    exit();
}

$guide_id = $_SESSION['user_id'];

// Handle booking status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = strtolower(trim($_POST['status'])); // Ensure lowercase and no whitespace
    
    // Validate status - use exact database values
    $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        $_SESSION['error'] = "Invalid status provided.";
        header("Location: guide_bookings.php");
        exit();
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First check if the booking exists and belongs to this guide
        $check_query = "SELECT id FROM bookings WHERE id = ? AND guide_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ii", $booking_id, $guide_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception("Booking not found or you don't have permission to update it.");
        }
        
        // Update booking status
        $update_query = "UPDATE bookings SET status = ? WHERE id = ? AND guide_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sii", $new_status, $booking_id, $guide_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update booking status: " . $update_stmt->error);
        }
        
        if ($update_stmt->affected_rows === 0) {
            throw new Exception("No changes were made to the booking status.");
        }
        
        // Get tourist information
        $tourist_query = "SELECT tourist_id, destination FROM bookings WHERE id = ?";
        $tourist_stmt = $conn->prepare($tourist_query);
        $tourist_stmt->bind_param("i", $booking_id);
        
        if (!$tourist_stmt->execute()) {
            throw new Exception("Failed to retrieve tourist information: " . $tourist_stmt->error);
        }
        
        $tourist_result = $tourist_stmt->get_result();
        $booking_info = $tourist_result->fetch_assoc();
        
        if (!$booking_info) {
            throw new Exception("Failed to retrieve booking information.");
        }
        
        // Create notification for tourist
        $message = "Your booking for " . $booking_info['destination'] . " has been " . $new_status;
        $notification_query = "INSERT INTO notifications (tourist_id, guide_id, booking_id, message) 
                             VALUES (?, ?, ?, ?)";
        $notification_stmt = $conn->prepare($notification_query);
        $notification_stmt->bind_param("iiis", $booking_info['tourist_id'], $guide_id, $booking_id, $message);
        
        if (!$notification_stmt->execute()) {
            throw new Exception("Failed to create notification: " . $notification_stmt->error);
        }
        
        $conn->commit();
        $_SESSION['success'] = "Booking #" . $booking_id . " has been " . $new_status . " successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: guide_bookings.php");
    exit();
}

// Get all bookings for the guide
$bookings_query = "SELECT b.*, u.name as tourist_name, u.email as tourist_email, u.phone as tourist_phone
                  FROM bookings b
                  JOIN users u ON b.tourist_id = u.id
                  WHERE b.guide_id = ?
                  ORDER BY 
                    CASE b.status 
                        WHEN 'pending' THEN 1 
                        WHEN 'confirmed' THEN 2 
                        WHEN 'completed' THEN 3 
                        WHEN 'cancelled' THEN 4 
                    END,
                    b.created_at DESC";
$stmt = $conn->prepare($bookings_query);
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$bookings_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .tourist-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
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
                        <a class="nav-link" href="guide_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="guide_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_messages.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_settings.php">Settings</a>
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

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($bookings_result->num_rows === 0): ?>
            <div class="alert alert-info">
                You don't have any bookings yet.
            </div>
        <?php else: ?>
            <div class="row">
                <?php while ($booking = $bookings_result->fetch_assoc()): ?>
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
                                    <strong>Destination:</strong> <?php echo htmlspecialchars($booking['destination']); ?><br>
                                    <strong>Dates:</strong> <?php echo date('M j, Y', strtotime($booking['start_date'])); ?> - 
                                    <?php echo date('M j, Y', strtotime($booking['end_date'])); ?><br>
                                    <strong>Duration:</strong> <?php echo $booking['total_days']; ?> days (<?php echo $booking['total_hours']; ?> hours)<br>
                                    <strong>Number of People:</strong> <?php echo $booking['number_of_people']; ?><br>
                                    <strong>Total Cost:</strong> $<?php echo number_format($booking['total_cost'], 2); ?>
                                </p>

                                <div class="tourist-info">
                                    <h6>Tourist Information</h6>
                                    <p class="mb-1">
                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($booking['tourist_name']); ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($booking['tourist_email']); ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking['tourist_phone']); ?>
                                    </p>
                                </div>

                                <div class="mt-3">
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <form action="" method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form action="" method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Decline
                                            </button>
                                        </form>
                                    <?php elseif ($booking['status'] === 'confirmed'): ?>
                                        <form action="" method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-check-double"></i> Mark as Completed
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?> 