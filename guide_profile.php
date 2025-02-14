<?php
session_start();
require_once 'db_connect.php';


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
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?php echo $cover_image; ?>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0 50px;
            margin-top: -20px;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            margin-top: -75px;
            object-fit: cover;
        }
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .skill-badge {
            background: #f8f9fa;
            padding: 5px 15px;
            border-radius: 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-block;
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
            <p class="lead"><?php echo htmlspecialchars($guide['specialization'] ?? 'Professional Guide'); ?></p>
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
                        $skills = explode(',', $guide['skills'] ?? '');
                        foreach ($skills as $skill) {
                            if (trim($skill)) {
                                echo '<span class="skill-badge">' . htmlspecialchars(trim($skill)) . '</span>';
                            }
                        }
                        ?>
                    </div>
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
                            <p class="text-muted">Price per day: $<?php echo number_format($guide['price_per_day'], 2); ?></p>
                        </div>
                        <button type="submit" name="book_guide" class="btn btn-primary w-100">Book Now</button>
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
    </script>
</body>
</html> 