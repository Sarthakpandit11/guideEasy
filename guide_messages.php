<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db_connect.php';

// Use the guide_easy database
$conn->select_db('guide_easy');

// Check if user is logged in and is a guide
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guide') {
    header("Location: login.php");
    exit();
}

$guide_id = $_SESSION['user_id'];

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['tourist_id'])) {
    $tourist_id = $_POST['tourist_id'];
    $message = $_POST['message'];
    
    $insert_query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iis", $guide_id, $tourist_id, $message);
    $stmt->execute();
    
    // Create notification for the tourist
    $notification_query = "INSERT INTO message_notifications (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $notification_message = "New message from " . $_SESSION['name'];
    $stmt = $conn->prepare($notification_query);
    $stmt->bind_param("iis", $guide_id, $tourist_id, $notification_message);
    $stmt->execute();
    
    header("Location: guide_messages.php?tourist_id=" . $tourist_id);
    exit();
}

// Get selected tourist's messages if tourist_id is provided
$selected_tourist = null;
$messages = [];
if (isset($_GET['tourist_id'])) {
    $tourist_id = $_GET['tourist_id'];
    
    // Get tourist info
    $tourist_query = "SELECT * FROM users WHERE id = ? AND role = 'tourist'";
    $stmt = $conn->prepare($tourist_query);
    $stmt->bind_param("i", $tourist_id);
    $stmt->execute();
    $selected_tourist = $stmt->get_result()->fetch_assoc();
    
    // Get messages with this tourist
    $messages_query = "SELECT m.*, 
                      CASE 
                          WHEN m.sender_id = ? THEN 'sent'
                          ELSE 'received'
                      END as message_type
                      FROM messages m
                      WHERE (m.sender_id = ? AND m.receiver_id = ?)
                      OR (m.sender_id = ? AND m.receiver_id = ?)
                      ORDER BY m.created_at ASC";
    $stmt = $conn->prepare($messages_query);
    $stmt->bind_param("iiiii", $guide_id, $guide_id, $tourist_id, $tourist_id, $guide_id);
    $stmt->execute();
    $messages_result = $stmt->get_result();
    $messages = $messages_result->fetch_all(MYSQLI_ASSOC);
    
    // Mark messages as read
    $update_query = "UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $guide_id, $tourist_id);
    $stmt->execute();
}

// Get all tourists the guide has messaged
$tourists_query = "SELECT DISTINCT u.*, 
                  (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count
                  FROM users u
                  JOIN messages m ON u.id = m.sender_id OR u.id = m.receiver_id
                  WHERE u.role = 'tourist'
                  AND (m.sender_id = ? OR m.receiver_id = ?)
                  GROUP BY u.id
                  ORDER BY unread_count DESC, u.name ASC";
$stmt = $conn->prepare($tourists_query);
$stmt->bind_param("iii", $guide_id, $guide_id, $guide_id);
$stmt->execute();
$tourists_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .message-container {
            height: calc(100vh - 200px);
            overflow-y: auto;
        }
        .message {
            max-width: 70%;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 10px;
        }
        .message.sent {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }
        .message.received {
            background-color: #f8f9fa;
            color: black;
            margin-right: auto;
        }
        .message-time {
            font-size: 0.7rem;
            color: #6c757d;
        }
        .tourist-list {
            height: calc(100vh - 200px);
            overflow-y: auto;
        }
        .tourist-item {
            cursor: pointer;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .tourist-item:hover {
            background-color: #f8f9fa;
        }
        .tourist-item.active {
            background-color: #e9ecef;
        }
        .unread-badge {
            position: absolute;
            top: 5px;
            right: 5px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="guide_dashboard.php">
                <i class="fas fa-compass fa-2x me-2"></i>
                Guide Easy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="guide_dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide_bookings.php">Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="guide_messages.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container" style="margin-top: 80px;">
        <div class="row">
            <!-- Tourist List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tourists</h5>
                    </div>
                    <div class="tourist-list">
                        <?php while ($tourist = $tourists_result->fetch_assoc()): ?>
                            <div class="tourist-item position-relative <?php echo isset($selected_tourist) && $selected_tourist['id'] == $tourist['id'] ? 'active' : ''; ?>"
                                 onclick="window.location.href='guide_messages.php?tourist_id=<?php echo $tourist['id']; ?>'">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($tourist['name']); ?></h6>
                                        <small class="text-muted">Click to chat</small>
                                    </div>
                                </div>
                                <?php if ($tourist['unread_count'] > 0): ?>
                                    <span class="badge bg-danger unread-badge"><?php echo $tourist['unread_count']; ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8">
                <?php if (isset($selected_tourist)): ?>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h5 class="mb-0"><?php echo htmlspecialchars($selected_tourist['name']); ?></h5>
                            </div>
                        </div>
                        <div class="message-container p-3" id="messageContainer">
                            <?php foreach ($messages as $message): ?>
                                <div class="message <?php echo $message['message_type']; ?>">
                                    <div class="message-text"><?php echo htmlspecialchars($message['message']); ?></div>
                                    <div class="message-time">
                                        <?php echo date('M d, Y h:i A', strtotime($message['created_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="card-footer">
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="tourist_id" value="<?php echo $selected_tourist['id']; ?>">
                                <input type="text" name="message" class="form-control me-2" placeholder="Type your message..." required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Select a tourist to start chatting
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll to bottom of messages
        document.addEventListener('DOMContentLoaded', function() {
            const messageContainer = document.getElementById('messageContainer');
            if (messageContainer) {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?> 