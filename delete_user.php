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
        // Guide category mappings
        $conn->query("DELETE FROM guide_category_mappings WHERE guide_id = $user_id");
        // Bookings (as guide, tourist, or user)
        $conn->query("DELETE FROM bookings WHERE guide_id = $user_id OR tourist_id = $user_id OR user_id = $user_id");
        // Messages
        $conn->query("DELETE FROM messages WHERE sender_id = $user_id OR receiver_id = $user_id");
        // Notifications
        $conn->query("DELETE FROM notifications WHERE guide_id = $user_id OR tourist_id = $user_id");
        // Reviews (if you have a reviews table)
        if ($conn->query("SHOW TABLES LIKE 'reviews'")->num_rows) {
            $conn->query("DELETE FROM reviews WHERE guide_id = $user_id OR user_id = $user_id");
        }
        // Now delete the user
        $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $user_id);
        $success = $stmt->execute();
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