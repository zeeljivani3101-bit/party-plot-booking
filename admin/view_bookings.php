<?php
session_start();
require_once '../config/db.php';

// Fetch all bookings with customer details
$sql = "
    SELECT b.id, c.customer_name, b.event_type, b.booking_date, b.total_amount, b.status 
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    ORDER BY b.created_at DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings | PartyPlot Admin</title>
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
                <h1 class="page-title">All Bookings</h1>
                <p style="color: var(--text-muted);">Manage event schedules and booking status.</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="export_bookings.php" class="btn btn-primary" style="background: #10B981; color: white; box-shadow: none;"><i class='bx bx-spreadsheet'></i> Export to Excel</a>
                <a href="add_booking.php" class="btn btn-primary"><i class='bx bx-plus' style="margin-right: 0.5rem;"></i> New Booking</a>
            </div>
        </div>

        <div class="glass" style="padding: 1.5rem;">
            <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                <input type="date" class="form-control" style="max-width: 200px;">
                <select class="form-control" >
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Completed">Completed</option>
                </select>
                <button class="btn btn-primary" >Filter</button>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Event Type</th>
                            <th>Event Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#BKG-<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['event_type']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['booking_date'])); ?></td>
                                    <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'Confirmed' || $row['status'] == 'Completed'): ?>
                                            <span class="badge badge-success"><?php echo $row['status']; ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-pending"><?php echo $row['status']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm"  title="View Details"><i class='bx bx-show'></i></a>
                                        <a href="payment.php?booking_id=<?php echo $row['id']; ?>" class="btn btn-sm"  title="Add Payment"><i class='bx bx-rupee'></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 2rem; color: var(--text-muted);">No bookings found.</td>
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
