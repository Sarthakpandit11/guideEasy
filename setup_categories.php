<?php
require_once 'db_connect.php';

try {
    // Create guide_categories table
    $conn->query("CREATE TABLE IF NOT EXISTS guide_categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create guide_category_mappings table with indexes
    $conn->query("CREATE TABLE IF NOT EXISTS guide_category_mappings (
        guide_id INT,
        category_id INT,
        location VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (guide_id, category_id, location),
        FOREIGN KEY (guide_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES guide_categories(id) ON DELETE CASCADE,
        INDEX idx_guide_category_location (location),
        INDEX idx_guide_category_guide (guide_id),
        INDEX idx_guide_category_category (category_id)
    )");

    // Insert default categories if they don't exist
    $categories = [
        ['name' => 'Sightseeing Tours', 'description' => 'Guided tours of popular tourist attractions and landmarks'],
        ['name' => 'Cultural Tours', 'description' => 'Immersive experiences in local culture, traditions, and heritage'],
        ['name' => 'Hiking Tours', 'description' => 'Guided hiking and trekking experiences'],
        ['name' => 'Food Tours', 'description' => 'Culinary experiences and local food exploration']
    ];

    // Check if categories already exist
    foreach ($categories as $category) {
        $check_stmt = $conn->prepare("SELECT id FROM guide_categories WHERE name = ?");
        $check_stmt->bind_param("s", $category['name']);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            $insert_stmt = $conn->prepare("INSERT INTO guide_categories (name, description) VALUES (?, ?)");
            $insert_stmt->bind_param("ss", $category['name'], $category['description']);
            $insert_stmt->execute();
        }
    }

    echo "Guide categories setup completed successfully!";
} catch (Exception $e) {
    echo "Error setting up guide categories: " . $e->getMessage();
}
?> 