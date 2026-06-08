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
$staff_data = null;

$month_year = isset($_GET['month']) ? $conn->real_escape_string($_GET['month']) : date('Y-m');
$staff_id = isset($_GET['staff_id']) ? intval($_GET['staff_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_salary'])) {
    $p_staff_id = intval($_POST['staff_id']);
    $p_month_year = $conn->real_escape_string($_POST['month_year']);
    $basic = floatval($_POST['basic_salary']);
    $advance_deduct = floatval($_POST['advance_deduction']);
    $absent_deduct = floatval($_POST['absent_deduction']);
    $other_deduct = floatval($_POST['other_deductions']);
    $net_paid = floatval($_POST['net_paid']);
    $payment_date = date('Y-m-d');
    
    // Check if already paid
    $check = $conn->query("SELECT id FROM staff_salary WHERE staff_id = $p_staff_id AND month_year = '$p_month_year'");
    if ($check && $check->num_rows > 0) {
        $error = "Salary for this month has already been generated for this staff member.";
    } else {
        $sql = "INSERT INTO staff_salary (staff_id, month_year, basic_salary, advance_deduction, absent_deduction, other_deductions, net_paid, payment_date) 
                VALUES ($p_staff_id, '$p_month_year', $basic, $advance_deduct, $absent_deduct, $other_deduct, $net_paid, '$payment_date')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Salary generated and recorded successfully!";
            $staff_id = 0; // reset
        } else {
            $error = "Error saving salary: " . $conn->error;
        }
    }
}

// Fetch active staff
$staff_res = $conn->query("SELECT id, name FROM staff WHERE status = 'Active' ORDER BY name ASC");
$active_staff = [];
if ($staff_res) {
    while($row = $staff_res->fetch_assoc()) {
        $active_staff[] = $row;
    }
}

// If staff selected, calculate details
if ($staff_id > 0) {
    // Get staff basic
    $s_res = $conn->query("SELECT * FROM staff WHERE id = $staff_id");
    if ($s_res && $s_res->num_rows > 0) {
        $staff_data = $s_res->fetch_assoc();
        
        // Advances in this month
        $adv_res = $conn->query("SELECT SUM(amount) as total FROM staff_advances WHERE staff_id = $staff_id AND DATE_FORMAT(advance_date, '%Y-%m') = '$month_year'");
        $month_advances = ($adv_res && $adv_res->num_rows > 0) ? floatval($adv_res->fetch_assoc()['total']) : 0;
        
        // Other deductions in this month
        $ded_res = $conn->query("SELECT SUM(amount) as total FROM staff_salary_deductions WHERE staff_id = $staff_id AND deduction_month = '$month_year'");
        $month_deductions = ($ded_res && $ded_res->num_rows > 0) ? floatval($ded_res->fetch_assoc()['total']) : 0;
        
        // Absent days
        $att_res = $conn->query("SELECT COUNT(*) as count FROM staff_attendance WHERE staff_id = $staff_id AND DATE_FORMAT(attendance_date, '%Y-%m') = '$month_year' AND status = 'Absent'");
        $absent_days = ($att_res && $att_res->num_rows > 0) ? intval($att_res->fetch_assoc()['count']) : 0;
        
        $half_res = $conn->query("SELECT COUNT(*) as count FROM staff_attendance WHERE staff_id = $staff_id AND DATE_FORMAT(attendance_date, '%Y-%m') = '$month_year' AND status = 'Half-Day'");
        $half_days = ($half_res && $half_res->num_rows > 0) ? intval($half_res->fetch_assoc()['count']) : 0;
        
        $total_absent = $absent_days + ($half_days * 0.5);
        $per_day_salary = $staff_data['basic_salary'] / 30; // Assuming 30 days
        $absent_deduction = round($total_absent * $per_day_salary, 2);
        
        $net_salary = $staff_data['basic_salary'] - $month_advances - $absent_deduction - $month_deductions;
        if ($net_salary < 0) $net_salary = 0;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Salary | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        function recalculate() {
            let basic = parseFloat(document.getElementById('basic_salary').value) || 0;
            let adv = parseFloat(document.getElementById('advance_deduction').value) || 0;
            let abs = parseFloat(document.getElementById('absent_deduction').value) || 0;
            let oth = parseFloat(document.getElementById('other_deductions').value) || 0;
            
            let net = basic - adv - abs - oth;
            document.getElementById('net_paid').value = net.toFixed(2);
            document.getElementById('display_net').innerText = '₹' + net.toFixed(2);
        }
    </script>
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Generate Salary</h1>
                <p style="color: var(--text-muted);">Calculate and process monthly salary for staff.</p>
            </div>
            <div>
                <a href="salary_report.php" class="btn btn-primary" style="background: rgba(255,255,255,0.1); box-shadow: none;"><i class='bx bx-list-ol'></i> Salary Report</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            
            <div class="glass" style="padding: 2rem; height: fit-content;">
                <h3 style="margin-bottom: 1.5rem;">Select Criteria</h3>
                <form action="salary.php" method="GET">
                    <div class="form-group">
                        <label class="form-label" for="month">Salary Month</label>
                        <input type="month" id="month" name="month" class="form-control" value="<?php echo htmlspecialchars($month_year); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="staff_id">Select Staff Member</label>
                        <select id="staff_id" name="staff_id" class="form-control" required style="background: rgba(15, 23, 42, 0.9);">
                            <option value="">-- Choose Staff --</option>
                            <?php foreach($active_staff as $s): ?>
                                <option value="<?php echo $s['id']; ?>" <?php echo ($staff_id == $s['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i class='bx bx-calculator'></i> Calculate</button>
                </form>
            </div>

            <div class="glass" style="padding: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">Salary Calculation</h3>
                
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

                <?php if ($staff_data): ?>
                    <form action="salary.php" method="POST">
                        <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
                        <input type="hidden" name="month_year" value="<?php echo htmlspecialchars($month_year); ?>">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div>
                                <label class="form-label">Basic Salary (₹)</label>
                                <input type="number" id="basic_salary" name="basic_salary" class="form-control" step="0.01" value="<?php echo $staff_data['basic_salary']; ?>" readonly style="background: rgba(255,255,255,0.05);">
                            </div>
                            <div>
                                <label class="form-label">Advances to Deduct (₹)</label>
                                <input type="number" id="advance_deduction" name="advance_deduction" class="form-control" step="0.01" value="<?php echo $month_advances; ?>" onkeyup="recalculate()" onchange="recalculate()">
                                <small style="color: var(--text-muted); font-size: 0.8rem;">Advances taken this month.</small>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div>
                                <label class="form-label">Absent Deduction (₹)</label>
                                <input type="number" id="absent_deduction" name="absent_deduction" class="form-control" step="0.01" value="<?php echo $absent_deduction; ?>" onkeyup="recalculate()" onchange="recalculate()">
                                <small style="color: var(--text-muted); font-size: 0.8rem;">Calculated based on <?php echo $total_absent; ?> absent days.</small>
                            </div>
                            <div>
                                <label class="form-label">Other Deductions (₹)</label>
                                <input type="number" id="other_deductions" name="other_deductions" class="form-control" step="0.01" value="<?php echo $month_deductions; ?>" onkeyup="recalculate()" onchange="recalculate()">
                            </div>
                        </div>

                        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1.5rem; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                            <div style="font-size: 1.2rem; font-weight: 600;">Net Salary Payable</div>
                            <div id="display_net" style="font-size: 2rem; font-weight: 700; color: #10B981;">₹<?php echo number_format($net_salary, 2); ?></div>
                            <input type="hidden" id="net_paid" name="net_paid" value="<?php echo $net_salary; ?>">
                        </div>

                        <button type="submit" name="generate_salary" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;"><i class='bx bx-check-double'></i> Process Salary Payment</button>
                    </form>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class='bx bx-search-alt' style="font-size: 3rem; margin-bottom: 1rem; color: rgba(255,255,255,0.2);"></i>
                        <p>Select a staff member and month to calculate salary.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

</body>
</html>
