<?php
require_once 'c:/xampp/htdocs/partyplot/config/db.php';
$res = $conn->query("DESCRIBE bookings");
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error describing table: " . $conn->error;
}
?>
