<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Everest Region - Guide Easy</title>
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
                <h1 class="mb-4">Everest Region</h1>
                
                <!-- Hero Image -->
                <div class="destination-hero mb-4">
                    <img src="images/everest.jpg" alt="Everest Region" class="img-fluid rounded">
                </div>

                <!-- Overview -->
                <div class="destination-section mb-5">
                    <h2>Overview</h2>
                    <p>The Everest Region, home to the world's highest peak, Mount Everest, offers some of the most spectacular trekking routes in the world. This region is a dream destination for adventure enthusiasts and nature lovers alike. The area is rich in Sherpa culture and offers breathtaking views of the Himalayan range.</p>
                </div>

                <!-- Activities -->
                <div class="destination-section mb-5">
                    <h2>Things to Do</h2>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/everest-base-camp.jpg" alt="Everest Base Camp" class="img-fluid rounded">
                                <h3>Everest Base Camp Trek</h3>
                                <p>Embark on the iconic trek to Everest Base Camp, experiencing the majestic Himalayas and Sherpa culture.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/kala-patthar.jpg" alt="Kala Patthar" class="img-fluid rounded">
                                <h3>Kala Patthar</h3>
                                <p>Hike to this famous viewpoint for the best panoramic views of Mount Everest and surrounding peaks.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/namche-bazaar.jpg" alt="Namche Bazaar" class="img-fluid rounded">
                                <h3>Namche Bazaar</h3>
                                <p>Visit the bustling Sherpa capital and acclimatization point for Everest treks.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/tengboche-monastery.jpg" alt="Tengboche Monastery" class="img-fluid rounded">
                                <h3>Tengboche Monastery</h3>
                                <p>Explore the largest Buddhist monastery in the Khumbu region with stunning mountain views.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Travel Tips -->
                <div class="destination-section mb-5">
                    <h2>Travel Tips</h2>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Best time to visit: March to May and September to November</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Required permits: TIMS and Sagarmatha National Park Entry Permit</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Altitude sickness prevention: Proper acclimatization is crucial</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Essential gear: Warm clothing, good hiking boots, and basic first aid kit</li>
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
                        <li><a href="#">Gokyo Lakes</a></li>
                        <li><a href="#">Island Peak</a></li>
                        <li><a href="#">Ama Dablam Base Camp</a></li>
                        <li><a href="#">Lukla</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 