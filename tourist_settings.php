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
        .settings-section { padding: 100px 0; background-color: #f8f9fa; min-height: 100vh; }
        .settings-card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .profile-image { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; margin-bottom: 20px; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="tourist_dashboard.php">
            <img src="images/logo.png" alt="Logo" height="40" class="me-2">Guide Easy
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="tourist_dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="all_guides.php">Find Guides</a></li>
                <li class="nav-item"><a class="nav-link" href="my_bookings.php">My Bookings</a></li>
                <li class="nav-item"><a class="nav-link active" href="tourist_settings.php">Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Settings Section -->
<section class="settings-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="settings-card">
                    <h2 class="text-center mb-4">Update Profile</h2>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="text-center mb-4">
                            <img src="<?= !empty($tourist['profile_image']) ? $tourist['profile_image'] : 'images/default_profile.jpg' ?>" class="profile-image" alt="Profile Image">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required value="<?= htmlspecialchars($tourist['name']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($tourist['email']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($tourist['phone']) ?>">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

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
