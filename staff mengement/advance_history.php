<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT sa.*, s.name as staff_name, s.role 
        FROM staff_advances sa 
        JOIN staff s ON sa.staff_id = s.id 
        ORDER BY sa.advance_date DESC, sa.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advance History | PartyPlot Admin</title>
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
                <h1 class="page-title">Advance History</h1>
                <p style="color: var(--text-muted);">View all advance payments given to staff.</p>
            </div>
            <div>
                <a href="staff_advance.php" class="btn btn-primary"><i class='bx bx-plus'></i> Issue New Advance</a>
            </div>
        </div>

        <div class="glass" style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Staff Name</th>
                        <th>Role</th>
                        <th>Amount</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d M, Y', strtotime($row['advance_date'])); ?></td>
                                <td style="font-weight: 500;">
                                    <a href="staff_profile.php?id=<?php echo $row['staff_id']; ?>" style="color: inherit; text-decoration: none;">
                                        <?php echo htmlspecialchars($row['staff_name']); ?>
                                    </a>
                                </td>
                                <td style="color: var(--text-muted);"><?php echo htmlspecialchars($row['role']); ?></td>
                                <td style="color: #EF4444; font-weight: 600;">₹<?php echo number_format($row['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                No advance records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
