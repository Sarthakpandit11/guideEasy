<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    header("Location: my_bookings.php");
    exit();
}

$booking_id = $_GET['id'];

// Verify that the booking belongs to the current user
$sql = "SELECT * FROM bookings WHERE id = ? AND tourist_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Booking not found or you don't have permission to cancel this booking.";
    header("Location: my_bookings.php");
    exit();
}

$booking = $result->fetch_assoc();

// Check if booking is already cancelled or completed
if ($booking['status'] === 'cancelled' || $booking['status'] === 'completed') {
    $_SESSION['error'] = "This booking cannot be cancelled.";
    header("Location: my_bookings.php");
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Update booking status to cancelled
    $update_sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $booking_id);
    $update_stmt->execute();

    // Create notification for the guide
    $notification_sql = "INSERT INTO notifications (guide_id, tourist_id, booking_id, message) 
                        VALUES (?, ?, ?, ?)";
    $notification_message = "Booking #" . $booking_id . " has been cancelled by the tourist.";
    $notification_stmt = $conn->prepare($notification_sql);
    $notification_stmt->bind_param("iiis", $booking['guide_id'], $_SESSION['user_id'], $booking_id, $notification_message);
    $notification_stmt->execute();

    // Commit transaction
    $conn->commit();

    $_SESSION['success'] = "Booking has been cancelled successfully.";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error'] = "An error occurred while cancelling the booking. Please try again.";
}

header("Location: my_bookings.php");
exit();
?> 