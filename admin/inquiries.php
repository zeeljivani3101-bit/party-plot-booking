<?php
session_start();
require_once '../config/db.php';

$message = '';

// Handle Status Update
if (isset($_GET['mark_status']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = $conn->real_escape_string($_GET['mark_status']);
    
    if (in_array($status, ['New', 'Contacted', 'Resolved'])) {
        $conn->query("UPDATE inquiries SET status = '$status' WHERE id = $id");
        $message = "Inquiry status updated to $status.";
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM inquiries WHERE id = $id");
    $message = "Inquiry deleted.";
}

// Fetch inquiries
$inquiries = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries | PartyPlot Admin</title>
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
                <h1 class="page-title">Customer Inquiries</h1>
                <p style="color: var(--text-muted);">Manage requests submitted from the public website.</p>
            </div>
        </div>

        <div class="glass" style="padding: 1.5rem;">
            <?php if ($message): ?>
                <div >
                    <i class='bx bx-check-circle'></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Event Date</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($inquiries && $inquiries->num_rows > 0): ?>
                            <?php while($row = $inquiries->fetch_assoc()): ?>
                                <tr>
                                    <td style="white-space: nowrap;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['event_date'])); ?></td>
                                    <td style="max-width: 200px; white-space: normal;"><?php echo htmlspecialchars($row['message']); ?></td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'badge-pending';
                                            if ($row['status'] == 'Contacted') $badgeClass = 'badge-success';
                                            if ($row['status'] == 'Resolved') $badgeClass = 'badge-success';
                                            if ($row['status'] == 'New') $badgeClass = 'badge-danger'; // Reuse or add custom
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $row['status']; ?></span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <select onchange="window.location.href='inquiries.php?id=<?php echo $row['id']; ?>&mark_status=' + this.value" class="form-control" >
                                                <option value="">Update...</option>
                                                <option value="New" <?php if($row['status']=='New') echo 'selected'; ?>>New</option>
                                                <option value="Contacted" <?php if($row['status']=='Contacted') echo 'selected'; ?>>Contacted</option>
                                                <option value="Resolved" <?php if($row['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
                                            </select>
                                            <a href="inquiries.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" style="padding: 0.3rem 0.5rem;" onclick="return confirm('Delete this inquiry?')"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 2rem; color: var(--text-muted);">No inquiries found.</td>
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
