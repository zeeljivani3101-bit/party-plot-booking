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
    $advance_date = $conn->real_escape_string($_POST['advance_date'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $reason = $conn->real_escape_string($_POST['reason'] ?? '');
    
    if ($staff_id > 0 && $amount > 0 && $advance_date != '') {
        $sql = "INSERT INTO staff_advances (staff_id, advance_date, amount, reason) VALUES ($staff_id, '$advance_date', $amount, '$reason')";
        if ($conn->query($sql) === TRUE) {
            $message = "Advance payment recorded successfully!";
        } else {
            $error = "Error recording advance: " . $conn->error;
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Advance | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Issue Advance Payment</h1>
                <p style="color: var(--text-muted);">Record a new advance payment for a staff member.</p>
            </div>
            <div>
                <a href="advance_history.php" class="btn btn-primary" ><i class='bx bx-history'></i> Advance History</a>
            </div>
        </div>

        <div class="glass" style="padding: 2rem; max-width: 600px;">
            <?php if ($message): ?>
                <div >
                    <i class='bx bx-check-circle'></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div >
                    <i class='bx bx-error-circle'></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="staff_advance.php" method="POST">
                <div class="form-group">
                    <label class="form-label" for="staff_id">Select Staff Member *</label>
                    <select id="staff_id" name="staff_id" class="form-control" required >
                        <option value="">-- Choose Staff --</option>
                        <?php foreach($active_staff as $staff): ?>
                            <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['name']) . ' (' . htmlspecialchars($staff['role']) . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="advance_date">Advance Date *</label>
                    <input type="date" id="advance_date" name="advance_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="amount">Amount (₹) *</label>
                    <input type="number" id="amount" name="amount" class="form-control" step="0.01" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label class="form-label" for="reason">Reason / Remarks</label>
                    <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Why was the advance given?"></textarea>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i class='bx bx-check-circle'></i> Record Advance</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
