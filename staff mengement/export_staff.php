<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT * FROM staff ORDER BY id DESC";
$result = $conn->query($sql);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Staff_List.csv"');

$output = fopen('php://output', 'w');

// Output CSV Headers
fputcsv($output, ['ID', 'Name', 'Role', 'Phone', 'Email', 'Basic Salary (Rs)', 'Joining Date', 'Status']);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['name'],
            $row['role'],
            $row['phone'],
            $row['email'],
            $row['basic_salary'],
            date('d M, Y', strtotime($row['joining_date'])),
            $row['status']
        ]);
    }
}

fclose($output);
exit;
?>
