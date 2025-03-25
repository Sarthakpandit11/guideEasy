<?php
require_once 'db_connect.php';

// Drop the existing notifications table
$sql = "DROP TABLE IF EXISTS notifications";
if ($conn->query($sql) === TRUE) {
    echo "Old notifications table dropped successfully<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Create new notifications table with correct structure
$sql = "CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guide_id INT NOT NULL,
    tourist_id INT NOT NULL,
    booking_id INT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guide_id) REFERENCES users(id),
    FOREIGN KEY (tourist_id) REFERENCES users(id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "New notifications table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 