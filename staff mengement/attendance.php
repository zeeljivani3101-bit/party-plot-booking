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

$attendance_date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $date = $conn->real_escape_string($_POST['attendance_date']);
    $statuses = $_POST['status'] ?? [];
    
    $success_count = 0;
    foreach ($statuses as $staff_id => $status) {
        $staff_id = intval($staff_id);
        $status = $conn->real_escape_string($status);
        
        // Check if attendance already exists for this staff and date
        $check = $conn->query("SELECT id FROM staff_attendance WHERE staff_id = $staff_id AND attendance_date = '$date'");
        
        if ($check && $check->num_rows > 0) {
            // Update
            $sql = "UPDATE staff_attendance SET status = '$status' WHERE staff_id = $staff_id AND attendance_date = '$date'";
        } else {
            // Insert
            $sql = "INSERT INTO staff_attendance (staff_id, attendance_date, status) VALUES ($staff_id, '$date', '$status')";
        }
        
        if ($conn->query($sql) === TRUE) {
            $success_count++;
        }
    }
    
    $message = "Attendance marked successfully for $success_count staff members on " . date('d M Y', strtotime($date));
    $attendance_date = $date;
}

// Fetch all active staff
$staff_res = $conn->query("SELECT id, name, role FROM staff WHERE status = 'Active' ORDER BY name ASC");
$active_staff = [];
if ($staff_res) {
    while($row = $staff_res->fetch_assoc()) {
        $active_staff[] = $row;
    }
}

// Fetch existing attendance for the selected date
$existing_att = [];
$att_res = $conn->query("SELECT staff_id, status FROM staff_attendance WHERE attendance_date = '$attendance_date'");
if ($att_res) {
    while($row = $att_res->fetch_assoc()) {
        $existing_att[$row['staff_id']] = $row['status'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .radio-group {
            display: flex;
            gap: 1rem;
        }
        .radio-label {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            cursor: pointer;
            font-size: 0.9rem;
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
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Daily Attendance</h1>
                <p style="color: var(--text-muted);">Mark attendance for all active staff members.</p>
            </div>
            <div>
                <a href="attendance_report.php" class="btn btn-primary" style="background: rgba(255,255,255,0.1); box-shadow: none;"><i class='bx bx-calendar-check'></i> View Report</a>
            </div>
        </div>

        <div class="glass" style="padding: 2rem;">
            
            <form action="attendance.php" method="GET" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="date">Select Date</label>
                    <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($attendance_date); ?>" max="<?php echo date('Y-m-d'); ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="background: rgba(255,255,255,0.1); box-shadow: none;">Change Date</button>
            </form>

            <?php if ($message): ?>
                <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--secondary); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <i class='bx bx-check-circle'></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="attendance.php" method="POST">
                <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($attendance_date); ?>">
                
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Staff Name</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($active_staff) > 0): ?>
                                <?php foreach($active_staff as $staff): ?>
                                    <?php 
                                        // Default to present if not marked, else use existing
                                        $current_status = $existing_att[$staff['id']] ?? 'Present'; 
                                    ?>
                                    <tr>
                                        <td style="font-weight: 500;"><?php echo htmlspecialchars($staff['name']); ?></td>
                                        <td style="color: var(--text-muted);"><?php echo htmlspecialchars($staff['role']); ?></td>
                                        <td>
                                            <div class="radio-group">
                                                <label class="radio-label" style="color: #10B981;">
                                                    <input type="radio" name="status[<?php echo $staff['id']; ?>]" value="Present" <?php echo $current_status == 'Present' ? 'checked' : ''; ?>>
                                                    Present
                                                </label>
                                                <label class="radio-label" style="color: #EF4444;">
                                                    <input type="radio" name="status[<?php echo $staff['id']; ?>]" value="Absent" <?php echo $current_status == 'Absent' ? 'checked' : ''; ?>>
                                                    Absent
                                                </label>
                                                <label class="radio-label" style="color: #F59E0B;">
                                                    <input type="radio" name="status[<?php echo $staff['id']; ?>]" value="Half-Day" <?php echo $current_status == 'Half-Day' ? 'checked' : ''; ?>>
                                                    Half-Day
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem;">No active staff found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (count($active_staff) > 0): ?>
                <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                    <button type="submit" name="mark_attendance" class="btn btn-primary"><i class='bx bx-save'></i> Save Attendance</button>
                </div>
                <?php endif; ?>
            </form>

        </div>
    </main>
</div>

</body>
</html>
