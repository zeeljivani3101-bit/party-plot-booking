<?php
require_once 'config/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS customer_documents (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        customer_id INT(11) NOT NULL,
        document_name VARCHAR(150) NOT NULL,
        document_type VARCHAR(100) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS booking_agreements (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        booking_id INT(11) NOT NULL,
        agreement_title VARCHAR(150) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        status ENUM('Pending Signature', 'Signed') DEFAULT 'Pending Signature',
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

$success = true;
foreach ($queries as $index => $query) {
    if ($conn->query($query) === TRUE) {
        echo "Query " . ($index + 1) . " executed successfully.<br>";
    } else {
        echo "Error in query " . ($index + 1) . ": " . $conn->error . "<br>";
        $success = false;
    }
}

if ($success) {
    echo "<b>All tables for Documents & Agreements created successfully!</b>";
} else {
    echo "<b>There were some errors creating the tables.</b>";
}
?>
