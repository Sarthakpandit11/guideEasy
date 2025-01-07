<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nepal Travel Guide</title>
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
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="destinations.php">Destinations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="btn btn-outline-light ms-2">Sign In</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Home Section -->
    <section id="home" class="hero-section">
        <img src="main_photo.jpg" alt="Nepal Landscape" class="hero-image">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome to Nepal</h1>
                <p>Discover the beauty of the Himalayas</p>
                <a href="destinations.php" class="btn btn-primary">Explore Destinations</a>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="about-section py-5">
        <div class="container">
            <h2 class="section-title text-center">About Nepal</h2>
            <div class="row">
                <div class="col-lg-6">
                    <p class="section-text">Nepal, nestled in the heart of the Himalayas, is a land of breathtaking landscapes, rich cultural heritage, and warm hospitality. From the majestic peaks of Mount Everest to the ancient temples of Kathmandu, Nepal offers a unique blend of natural beauty and cultural richness.</p>
                    <div class="features">
                        <div class="feature-item">
                            <i class="fas fa-mountain"></i>
                            <span>8 of the world's 10 highest peaks</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-umbrella-beach"></i>
                            <span>Diverse landscapes</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-utensils"></i>
                            <span>Rich cultural heritage</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="map-container">
                        <img src="images/nepal map.jpg" alt="Nepal Map" class="img-fluid map-image">
                        <div class="map-pin" style="top: 30%; left: 45%;" data-destination="Kathmandu">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="pin-info">Kathmandu</div>
                        </div>
                        <div class="map-pin" style="top: 40%; left: 35%;" data-destination="Pokhara">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="pin-info">Pokhara</div>
                        </div>
                        <div class="map-pin" style="top: 20%; left: 50%;" data-destination="Everest Base Camp">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="pin-info">Everest Base Camp</div>
                        </div>
                        <div class="map-pin" style="top: 60%; left: 40%;" data-destination="Chitwan">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="pin-info">Chitwan</div>
                        </div>
                        <div class="map-pin" style="top: 70%; left: 30%;" data-destination="Lumbini">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="pin-info">Lumbini</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section py-5">
        <div class="container">
            <h2 class="section-title text-center">Contact Us</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-form">
                        <form>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Your Email" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
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
    <script src="script.js"></script>
</body>
</html>
