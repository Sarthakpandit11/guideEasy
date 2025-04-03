<?php
require_once 'db_connect.php';

// Modify notifications table to make booking_id optional
$sql = "ALTER TABLE notifications MODIFY COLUMN booking_id INT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Notifications table updated successfully";
} else {
    echo "Error updating notifications table: " . $conn->error;
}

$conn->close();
?> 