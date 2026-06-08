<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$month_year = isset($_GET['month']) ? $conn->real_escape_string($_GET['month']) : date('Y-m');

// Fetch all staff members (even inactive, to see past records)
$staff_res = $conn->query("SELECT id, name, role FROM staff ORDER BY name ASC");
$staff_list = [];
if ($staff_res) {
    while($row = $staff_res->fetch_assoc()) {
        $staff_list[] = $row;
    }
}

// Fetch attendance for the selected month
$att_sql = "SELECT staff_id, status, COUNT(*) as count FROM staff_attendance 
            WHERE DATE_FORMAT(attendance_date, '%Y-%m') = '$month_year' 
            GROUP BY staff_id, status";
$att_res = $conn->query($att_sql);

$report_data = [];
// Initialize with zeros
foreach ($staff_list as $s) {
    $report_data[$s['id']] = ['Present' => 0, 'Absent' => 0, 'Half-Day' => 0];
}

if ($att_res) {
    while($row = $att_res->fetch_assoc()) {
        $report_data[$row['staff_id']][$row['status']] = $row['count'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
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
        .count-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            min-width: 30px;
            text-align: center;
        }
        .bg-present { background: rgba(16, 185, 129, 0.1); color: #10B981; }
        .bg-absent { background: rgba(239, 68, 68, 0.1); color: #EF4444; }
        .bg-half { background: rgba(245, 158, 11, 0.1); color: #F59E0B; }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Attendance Report</h1>
                <p style="color: var(--text-muted);">Monthly summary of staff attendance.</p>
            </div>
            <div>
                <a href="attendance.php" class="btn btn-primary" ><i class='bx bx-check-square'></i> Mark Daily Attendance</a>
            </div>
        </div>

        <div class="glass" style="padding: 2rem;">
            
            <form action="attendance_report.php" method="GET" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="month">Select Month</label>
                    <input type="month" id="month" name="month" class="form-control" value="<?php echo htmlspecialchars($month_year); ?>">
                </div>
                <button type="submit" class="btn btn-primary" ><i class='bx bx-filter'></i> Filter</button>
            </form>

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Role</th>
                            <th style="text-align: center;">Total Present</th>
                            <th style="text-align: center;">Total Absent</th>
                            <th style="text-align: center;">Total Half-Day</th>
                            <th>Total Logged Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($staff_list) > 0): ?>
                            <?php foreach($staff_list as $staff): ?>
                                <?php 
                                    $stats = $report_data[$staff['id']]; 
                                    $total_days = $stats['Present'] + $stats['Absent'] + $stats['Half-Day'];
                                ?>
                                <tr>
                                    <td style="font-weight: 500;">
                                        <a href="staff_profile.php?id=<?php echo $staff['id']; ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($staff['name']); ?></a>
                                    </td>
                                    <td style="color: var(--text-muted);"><?php echo htmlspecialchars($staff['role']); ?></td>
                                    <td style="text-align: center;"><span class="count-badge bg-present"><?php echo $stats['Present']; ?></span></td>
                                    <td style="text-align: center;"><span class="count-badge bg-absent"><?php echo $stats['Absent']; ?></span></td>
                                    <td style="text-align: center;"><span class="count-badge bg-half"><?php echo $stats['Half-Day']; ?></span></td>
                                    <td style="font-weight: 600;"><?php echo $total_days; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No staff found in the system.</td>
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
