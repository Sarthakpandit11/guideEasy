<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chitwan - Guide Easy</title>
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
                <h1 class="mb-4">Chitwan National Park</h1>
                
                <!-- Hero Image -->
                <div class="destination-hero mb-4">
                    <img src="images/chitwan.jpg" alt="Chitwan National Park" class="img-fluid rounded">
                </div>

                <!-- Overview -->
                <div class="destination-section mb-5">
                    <h2>Overview</h2>
                    <p>Chitwan National Park, Nepal's first national park and a UNESCO World Heritage Site, is located in the subtropical lowlands of the Terai region. Established in 1973, it covers an area of 952.63 square kilometers and is home to a rich variety of wildlife, including the endangered one-horned rhinoceros, Bengal tigers, and over 500 species of birds. The park also preserves the traditional Tharu culture and offers various jungle activities for visitors.</p>
                </div>

                <!-- Activities -->
                <div class="destination-section mb-5">
                    <h2>Things to Do</h2>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/jungle-safari.jpg" alt="Jungle Safari" class="img-fluid rounded">
                                <h3>Jungle Safari</h3>
                                <p>Experience an exciting jeep or elephant safari through the dense forests to spot wildlife like rhinos, tigers, and various bird species.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/canoeing.jpg" alt="Canoeing" class="img-fluid rounded">
                                <h3>Canoeing</h3>
                                <p>Take a peaceful canoe ride along the Rapti River to observe crocodiles, water birds, and other aquatic wildlife.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/tharu-village.jpg" alt="Tharu Village Visit" class="img-fluid rounded">
                                <h3>Tharu Village Visit</h3>
                                <p>Explore the traditional Tharu villages to learn about their unique culture, lifestyle, and traditional dance performances.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="activity-card">
                                <img src="images/elephant-bathing.jpg" alt="Elephant Bathing" class="img-fluid rounded">
                                <h3>Elephant Bathing</h3>
                                <p>Join the elephants for their daily bath in the river, a unique and fun experience for visitors.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Travel Tips -->
                <div class="destination-section mb-5">
                    <h2>Travel Tips</h2>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Best time to visit: October to March</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Required permits: National Park entry permit</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Clothing: Light cotton clothes, comfortable shoes, and a hat</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Essential items: Binoculars, camera, sunscreen, and insect repellent</li>
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
                        <li><a href="#">Bardia National Park</a></li>
                        <li><a href="#">Lumbini</a></li>
                        <li><a href="#">Pokhara</a></li>
                        <li><a href="#">Kathmandu</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 