<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a guide
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guide') {
    header("Location: login.php");
    exit();
}

$guide_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Fetch guide's current information
$guide_query = "SELECT u.*, gs.* 
                FROM users u
                LEFT JOIN guide_settings gs ON u.id = gs.user_id
                WHERE u.id = ?";
$guide_stmt = $conn->prepare($guide_query);
$guide_stmt->bind_param("i", $guide_id);
$guide_stmt->execute();
$guide = $guide_stmt->get_result()->fetch_assoc();

// After the guide query, add this to fetch categories and guide's current categories
$categories_query = "SELECT * FROM guide_categories ORDER BY name";
$categories_result = $conn->query($categories_query);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch guide's current categories for each location
$guide_categories_query = "SELECT category_id, location FROM guide_category_mappings WHERE guide_id = ?";
$guide_categories_stmt = $conn->prepare($guide_categories_query);
$guide_categories_stmt->bind_param("i", $guide_id);
$guide_categories_stmt->execute();
$guide_categories_result = $guide_categories_stmt->get_result();
$guide_categories = [];
while ($row = $guide_categories_result->fetch_assoc()) {
    $guide_categories[$row['location']][] = $row['category_id'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone_number = $_POST['phone_number'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $specialization = $_POST['specialization'] ?? '';
        $rate_per_hour = $_POST['rate_per_hour'] ?? 0;
        $languages = $_POST['languages'] ?? '';
        $experience = $_POST['experience'] ?? '';
        $location = $_POST['location'] ?? '';

        try {
            $conn->begin_transaction();

            // Update users table
            $update_user_query = "UPDATE users SET 
                                name = ?, 
                                email = ?, 
                                phone = ?
                                WHERE id = ?";
            $update_user_stmt = $conn->prepare($update_user_query);
            $update_user_stmt->bind_param("sssi", 
                $full_name, $email, $phone_number, $guide_id);
            $update_user_stmt->execute();

            // Check if guide settings exist
            $check_query = "SELECT id FROM guide_settings WHERE user_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("i", $guide_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                // Update existing guide settings
                $update_settings_query = "UPDATE guide_settings SET 
                                        bio = ?,
                                        specialization = ?,
                                        rate_per_hour = ?,
                                        languages = ?,
                                        experience = ?,
                                        location = ?
                                        WHERE user_id = ?";
                $update_settings_stmt = $conn->prepare($update_settings_query);
                $update_settings_stmt->bind_param("ssdsssi", 
                    $bio, $specialization, $rate_per_hour, $languages, $experience, $location, $guide_id);
                $update_settings_stmt->execute();
            } else {
                // Insert new guide settings
                $insert_settings_query = "INSERT INTO guide_settings 
                                        (user_id, bio, specialization, rate_per_hour, languages, experience, location) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insert_settings_stmt = $conn->prepare($insert_settings_query);
                $insert_settings_stmt->bind_param("issdsss", 
                    $guide_id, $bio, $specialization, $rate_per_hour, $languages, $experience, $location);
                $insert_settings_stmt->execute();
            }

            // Handle profile picture upload if provided
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/profile_pictures/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
                $new_filename = 'guide_' . $guide_id . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                    $update_picture_query = "UPDATE guide_settings SET profile_picture = ? WHERE user_id = ?";
                    $update_picture_stmt = $conn->prepare($update_picture_query);
                    $update_picture_stmt->bind_param("si", $upload_path, $guide_id);
                    $update_picture_stmt->execute();
                }
            }

            // Handle category updates
            if (isset($_POST['categories'])) {
                // Delete existing category mappings for this guide and location
                $delete_categories = "DELETE FROM guide_category_mappings WHERE guide_id = ? AND location = ?";
                $delete_stmt = $conn->prepare($delete_categories);
                $delete_stmt->bind_param("is", $guide_id, $location);
                $delete_stmt->execute();

                // Insert new category mappings for the current location
                if (isset($_POST['categories'][$location]) && is_array($_POST['categories'][$location])) {
                    foreach ($_POST['categories'][$location] as $category_id) {
                        $insert_category = "INSERT INTO guide_category_mappings (guide_id, category_id, location) VALUES (?, ?, ?)";
                        $insert_stmt = $conn->prepare($insert_category);
                        $insert_stmt->bind_param("iis", $guide_id, $category_id, $location);
                        $insert_stmt->execute();
                    }
                }
            }

            $conn->commit();
            $success_message = "Profile updated successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error updating profile: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_availability'])) {
        try {
            $conn->begin_transaction();

            // Get the date range from the form
            $date_range = $_POST['date_range'] ?? '';
            $status = $_POST['status'] ?? 'available';

            // Parse the date range
            $dates = explode(' to ', $date_range);
            if (count($dates) !== 2) {
                throw new Exception("Invalid date range format");
            }

            $start_date = trim($dates[0]);
            $end_date = trim($dates[1]);

            // Validate dates
            if (strtotime($start_date) > strtotime($end_date)) {
                throw new Exception("End date must be after start date");
            }

            // Delete existing availability for the date range
            $delete_query = "DELETE FROM guide_availability 
                           WHERE guide_id = ? 
                           AND date BETWEEN ? AND ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("iss", $guide_id, $start_date, $end_date);
            $delete_stmt->execute();

            // Insert new availability
            $current_date = $start_date;
            while (strtotime($current_date) <= strtotime($end_date)) {
                $insert_query = "INSERT INTO guide_availability (guide_id, date, status) 
                               VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("iss", $guide_id, $current_date, $status);
                $insert_stmt->execute();
                
                $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
            }

            $conn->commit();
            $success_message = "Availability updated successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error updating availability: " . $e->getMessage();
        }
    }
}

// Fetch guide's availability for the current month
$current_month = date('Y-m');
$availability_query = "SELECT date, status FROM guide_availability 
                      WHERE guide_id = ? 
                      AND date LIKE ? 
                      ORDER BY date";
$availability_stmt = $conn->prepare($availability_query);
$month_pattern = $current_month . '%';
$availability_stmt->bind_param("is", $guide_id, $month_pattern);
$availability_stmt->execute();
$availability_result = $availability_stmt->get_result();
$availability = [];
while ($row = $availability_result->fetch_assoc()) {
    $availability[$row['date']] = $row['status'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Settings - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .settings-section {
            padding: 50px 0;
            min-height: 100vh;
        }
        .settings-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .calendar-day {
            height: 100px;
            border: 1px solid #dee2e6;
            padding: 5px;
        }
        .calendar-day.available {
            background-color: #d4edda;
        }
        .calendar-day.unavailable {
            background-color: #f8d7da;
        }
        .calendar-day.today {
            border: 2px solid #007bff;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="guide_dashboard.php">
                <img src="images/logo.png" alt="Guide Easy Logo" height="40" class="d-inline-block align-text-top me-2">
                Guide Easy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
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
                        <a class="nav-link active" href="guide_settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Settings Section -->
    <section class="settings-section">
        <div class="container">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="row">
                <!-- Profile Settings -->
                <div class="col-md-6">
                    <div class="settings-card">
                        <h3 class="mb-4">Profile Settings</h3>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo htmlspecialchars($guide['name'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($guide['email'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" 
                                       value="<?php echo htmlspecialchars($guide['phone'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($guide['bio'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control" id="specialization" name="specialization" 
                                       value="<?php echo htmlspecialchars($guide['specialization'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="rate_per_hour" class="form-label">Rate per Hour ($)</label>
                                <input type="number" class="form-control" id="rate_per_hour" name="rate_per_hour" 
                                       value="<?php echo htmlspecialchars($guide['rate_per_hour'] ?? 0); ?>" min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="languages" class="form-label">Languages</label>
                                <input type="text" class="form-control" id="languages" name="languages" 
                                       value="<?php echo htmlspecialchars($guide['languages'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="experience" class="form-label">Experience</label>
                                <input type="text" class="form-control" id="experience" name="experience" 
                                       value="<?php echo htmlspecialchars($guide['experience'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?php echo htmlspecialchars($guide['location'] ?? ''); ?>">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Tour Categories</label>
                                <?php
                                $current_location = $guide['location'] ?? '';
                                if ($current_location): ?>
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($current_location); ?></h6>
                                        </div>
                                        <div class="card-body">
                                            <?php foreach ($categories as $category): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="categories[<?php echo htmlspecialchars($current_location); ?>][]" 
                                                           value="<?php echo $category['id']; ?>"
                                                           id="category_<?php echo htmlspecialchars($current_location) . '_' . $category['id']; ?>"
                                                           <?php echo (isset($guide_categories[$current_location]) && in_array($category['id'], $guide_categories[$current_location])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="category_<?php echo htmlspecialchars($current_location) . '_' . $category['id']; ?>">
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">Please set your location to select tour categories.</div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>

                <!-- Availability Settings -->
                <div class="col-md-6">
                    <div class="settings-card">
                        <h3 class="mb-4">Availability Settings</h3>
                        <form method="POST" action="">
                            <input type="hidden" name="update_availability" value="1">
                            <div class="mb-3">
                                <label for="date_range" class="form-label">Select Date Range</label>
                                <input type="text" class="form-control" id="date_range" name="date_range" 
                                       placeholder="Select date range">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" 
                                           id="status_available" value="available" checked>
                                    <label class="form-check-label" for="status_available">
                                        Available
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" 
                                           id="status_unavailable" value="unavailable">
                                    <label class="form-check-label" for="status_unavailable">
                                        Unavailable
                                    </label>
                                </div>
                </div>
                            <button type="submit" class="btn btn-primary">Update Availability</button>
                        </form>

                        <!-- Calendar Display -->
                        <div class="mt-4">
                            <h5>Current Month Availability</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sun</th>
                                            <th>Mon</th>
                                            <th>Tue</th>
                                            <th>Wed</th>
                                            <th>Thu</th>
                                            <th>Fri</th>
                                            <th>Sat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        <?php
                                        $first_day = date('Y-m-01');
                                        $last_day = date('Y-m-t');
                                        $current_day = $first_day;
                                        $week = [];
                                        
                                        while (strtotime($current_day) <= strtotime($last_day)) {
                                            $day_of_week = date('w', strtotime($current_day));
                                            if ($day_of_week == 0 && !empty($week)) {
                                                echo '<tr>';
                                                foreach ($week as $day) {
                                                    $status = $availability[$day] ?? '';
                                                    $is_today = $day == date('Y-m-d');
                                                    echo '<td class="calendar-day ' . $status . ($is_today ? ' today' : '') . '">';
                                                    echo date('d', strtotime($day));
                                                    echo '</td>';
                                                }
                                                echo '</tr>';
                                                $week = [];
                                            }
                                            $week[] = $current_day;
                                            $current_day = date('Y-m-d', strtotime($current_day . ' +1 day'));
                                        }
                                        
                                        if (!empty($week)) {
                                            echo '<tr>';
                                            foreach ($week as $day) {
                                                $status = $availability[$day] ?? '';
                                                $is_today = $day == date('Y-m-d');
                                                echo '<td class="calendar-day ' . $status . ($is_today ? ' today' : '') . '">';
                                                echo date('d', strtotime($day));
                                                echo '</td>';
                                            }
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                </div>
    </section>

    <!-- Footer -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    document.querySelector('input[name="date_range"]').value = dateStr;
                }
            }
        });
    </script>
</body>
</html> 
<?php
$conn->close();
?> 