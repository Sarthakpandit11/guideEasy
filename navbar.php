<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo isset($_SESSION['role']) ? ($_SESSION['role'] . '_dashboard.php') : 'index.php'; ?>">
            <img src="images/logo.png" alt="Guide Easy Logo" height="40" class="d-inline-block align-text-top me-2">
            Guide Easy
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <!-- Public Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="destinations.php">Destinations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="nav-link btn btn-outline-light ms-2">Sign In</a>
                    </li>
                <?php elseif ($_SESSION['role'] === 'tourist'): ?>
                    <!-- Tourist Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="tourist_dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tourist_destinations.php">Destinations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tourist_messages.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tourist_settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php elseif ($_SESSION['role'] === 'guide'): ?>
                    <!-- Guide Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="guide_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_messages.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_categories.php">Tour Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php elseif ($_SESSION['role'] === 'admin'): ?>
                    <!-- Admin Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_reports.php">Reports & Analytics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_messages.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav> 