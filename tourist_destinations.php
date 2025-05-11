<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and is a tourist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
    header("Location: login.php");
    exit();
}

// Fetch available guides
$guides_query = "SELECT u.*, gs.availability_status, gs.location, gs.rate_per_hour,
                (SELECT AVG(rating) FROM reviews WHERE guide_id = u.id) as avg_rating,
                (SELECT COUNT(*) FROM reviews WHERE guide_id = u.id) as total_reviews
                FROM users u
                LEFT JOIN guide_settings gs ON u.id = gs.user_id
                WHERE u.role = 'guide' AND gs.availability_status = 'available'";
$guides_result = $conn->query($guides_query);
$available_guides = [];
while ($guide = $guides_result->fetch_assoc()) {
    $available_guides[] = $guide;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations - Guide Easy</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .destination-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 2rem;
        }
        .destination-card:hover {
            transform: translateY(-5px);
        }
        .destination-card img {
            height: 250px;
            object-fit: cover;
        }
        .hero-section.bg-primary {
            background: linear-gradient(rgba(0, 60, 180, 0.7), rgba(0, 60, 180, 0.7)), url('kathmandu/thamel.png') center center/cover no-repeat !important;
            color: #fff;
            position: relative;
        }
        .booking-form {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .booking-form .form-control {
            border: none;
            border-bottom: 2px solid #007bff;
            border-radius: 0;
            padding: 10px 0;
            background: transparent;
        }
        .booking-form .form-control:focus {
            box-shadow: none;
            border-bottom: 2px solid #0056b3;
        }
        .guide-select {
            margin-bottom: 1rem;
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
                        <a class="nav-link active" href="tourist_destinations.php">Destinations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section bg-primary text-white py-5" style="margin-top: 56px;">
        <div class="container">
            <h1 class="display-4">Explore Nepal's Wonders</h1>
            <p class="lead">Discover amazing destinations and book your perfect guide</p>
        </div>
    </div>

    <!-- Destinations Section -->
    <section class="destinations py-5">
        <div class="container">
            <div class="row">
                <!-- Kathmandu -->
                <div class="col-md-6">
                    <div class="destination-card">
                        <img src="kathmandu/kathmandumain.png" class="card-img-top" alt="Kathmandu">
                        <div class="card-body">
                            <h5 class="card-title">Kathmandu</h5>
                            <p class="card-text">Explore the cultural heart of Nepal with its ancient temples and vibrant streets.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kathmanduModal">
                                Book Guide
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pokhara -->
                <div class="col-md-6">
                    <div class="destination-card">
                        <img src="pokhara/pokhara.png" class="card-img-top" alt="Pokhara">
                        <div class="card-body">
                            <h5 class="card-title">Pokhara</h5>
                            <p class="card-text">Discover the beauty of lakes and mountains in Nepal's adventure capital.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pokharaModal">
                                Book Guide
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Everest Region -->
                <div class="col-md-6">
                    <div class="destination-card">
                        <img src="Everest/EverestRegion.png" class="card-img-top" alt="Everest Region">
                        <div class="card-body">
                            <h5 class="card-title">Everest Region</h5>
                            <p class="card-text">Experience the majesty of the world's highest peak and its surrounding beauty.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#everestModal">
                                Book Guide
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chitwan -->
                <div class="col-md-6">
                    <div class="destination-card">
                        <img src="Everest/chitwan.png" class="card-img-top" alt="Chitwan">
                        <div class="card-body">
                            <h5 class="card-title">Chitwan</h5>
                            <p class="card-text">Experience wildlife and jungle adventures in Nepal's famous national park.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#chitwanModal">
                                Book Guide
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Modals -->
    <!-- Kathmandu Modal -->
    <div class="modal fade" id="kathmanduModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Guide for Kathmandu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="process_booking.php" method="POST">
                        <input type="hidden" name="destination" value="Kathmandu">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of People</label>
                            <input type="number" class="form-control" name="people" min="1" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Continue Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pokhara Modal -->
    <div class="modal fade" id="pokharaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Guide for Pokhara</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="process_booking.php" method="POST">
                        <input type="hidden" name="destination" value="Pokhara">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of People</label>
                            <input type="number" class="form-control" name="people" min="1" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Continue Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Everest Modal -->
    <div class="modal fade" id="everestModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Guide for Everest Region</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="process_booking.php" method="POST">
                        <input type="hidden" name="destination" value="Everest Region">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of People</label>
                            <input type="number" class="form-control" name="people" min="1" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Continue Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Chitwan Modal -->
    <div class="modal fade" id="chitwanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Guide for Chitwan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="process_booking.php" method="POST">
                        <input type="hidden" name="destination" value="Chitwan">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of People</label>
                            <input type="number" class="form-control" name="people" min="1" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Continue Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
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
    <script>
        // Set minimum date to today for all date inputs
        const today = new Date().toISOString().split('T')[0];
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.min = today;
        });

        // Update end date minimum when start date changes
        document.querySelectorAll('input[name="start_date"]').forEach(startDate => {
            startDate.addEventListener('change', function() {
                const endDate = this.closest('form').querySelector('input[name="end_date"]');
                endDate.min = this.value;
            });
        });
    </script>
</body>
</html> 