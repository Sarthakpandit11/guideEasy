<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a tourist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tourist = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone'];
    $user_id = $_SESSION['user_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update users table
        $update_users_query = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        $update_users_stmt = $conn->prepare($update_users_query);
        $update_users_stmt->bind_param("sssi", $full_name, $email, $phone_number, $user_id);
        $update_success = $update_users_stmt->execute();

        if (!$update_success) {
            throw new Exception("User table update failed: " . $update_users_stmt->error);
        }

        // Insert into update_profile
        $insert_profile_query = "INSERT INTO update_profile (user_id, full_name, email, phone_number) 
                               VALUES (?, ?, ?, ?)";
        $insert_profile_stmt = $conn->prepare($insert_profile_query);
        $insert_profile_stmt->bind_param("isss", $user_id, $full_name, $email, $phone_number);
        $insert_success = $insert_profile_stmt->execute();

        if (!$insert_success) {
            throw new Exception("Insert into update_profile failed: " . $insert_profile_stmt->error);
        }

        $conn->commit();
        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: tourist_settings.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header("Location: tourist_settings.php");
        exit();
    }
}

// Show session messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Fetch latest update_profile info
$latest_update_query = "SELECT * FROM update_profile WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1";
$latest_update_stmt = $conn->prepare($latest_update_query);
$latest_update_stmt->bind_param("i", $user_id);
$latest_update_stmt->execute();
$latest_update_result = $latest_update_stmt->get_result();
$latest_update = $latest_update_result->fetch_assoc();

if ($latest_update) {
    $tourist['name'] = $latest_update['full_name'];
    $tourist['email'] = $latest_update['email'];
    $tourist['phone'] = $latest_update['phone_number'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tourist Settings - Guide Easy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .curvy-navbar-wrapper {
            position: relative;
            z-index: 10;
        }
        .curvy-navbar-bg {
            position: absolute;
            left: 0; right: 0; top: 0;
            width: 100%;
            height: 110px;
            pointer-events: none;
        }
        .custom-navbar {
            background: rgba(20, 30, 40, 0.7);
            backdrop-filter: blur(8px);
            border: none;
            box-shadow: none;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }
        .custom-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.7rem;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
        }
        .custom-navbar .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }
        .custom-navbar .navbar-brand .site-name {
            color: #fff;
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 1.5px;
        }
        .custom-navbar .navbar-nav .nav-link {
            color: #fff;
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 1.5px;
            margin-left: 1.2rem;
            margin-right: 1.2rem;
            font-size: 1.05rem;
            transition: color 0.2s;
        }
        .custom-navbar .navbar-nav .nav-link.active,
        .custom-navbar .navbar-nav .nav-link:focus,
        .custom-navbar .navbar-nav .nav-link:hover {
            color: #FF6B4A;
        }
        .custom-navbar .navbar-nav .nav-link:last-child {
            margin-right: 0;
        }
        .custom-navbar .navbar-toggler {
            border: none;
        }
        .custom-navbar .navbar-toggler:focus {
            box-shadow: none;
        }
        .profile-card {
            background: linear-gradient(135deg, #f8fafc 80%, #e3e9f7 100%);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(20,30,40,0.10);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            max-width: 500px;
            margin: 60px auto 0 auto;
            border: 2.5px solid #FF6B4A;
        }
        .profile-card h2 {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .profile-card label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .profile-card .form-control {
            border-radius: 18px;
            border: 1.5px solid #e0e7ff;
            padding: 12px 18px;
            font-size: 1.08rem;
            margin-bottom: 1.2rem;
            background: #f1f5f9;
            box-shadow: 0 2px 8px rgba(79, 140, 255, 0.04);
        }
        .profile-card .form-control:focus {
            border-color: #4f8cff;
            box-shadow: 0 0 0 2px #4f8cff33;
        }
        .profile-card .btn-update {
            border-radius: 22px;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 12px 32px;
            border: none;
            background: linear-gradient(90deg, #4f8cff 60%, #6be0ff 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(79, 140, 255, 0.10);
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
            display: block;
            margin: 0 auto;
        }
        .profile-card .btn-update:hover {
            background: linear-gradient(90deg, #2563eb 60%, #38bdf8 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 4px 16px rgba(79, 140, 255, 0.18);
        }
        .profile-image-preview {
            display: block;
            margin: 0 auto 1.2rem auto;
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 2.5px solid #4f8cff;
            box-shadow: 0 2px 8px rgba(79, 140, 255, 0.10);
        }
    </style>
</head>
<body>
    <div class="curvy-navbar-wrapper">
        <nav class="navbar navbar-expand-lg custom-navbar fixed-top">
            <div class="container">
                <a class="navbar-brand" href="tourist_dashboard.php">
                    <img src="images/logo.png" alt="Guide Easy Logo">
                    <span class="site-name">Guide Easy</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="tourist_dashboard.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="all_guides.php">Find Guides</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my_bookings.php">My Bookings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="tourist_settings.php">Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- SVG for curvy bottom -->
        <svg class="curvy-navbar-bg" viewBox="0 0 1440 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,0 H1440 V60 Q1200,110 720,80 Q240,50 0,110 Z" fill="rgba(20,30,40,0.7)"/>
        </svg>
    </div>
    <div class="profile-card">
        <h2 class="text-center">Update Profile</h2>
        <img src="<?php echo !empty($tourist['profile_image']) ? $tourist['profile_image'] : 'images/default_profile.jpg'; ?>" alt="Profile Image" class="profile-image-preview">
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($tourist['name']); ?>" required>
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($tourist['email']); ?>" required>
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($tourist['phone']); ?>" required>
            <button type="submit" class="btn btn-update">Update Profile</button>
        </form>
    </div>

<!-- Footer -->
<footer class="bg-dark text-light py-4 mt-auto">
    <div class="container text-center">
        <p class="mb-0">&copy; 2024 Guide Easy. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
