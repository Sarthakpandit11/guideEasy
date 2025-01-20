<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check if it's the default admin login
    if ($email === "admin@guideeasy.com" && $password === "admin123") {
        $_SESSION['user_id'] = 1;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';
        $_SESSION['name'] = 'Admin User';
        header("Location: admin_dashboard.php");
        exit();
    }
    
    // For other users, check database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            
            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'guide':
                    header("Location: guide_dashboard.php");
                    break;
                case 'tourist':
                    header("Location: tourist_dashboard.php");
                    break;
                default:
                    header("Location: index.php");
            }
            exit();
        } else {
            header("Location: login.php?error=invalid_credentials");
            exit();
        }
    } else {
        header("Location: login.php?error=user_not_found");
        exit();
    }
}

$conn->close();
?> 