<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle user approval/cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guide_action'], $_POST['guide_id'])) {
    $user_id = intval($_POST['guide_id']);
    $action = $_POST['guide_action'];
    if ($action === 'approve') {
        $conn->query("UPDATE users SET status = 'approved' WHERE id = $user_id");
    } elseif ($action === 'cancel') {
        $conn->query("UPDATE users SET status = 'cancelled' WHERE id = $user_id");
    }
    // Refresh to avoid resubmission
    header("Location: admin_dashboard.php");
    exit();
}

// Get user statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_guides = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'guide'")->fetch_assoc()['count'];
$total_tourists = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'tourist'")->fetch_assoc()['count'];

// Get all users
$users_query = "SELECT * FROM users ORDER BY created_at DESC";
$users_result = $conn->query($users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Guide Easy</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-container {
            margin-top: 80px;
            padding: 20px;
        }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .users-table {
            margin-top: 30px;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Guide Easy Logo" height="40" class="d-inline-block align-text-top me-2">
                Guide Easy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">Dashboard</a>
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="admin-wrapper">
        <div class="dashboard-container">
            <div class="container">
                <h2 class="mb-4">Admin Dashboard</h2>
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card bg-primary text-white">
                            <i class="fas fa-users"></i>
                            <h3><?php echo $total_users; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-success text-white">
                            <i class="fas fa-user-tie"></i>
                            <h3><?php echo $total_guides; ?></h3>
                            <p>Total Guides</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-info text-white">
                            <i class="fas fa-user"></i>
                            <h3><?php echo $total_tourists; ?></h3>
                            <p>Total Tourists</p>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="users-table">
                    <h3 class="mb-3">All Users</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Photo Verification</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($user = $users_result->fetch_assoc()): ?>
                                <tr id="user-row-<?php echo $user['id']; ?>">
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $user['role'] === 'admin' ? 'danger' : 
                                                ($user['role'] === 'guide' ? 'success' : 'info'); 
                                        ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['status'] === 'pending'): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="guide_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="guide_action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                                <button type="submit" name="guide_action" value="cancel" class="btn btn-danger btn-sm">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                        <span class="badge bg-<?php 
                                            echo $user['status'] === 'approved' ? 'success' : 
                                                ($user['status'] === 'pending' ? 'warning' : 'danger'); 
                                        ?>">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['role'] === 'tourist' && !empty($user['profile_photo'])): ?>
                                            <a href="#" class="photo-popup" data-img="<?php echo htmlspecialchars($user['profile_photo']); ?>">
                                                <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" style="width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid #ccc; cursor:pointer;">
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-outline-danger btn-sm delete-user-btn" data-user-id="<?php echo $user['id']; ?>">Delete</button>
                                        <button class="btn btn-outline-primary btn-sm mail-user-btn" data-user-id="<?php echo $user['id']; ?>" data-user-email="<?php echo htmlspecialchars($user['email']); ?>" data-user-name="<?php echo htmlspecialchars($user['name']); ?>">Mail</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fixed Footer -->
        <footer class="bg-dark text-light py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Guide Easy</h5>
                        <p>Your trusted companion for exploring Nepal's wonders.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p>&copy; 2024 Guide Easy. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Profile Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPhoto" src="" alt="Profile Photo" style="max-width:100%; max-height:70vh; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.2);">
                </div>
            </div>
        </div>
    </div>

    <!-- Mail Modal -->
    <div class="modal fade" id="mailModal" tabindex="-1" aria-labelledby="mailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="send_mail.php" id="mailForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mailModalLabel">Send Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="mailUserId">
                        <div class="mb-3">
                            <label for="mailTo" class="form-label">To</label>
                            <input type="email" class="form-control" id="mailTo" name="to" readonly required>
                        </div>
                        <div class="mb-3">
                            <label for="mailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="mailSubject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="mailMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="mailMessage" name="message" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-user-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this user?')) {
                    const userId = this.getAttribute('data-user-id');
                    fetch('delete_user.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'user_id=' + encodeURIComponent(userId)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('user-row-' + userId).remove();
                        } else {
                            alert('Failed to delete user.' + (data.error ? '\n' + data.error : ''));
                        }
                    })
                    .catch(() => alert('Failed to delete user.'));
                }
            });
        });

        document.querySelectorAll('.photo-popup').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                var imgSrc = this.getAttribute('data-img');
                document.getElementById('modalPhoto').src = imgSrc;
                var modal = new bootstrap.Modal(document.getElementById('photoModal'));
                modal.show();
            });
        });

        document.querySelectorAll('.mail-user-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('mailUserId').value = this.getAttribute('data-user-id');
                document.getElementById('mailTo').value = this.getAttribute('data-user-email');
                document.getElementById('mailSubject').value = '';
                document.getElementById('mailMessage').value = '';
                var modal = new bootstrap.Modal(document.getElementById('mailModal'));
                modal.show();
            });
        });
    });
    </script>
</body>
</html>
<?php
$conn->close();
?> 