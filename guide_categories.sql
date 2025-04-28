-- Create guide_categories table
CREATE TABLE IF NOT EXISTS guide_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create guide_category_mappings table for many-to-many relationship
CREATE TABLE IF NOT EXISTS guide_category_mappings (
    guide_id INT,
    category_id INT,
    location VARCHAR(100) NOT NULL,
    PRIMARY KEY (guide_id, category_id, location),
    FOREIGN KEY (guide_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES guide_categories(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO guide_categories (name, description) VALUES
('Sightseeing Tours', 'Guided tours of popular tourist attractions and landmarks'),
('Cultural Tours', 'Immersive experiences in local culture, traditions, and heritage'),
('Hiking Tours', 'Guided hiking and trekking experiences'),
('Food Tours', 'Culinary experiences and local food exploration');

-- Add indexes for better performance
CREATE INDEX idx_guide_category_location ON guide_category_mappings(location);
CREATE INDEX idx_guide_category_guide ON guide_category_mappings(guide_id);
CREATE INDEX idx_guide_category_category ON guide_category_mappings(category_id); 