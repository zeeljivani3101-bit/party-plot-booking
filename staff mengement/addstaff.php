<?php
session_start();
require_once '../config/db.php';

// Security Check: Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $role = $conn->real_escape_string($_POST['role'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $basic_salary = floatval($_POST['basic_salary'] ?? 0);
    $joining_date = $conn->real_escape_string($_POST['joining_date'] ?? '');
    
    // Check if phone or email already exists for active staff
    $check_sql = "SELECT id FROM staff WHERE phone = '$phone'";
    $check_res = $conn->query($check_sql);
    
    if ($check_res && $check_res->num_rows > 0) {
        $error = "Staff member with this phone number already exists.";
    } else {
        $sql = "INSERT INTO staff (name, phone, email, role, address, basic_salary, joining_date, status) 
                VALUES ('$name', '$phone', '$email', '$role', '$address', $basic_salary, '$joining_date', 'Active')";
                
        if ($conn->query($sql) === TRUE) {
            $message = "Staff member added successfully!";
        } else {
            $error = "Error adding staff: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Add New Staff</h1>
                <p style="color: var(--text-muted);">Register a new employee into the system.</p>
            </div>
            <div>
                <a href="staff_list.php" class="btn btn-primary" style="background: rgba(255,255,255,0.1); box-shadow: none;"><i class='bx bx-list-ul'></i> View All Staff</a>
            </div>
        </div>

        <div class="glass" style="padding: 2rem; max-width: 800px;">
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

            <form action="addstaff.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. John Doe">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number *</label>
                        <input type="text" id="phone" name="phone" class="form-control" required placeholder="10-digit number">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="role">Role / Position *</label>
                        <input type="text" id="role" name="role" class="form-control" required placeholder="e.g. Manager, Guard, Waiter">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="basic_salary">Basic Monthly Salary (₹) *</label>
                        <input type="number" id="basic_salary" name="basic_salary" class="form-control" step="0.01" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="joining_date">Joining Date *</label>
                        <input type="date" id="joining_date" name="joining_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3" placeholder="Full residential address..."></textarea>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary"><i class='bx bx-user-plus' style="margin-right: 0.5rem;"></i> Register Staff Member</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
