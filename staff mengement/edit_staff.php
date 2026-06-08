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
$staff = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM staff WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $staff = $res->fetch_assoc();
    } else {
        header("Location: staff_list.php");
        exit;
    }
} else {
    header("Location: staff_list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $role = $conn->real_escape_string($_POST['role'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $basic_salary = floatval($_POST['basic_salary'] ?? 0);
    $status = $conn->real_escape_string($_POST['status'] ?? 'Active');
    
    // Check if phone exists for another staff
    $check_res = $conn->query("SELECT id FROM staff WHERE phone = '$phone' AND id != $id");
    
    if ($check_res && $check_res->num_rows > 0) {
        $error = "Phone number already used by another staff member.";
    } else {
        $sql = "UPDATE staff SET 
                name = '$name', 
                phone = '$phone', 
                email = '$email', 
                role = '$role', 
                address = '$address', 
                basic_salary = $basic_salary,
                status = '$status'
                WHERE id = $id";
                
        if ($conn->query($sql) === TRUE) {
            $message = "Staff updated successfully!";
            // refresh data
            $res = $conn->query("SELECT * FROM staff WHERE id = $id");
            $staff = $res->fetch_assoc();
        } else {
            $error = "Error updating staff: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff | PartyPlot Admin</title>
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
                <h1 class="page-title">Edit Staff Details</h1>
                <p style="color: var(--text-muted);">Update employee information.</p>
            </div>
            <div>
                <a href="staff_list.php" class="btn btn-primary" style="background: rgba(255,255,255,0.1); box-shadow: none;"><i class='bx bx-arrow-back'></i> Back to List</a>
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

            <form action="edit_staff.php?id=<?php echo $staff['id']; ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo $staff['id']; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($staff['name']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number *</label>
                        <input type="text" id="phone" name="phone" class="form-control" required value="<?php echo htmlspecialchars($staff['phone']); ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($staff['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="role">Role / Position *</label>
                        <input type="text" id="role" name="role" class="form-control" required value="<?php echo htmlspecialchars($staff['role']); ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="basic_salary">Basic Monthly Salary (₹) *</label>
                        <input type="number" id="basic_salary" name="basic_salary" class="form-control" step="0.01" required value="<?php echo htmlspecialchars($staff['basic_salary']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="status">Account Status</label>
                        <select id="status" name="status" class="form-control" style="background: rgba(15, 23, 42, 0.9);">
                            <option value="Active" <?php echo ($staff['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Inactive" <?php echo ($staff['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($staff['address']); ?></textarea>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary"><i class='bx bx-save' style="margin-right: 0.5rem;"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
