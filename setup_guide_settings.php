<?php
require_once 'db_connect.php';

// Read and execute the SQL file
$sql = file_get_contents('create_guide_settings.sql');

if ($conn->multi_query($sql)) {
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    echo "Guide settings table created successfully!";
} else {
    echo "Error creating guide settings table: " . $conn->error;
}

$conn->close();
?> 