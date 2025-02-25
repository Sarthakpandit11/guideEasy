<?php
require_once 'db_connect.php';

// SQL to create notifications table
$sql = "CREATE TABLE IF NOT EXISTS notifications (
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

$conn->close();
?> 