<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch staff members
$sql = "SELECT * FROM staff ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff List | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: #10B981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .action-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-main);
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .action-btn:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        .action-btn.delete:hover {
            background: #EF4444;
            border-color: #EF4444;
        }
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
            letter-spacing: 0.05em;
        }
        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Staff Members</h1>
                <p style="color: var(--text-muted);">Manage all employees and view their details.</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="export_staff.php" class="btn btn-primary" style="background: #10B981; color: white; box-shadow: none;"><i class='bx bx-spreadsheet'></i> Export to Excel</a>
                <a href="addstaff.php" class="btn btn-primary"><i class='bx bx-plus'></i> Add New Staff</a>
            </div>
        </div>

        <div class="glass" style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Contact</th>
                        <th>Basic Salary</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td style="font-weight: 500;">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['role']); ?></td>
                                <td>
                                    <div><i class='bx bx-phone' style="color: var(--text-muted); font-size: 0.8rem;"></i> <?php echo htmlspecialchars($row['phone']); ?></div>
                                    <?php if ($row['email']): ?>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);"><i class='bx bx-envelope' style="font-size: 0.8rem;"></i> <?php echo htmlspecialchars($row['email']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>₹<?php echo number_format($row['basic_salary'], 2); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $row['status'] == 'Active' ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="staff_profile.php?id=<?php echo $row['id']; ?>" class="action-btn" title="View Profile">
                                            <i class='bx bx-user'></i>
                                        </a>
                                        <a href="edit_staff.php?id=<?php echo $row['id']; ?>" class="action-btn" title="Edit">
                                            <i class='bx bx-edit-alt'></i>
                                        </a>
                                        <a href="delete_staff.php?id=<?php echo $row['id']; ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this staff member? This will also remove their attendance and salary records.');">
                                            <i class='bx bx-trash'></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                No staff members found. <a href="addstaff.php" style="color: var(--primary);">Add one now</a>.
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
