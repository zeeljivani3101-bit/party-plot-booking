<?php
require 'c:/xampp/htdocs/partyplot/config/db.php';
$res = $conn->query("SELECT id, total_amount, advance_amount, balance_amount, status FROM bookings");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
