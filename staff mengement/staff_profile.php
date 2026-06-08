<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: staff_list.php");
    exit;
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM staff WHERE id = $id");
if (!$res || $res->num_rows == 0) {
    header("Location: staff_list.php");
    exit;
}
$staff = $res->fetch_assoc();

// Get Attendance Summary (Current Month)
$current_month = date('Y-m');
$att_sql = "SELECT status, COUNT(*) as count FROM staff_attendance 
            WHERE staff_id = $id AND DATE_FORMAT(attendance_date, '%Y-%m') = '$current_month'
            GROUP BY status";
$att_res = $conn->query($att_sql);
$attendance = ['Present' => 0, 'Absent' => 0, 'Half-Day' => 0];
if ($att_res) {
    while($row = $att_res->fetch_assoc()) {
        $attendance[$row['status']] = $row['count'];
    }
}

// Get Total Advances
$adv_sql = "SELECT SUM(amount) as total_advance FROM staff_advances WHERE staff_id = $id";
$adv_res = $conn->query($adv_sql);
$total_advance = ($adv_res && $adv_res->num_rows > 0) ? $adv_res->fetch_assoc()['total_advance'] : 0;
if (!$total_advance) $total_advance = 0;

// Get Advance History (Recent 5)
$adv_hist = $conn->query("SELECT * FROM staff_advances WHERE staff_id = $id ORDER BY advance_date DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile | PartyPlot Admin</title>
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
                <h1 class="page-title">Staff Profile</h1>
                <p style="color: var(--text-muted);">Overview of employee performance and details.</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="edit_staff.php?id=<?php echo $staff['id']; ?>" class="btn btn-primary" ><i class='bx bx-edit-alt'></i> Edit Profile</a>
                <a href="staff_list.php" class="btn btn-primary" ><i class='bx bx-list-ul'></i> All Staff</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            
            <!-- Left Column: Details -->
            <div class="glass" style="padding: 2rem; height: fit-content;">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 100px; height: 100px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; margin: 0 auto 1rem auto;">
                        <?php echo strtoupper(substr($staff['name'], 0, 1)); ?>
                    </div>
                    <h2 style="font-size: 1.5rem; margin-bottom: 0.2rem;"><?php echo htmlspecialchars($staff['name']); ?></h2>
                    <p style="color: var(--primary); font-weight: 500;"><?php echo htmlspecialchars($staff['role']); ?></p>
                    <div style="margin-top: 1rem;">
                        <span >
                            <?php echo $staff['status']; ?>
                        </span>
                    </div>
                </div>

                <hr style="border-color: rgba(255,255,255,0.1); margin: 1.5rem 0;">

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.2rem;">Phone Number</div>
                        <div style="font-weight: 500;"><i class='bx bx-phone'></i> <?php echo htmlspecialchars($staff['phone']); ?></div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.2rem;">Email Address</div>
                        <div style="font-weight: 500;"><i class='bx bx-envelope'></i> <?php echo $staff['email'] ? htmlspecialchars($staff['email']) : 'N/A'; ?></div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.2rem;">Basic Salary</div>
                        <div style="font-weight: 500; color: var(--secondary);">₹<?php echo number_format($staff['basic_salary'], 2); ?></div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.2rem;">Date of Joining</div>
                        <div style="font-weight: 500;"><i class='bx bx-calendar'></i> <?php echo date('d M, Y', strtotime($staff['joining_date'])); ?></div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.2rem;">Address</div>
                        <div style="font-weight: 500;"><i class='bx bx-map'></i> <?php echo $staff['address'] ? htmlspecialchars($staff['address']) : 'N/A'; ?></div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Stats & History -->
            <div>
                <!-- Attendance Stats -->
                <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">Attendance (<?php echo date('F Y'); ?>)</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem;">
                    <div class="glass" style="padding: 1.5rem; text-align: center; border-left: 4px solid #10B981;">
                        <div style="color: var(--text-muted); font-size: 0.9rem;">Present</div>
                        <div style="font-size: 2rem; font-weight: 700; color: #10B981;"><?php echo $attendance['Present']; ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Days</div>
                    </div>
                    <div class="glass" style="padding: 1.5rem; text-align: center; border-left: 4px solid #EF4444;">
                        <div style="color: var(--text-muted); font-size: 0.9rem;">Absent</div>
                        <div style="font-size: 2rem; font-weight: 700; color: #EF4444;"><?php echo $attendance['Absent']; ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Days</div>
                    </div>
                    <div class="glass" style="padding: 1.5rem; text-align: center; border-left: 4px solid #F59E0B;">
                        <div style="color: var(--text-muted); font-size: 0.9rem;">Half-Day</div>
                        <div style="font-size: 2rem; font-weight: 700; color: #F59E0B;"><?php echo $attendance['Half-Day']; ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Days</div>
                    </div>
                </div>

                <!-- Advances Section -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.2rem;">Advances Overview</h3>
                    <div style="font-size: 1.1rem; font-weight: 600;">Total Lifetime Advance: <span style="color: var(--secondary);">₹<?php echo number_format($total_advance, 2); ?></span></div>
                </div>
                
                <div class="glass" style="padding: 1.5rem;">
                    <h4 style="margin-bottom: 1rem; color: var(--text-muted);">Recent Advance Payments</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.1); color: var(--text-muted);">Date</th>
                                <th style="text-align: left; padding: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.1); color: var(--text-muted);">Amount</th>
                                <th style="text-align: left; padding: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.1); color: var(--text-muted);">Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($adv_hist && $adv_hist->num_rows > 0): ?>
                                <?php while($adv = $adv_hist->fetch_assoc()): ?>
                                    <tr>
                                        <td style="padding: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.05);"><?php echo date('d M, Y', strtotime($adv['advance_date'])); ?></td>
                                        <td style="padding: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.05); color: #EF4444; font-weight: 500;">-₹<?php echo number_format($adv['amount'], 2); ?></td>
                                        <td style="padding: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.05);"><?php echo htmlspecialchars($adv['reason']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 1.5rem; color: var(--text-muted);">No recent advances found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div style="margin-top: 1rem; text-align: right;">
                        <a href="staff_advance.php" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.9rem;">Give Advance</a>
                    </div>
                </div>

            </div>

        </div>
    </main>
</div>

</body>
</html>
