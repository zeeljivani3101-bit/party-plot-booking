<?php
session_start();
require_once '../config/db.php';

// Check type
$type = isset($_GET['type']) ? $_GET['type'] : 'upcoming';

// Build Query based on type
$condition = "b.booking_date > CURDATE() AND b.status != 'Cancelled'";
$title = "Upcoming Event Reminders";
$template = "Hello {name},\n\nThis is a reminder for your upcoming event at Ratan Farm on {date}. We look forward to hosting you!";

if ($type == 'today') {
    $condition = "b.booking_date = CURDATE() AND b.status != 'Cancelled'";
    $title = "Today's Event Reminders";
    $template = "Hello {name},\n\nToday is the big day! We are ready to host your amazing event at Ratan Farm.";
} elseif ($type == 'completed') {
    $condition = "b.status = 'Completed'";
    $title = "Completed Event Thank Yous";
    $template = "Hello {name},\n\nThank you for choosing Ratan Farm. We hope you had a memorable event on {date}!";
} elseif ($type == 'cancelled') {
    $condition = "b.status = 'Cancelled'";
    $title = "Cancelled Event Notices";
    $template = "Hello {name},\n\nWe noticed your event scheduled for {date} was cancelled. Please contact us if you wish to re-book.";
}

$sql = "
    SELECT b.id, c.customer_name, c.mobile, b.booking_date 
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    WHERE $condition
";
$result = $conn->query($sql);

$customers_to_message = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Format Phone
        $phone = preg_replace('/\D/', '', $row['mobile']);
        if (strlen($phone) == 10) $phone = '91' . $phone;
        
        $customers_to_message[] = [
            'name' => $row['customer_name'],
            'phone' => $phone,
            'date' => date('M d, Y', strtotime($row['booking_date']))
        ];
    }
}

// Generate Admin Summary Message
$admin_phone = "919999999999"; // Placeholder, can be changed
$total_count = count($customers_to_message);
$admin_message = "Admin Alert: Reminder sequence for '$title' completed. Total customers notified: $total_count.";

// Pass to Javascript
$customers_json = json_encode($customers_to_message);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Reminders | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .customer-list {
            margin-top: 1.5rem;
            text-align: left;
            max-height: 300px;
            overflow-y: auto;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1rem;
        }
        .customer-item {
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-pending { color: #F59E0B; }
        .status-sent { color: #10B981; }
    </style>
    
    <script>
        const customers = <?php echo $customers_json; ?>;
        const adminPhone = "<?php echo $admin_phone; ?>";
        const adminMessage = "<?php echo $admin_message; ?>";
        let currentIndex = 0;
        
        function sendNextMessage() {
            if (currentIndex < customers.length) {
                const customer = customers[currentIndex];
                let templateText = document.getElementById('message_template').value;
                
                // Replace variables
                let finalMsg = templateText.replace(/{name}/g, customer.name).replace(/{date}/g, customer.date);
                
                const waLink = `https://wa.me/${customer.phone}?text=${encodeURIComponent(finalMsg)}`;
                
                // Update UI Status
                document.getElementById(`status-${currentIndex}`).innerHTML = "<i class='bx bx-check-circle'></i> Sent";
                document.getElementById(`status-${currentIndex}`).className = "status-sent";
                
                currentIndex++;
                updateProgress();
                
                // Open WhatsApp Web
                window.open(waLink, '_blank');
            } else {
                // Done with customers, send to Admin
                const adminWaLink = `https://wa.me/${adminPhone}?text=${encodeURIComponent(adminMessage)}`;
                
                document.getElementById('action-button').innerHTML = "<i class='bx bx-check-double'></i> All Done!";
                document.getElementById('action-button').classList.add('disabled');
                document.getElementById('action-button').style.background = "#10B981";
                document.getElementById('action-button').onclick = null;
                
                window.open(adminWaLink, '_blank');
            }
        }
        
        function updateProgress() {
            document.getElementById('progress-text').innerText = `Progress: ${currentIndex} / ${customers.length}`;
            if (currentIndex >= customers.length) {
                document.getElementById('action-button').innerHTML = "<i class='bx bxl-whatsapp'></i> Send Summary to Admin";
            }
        }
    </script>
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title"><?php echo htmlspecialchars($title); ?></h1>
                <p style="color: var(--text-muted);">Process WhatsApp reminders securely via WhatsApp Web.</p>
            </div>
            <div>
                <a href="javascript:history.back()" class="btn" style="border: 1px solid var(--border-color); color: var(--text-main);"><i class='bx bx-arrow-back'></i> Go Back</a>
            </div>
        </div>

        <div class="glass" style="padding: 2rem; max-width: 800px; margin: 0 auto; text-align: center;">
            <?php if ($total_count > 0): ?>
                <h2 style="margin-bottom: 1rem;">Ready to send <?php echo $total_count; ?> messages</h2>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You can edit the message below before sending. Keep <span style="color: #10B981;">{name}</span> and <span style="color: #10B981;">{date}</span> as they will be automatically replaced for each customer.</p>
                
                <div style="text-align: left; margin-bottom: 1.5rem;">
                    <label class="form-label" for="message_template">WhatsApp Message Template</label>
                    <textarea id="message_template" class="form-control" rows="5" style="width: 100%;"><?php echo htmlspecialchars($template); ?></textarea>
                </div>
                
                <div style="margin: 2rem 0;">
                    <h3 id="progress-text" style="color: #10B981; margin-bottom: 1rem;">Progress: 0 / <?php echo $total_count; ?></h3>
                    <button id="action-button" onclick="sendNextMessage()" class="btn btn-primary" style="background: #10B981; color: white; padding: 1rem 2rem; font-size: 1.2rem; cursor: pointer; border: none; border-radius: 8px; width: 100%;">
                        <i class='bx bxl-whatsapp'></i> Send Next Message
                    </button>
                </div>
                
                <div class="customer-list">
                    <?php foreach($customers_to_message as $index => $c): ?>
                        <div class="customer-item">
                            <div>
                                <strong><?php echo htmlspecialchars($c['name']); ?></strong> 
                                <span style="color: var(--text-muted); font-size: 0.9rem;">(<?php echo htmlspecialchars($c['phone']); ?>)</span>
                            </div>
                            <div id="status-<?php echo $index; ?>" class="status-pending">
                                <i class='bx bx-time-five'></i> Pending
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <h2 style="margin-bottom: 1rem;">No customers found for this category!</h2>
                <p style="color: var(--text-muted);">There are no messages to send right now.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
