<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=password_mismatch");
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register.php?error=email_exists");
        exit();
    }

    $profile_photo_path = null;
    // Only guides require admin approval and photo upload
    if ($role === 'guide') {
        $status = 'pending';
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid('guide_', true) . '.' . $file_ext;
            $upload_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                $profile_photo_path = $upload_path;
            }
        }
        if (!$profile_photo_path) {
            header("Location: register.php?error=photo_upload");
            exit();
        }
    } else {
        $status = 'approved';
        // Optional: handle tourist photo upload if you want
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid('tourist_', true) . '.' . $file_ext;
            $upload_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                $profile_photo_path = $upload_path;
            }
        }
    }

    // Insert user data into database
    if ($role === 'guide') {
        $sql = "INSERT INTO users (name, email, phone, password, role, status, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $name, $email, $phone, $hashed_password, $role, $status, $profile_photo_path);
    } else {
        $sql = "INSERT INTO users (name, email, phone, password, role, status, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $name, $email, $phone, $hashed_password, $role, $status, $profile_photo_path);
    }

    if ($stmt->execute()) {
        // Registration successful
        header("Location: login.php?success=registered");
        exit();
    } else {
        // Registration failed
        header("Location: register.php?error=registration_failed");
        exit();
    }
}

$conn->close();
?> 