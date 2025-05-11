<?php
session_start();
require_once 'db_connect.php';

if (!isset($_GET['id'])) {
    die('Booking ID is required.');
}
$booking_id = intval($_GET['id']);

$query = "SELECT b.*, 
    g.name as guide_name, g.id as guide_id, 
    t.name as tourist_name, 
    gs.rate_per_hour
    FROM bookings b
    JOIN users g ON b.guide_id = g.id
    JOIN users t ON b.tourist_id = t.id
    LEFT JOIN guide_settings gs ON g.id = gs.user_id
    WHERE b.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die('Booking not found.');
}
$booking = $result->fetch_assoc();
$price_per_day = isset($booking['rate_per_hour']) ? floatval($booking['rate_per_hour']) * 8 : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Booking #<?php echo $booking['id']; ?> Details</h2>
    <div class="card p-4">
        <p><strong>Guide:</strong> <a href="guide_profile.php?id=<?php echo $booking['guide_id']; ?>"><?php echo htmlspecialchars($booking['guide_name']); ?></a></p>
        <p><strong>Tourist:</strong> <?php echo htmlspecialchars($booking['tourist_name']); ?></p>
        <p><strong>Destination:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
        <p><strong>Dates:</strong> <?php echo date('M d, Y', strtotime($booking['start_date'])); ?> - <?php echo date('M d, Y', strtotime($booking['end_date'])); ?></p>
        <p><strong>People:</strong> <?php echo $booking['number_of_people']; ?></p>
        <p><strong>Price per day:</strong> $<?php echo number_format($price_per_day, 2); ?></p>
        <p><strong>Total days:</strong> <?php echo $booking['total_days']; ?></p>
        <p><strong>Total cost:</strong> $<?php echo number_format($booking['total_cost'], 2); ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($booking['status']); ?></p>
    </div>
    <a href="my_bookings.php" class="btn btn-secondary mt-3">Back to My Bookings</a>
</div>
</body>
</html> 