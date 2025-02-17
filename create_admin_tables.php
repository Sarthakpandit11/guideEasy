<?php
require_once 'db_connect.php';

// Create bookings table
$create_bookings_table = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tourist_id INT NOT NULL,
    guide_id INT NOT NULL,
    destination VARCHAR(255) NOT NULL,
    booking_date DATE NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tourist_id) REFERENCES users(id),
    FOREIGN KEY (guide_id) REFERENCES users(id)
)";

if ($conn->query($create_bookings_table)) {
    echo "Bookings table created successfully<br>";
} else {
    echo "Error creating bookings table: " . $conn->error . "<br>";
}

// Create messages table
$create_messages_table = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
)";

if ($conn->query($create_messages_table)) {
    echo "Messages table created successfully<br>";
} else {
    echo "Error creating messages table: " . $conn->error . "<br>";
}

// Insert sample data for bookings
$insert_bookings = "INSERT INTO bookings (tourist_id, guide_id, destination, booking_date, status, notes) VALUES
    (1, 2, 'Kathmandu', '2024-04-15', 'completed', 'Tour completed successfully'),
    (3, 2, 'Pokhara', '2024-04-20', 'pending', 'Waiting for confirmation'),
    (1, 4, 'Chitwan', '2024-04-25', 'pending', 'New booking request')";

if ($conn->query($insert_bookings)) {
    echo "Sample booking data inserted successfully<br>";
} else {
    echo "Error inserting booking data: " . $conn->error . "<br>";
}

// Insert sample data for messages
$insert_messages = "INSERT INTO messages (sender_id, receiver_id, message, is_read) VALUES
    (1, 2, 'Hello, I would like to book a tour for next week.', TRUE),
    (2, 1, 'Sure, I am available. What dates are you looking for?', TRUE),
    (3, 4, 'Can you guide me through the Everest Base Camp trek?', FALSE)";

if ($conn->query($insert_messages)) {
    echo "Sample message data inserted successfully<br>";
} else {
    echo "Error inserting message data: " . $conn->error . "<br>";
}

$conn->close();
?> 