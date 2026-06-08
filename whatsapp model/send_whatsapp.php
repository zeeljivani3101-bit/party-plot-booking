<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch all templates
$templates = [];
$t_res = $conn->query("SELECT * FROM whatsapp_templates ORDER BY template_name ASC");
if ($t_res) {
    while($row = $t_res->fetch_assoc()) {
        $templates[] = $row;
    }
}

// Fetch all bookings for the dropdown
$bookings = [];
$b_sql = "SELECT b.id as booking_id, c.customer_name, c.mobile, b.event_type, b.booking_date, b.total_amount, b.advance_amount, b.balance_amount 
          FROM bookings b 
          JOIN customers c ON b.customer_id = c.id 
          ORDER BY b.id DESC LIMIT 100";
$b_res = $conn->query($b_sql);
if ($b_res) {
    while($row = $b_res->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Convert PHP arrays to JSON for JavaScript handling
$bookings_json = json_encode($bookings);
$templates_json = json_encode($templates);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send WhatsApp | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <script>
        const bookingsData = <?php echo $bookings_json; ?>;
        const templatesData = <?php echo $templates_json; ?>;
        
        function updatePreview() {
            const bookingSelect = document.getElementById('booking_select');
            const templateSelect = document.getElementById('template_select');
            const phoneInput = document.getElementById('customer_phone');
            const previewBox = document.getElementById('message_preview');
            const sendBtn = document.getElementById('send_btn');
            
            const selectedBookingIdx = bookingSelect.selectedIndex - 1;
            const selectedTemplateIdx = templateSelect.selectedIndex - 1;
            
            if (selectedBookingIdx >= 0 && selectedTemplateIdx >= 0) {
                const booking = bookingsData[selectedBookingIdx];
                const template = templatesData[selectedTemplateIdx];
                
                // Auto-fill phone number
                let phone = booking.mobile.replace(/\D/g,''); // remove non-digits
                if (phone.length === 10) {
                    phone = "91" + phone; // Add India country code if 10 digits
                }
                phoneInput.value = phone;
                
                // Replace variables
                let message = template.content;
                message = message.replace(/{customer_name}/g, booking.customer_name);
                
                // Format date nicely
                let d = new Date(booking.booking_date);
                let dateStr = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                
                message = message.replace(/{booking_date}/g, dateStr);
                message = message.replace(/{event_type}/g, booking.event_type);
                message = message.replace(/{total_amount}/g, booking.total_amount);
                message = message.replace(/{advance_amount}/g, booking.advance_amount);
                message = message.replace(/{balance_amount}/g, booking.balance_amount);
                
                previewBox.value = message;
                
                // Update WhatsApp Link
                const waLink = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                sendBtn.href = waLink;
                sendBtn.classList.remove('disabled');
            } else {
                phoneInput.value = '';
                previewBox.value = '';
                sendBtn.href = '#';
                sendBtn.classList.add('disabled');
            }
        }
        
        function customEdit() {
            const phone = document.getElementById('customer_phone').value;
            const message = document.getElementById('message_preview').value;
            const sendBtn = document.getElementById('send_btn');
            
            if (phone && message) {
                const waLink = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                sendBtn.href = waLink;
                sendBtn.classList.remove('disabled');
            } else {
                sendBtn.classList.add('disabled');
            }
        }
    </script>
    <style>
        .btn.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Send WhatsApp</h1>
                <p style="color: var(--text-muted);">Send automated updates and reminders directly to customers.</p>
            </div>
            <div>
                <a href="whatsapp_templet.php" class="btn btn-primary" ><i class='bx bx-list-ul'></i> Manage Templates</a>
            </div>
        </div>

        <div class="glass" style="padding: 2rem; max-width: 800px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="booking_select">Select Customer Booking</label>
                    <select id="booking_select" class="form-control" onchange="updatePreview()" >
                        <option value="">-- Choose Booking --</option>
                        <?php foreach($bookings as $b): ?>
                            <option value="<?php echo $b['booking_id']; ?>">
                                #BKG-<?php echo $b['booking_id']; ?> | <?php echo htmlspecialchars($b['customer_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="template_select">Select Template</label>
                    <select id="template_select" class="form-control" onchange="updatePreview()" >
                        <option value="">-- Choose Template --</option>
                        <?php foreach($templates as $t): ?>
                            <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['template_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="customer_phone">WhatsApp Phone Number</label>
                <div >
                    <i class='bx bxl-whatsapp' style="font-size: 1.2rem; color: #10B981;"></i>
                    <input type="text" id="customer_phone" class="form-control" style="border: none; background: transparent; box-shadow: none;" placeholder="e.g. 919876543210" onkeyup="customEdit()">
                </div>
                <small style="color: var(--text-muted); font-size: 0.8rem;">Include country code without '+' (e.g., 91 for India).</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="message_preview">Message Preview (You can edit before sending)</label>
                <textarea id="message_preview" class="form-control" rows="8" placeholder="Select a booking and a template to generate the message..." onkeyup="customEdit()"></textarea>
            </div>

            <div style="margin-top: 2rem;">
                <a href="#" id="send_btn" target="_blank" class="btn btn-primary disabled" style="background: #10B981; color: white; width: 100%; font-size: 1.1rem; padding: 1rem;"><i class='bx bxl-whatsapp'></i> Send via WhatsApp Web</a>
            </div>
            
            <div >
                <i class='bx bx-info-circle'></i> <strong>How it works:</strong> Clicking the button will open a new tab to WhatsApp Web (or your WhatsApp desktop app) with this message pre-typed to the selected number.
            </div>
        </div>
    </main>
</div>

</body>
</html>
