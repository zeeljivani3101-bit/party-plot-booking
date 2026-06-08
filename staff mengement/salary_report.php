<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$month_year = isset($_GET['month']) ? $conn->real_escape_string($_GET['month']) : '';

// Build query based on filter
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

$total_paid = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Report | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Salary Report</h1>
                <p style="color: var(--text-muted);">View records of all salaries paid to staff.</p>
            </div>
            <div>
                <a href="salary.php" class="btn btn-primary"><i class='bx bx-money'></i> Generate Salary</a>
            </div>
        </div>

        <div class="glass" style="padding: 2rem;">
            
            <form action="salary_report.php" method="GET" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="month">Filter by Month</label>
                    <input type="month" id="month" name="month" class="form-control" value="<?php echo htmlspecialchars($month_year); ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="background: rgba(255,255,255,0.1); box-shadow: none;"><i class='bx bx-filter'></i> Filter</button>
                <?php if ($month_year != ''): ?>
                    <a href="salary_report.php" class="btn btn-primary" style="background: rgba(239, 68, 68, 0.1); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.2); box-shadow: none;">Clear Filter</a>
                <?php endif; ?>
                <a href="export_salary.php?month=<?php echo urlencode($month_year); ?>" class="btn btn-primary" style="background: #10B981; color: white; margin-left: auto;"><i class='bx bx-spreadsheet'></i> Export to Excel</a>
            </form>

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Staff Name</th>
                            <th>Basic Salary</th>
                            <th>Deductions</th>
                            <th>Net Paid</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php 
                                    $total_deductions = $row['advance_deduction'] + $row['absent_deduction'] + $row['other_deductions'];
                                    $total_paid += $row['net_paid'];
                                ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($row['month_year']); ?></td>
                                    <td style="font-weight: 500;">
                                        <a href="staff_profile.php?id=<?php echo $row['staff_id']; ?>" style="color: inherit; text-decoration: none;">
                                            <?php echo htmlspecialchars($row['staff_name']); ?>
                                        </a>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($row['role']); ?></div>
                                    </td>
                                    <td>₹<?php echo number_format($row['basic_salary'], 2); ?></td>
                                    <td style="color: #EF4444;">-₹<?php echo number_format($total_deductions, 2); ?></td>
                                    <td style="color: #10B981; font-weight: 600;">₹<?php echo number_format($row['net_paid'], 2); ?></td>
                                    <td style="color: var(--text-muted); font-size: 0.9rem;"><?php echo date('d M, Y', strtotime($row['payment_date'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            
                            <!-- Summary Row -->
                            <tr style="background: rgba(255,255,255,0.02);">
                                <td colspan="4" style="text-align: right; font-weight: 600; padding: 1.5rem 1rem;">Total Paid Amount:</td>
                                <td colspan="2" style="font-weight: 700; color: var(--secondary); font-size: 1.2rem; padding: 1.5rem 1rem;">₹<?php echo number_format($total_paid, 2); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    No salary records found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

</body>
</html>
