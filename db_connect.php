<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "guide_easy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'tourist', 'guide') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

// Check if admin user exists, if not create it
$admin_email = "admin@guideeasy.com";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);


$check_admin = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
$stmt = $conn->prepare($check_admin);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Create admin user
    $create_admin = "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'admin')";
    $stmt = $conn->prepare($create_admin);
    $admin_name = "Admin User";
    $admin_phone = "0000000000";
    $stmt->bind_param("ssss", $admin_name, $admin_email, $admin_phone, $admin_password);
    
    if (!$stmt->execute()) {
        die("Error creating admin user: " . $stmt->error);
    }
}
?> 