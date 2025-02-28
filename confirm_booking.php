<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a tourist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
    header("Location: login.php");
    exit();
}

// Check if booking details and guide ID are set
if (!isset($_SESSION['booking_details']) || !isset($_GET['guide_id'])) {
    header("Location: tourist_destinations.php");
    exit();
}

$guide_id = $_GET['guide_id'];
$tourist_id = $_SESSION['user_id'];
$destination = $_SESSION['booking_details']['destination'];
$start_date = $_SESSION['booking_details']['start_date'];
$end_date = $_SESSION['booking_details']['end_date'];
$people = $_SESSION['booking_details']['people'];

// Get guide details
$guide_query = "SELECT u.*, gs.rate_per_hour FROM users u 
                LEFT JOIN guide_settings gs ON u.id = gs.user_id 
                WHERE u.id = ?";
$stmt = $conn->prepare($guide_query);
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$guide_result = $stmt->get_result();
$guide = $guide_result->fetch_assoc();

// Calculate total hours and cost
$start = new DateTime($start_date);
$end = new DateTime($end_date);
$interval = $start->diff($end);
$total_hours = $interval->days * 8; // Assuming 8 hours per day
$total_cost = $total_hours * $guide['rate_per_hour'];

// Insert booking into database
$insert_query = "INSERT INTO bookings (tourist_id, guide_id, destination, start_date, end_date, 
                number_of_people, total_cost, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
$stmt = $conn->prepare($insert_query);
$stmt->bind_param("iisssid", $tourist_id, $guide_id, $destination, $start_date, $end_date, $people, $total_cost);

if ($stmt->execute()) {
    $booking_id = $conn->insert_id;
    
    // Create notification for the guide
    $notification_query = "INSERT INTO notifications (guide_id, tourist_id, booking_id, message, is_read, created_at) 
                          VALUES (?, ?, ?, ?, 0, NOW())";
    $message = "New booking request for " . $destination;
    $stmt = $conn->prepare($notification_query);
    $stmt->bind_param("iiis", $guide_id, $tourist_id, $booking_id, $message);
    $stmt->execute();
    
    // Clear booking details from session
    unset($_SESSION['booking_details']);
    // Set success message
    $_SESSION['success'] = "Your booking has been successfully created!";
    // Redirect to my bookings page
    header("Location: my_bookings.php");
    exit();
} else {
    // Handle error
    header("Location: tourist_destinations.php?error=booking_failed");
    exit();
}
?> 