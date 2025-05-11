<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['booking_id'])) {
    die('Booking ID is required.');
}
$booking_id = intval($_GET['booking_id']);

// Fetch booking details
$stmt = $conn->prepare("SELECT b.*, u.name as guide_name FROM bookings b JOIN users u ON b.guide_id = u.id WHERE b.id = ? AND b.tourist_id = ?");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
if (!$booking) {
    die('Booking not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    echo '<div class="container mt-5"><div class="alert alert-success text-center"><i class="fas fa-check-circle fa-2x mb-2"></i><br>Payment successful via ' . htmlspecialchars($payment_method) . '!</div></div>';
    echo '<div class="container text-center"><a href="my_bookings.php" class="btn btn-primary mt-3">Back to My Bookings</a></div>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .payment-card {
            max-width: 480px;
            margin: 40px auto;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.15);
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            position: relative;
        }
        .payment-icon {
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.08);
            padding: 18px 20px;
        }
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .step-indicator .step {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #003366;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .step-indicator .line {
            width: 40px;
            height: 3px;
            background: #003366;
            margin: 0 8px;
            border-radius: 2px;
        }
        .form-check-input:checked {
            background-color: #003366;
            border-color: #003366;
        }
        .btn-success {
            background: #003366;
            border: none;
            font-weight: 600;
            letter-spacing: 1px;
            font-size: 1.1rem;
        }
        .btn-success:hover {
            background: #00509e;
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="payment-icon">
            <i class="fas fa-credit-card fa-2x text-primary"></i>
        </div>
        <div class="step-indicator mb-4 mt-2">
            <div class="step">1</div>
            <div class="line"></div>
            <div class="step" style="background:#fff; color:#003366; border:2px solid #003366;">2</div>
        </div>
        <h3 class="text-center mb-4">Payment for Booking #<?php echo $booking_id; ?></h3>
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <p><strong>Guide:</strong> <?php echo htmlspecialchars($booking['guide_name']); ?></p>
                <p><strong>Destination:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
                <p><strong>Dates:</strong> <?php echo date('M j, Y', strtotime($booking['start_date'])); ?> - <?php echo date('M j, Y', strtotime($booking['end_date'])); ?></p>
                <p><strong>People:</strong> <?php echo $booking['number_of_people']; ?></p>
                <p><strong>Total Cost:</strong> <span class="fw-bold text-success">$<?php echo number_format($booking['total_cost'], 2); ?></span></p>
            </div>
        </div>
        <form method="POST">
            <h5 class="mb-3">Select Payment Method:</h5>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="cash" value="Cash on Hand" required>
                <label class="form-check-label" for="cash"><i class="fas fa-money-bill-wave me-2"></i>Cash on Hand</label>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="radio" name="payment_method" id="khalti" value="Khalti" required>
                <label class="form-check-label" for="khalti"><img src="https://khalti.com/static/icons/favicon-32x32.png" style="height: 20px; margin-right: 6px;">Khalti</label>
            </div>
            <button type="submit" class="btn btn-success w-100 py-2">Pay Now</button>
        </form>
    </div>
</body>
</html> 