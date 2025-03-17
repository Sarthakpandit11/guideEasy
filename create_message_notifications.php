<?php
require_once 'db_connect.php';

// Use the guide_easy database
$conn->select_db('guide_easy');

// Create message_notifications table
$sql = "CREATE TABLE IF NOT EXISTS message_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "Message notifications table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 