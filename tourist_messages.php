<?php
session_start();
require_once 'db_connect.php';

//left to do
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
        .guide-list {
            height: calc(100vh - 200px);
            overflow-y: auto;
        }
        .guide-item {
            cursor: pointer;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .guide-item:hover {
            background-color: #f8f9fa;
        }
        .guide-item.active {
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
        <div class="row">
            <!-- Guide List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Guides</h5>
                    </div>
                    <div class="guide-list">
                        <?php while ($guide = $guides_result->fetch_assoc()): ?>
                            <div class="guide-item position-relative <?php echo isset($selected_guide) && $selected_guide['id'] == $guide['id'] ? 'active' : ''; ?>"
                                 onclick="window.location.href='tourist_messages.php?guide_id=<?php echo $guide['id']; ?>'">
                                <div class="d-flex align-items-center">
                                    <img src="images/default_guide.jpg" alt="<?php echo htmlspecialchars($guide['name']); ?>" 
                                         class="rounded-circle me-2" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($guide['name']); ?></h6>
                                        <small class="text-muted">Click to chat</small>
                                    </div>
                                </div>
                                <?php if ($guide['unread_count'] > 0): ?>
                                    <span class="badge bg-danger unread-badge"><?php echo $guide['unread_count']; ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8">
                <?php if (isset($selected_guide)): ?>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="images/default_guide.jpg" alt="<?php echo htmlspecialchars($selected_guide['name']); ?>" 
                                     class="rounded-circle me-2" width="40" height="40">
                                <h5 class="mb-0"><?php echo htmlspecialchars($selected_guide['name']); ?></h5>
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
                                <input type="hidden" name="guide_id" value="<?php echo $selected_guide['id']; ?>">
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
                        Select a guide to start chatting
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