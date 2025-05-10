<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations - Nepal Travel Guide</title>
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
                <h1 class="mb-4">Popular Destinations in Nepal</h1>
                
                <!-- Kathmandu -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="kathmandu/thamel.png" alt="Kathmandu" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Kathmandu</h2>
                            <p>The capital city of Nepal, Kathmandu is a vibrant metropolis that beautifully blends ancient traditions with modern life. It's home to numerous UNESCO World Heritage Sites, including the famous Pashupatinath Temple and Boudhanath Stupa.</p>
                            <a href="kathmandu.php" class="btn btn-primary">Explore Kathmandu</a>
                        </div>
                    </div>
                </div>

                <!-- Pokhara -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="pokhara/pokhara.png" alt="Pokhara" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Pokhara</h2>
                            <p>Known as the gateway to the Annapurna Circuit, Pokhara is famous for its stunning lakes, breathtaking mountain views, and adventure sports. It's a perfect blend of natural beauty and modern amenities.</p>
                            <a href="pokhara.php" class="btn btn-primary">Explore Pokhara</a>
                        </div>
                    </div>
                </div>

                <!-- Everest Region -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="Everest/EverestRegion.png" alt="Everest Region" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Everest Region</h2>
                            <p>Home to the world's highest peak, Mount Everest, this region offers some of the most spectacular trekking routes in the world. The Everest Base Camp trek is a dream for many adventure enthusiasts.</p>
                            <a href="everest.php" class="btn btn-primary">Explore Everest Region</a>
                        </div>
                    </div>
                </div>

                <!-- Chitwan -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="Everest/chitwan.png" alt="Chitwan" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Chitwan</h2>
                            <p>Chitwan National Park is a UNESCO World Heritage Site known for its rich wildlife, including the endangered one-horned rhinoceros and Bengal tigers. It offers excellent jungle safari experiences.</p>
                            <a href="chitwan.php" class="btn btn-primary">Explore Chitwan</a>
                        </div>
                    </div>
                </div>

                <!-- Lumbini -->
                <div class="destination-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="Everest/lumbini.png" alt="Lumbini" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h2>Lumbini</h2>
                            <p>The birthplace of Lord Buddha, Lumbini is a sacred pilgrimage site for Buddhists worldwide. The Maya Devi Temple and numerous monasteries make it a spiritual and cultural hub.</p>
                            <a href="lumbini.php" class="btn btn-primary">Explore Lumbini</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar">
                    <h3>Quick Links</h3>
                    <ul class="list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#about">About Us</a></li>
                        <li><a href="destinations.php" class="active">Destinations</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                    </ul>

                    <h3>Popular Activities</h3>
                    <ul class="list-unstyled">
                        <li><a href="#">Trekking</a></li>
                        <li><a href="#">Jungle Safari</a></li>
                        <li><a href="#">Cultural Tours</a></li>
                        <li><a href="#">Adventure Sports</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 