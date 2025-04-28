<?php
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['location']) && isset($_POST['categories'])) {
        $location = $_POST['location'];
        $selected_categories = $_POST['categories'];
        
        try {
            // Start transaction
            $conn->begin_transaction();
            
            // Delete existing categories for this location
            $delete_query = "DELETE FROM guide_category_mappings 
                           WHERE guide_id = ? AND location = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("is", $guide_id, $location);
            $delete_stmt->execute();
            
            // Insert new categories
            if (!empty($selected_categories)) {
                $insert_query = "INSERT INTO guide_category_mappings 
                               (guide_id, category_id, location) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                
                foreach ($selected_categories as $category_id) {
                    $insert_stmt->bind_param("iis", $guide_id, $category_id, $location);
                    $insert_stmt->execute();
                }
            }
            
            $conn->commit();
            $success_message = "Categories updated successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error updating categories: " . $e->getMessage();
        }
    }
}

// Get guide's locations
$locations_query = "SELECT DISTINCT location FROM guide_settings WHERE user_id = ?";
$locations_stmt = $conn->prepare($locations_query);
$locations_stmt->bind_param("i", $guide_id);
$locations_stmt->execute();
$locations_result = $locations_stmt->get_result();
$locations = $locations_result->fetch_all(MYSQLI_ASSOC);

// Get all categories
$categories_query = "SELECT * FROM guide_categories ORDER BY name";
$categories_result = $conn->query($categories_query);
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// Get guide's current categories for each location
$current_categories = [];
$current_query = "SELECT location, category_id FROM guide_category_mappings WHERE guide_id = ?";
$current_stmt = $conn->prepare($current_query);
$current_stmt->bind_param("i", $guide_id);
$current_stmt->execute();
$current_result = $current_stmt->get_result();

while ($row = $current_result->fetch_assoc()) {
    $current_categories[$row['location']][] = $row['category_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tour Categories - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .category-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .category-checkbox {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container" style="margin-top: 80px;">
        <h2 class="mb-4">Manage Tour Categories</h2>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php foreach ($locations as $location): ?>
            <div class="category-section">
                <h3 class="mb-4"><?php echo htmlspecialchars($location['location']); ?></h3>
                <form method="POST" action="">
                    <input type="hidden" name="location" value="<?php echo htmlspecialchars($location['location']); ?>">
                    
                    <div class="row">
                        <?php foreach ($categories as $category): ?>
                            <div class="col-md-6">
                                <div class="category-checkbox">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="categories[]" 
                                               value="<?php echo $category['id']; ?>"
                                               id="category_<?php echo $category['id']; ?>_<?php echo $location['location']; ?>"
                                               <?php echo isset($current_categories[$location['location']]) && 
                                                          in_array($category['id'], $current_categories[$location['location']]) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="category_<?php echo $category['id']; ?>_<?php echo $location['location']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                            <small class="text-muted d-block"><?php echo htmlspecialchars($category['description']); ?></small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-save"></i> Update Categories
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 