<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokhara - Guide Easy</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="destinations.php">Destinations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a href="signin.php" class="nav-link btn btn-outline-light ms-2">Sign In</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <h1 class="mb-4">Pokhara</h1>
                
                <!-- Hero Image -->
                <div class="destination-hero mb-4">
                    <img src="images/pokhara.jpg" alt="Pokhara" class="img-fluid rounded">
                </div>

                <!-- Overview -->
                <div class="destination-section mb-5">
                    <h2>Overview</h2>
                    <p>Pokhara, known as the gateway to the Annapurna Circuit, is Nepal's second-largest city and a major tourist destination. Famous for its stunning lakes, breathtaking mountain views, and adventure sports, Pokhara offers a perfect blend of natural beauty and modern amenities. The city is set against the backdrop of the Annapurna mountain range and is home to the beautiful Phewa Lake.</p>
                </div>

                <!-- Activities -->
                <div class="destination-section mb-5">
                    <h2>Things to Do</h2>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/phewa-lake.jpg" alt="Phewa Lake" class="img-fluid rounded">
                                <h3>Phewa Lake</h3>
                                <p>Enjoy boating on Nepal's second-largest lake with stunning views of the Annapurna range reflected in its waters.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/sarangkot.jpg" alt="Sarangkot" class="img-fluid rounded">
                                <h3>Sarangkot Viewpoint</h3>
                                <p>Witness breathtaking sunrise views over the Annapurna range from this popular viewpoint.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/paragliding.jpg" alt="Paragliding" class="img-fluid rounded">
                                <h3>Paragliding</h3>
                                <p>Experience the thrill of paragliding over the beautiful Pokhara valley with professional instructors.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/davis-falls.jpg" alt="Davis Falls" class="img-fluid rounded">
                                <h3>Davis Falls</h3>
                                <p>Visit this unique waterfall that disappears into an underground tunnel beneath the city.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Travel Tips -->
                <div class="destination-section mb-5">
                    <h2>Travel Tips</h2>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Best time to visit: September to November and March to May</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Currency: Nepalese Rupee (NPR)</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Language: Nepali, but English is widely spoken</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Transportation: Taxis, local buses, and rental bikes available</li>
                    </ul>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar">
                    <h3>Quick Links</h3>
                    <ul class="list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#about">About Us</a></li>
                        <li><a href="destinations.php">Destinations</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                    </ul>

                    <h3>Nearby Destinations</h3>
                    <ul class="list-unstyled">
                        <li><a href="#">Annapurna Base Camp</a></li>
                        <li><a href="#">Ghandruk</a></li>
                        <li><a href="#">Dhampus</a></li>
                        <li><a href="#">Australian Camp</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 