<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = intval($_POST['staff_id']);
    $deduction_month = $conn->real_escape_string($_POST['deduction_month'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $reason = $conn->real_escape_string($_POST['reason'] ?? '');
    
    if ($staff_id > 0 && $amount > 0 && $deduction_month != '') {
        $sql = "INSERT INTO staff_salary_deductions (staff_id, deduction_month, amount, reason) VALUES ($staff_id, '$deduction_month', $amount, '$reason')";
        if ($conn->query($sql) === TRUE) {
            $message = "Deduction recorded successfully!";
        } else {
            $error = "Error recording deduction: " . $conn->error;
        }
    } else {
        $error = "Please fill in all required fields properly.";
    }
}

// Fetch staff for dropdown
$staff_res = $conn->query("SELECT id, name, role FROM staff WHERE status = 'Active' ORDER BY name ASC");
$active_staff = [];
if ($staff_res) {
    while($row = $staff_res->fetch_assoc()) {
        $active_staff[] = $row;
    }
}

// Fetch recent deductions
$ded_hist = $conn->query("SELECT d.*, s.name as staff_name FROM staff_salary_deductions d JOIN staff s ON d.staff_id = s.id ORDER BY d.id DESC LIMIT 10");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Other Deductions | PartyPlot Admin</title>
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
                <h1 class="page-title">Salary Deductions</h1>
                <p style="color: var(--text-muted);">Record additional penalties/deductions for a specific month.</p>
            </div>
            <div>
                <a href="salary.php" class="btn btn-primary" style="background: rgba(255,255,255,0.1); box-shadow: none;"><i class='bx bx-money'></i> Generate Salary</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div class="glass" style="padding: 2rem; height: fit-content;">
                <h3 style="margin-bottom: 1.5rem;">Add Deduction</h3>
                <?php if ($message): ?>
                    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--secondary); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <i class='bx bx-check-circle'></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #EF4444; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <i class='bx bx-error-circle'></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="salary_deduction.php" method="POST">
                    <div class="form-group">
                        <label class="form-label" for="staff_id">Select Staff Member *</label>
                        <select id="staff_id" name="staff_id" class="form-control" required style="background: rgba(15, 23, 42, 0.9);">
                            <option value="">-- Choose Staff --</option>
                            <?php foreach($active_staff as $staff): ?>
                                <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="deduction_month">Applicable Month *</label>
                        <input type="month" id="deduction_month" name="deduction_month" class="form-control" required value="<?php echo date('Y-m'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="amount">Amount (₹) *</label>
                        <input type="number" id="amount" name="amount" class="form-control" step="0.01" required placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reason">Reason / Remarks</label>
                        <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Why is this deduction applied?"></textarea>
                    </div>

                    <div style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;"><i class='bx bx-minus-circle'></i> Record Deduction</button>
                    </div>
                </form>
            </div>

            <div class="glass" style="padding: 2rem; height: fit-content;">
                <h3 style="margin-bottom: 1.5rem;">Recent Deductions</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Staff Name</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ded_hist && $ded_hist->num_rows > 0): ?>
                            <?php while($row = $ded_hist->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['deduction_month']); ?></td>
                                    <td style="font-weight: 500;"><?php echo htmlspecialchars($row['staff_name']); ?></td>
                                    <td style="color: #EF4444; font-weight: 600;">-₹<?php echo number_format($row['amount'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 1.5rem; color: var(--text-muted);">No deductions recorded yet.</td>
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
