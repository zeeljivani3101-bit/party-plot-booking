<?php
session_start();
require_once '../config/db.php';

// Fetch Total Bookings
$res_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings");
$total_bookings = ($res_bookings && $res_bookings->num_rows > 0) ? $res_bookings->fetch_assoc()['count'] : 0;

// Fetch Total Revenue
$res_revenue = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status != 'Cancelled'");
if ($res_revenue && $res_revenue->num_rows > 0) {
    $row_rev = $res_revenue->fetch_assoc();
    $total_revenue = isset($row_rev['total']) ? floatval($row_rev['total']) : 0;
} else {
    $total_revenue = 0;
}

// Fetch Pending Payments (using balance_amount > 0 as the logic for pending payment logic)
$res_pending = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE balance_amount > 0 AND status != 'Cancelled'");
$pending_payments = ($res_pending && $res_pending->num_rows > 0) ? $res_pending->fetch_assoc()['count'] : 0;

// Fetch Active Customers
$res_customers = $conn->query("SELECT COUNT(*) as count FROM customers");
$active_customers = ($res_customers && $res_customers->num_rows > 0) ? $res_customers->fetch_assoc()['count'] : 0;

// Fetch Recent Bookings
$recent_bookings = $conn->query("
    SELECT b.id, c.customer_name, b.booking_date, b.status, b.total_amount 
    FROM bookings b 
    LEFT JOIN customers c ON b.customer_id = c.id 
    ORDER BY b.created_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PartyPlot</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-layout">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard Overview</h1>
                <p style="color: var(--text-muted);">Welcome back, Admin! Here's what's happening today.</p>
            </div>
            <div>
                <a href="add_booking.php" class="btn btn-primary"><i class='bx bx-plus' style="margin-right: 0.5rem;"></i> New Booking</a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card glass">
                <div class="stat-icon"><i class='bx bx-calendar-check'></i></div>
                <div class="stat-info">
                    <p>Total Bookings</p>
                    <h3><?php echo number_format($total_bookings); ?></h3>
                </div>
            </div>
            <div class="stat-card glass">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--secondary);"><i class='bx bx-rupee'></i></div>
                <div class="stat-info">
                    <p>Total Revenue</p>
                    <h3>₹<?php echo number_format($total_revenue, 2); ?></h3>
                </div>
            </div>
            <div class="stat-card glass">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;"><i class='bx bx-time'></i></div>
                <div class="stat-info">
                    <p>Pending Bookings</p>
                    <h3><?php echo number_format($pending_payments); ?></h3>
                </div>
            </div>
            <div class="stat-card glass">
                <div class="stat-icon" style="background: rgba(236, 72, 153, 0.1); color: #EC4899;"><i class='bx bx-group'></i></div>
                <div class="stat-info">
                    <p>Active Customers</p>
                    <h3><?php echo number_format($active_customers); ?></h3>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="glass" style="padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3>Recent Bookings</h3>
                <a href="view_bookings.php" style="font-size: 0.875rem; font-weight: 600;">View All</a>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_bookings && $recent_bookings->num_rows > 0): ?>
                            <?php while($row = $recent_bookings->fetch_assoc()): ?>
                                <tr>
                                    <td>#BKG-<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['booking_date'])); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'Confirmed' || $row['status'] == 'Completed'): ?>
                                            <span class="badge badge-success"><?php echo $row['status']; ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-pending"><?php echo $row['status']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 2rem; color: var(--text-muted);">No recent bookings found.</td>
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
