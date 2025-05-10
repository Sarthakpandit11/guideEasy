<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Mark notifications as read when viewing the page
if ($user_role === 'guide') {
    $mark_read_query = "UPDATE notifications SET is_read = 1 WHERE guide_id = ?";
} else {
    $mark_read_query = "UPDATE notifications SET is_read = 1 WHERE tourist_id = ?";
}
$stmt = $conn->prepare($mark_read_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_sql = '';
$search_params = [];
if ($search !== '') {
    $search_sql = " AND (n.message LIKE ? OR u.name LIKE ? OR b.destination LIKE ?)";
    $search_term = "%$search%";
    $search_params = [$search_term, $search_term, $search_term];
}

// Get notifications based on user role
if ($user_role === 'guide') {
    $notifications_query = "SELECT n.*, 
                          u.name as tourist_name,
                          b.destination,
                          b.start_date,
                          b.end_date
                   FROM notifications n
                   LEFT JOIN users u ON n.tourist_id = u.id
                   LEFT JOIN bookings b ON n.booking_id = b.id
                   WHERE n.guide_id = ?" . $search_sql . "
                   ORDER BY n.created_at DESC";
    $params = array_merge([$user_id], $search_params);
} else {
    $notifications_query = "SELECT n.*, 
                          u.name as guide_name,
                          b.destination,
                          b.start_date,
                          b.end_date
                   FROM notifications n
                   LEFT JOIN users u ON n.guide_id = u.id
                   LEFT JOIN bookings b ON n.booking_id = b.id
                   WHERE n.tourist_id = ?" . $search_sql . "
                   ORDER BY n.created_at DESC";
    $params = array_merge([$user_id], $search_params);
}

$stmt = $conn->prepare($notifications_query);
// Dynamically bind params
$types = str_repeat('s', count($params));
$types[0] = 'i'; // first param is always user_id (int)
$stmt->bind_param($types, ...$params);
$stmt->execute();
$notifications_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .notification-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #007bff;
        }
        .notification-card.unread {
            border-left-color: #28a745;
            background-color: #f8f9fa;
        }
        .notification-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .booking-details {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .search-bar {
            max-width: 350px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $user_role === 'guide' ? 'guide_dashboard.php' : 'tourist_dashboard.php'; ?>">
                <img src="images/logo.png" alt="Guide Easy Logo" height="40" class="d-inline-block align-text-top me-2">
                Guide Easy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $user_role === 'guide' ? 'guide_dashboard.php' : 'tourist_dashboard.php'; ?>">Home</a>
                    </li>
                    <?php if ($user_role === 'tourist'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="tourist_destinations.php">Destinations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my_bookings.php">My Bookings</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="guide_bookings.php">My Bookings</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container" style="margin-top: 80px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Notifications</h2>
            <a href="clear_notifications.php" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-trash"></i> Clear All
            </a>
        </div>
        <form class="mb-4 d-flex" method="get" action="">
            <input type="text" name="search" class="form-control me-2 search-bar" placeholder="Search notifications..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php if ($notifications_result->num_rows > 0): ?>
            <div class="notifications-list">
                <?php while ($notification = $notifications_result->fetch_assoc()): ?>
                    <div class="notification-card <?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                        <div class="d-flex">
                            <div class="notification-icon">
                                <?php if ($user_role === 'guide'): ?>
                                    <i class="fas fa-user-tie text-primary"></i>
                                <?php else: ?>
                                    <i class="fas fa-user text-primary"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="mb-1">
                                        <?php if ($user_role === 'guide'): ?>
                                            <?php echo htmlspecialchars($notification['tourist_name']); ?>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($notification['guide_name']); ?>
                                        <?php endif; ?>
                                    </h5>
                                    <small class="notification-time">
                                        <?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?>
                                    </small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                
                                <?php if ($notification['booking_id']): ?>
                                    <div class="booking-details">
                                        <p class="mb-0">
                                            <strong>Destination:</strong> <?php echo htmlspecialchars($notification['destination']); ?><br>
                                            <strong>Dates:</strong> <?php echo date('M d, Y', strtotime($notification['start_date'])); ?> to 
                                            <?php echo date('M d, Y', strtotime($notification['end_date'])); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                No notifications found.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 