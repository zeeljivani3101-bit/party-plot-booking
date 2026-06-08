<?php
require_once 'config/db.php';

$query = "CREATE TABLE IF NOT EXISTS whatsapp_templates (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($query) === TRUE) {
    echo "<b>WhatsApp templates table created successfully!</b>";
    
    // Insert a default template if none exist
    $check = $conn->query("SELECT id FROM whatsapp_templates");
    if ($check->num_rows == 0) {
        $default_content = "Hello {customer_name},\n\nThis is a confirmation for your booking on {booking_date} for {event_type}.\n\nTotal Amount: Rs. {total_amount}\nAdvance Paid: Rs. {advance_amount}\nBalance Due: Rs. {balance_amount}\n\nThank you for choosing PartyPlot!";
        $stmt = $conn->prepare("INSERT INTO whatsapp_templates (template_name, content) VALUES (?, ?)");
        $name = "Booking Confirmation";
        $stmt->bind_param("ss", $name, $default_content);
        $stmt->execute();
        echo "<br>Default template inserted.";
    }
} else {
    echo "<b>Error creating table:</b> " . $conn->error;
}
?>
