<?php
session_start();
header('Content-Type: application/json');
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'error' => 'Cannot delete self']);
        exit();
    }
    
    // Delete related records
    $conn->begin_transaction();
    try {
        // Guide settings
        if (!$conn->query("DELETE FROM guide_settings WHERE user_id = $user_id")) throw new Exception($conn->error);
        // Guide category mappings
        if (!$conn->query("DELETE FROM guide_category_mappings WHERE guide_id = $user_id")) throw new Exception($conn->error);
        // Get all booking IDs for this user (as guide or tourist)
        $booking_ids_result = $conn->query("SELECT id FROM bookings WHERE guide_id = $user_id OR tourist_id = $user_id");
        $booking_ids = [];
        while ($booking_ids_result && $row = $booking_ids_result->fetch_assoc()) {
            $booking_ids[] = $row['id'];
        }
        // Delete notifications for these bookings
        if (!empty($booking_ids)) {
            $ids_str = implode(',', array_map('intval', $booking_ids));
            if (!$conn->query("DELETE FROM notifications WHERE booking_id IN ($ids_str)")) throw new Exception($conn->error);
        }
        // Delete all notifications for this user (by guide_id or tourist_id)
        if (!$conn->query("DELETE FROM notifications WHERE guide_id = $user_id OR tourist_id = $user_id")) throw new Exception($conn->error);
        // Bookings (as guide or tourist)
        if (!$conn->query("DELETE FROM bookings WHERE guide_id = $user_id OR tourist_id = $user_id")) throw new Exception($conn->error);
        // Messages
        if (!$conn->query("DELETE FROM messages WHERE sender_id = $user_id OR receiver_id = $user_id")) throw new Exception($conn->error);
        // Reviews (if you have a reviews table)
        if ($conn->query("SHOW TABLES LIKE 'reviews'")->num_rows) {
            if (!$conn->query("DELETE FROM reviews WHERE guide_id = $user_id")) throw new Exception($conn->error);
        }
        // Now delete the user
        $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $user_id);
        $success = $stmt->execute();
        if (!$success) throw new Exception($stmt->error);
        $conn->commit();
        echo json_encode(['success' => $success]);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid request']); 