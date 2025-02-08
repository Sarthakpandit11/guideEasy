<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get all messages with sender and receiver information
$messages_query = "SELECT m.*, 
                  s.name as sender_name, s.email as sender_email,
                  r.name as receiver_name, r.email as receiver_email
                  FROM messages m
                  LEFT JOIN users s ON m.sender_id = s.id
                  LEFT JOIN users r ON m.receiver_id = r.id
                  ORDER BY m.created_at DESC";
$messages_result = $conn->query($messages_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Guide Easy</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-container {
            margin-top: 80px;
            padding: 20px;
        }
        .messages-table {
            margin-top: 30px;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .message-content {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        .message-content-box {
            white-space: pre-wrap;
            word-wrap: break-word;
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
                        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_reports.php">Reports & Analytics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_messages.php">Messages</a>
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

    <!-- Messages Content -->
    <div class="admin-wrapper">
        <div class="dashboard-container">
            <div class="container">
                <h2 class="mb-4">Messages</h2>
                
                <!-- Messages Table -->
                <div class="messages-table">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Preview</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($message = $messages_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($message['sender_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($message['sender_email']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($message['receiver_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($message['receiver_email']); ?></small>
                                    </td>
                                    <td class="message-content" title="<?php echo htmlspecialchars($message['message']); ?>">
                                        <?php echo substr(htmlspecialchars($message['message']), 0, 50) . '...'; ?>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $message['is_read'] ? 'success' : 'warning'; 
                                        ?>">
                                            <?php echo $message['is_read'] ? 'Read' : 'Unread'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" 
                                                data-bs-target="#messageModal<?php echo $message['id']; ?>">
                                            View Message
                                        </button>
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
        <footer class="fixed-footer">
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

    <!-- Modals -->
    <?php 
    // Reset the result pointer
    $messages_result->data_seek(0);
    while ($message = $messages_result->fetch_assoc()): 
    ?>
    <div class="modal fade" id="messageModal<?php echo $message['id']; ?>" tabindex="-1" aria-labelledby="messageModalLabel<?php echo $message['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel<?php echo $message['id']; ?>">Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Sender Information</h6>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($message['sender_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($message['sender_email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Receiver Information</h6>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($message['receiver_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($message['receiver_email']); ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6>Message Content</h6>
                            <div class="message-content-box p-3 bg-light rounded">
                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Sent:</strong> <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?php 
                                    echo $message['is_read'] ? 'success' : 'warning'; 
                                ?>">
                                    <?php echo $message['is_read'] ? 'Read' : 'Unread'; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?> 