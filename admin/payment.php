<?php
session_start();
require_once '../config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);
    $amount = floatval($_POST['amount']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $transaction_id = $conn->real_escape_string($_POST['transaction_id'] ?? '');

    // Insert payment
    $sql = "INSERT INTO payments (booking_id, amount, payment_method, transaction_id) 
            VALUES ($booking_id, $amount, '$payment_method', '$transaction_id')";
            
    if ($conn->query($sql) === TRUE) {
        // Update booking balance and status
        $conn->query("UPDATE bookings SET balance_amount = balance_amount - $amount WHERE id = $booking_id");
        $conn->query("UPDATE bookings SET status = 'Completed' WHERE id = $booking_id AND balance_amount <= 0");
        $message = "Payment received successfully!";
    } else {
        $error = "Error saving payment: " . $conn->error;
    }
}

// Fetch bookings with pending balances for the dropdown
$sql_bookings = "
    SELECT b.id, c.customer_name, b.balance_amount 
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    WHERE b.balance_amount > 0 AND b.status != 'Cancelled'
";
$pending_bookings = $conn->query($sql_bookings);

// If booking_id is passed in URL
$preselect_booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | PartyPlot Admin</title>
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
                <h1 class="page-title">Record Payment</h1>
                <p style="color: var(--text-muted);">Process transactions for existing bookings.</p>
            </div>
        </div>

        <div class="glass" style="padding: 2rem; max-width: 600px;">
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

            <form action="payment.php" method="POST">
                <div class="form-group">
                    <label class="form-label" for="booking_id">Booking Reference</label>
                    <select id="booking_id" name="booking_id" class="form-control" required >
                        <option value="">-- Select Booking --</option>
                        <?php if ($pending_bookings && $pending_bookings->num_rows > 0): ?>
                            <?php while($b = $pending_bookings->fetch_assoc()): ?>
                                <option value="<?php echo $b['id']; ?>" <?php echo ($preselect_booking_id == $b['id']) ? 'selected' : ''; ?>>
                                    #BKG-<?php echo $b['id']; ?> - <?php echo htmlspecialchars($b['customer_name']); ?> (Due: ₹<?php echo number_format($b['balance_amount'], 2); ?>)
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="amount">Payment Amount (₹)</label>
                    <input type="number" id="amount" name="amount" class="form-control" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="form-control" required >
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer / NEFT</option>
                        <option value="UPI">UPI / GPay</option>
                        <option value="Credit Card">Credit Card</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="transaction_id">Transaction ID (Optional)</label>
                    <input type="text" id="transaction_id" name="transaction_id" class="form-control">
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary"><i class='bx bx-rupee' style="margin-right: 0.5rem;"></i> Process Payment</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
