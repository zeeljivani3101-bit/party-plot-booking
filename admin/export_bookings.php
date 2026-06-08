<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch all bookings with customer details
$sql = "
    SELECT b.id, c.customer_name, c.mobile as customer_phone, b.event_type, b.booking_date, b.total_amount, b.advance_amount, b.balance_amount, b.status, b.created_at
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    ORDER BY b.created_at DESC
";
$result = $conn->query($sql);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Customer_Bookings.csv"');

$output = fopen('php://output', 'w');

// Output CSV Headers
fputcsv($output, ['Booking ID', 'Customer Name', 'Customer Phone', 'Event Type', 'Event Date', 'Total Amount (Rs)', 'Advance Amount (Rs)', 'Balance Amount (Rs)', 'Status', 'Booking Date']);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        fputcsv($output, [
            'BKG-' . $row['id'],
            $row['customer_name'],
            $row['customer_phone'],
            $row['event_type'],
            date('d M, Y', strtotime($row['booking_date'])),
            $row['total_amount'],
            $row['advance_amount'],
            $row['balance_amount'],
            $row['status'],
            date('d M, Y', strtotime($row['created_at']))
        ]);
    }
}

fclose($output);
exit;
?>
