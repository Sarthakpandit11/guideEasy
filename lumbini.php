<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumbini - Guide Easy</title>
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
                <h1 class="mb-4">Lumbini</h1>
                
                <!-- Hero Image -->
                <div class="destination-hero mb-4">
                    <img src="images/lumbini.jpg" alt="Lumbini" class="img-fluid rounded">
                </div>

                <!-- Overview -->
                <div class="destination-section mb-5">
                    <h2>Overview</h2>
                    <p>Lumbini, the birthplace of Lord Buddha, is a sacred pilgrimage site for Buddhists worldwide. Located in the Terai plains of southern Nepal, this UNESCO World Heritage Site is home to the Maya Devi Temple and numerous monasteries built by Buddhist communities from around the world. The peaceful atmosphere and spiritual significance make it a must-visit destination.</p>
                </div>

                <!-- Activities -->
                <div class="destination-section mb-5">
                    <h2>Things to Do</h2>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/maya-devi.jpg" alt="Maya Devi Temple" class="img-fluid rounded">
                                <h3>Maya Devi Temple</h3>
                                <p>Visit the sacred site where Queen Maya Devi gave birth to Prince Siddhartha, who later became Buddha.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/world-peace.jpg" alt="World Peace Pagoda" class="img-fluid rounded">
                                <h3>World Peace Pagoda</h3>
                                <p>Visit this beautiful white stupa built by Japanese Buddhists, symbolizing peace and harmony.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/monasteries.jpg" alt="International Monasteries" class="img-fluid rounded">
                                <h3>International Monasteries</h3>
                                <p>Explore the unique architectural styles of monasteries built by different Buddhist communities.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/sacred-garden.jpg" alt="Sacred Garden" class="img-fluid rounded">
                                <h3>Sacred Garden</h3>
                                <p>Walk through the peaceful garden where Buddha spent his early years.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Travel Tips -->
                <div class="destination-section mb-5">
                    <h2>Travel Tips</h2>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Best time to visit: October to March</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Dress code: Modest clothing recommended</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Photography: Respect local customs and restrictions</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Transportation: Local buses and taxis available from nearby cities</li>
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
                        <li><a href="#">Kapilvastu</a></li>
                        <li><a href="#">Tilaurakot</a></li>
                        <li><a href="#">Devdaha</a></li>
                        <li><a href="#">Ramgram</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 