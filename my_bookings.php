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
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(20, 30, 40, 0.18), 0 1.5px 6px rgba(20, 30, 40, 0.10);
            margin-bottom: 32px;
            transition: transform 0.25s cubic-bezier(.4,2,.3,1), box-shadow 0.25s cubic-bezier(.4,2,.3,1);
            border: 2.5px solid #FF6B4A;
            background: linear-gradient(135deg, #f8fafc 80%, #e3e9f7 100%);
            position: relative;
        }
        .booking-card:hover {
            transform: translateY(-10px) scale(1.025);
            box-shadow: 0 16px 32px rgba(20, 30, 40, 0.22), 0 3px 12px rgba(20, 30, 40, 0.13);
        }
        .status-badge {
            padding: 7px 18px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(255, 107, 74, 0.10);
            letter-spacing: 1px;
            border: 1.5px solid #FF6B4A;
            background: linear-gradient(90deg, #fff3cd 80%, #ffe5d0 100%);
        }
        .status-pending {
            color: #FF6B4A;
        }
        .status-confirmed {
            background: linear-gradient(90deg, #d4edda 80%, #b6f0c2 100%);
            color: #1e7e34;
            border-color: #1e7e34;
        }
        .status-cancelled {
            background: linear-gradient(90deg, #f8d7da 80%, #ffd6d6 100%);
            color: #c82333;
            border-color: #c82333;
        }
        .status-completed {
            background: linear-gradient(90deg, #e2e3e5 80%, #f0f0f0 100%);
            color: #383d41;
            border-color: #383d41;
        }
        .booking-card .card-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #222;
        }
        .booking-card .card-text {
            font-size: 1.08rem;
            color: #333;
        }
        .booking-card .btn-modern {
            border-radius: 22px;
            font-weight: 600;
            font-size: 1rem;
            padding: 8px 22px;
            margin-right: 8px;
            border: none;
            background: linear-gradient(90deg, #4f8cff 60%, #6be0ff 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(79, 140, 255, 0.10);
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
        }
        .booking-card .btn-modern i {
            margin-right: 6px;
        }
        .booking-card .btn-modern:hover {
            background: linear-gradient(90deg, #2563eb 60%, #38bdf8 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 4px 16px rgba(79, 140, 255, 0.18);
        }
        .booking-card .btn-cancel {
            background: linear-gradient(90deg, #ff6b4a 60%, #ffb86b 100%);
            color: #fff;
            border: none;
            margin-right: 0;
        }
        .booking-card .btn-cancel:hover {
            background: linear-gradient(90deg, #d7263d 60%, #ff6b4a 100%);
            color: #fff;
        }
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
                            <a class="nav-link active" href="my_bookings.php">My Bookings</a>
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
                                    <a href="guide_profile.php?id=<?php echo $booking['guide_id']; ?>" class="btn btn-modern">
                                        <i class="fas fa-user"></i> View Guide
                                    </a>
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <button class="btn btn-modern btn-cancel" onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                                            <i class="fas fa-times-circle"></i> Cancel
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