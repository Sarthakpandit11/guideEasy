<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_connect.php';



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
            padding: 100px 0 50px;
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
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