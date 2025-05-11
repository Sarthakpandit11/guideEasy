<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a tourist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
    header("Location: login.php");
    exit();
}

// Get guide ID from URL
if (!isset($_GET['id'])) {
    header("Location: tourist_destinations.php");
    exit();
}

$guide_id = $_GET['id'];

// Fetch guide details
$guide_query = "SELECT u.*, gs.*,
                (SELECT AVG(rating) FROM reviews WHERE guide_id = u.id) as avg_rating,
                (SELECT COUNT(*) FROM reviews WHERE guide_id = u.id) as total_reviews
                FROM users u
                LEFT JOIN guide_settings gs ON u.id = gs.user_id
                WHERE u.id = ? AND u.role = 'guide'";
$stmt = $conn->prepare($guide_query);
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$guide_result = $stmt->get_result();

if ($guide_result->num_rows === 0) {
    header("Location: tourist_destinations.php");
    exit();
}

$guide = $guide_result->fetch_assoc();

// Fetch guide's categories (all locations)
$categories = [];
$cat_query = "SELECT gc.name FROM guide_category_mappings gcm JOIN guide_categories gc ON gcm.category_id = gc.id WHERE gcm.guide_id = ?";
$cat_stmt = $conn->prepare($cat_query);
$cat_stmt->bind_param("i", $guide_id);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row['name'];
}
$categories = array_unique($categories); // Remove duplicates

// Handle booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_guide'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $number_of_people = (int)$_POST['number_of_people'];
    $destination = trim($_POST['destination']); // Trim whitespace
    
    // Validate all inputs
    $errors = [];
    
    if (empty($destination)) {
        $errors[] = "Destination is required";
    }
    
    if (empty($start_date)) {
        $errors[] = "Start date is required";
    } elseif (strtotime($start_date) < strtotime('today')) {
        $errors[] = "Start date cannot be in the past";
    }
    
    if (empty($end_date)) {
        $errors[] = "End date is required";
    } elseif (strtotime($end_date) < strtotime($start_date)) {
        $errors[] = "End date must be after start date";
    }
    
    if ($number_of_people < 1) {
        $errors[] = "Number of people must be at least 1";
    }
    
    if (empty($errors)) {
        // Calculate total days and hours
        $total_days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
        $total_hours = $total_days * 8; // Assuming 8 hours per day
        
        // Calculate total cost
        $price_per_day = isset($guide['rate_per_hour']) ? floatval($guide['rate_per_hour']) * 8 : 0;
        $total_cost = $total_days * $price_per_day * $number_of_people;
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert booking
            $sql = "INSERT INTO bookings (tourist_id, guide_id, destination, start_date, end_date, 
                    number_of_people, total_hours, total_days, total_cost, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisssiiid", 
                $_SESSION['user_id'], 
                $guide_id, 
                $destination,
                $start_date, 
                $end_date, 
                $number_of_people,
                $total_hours,
                $total_days,
                $total_cost
            );
            
            if ($stmt->execute()) {
                $booking_id = $conn->insert_id;
                
                // Create notification for the guide
                $message = "New booking request from " . $_SESSION['username'];
                $sql = "INSERT INTO notifications (guide_id, tourist_id, booking_id, message) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiis", $guide_id, $_SESSION['user_id'], $booking_id, $message);
                
                if ($stmt->execute()) {
                    $conn->commit();
                    $_SESSION['success'] = "Your booking has been successfully created!";
                    header("Location: my_bookings.php");
                    exit();
                } else {
                    throw new Exception("Failed to create notification");
                }
            } else {
                throw new Exception("Failed to create booking");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = "An error occurred: " . $e->getMessage();
        }
    } else {
        $error = implode("<br>", $errors);
    }
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'tourist') {
    $rating = intval($_POST['rating'] ?? 0);
    $review_text = trim($_POST['review_text'] ?? '');
    $tourist_id = $_SESSION['user_id'];
    if ($rating > 0 && $rating <= 5 && $review_text !== '') {
        $insert_review = $conn->prepare("INSERT INTO reviews (guide_id, tourist_id, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insert_review->bind_param("iiis", $guide_id, $tourist_id, $rating, $review_text);
        $insert_review->execute();
    }
    // Refresh to show the new review
    header("Location: guide_profile.php?id=$guide_id");
    exit();
}

// Check if this tourist has already reviewed this guide
$has_reviewed = false;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'tourist') {
    $tourist_id = $_SESSION['user_id'];
    $check_review = $conn->prepare("SELECT id FROM reviews WHERE guide_id = ? AND tourist_id = ?");
    $check_review->bind_param("ii", $guide_id, $tourist_id);
    $check_review->execute();
    $check_review->store_result();
    $has_reviewed = $check_review->num_rows > 0;
}

// Fetch all reviews for this guide
$all_reviews = [];
$reviews_stmt = $conn->prepare("SELECT r.*, u.name as tourist_name FROM reviews r JOIN users u ON r.tourist_id = u.id WHERE r.guide_id = ? ORDER BY r.created_at DESC");
$reviews_stmt->bind_param("i", $guide_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
while ($row = $reviews_result->fetch_assoc()) {
    $all_reviews[] = $row;
}

// Set default images if not provided
$profile_image = !empty($guide['profile_picture']) ? $guide['profile_picture'] : 'images/default_profile.png';
$cover_image = !empty($guide['cover_image']) ? $guide['cover_image'] : 'images/default_cover.png';

// Check if guide is unavailable today
$today = date('Y-m-d');
$unavail_stmt = $conn->prepare("SELECT 1 FROM guide_availability WHERE guide_id = ? AND status = 'unavailable' AND date = ? LIMIT 1");
$unavail_stmt->bind_param("is", $guide_id, $today);
$unavail_stmt->execute();
$unavail_stmt->store_result();
$is_unavailable_today = $unavail_stmt->num_rows > 0;

$price_per_day = isset($guide['rate_per_hour']) ? floatval($guide['rate_per_hour']) * 8 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($guide['name']); ?> - Guide Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            color: white;
            padding: 120px 0 40px 0; /* More top padding for fixed navbar and image */
            text-align: center;
            position: relative;
        }
        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid #fff;
            object-fit: cover;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            margin-top: 0;
            background: #fff;
        }
        .profile-header h1 {
            margin-top: 15px;
            font-size: 2.2rem;
            font-weight: 700;
        }
        .profile-header .lead {
            font-size: 1.2rem;
            color: #e0e0e0;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-bottom: 24px;
            padding: 24px;
            background: #fff;
        }
        .card h3 {
            font-size: 1.3rem;
            margin-bottom: 12px;
        }
        .btn-primary, .btn-outline-primary {
            border-radius: 25px;
            padding: 10px 28px;
            font-weight: 600;
            transition: background 0.2s, color 0.2s;
        }
        .btn-primary:hover, .btn-outline-primary:hover {
            background: #4e54c8;
            color: #fff;
        }
        .booking-form input, .booking-form label {
            border-radius: 8px;
        }
        @media (max-width: 768px) {
            .profile-header {
                padding: 40px 0 20px 0;
            }
            .profile-image {
                width: 90px;
                height: 90px;
                margin-top: -45px;
            }
            .card {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Profile Header -->
    <div class="profile-header text-center">
        <div class="container">
            <img src="<?php echo $profile_image; ?>" 
                 alt="<?php echo htmlspecialchars($guide['name']); ?>" class="profile-image">
            <h1 class="mt-3"><?php echo htmlspecialchars($guide['name']); ?></h1>
            <p class="lead">
                <?php echo htmlspecialchars($guide['specialization'] ?? 'Professional Guide'); ?>
            </p>
            <div class="rating mb-3">
                <?php
                $avg_rating = round($guide['avg_rating'] ?? 0, 1);
                $full_stars = floor($avg_rating);
                $half_star = $avg_rating - $full_stars >= 0.5;
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $full_stars) {
                        echo '<i class="fas fa-star text-warning"></i>';
                    } elseif ($i == $full_stars + 1 && $half_star) {
                        echo '<i class="fas fa-star-half-alt text-warning"></i>';
                    } else {
                        echo '<i class="far fa-star text-warning"></i>';
                    }
                }
                ?>
                <span class="ms-2"><?php echo $avg_rating; ?> (<?php echo $guide['total_reviews'] ?? 0; ?> reviews)</span>
            </div>
            <?php if ($is_unavailable_today): ?>
                <span class="badge bg-danger fs-6 mb-2">Unavailable Today</span>
            <?php else: ?>
                <span class="badge bg-success fs-6 mb-2">Available</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- About Section -->
                <div class="info-card">
                    <h3>About</h3>
                    <p><?php echo htmlspecialchars($guide['bio'] ?? 'No bio available'); ?></p>
                    <div class="mb-2">
                        <strong>Languages:</strong> <?php echo htmlspecialchars($guide['languages'] ?? 'Not specified'); ?>
                    </div>
                    <div class="mb-2">
                        <strong>Tour Categories:</strong>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <span class="badge bg-info text-dark mb-1"><?php echo htmlspecialchars($cat); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">No categories set</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Experience Section -->
                <div class="info-card">
                    <h3>Experience</h3>
                    <p><?php echo htmlspecialchars($guide['experience'] ?? 'No experience details available'); ?></p>
                </div>

                <!-- Skills Section -->
                <div class="info-card">
                    <h3>Skills & Specializations</h3>
                    <div>
                        <?php
                        $skills = array_filter(array_map('trim', explode(',', $guide['skills'] ?? '')));
                        if (!empty($skills)) {
                            foreach ($skills as $skill) {
                                echo '<span class="badge bg-secondary me-1 mb-1">' . htmlspecialchars($skill) . '</span>';
                            }
                        } else {
                            echo '<span class="text-muted">No skills or specializations listed.</span>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="info-card">
                    <h3>Reviews</h3>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'tourist' && !$has_reviewed): ?>
                        <form method="POST" class="mb-4">
                            <div class="mb-2">
                                <label for="rating" class="form-label"><strong>Your Rating:</strong></label><br>
                                <span id="star-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" style="display:none;">
                                        <label for="star<?php echo $i; ?>" style="font-size:2rem; color:#ffc107; cursor:pointer;">&#9733;</label>
                                    <?php endfor; ?>
                                </span>
                            </div>
                            <div class="mb-2">
                                <label for="review_text" class="form-label"><strong>Your Review:</strong></label>
                                <textarea name="review_text" id="review_text" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                        </form>
                        <script>
                        // Star rating highlight
                        document.querySelectorAll('#star-rating label').forEach(function(label, idx) {
                            label.addEventListener('mouseover', function() {
                                for (let i = 0; i <= idx; i++) {
                                    document.querySelectorAll('#star-rating label')[i].style.color = '#ffc107';
                                }
                                for (let i = idx + 1; i < 5; i++) {
                                    document.querySelectorAll('#star-rating label')[i].style.color = '#e4e5e9';
                                }
                            });
                            label.addEventListener('mouseout', function() {
                                let checked = document.querySelector('#star-rating input:checked');
                                let val = checked ? parseInt(checked.value) : 0;
                                for (let i = 0; i < 5; i++) {
                                    document.querySelectorAll('#star-rating label')[i].style.color = (i < val) ? '#ffc107' : '#e4e5e9';
                                }
                            });
                            label.addEventListener('click', function() {
                                document.getElementById('star' + (idx + 1)).checked = true;
                            });
                        });
                        </script>
                    <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'tourist'): ?>
                        <div class="alert alert-info">You have already reviewed this guide.</div>
                    <?php endif; ?>
                    <?php if (!empty($all_reviews)): ?>
                        <?php foreach ($all_reviews as $review): ?>
                            <div class="border rounded p-2 mb-2">
                                <div class="d-flex align-items-center mb-1">
                                    <strong><?php echo htmlspecialchars($review['tourist_name']); ?></strong>
                                    <span class="ms-2 text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa<?php echo $i <= $review['rating'] ? 's' : 'r'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </span>
                                    <span class="ms-2 text-muted" style="font-size:0.9em;">
                                        <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                    </span>
                                </div>
                                <div><?php echo nl2br(htmlspecialchars($review['review'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted">No reviews yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Message Button -->
                <div class="info-card mb-4">
                    <a href="tourist_messages.php?guide_id=<?php echo $guide_id; ?>" class="btn btn-primary w-100">
                        <i class="fas fa-envelope me-2"></i> Message Guide
                    </a>
                </div>

                <!-- Booking Form -->
                <div class="info-card">
                    <h3>Book This Guide</h3>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="destination" name="destination" 
                                   value="<?php echo isset($_POST['destination']) ? htmlspecialchars($_POST['destination']) : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="number_of_people" class="form-label">Number of People <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="number_of_people" name="number_of_people" 
                                   value="<?php echo isset($_POST['number_of_people']) ? htmlspecialchars($_POST['number_of_people']) : '1'; ?>" 
                                   min="1" required>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted">Price per day: $<?php echo number_format($price_per_day, 2); ?></p>
                        </div>
                        <input type="hidden" id="price_per_day" value="<?php echo $price_per_day; ?>">
                        <div id="booking-summary" class="alert alert-info" style="display:none;">
                            <span id="summary-days"></span> days, 
                            <span id="summary-hours"></span> hours, 
                            <span id="summary-people"></span> people<br>
                            <strong>Total: $<span id="summary-total"></span></strong>
                        </div>
                        <button type="submit" id="book-btn" name="book_guide" class="btn btn-primary w-100" disabled>Book Now</button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="info-card">
                    <h3>Contact Information</h3>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($guide['email']); ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($guide['phone'] ?? 'Not provided'); ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($guide['location'] ?? 'Not specified'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').min = today;
        document.getElementById('end_date').min = today;

        // Update end date minimum when start date changes
        document.getElementById('start_date').addEventListener('change', function() {
            document.getElementById('end_date').min = this.value;
        });

        // Live price calculation
        function updateBookingSummary() {
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            const people = parseInt(document.getElementById('number_of_people').value) || 1;
            const pricePerDay = parseFloat(document.getElementById('price_per_day').value);
            const bookBtn = document.getElementById('book-btn');

            if (start && end && pricePerDay > 0) {
                const startDate = new Date(start);
                const endDate = new Date(end);
                if (endDate >= startDate) {
                    const days = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                    const hours = days * 8; // Assuming 8 hours per day
                    const total = days * pricePerDay * people;

                    document.getElementById('summary-days').textContent = days;
                    document.getElementById('summary-hours').textContent = hours;
                    document.getElementById('summary-people').textContent = people;
                    document.getElementById('summary-total').textContent = total.toFixed(2);
                    document.getElementById('booking-summary').style.display = '';
                    bookBtn.disabled = false;
                } else {
                    document.getElementById('booking-summary').style.display = 'none';
                    bookBtn.disabled = true;
                }
            } else {
                document.getElementById('booking-summary').style.display = 'none';
                bookBtn.disabled = true;
            }
        }

        document.getElementById('start_date').addEventListener('change', updateBookingSummary);
        document.getElementById('end_date').addEventListener('change', updateBookingSummary);
        document.getElementById('number_of_people').addEventListener('input', updateBookingSummary);
    </script>
</body>
</html> 