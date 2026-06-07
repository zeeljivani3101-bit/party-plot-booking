<?php
session_start();
require_once 'config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $msg = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO inquiries (name, phone, event_date, message) VALUES ('$name', '$phone', '$event_date', '$msg')";
    if ($conn->query($sql) === TRUE) {
        $message = "Your inquiry has been submitted! Our team will contact you soon.";
    } else {
        $error = "Error submitting inquiry. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry | PartyPlot</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .public-layout {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0 3rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 3rem;
        }
        .inquiry-section {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 3rem;
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body style="background: var(--background); color: var(--text-main);">

<div class="public-layout">
    <header class="header">
        <a href="index.php" style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); text-decoration: none; display: flex; align-items: center; gap: 0.5rem; font-family: 'Playfair Display', serif;">
            <i class='bx bxs-party' style="color: var(--primary);"></i> PartyPlot
        </a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="gallery.php">Gallery</a>
            <button id="themeToggle" style="background: transparent; border: none; color: var(--primary); font-size: 1.5rem; cursor: pointer; transition: color 0.3s ease; display: flex; align-items: center;" title="Toggle Theme"><i class='bx bx-moon'></i></button>
        </nav>
    </header>

    <!-- Inquiry Form -->
    <div class="inquiry-section" id="inquire" style="background: var(--surface); border-radius: 24px; box-shadow: var(--shadow-lg); border: 1px solid var(--border-color);">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: var(--secondary);">Book Your Event</h2>
            <p style="color: var(--text-muted); font-family: 'Inter', sans-serif; font-weight: 300;">Fill out the form and our management team will reach out to you.</p>
        </div>

        <?php if ($message): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #059669; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; text-align: center; font-weight: 500;">
                <i class='bx bx-check-circle'></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #DC2626; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; text-align: center; font-weight: 500;">
                <i class='bx bx-error-circle'></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="inquiry.php" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="name">Your Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="event_date">Expected Event Date</label>
                <input type="date" id="event_date" name="event_date" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 2.5rem;">
                <label class="form-label" for="message">Message / Details</label>
                <textarea id="message" name="message" class="form-control" rows="4" placeholder="Tell us about the event (Wedding, Reception, estimated guests, etc.)" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.2rem; font-size: 1.1rem; border-radius: 12px;">Submit Inquiry</button>
        </form>
    </div>
</div>

<!-- Footer -->
<footer style="text-align: center; padding: 2.5rem; color: var(--text-muted); border-top: 1px solid var(--border-color); background: var(--surface);">
    <p style="font-weight: 500; font-size: 0.9rem;">&copy; <?php echo date('Y'); ?> The Royal Party Plot. All rights reserved.</p>
</footer>

<!-- Theme JS -->
<script src="assets/js/theme.js"></script>

</body>
</html>
