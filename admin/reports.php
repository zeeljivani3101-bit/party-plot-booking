<?php
session_start();
require_once '../config/db.php';

// Fetch Revenue Summary
// 1. Total Income (Actual payments received)
$res_income = $conn->query("SELECT SUM(amount) as income FROM payments");
$total_income = ($res_income && $res_income->num_rows > 0) ? $res_income->fetch_assoc()['income'] : 0;
if (!$total_income) $total_income = 0;

// 2. Pending Dues (Total balance amount from active bookings)
$res_pending = $conn->query("SELECT SUM(balance_amount) as pending FROM bookings WHERE status != 'Cancelled'");
$pending_dues = ($res_pending && $res_pending->num_rows > 0) ? $res_pending->fetch_assoc()['pending'] : 0;
if (!$pending_dues) $pending_dues = 0;

// 3. Cancellations (If we want to track cancelled amount)
$res_cancelled = $conn->query("SELECT SUM(total_amount) as cancelled FROM bookings WHERE status = 'Cancelled'");
$cancelled_amount = ($res_cancelled && $res_cancelled->num_rows > 0) ? $res_cancelled->fetch_assoc()['cancelled'] : 0;
if (!$cancelled_amount) $cancelled_amount = 0;


// Fetch Booking Statistics
$res_stats = $conn->query("SELECT event_type, COUNT(*) as count FROM bookings GROUP BY event_type ORDER BY count DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Business Reports</h1>
                <p style="color: var(--text-muted);">Analyze revenue and booking trends.</p>
            </div>
            <div>
                <button class="btn btn-primary" onclick="window.print()"><i class='bx bx-printer' style="margin-right: 0.5rem;"></i> Print Report</button>
            </div>
        </div>

        <!-- Filter Controls (Functional for UI, queries can be extended later) -->
        <div class="glass" style="padding: 1.5rem; margin-bottom: 2rem;">
            <form style="display: flex; gap: 1rem; align-items: flex-end;">
                <div>
                    <label class="form-label" style="font-size: 0.75rem;">Start Date</label>
                    <input type="date" class="form-control" style="padding: 0.5rem;">
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.75rem;">End Date</label>
                    <input type="date" class="form-control" style="padding: 0.5rem;">
                </div>
                <button type="button" class="btn btn-primary" style="padding: 0.5rem 1.5rem;" onclick="alert('Filtering will be implemented soon!')">Generate</button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Revenue Summary -->
            <div class="glass" style="padding: 2rem;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Revenue Summary</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Total Income Received:</span>
                        <span style="font-weight: 600; color: var(--secondary);">₹ <?php echo number_format($total_income, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Pending Dues:</span>
                        <span style="font-weight: 600; color: #F59E0B;">₹ <?php echo number_format($pending_dues, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Lost to Cancellations:</span>
                        <span style="font-weight: 600; color: #EF4444;">₹ <?php echo number_format($cancelled_amount, 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Booking Stats -->
            <div class="glass" style="padding: 2rem;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Booking Statistics (By Event Type)</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if ($res_stats && $res_stats->num_rows > 0): ?>
                        <?php while($row = $res_stats->fetch_assoc()): ?>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);"><?php echo htmlspecialchars($row['event_type']); ?>:</span>
                                <span style="font-weight: 600;"><?php echo $row['count']; ?></span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="color: var(--text-muted);">No bookings available to analyze.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
