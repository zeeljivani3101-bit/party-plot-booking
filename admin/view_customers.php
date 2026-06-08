<?php
session_start();
require_once '../config/db.php';

$message = '';
$error = '';

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // First, delete associated payments (linked to bookings)
    $conn->query("DELETE FROM payments WHERE booking_id IN (SELECT id FROM bookings WHERE customer_id = $delete_id)");
    
    // Second, delete associated bookings
    $conn->query("DELETE FROM bookings WHERE customer_id = $delete_id");
    
    // Finally, delete the customer
    $sql = "DELETE FROM customers WHERE id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        $message = "Customer and all associated bookings deleted successfully!";
    } else {
        $error = "Error deleting customer: " . $conn->error;
    }
}

// Fetch all customers (and count their bookings)
$sql = "
    SELECT c.id, c.customer_name, c.mobile as phone, 
           COUNT(b.id) as total_bookings 
    FROM customers c
    LEFT JOIN bookings b ON c.id = b.customer_id
    GROUP BY c.id
    ORDER BY c.created_at DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customers | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this customer? This will also delete their bookings.")) {
                window.location.href = "view_customers.php?delete_id=" + id;
            }
        }
    </script>
</head>
<body>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Customers Directory</h1>
                <p style="color: var(--text-muted);">Manage your registered clients.</p>
            </div>
            <div>
                <a href="add_customer.php" class="btn btn-primary"><i class='bx bx-plus' style="margin-right: 0.5rem;"></i> Add Customer</a>
            </div>
        </div>

        <div class="glass" style="padding: 1.5rem;">
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

            <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem;">
                <input type="text" class="form-control" placeholder="Search customers..." style="max-width: 300px;">
                <button class="btn btn-primary" >Search</button>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Total Bookings</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo $row['total_bookings']; ?></td>
                                    <td>
                                        <a href="edit_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm"  title="Edit"><i class='bx bx-edit-alt'></i></a>
                                        <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn btn-sm btn-danger" style="border: none; cursor: pointer;"><i class='bx bx-trash'></i></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 2rem; color: var(--text-muted);">No customers found in database.</td>
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
