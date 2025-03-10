<?php
require_once 'db_connect.php';

// First, check if bookings table exists
$result = $conn->query("SHOW TABLES LIKE 'bookings'");

if ($result->num_rows == 0) {
    // Create bookings table if it doesn't exist
    $sql = "CREATE TABLE bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tourist_id INT NOT NULL,
        guide_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        num_people INT NOT NULL DEFAULT 1,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tourist_id) REFERENCES users(id),
        FOREIGN KEY (guide_id) REFERENCES users(id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Bookings table created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }
} else {
    // Check if num_people column exists
    $result = $conn->query("SHOW COLUMNS FROM bookings LIKE 'num_people'");
    
    if ($result->num_rows == 0) {
        // Add num_people column if it doesn't exist
        $sql = "ALTER TABLE bookings 
                ADD COLUMN num_people INT NOT NULL DEFAULT 1 AFTER end_date";
        
        if ($conn->query($sql) === TRUE) {
            echo "Added num_people column to bookings table";
        } else {
            echo "Error adding column: " . $conn->error;
        }
    } else {
        echo "num_people column already exists in bookings table";
    }
}

$conn->close();
?> 