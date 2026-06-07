<?php
require 'c:/xampp/htdocs/partyplot/config/db.php';

$sql1 = "CREATE TABLE IF NOT EXISTS inquiries (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    event_date DATE NOT NULL,
    message TEXT,
    status ENUM('New', 'Contacted', 'Resolved') DEFAULT 'New',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql2 = "CREATE TABLE IF NOT EXISTS gallery (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

echo "Inquiries table: " . ($conn->query($sql1) ? "OK\n" : $conn->error . "\n");
echo "Gallery table: " . ($conn->query($sql2) ? "OK\n" : $conn->error . "\n");

if (!file_exists('c:/xampp/htdocs/partyplot/uploads')) {
    mkdir('c:/xampp/htdocs/partyplot/uploads', 0777, true);
    echo "Uploads directory created.\n";
} else {
    echo "Uploads directory already exists.\n";
}
?>
