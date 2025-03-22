<?php
require_once 'db_connect.php';

// Check if notifications table exists
$result = $conn->query("SHOW TABLES LIKE 'notifications'");

if ($result->num_rows == 0) {
    // Create notifications table if it doesn't exist
    $sql = "CREATE TABLE notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        guide_id INT NOT NULL,
        tourist_id INT NOT NULL,
        booking_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (guide_id) REFERENCES users(id),
        FOREIGN KEY (tourist_id) REFERENCES users(id),
        FOREIGN KEY (booking_id) REFERENCES bookings(id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Notifications table created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }
} else {
    echo "Notifications table already exists";
}

$conn->close();
?> 