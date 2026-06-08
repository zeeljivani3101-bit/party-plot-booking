<?php
session_start();
require_once '../config/db.php';

// Fetch upcoming bookings
$sql = "
    SELECT b.id, c.customer_name, c.mobile, b.event_type, b.booking_date, b.total_amount, b.status 
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    WHERE b.booking_date > CURDATE() AND b.status != 'Cancelled'
    ORDER BY b.booking_date ASC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Upcoming Events</h1>
                <p style="color: var(--text-muted);">Manage your future bookings.</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="send_bulk_reminders.php?type=upcoming" class="btn btn-primary" style="background: #10B981; color: white; box-shadow: none;">
                    <i class='bx bx-bell'></i> Send Reminders to All
                </a>
            </div>
        </div>

        <div class="glass" style="padding: 1.5rem;">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Mobile</th>
                            <th>Event Type</th>
                            <th>Event Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#BKG-<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                                    <td><?php echo htmlspecialchars($row['event_type']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['booking_date'])); ?></td>
                                    <td>
                                        <span class="badge badge-success"><?php echo $row['status']; ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 2rem; color: var(--text-muted);">No upcoming events found.</td>
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
