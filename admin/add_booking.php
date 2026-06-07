<?php
session_start();
require_once '../config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = intval($_POST['customer_id']);
    $event_type = $conn->real_escape_string($_POST['event_type'] ?? '');
    $booking_date = $conn->real_escape_string($_POST['booking_date'] ?? '');
    $total_amount = floatval($_POST['total_amount'] ?? 0);
    $advance_amount = floatval($_POST['advance_amount'] ?? 0);
    $balance_amount = $total_amount - $advance_amount;
    
    $status = ($advance_amount >= $total_amount) ? 'Completed' : (($advance_amount > 0) ? 'Confirmed' : 'Pending');

    $sql = "INSERT INTO bookings (customer_id, event_type, booking_date, total_amount, advance_amount, balance_amount, status) 
            VALUES ($customer_id, '$event_type', '$booking_date', $total_amount, $advance_amount, $balance_amount, '$status')";
            
    if ($conn->query($sql) === TRUE) {
        $message = "Booking recorded successfully!";
    } else {
        $error = "Error saving booking: " . $conn->error;
    }
}

// Fetch all customers for the dropdown
$customers_result = $conn->query("SELECT id, customer_name, mobile FROM customers ORDER BY customer_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Booking | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Create New Booking</h1>
                <p style="color: var(--text-muted);">Schedule an event for a customer.</p>
            </div>
            <div>
                <a href="view_bookings.php" class="btn btn-primary" style="background: rgba(255,255,255,0.1); box-shadow: none;">All Bookings</a>
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

            <form action="add_booking.php" method="POST">
                <div class="form-group">
                    <label class="form-label" for="customer_id">Select Customer</label>
                    <select id="customer_id" name="customer_id" class="form-control" required style="background: rgba(15, 23, 42, 0.9);">
                        <option value="">-- Choose Customer --</option>
                        <?php if ($customers_result && $customers_result->num_rows > 0): ?>
                            <?php while($c = $customers_result->fetch_assoc()): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['customer_name']) . ' (' . htmlspecialchars($c['mobile']) . ')'; ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="booking_date">Event Date</label>
                        <input type="date" id="booking_date" name="booking_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="event_type">Event Type</label>
                        <select id="event_type" name="event_type" class="form-control" required style="background: rgba(15, 23, 42, 0.9);">
                            <option value="Wedding">Wedding</option>
                            <option value="Reception">Reception</option>
                            <option value="Birthday">Birthday Party</option>
                            <option value="Corporate">Corporate Event</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="total_amount">Total Amount (₹)</label>
                        <input type="number" id="total_amount" name="total_amount" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="advance_amount">Advance Amount (₹)</label>
                        <input type="number" id="advance_amount" name="advance_amount" class="form-control" step="0.01" value="0" required>
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary"><i class='bx bx-check' style="margin-right: 0.5rem;"></i> Confirm Booking</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
