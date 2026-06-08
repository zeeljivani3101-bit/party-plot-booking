<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$month_year = isset($_GET['month']) ? $conn->real_escape_string($_GET['month']) : '';

$where = "";
if ($month_year != '') {
    $where = "WHERE ss.month_year = '$month_year'";
}

$sql = "SELECT ss.*, s.name as staff_name, s.role 
        FROM staff_salary ss 
        JOIN staff s ON ss.staff_id = s.id 
        $where
        ORDER BY ss.payment_date DESC, ss.id DESC";
$result = $conn->query($sql);

$filename = "Salary_Report" . ($month_year ? "_" . $month_year : "") . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Output CSV Headers
fputcsv($output, ['Month', 'Staff Name', 'Role', 'Basic Salary (Rs)', 'Total Deductions (Rs)', 'Net Paid (Rs)', 'Payment Date']);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $total_deductions = $row['advance_deduction'] + $row['absent_deduction'] + $row['other_deductions'];
        fputcsv($output, [
            $row['month_year'],
            $row['staff_name'],
            $row['role'],
            $row['basic_salary'],
            $total_deductions,
            $row['net_paid'],
            date('d M, Y', strtotime($row['payment_date']))
        ]);
    }
}

fclose($output);
exit;
?>
