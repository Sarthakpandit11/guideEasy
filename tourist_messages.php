<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a tourist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
    header("Location: login.php");
    exit();
}

$tourist_id = $_SESSION['user_id'];

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['guide_id'])) {
    $guide_id = $_POST['guide_id'];
    $message = $_POST['message'];
    
    $insert_query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iis", $tourist_id, $guide_id, $message);
    $stmt->execute();
    
    // Create notification for the guide
    $notification_query = "INSERT INTO notifications (guide_id, tourist_id, message) VALUES (?, ?, ?)";
    $notification_message = "New message from " . $_SESSION['username'];
    $stmt = $conn->prepare($notification_query);
    $stmt->bind_param("iis", $guide_id, $tourist_id, $notification_message);
    $stmt->execute();
    
    header("Location: tourist_messages.php?guide_id=" . $guide_id);
    exit();
}

// Get selected guide's messages if guide_id is provided
$selected_guide = null;
$messages = [];
if (isset($_GET['guide_id'])) {
    $guide_id = $_GET['guide_id'];
    
    // Get guide info
    $guide_query = "SELECT * FROM users WHERE id = ? AND role = 'guide'";
    $stmt = $conn->prepare($guide_query);
    $stmt->bind_param("i", $guide_id);
    $stmt->execute();
    $selected_guide = $stmt->get_result()->fetch_assoc();
    
    // Get messages with this guide
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
    $stmt->bind_param("iiiii", $tourist_id, $tourist_id, $guide_id, $guide_id, $tourist_id);
    $stmt->execute();
    $messages_result = $stmt->get_result();
    $messages = $messages_result->fetch_all(MYSQLI_ASSOC);
    
    // Mark messages as read
    $update_query = "UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $tourist_id, $guide_id);
    $stmt->execute();
}

// Get all guides the tourist has messaged
$guides_query = "SELECT DISTINCT u.*, 
                (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count
                FROM users u
                JOIN messages m ON u.id = m.sender_id OR u.id = m.receiver_id
                WHERE u.role = 'guide'
                AND (m.sender_id = ? OR m.receiver_id = ?)
                GROUP BY u.id
                ORDER BY unread_count DESC, u.name ASC";
$stmt = $conn->prepare($guides_query);
$stmt->bind_param("iii", $tourist_id, $tourist_id, $tourist_id);
$stmt->execute();
$guides_result = $stmt->get_result();
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
        body {
            background: #f0f2f5;
        }
        .chat-container {
            display: flex;
            height: 75vh;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            background: #fff;
            margin-top: 30px;
        }
        .user-list {
            width: 280px;
            background: #f5f6fa;
            border-right: 1px solid #e4e6eb;
            padding: 0;
            margin: 0;
            list-style: none;
            overflow-y: auto;
        }
        .user-list .guide-item {
            display: flex;
            align-items: center;
            padding: 16px;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid #e4e6eb;
            background: none;
        }
        .user-list .guide-item.active,
        .user-list .guide-item:hover {
            background: #e7f3ff;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #2563eb;
            margin-right: 12px;
            font-size: 20px;
        }
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f0f2f5;
            padding: 0;
        }
        .chat-header {
            padding: 18px 24px;
            background: #fff;
            border-bottom: 1px solid #e4e6eb;
            font-weight: bold;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .message-row {
            display: flex;
            align-items: flex-end;
        }
        .message-row.sent {
            justify-content: flex-end;
        }
        .message-bubble {
            max-width: 60%;
            padding: 12px 18px;
            border-radius: 20px;
            background: #e4e6eb;
            color: #1a1a1a;
            font-size: 16px;
            margin-bottom: 2px;
            position: relative;
            word-break: break-word;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .message-row.sent .message-bubble {
            background: #0084ff;
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        .message-row.received .message-bubble {
            background: #e4e6eb;
            color: #1a1a1a;
            border-bottom-left-radius: 4px;
        }
        .message-meta {
            font-size: 12px;
            color: #888;
            margin: 0 8px;
            margin-bottom: 8px;
            text-align: right;
        }
        .message-input-container {
            display: flex;
            padding: 16px 24px;
            background: #fff;
            border-top: 1px solid #e4e6eb;
        }
        .message-input {
            flex: 1;
            border: none;
            border-radius: 20px;
            padding: 10px 16px;
            font-size: 16px;
            background: #f1f5f9;
            margin-right: 8px;
            outline: none;
        }
        .send-btn {
            background: #0084ff;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .send-btn:hover {
            background: #005bb5;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="tourist_dashboard.php">
                <img src="images/logo.png" alt="Guide Easy Logo" height="40" class="d-inline-block align-text-top me-2">
                Guide Easy
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
                        <a class="nav-link" href="tourist_destinations.php">Destinations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="tourist_messages.php">Messages</a>
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
        <div class="chat-container">
            <!-- Sidebar: Guide List -->
            <div class="user-list">
                <?php while ($guide = $guides_result->fetch_assoc()): ?>
                    <div class="guide-item<?php echo isset($selected_guide) && $selected_guide['id'] == $guide['id'] ? ' active' : ''; ?>"
                         onclick="window.location.href='tourist_messages.php?guide_id=<?php echo $guide['id']; ?>'">
                        <img src="images/default_guide.jpg" alt="<?php echo htmlspecialchars($guide['name']); ?>" class="user-avatar">
                        <div>
                            <div style="font-weight:600;"><?php echo htmlspecialchars($guide['name']); ?></div>
                            <small class="text-muted">Click to chat</small>
                        </div>
                        <?php if ($guide['unread_count'] > 0): ?>
                            <span class="badge bg-danger unread-badge"><?php echo $guide['unread_count']; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <!-- Chat Area -->
            <div class="chat-area">
                <?php if ($selected_guide): ?>
                    <div class="chat-header">
                        <img src="images/default_guide.jpg" alt="<?php echo htmlspecialchars($selected_guide['name']); ?>" class="user-avatar me-2">
                        <?php echo htmlspecialchars($selected_guide['name']); ?>
                    </div>
                    <div class="messages">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-row <?php echo $msg['message_type']; ?>">
                                <div>
                                    <div class="message-bubble">
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                    </div>
                                    <div class="message-meta">
                                        <?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form class="message-input-container" method="POST" action="">
                        <input type="hidden" name="guide_id" value="<?php echo $selected_guide['id']; ?>">
                        <input class="message-input" type="text" name="message" placeholder="Type your message..." required />
                        <button class="send-btn" type="submit">Send</button>
                    </form>
                <?php else: ?>
                    <div class="d-flex flex-column justify-content-center align-items-center h-100 w-100">
                        <div class="text-muted">Select a guide to start chatting.</div>
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