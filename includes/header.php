<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PartyPlot | Elevate Your Events</title>
    <meta name="description" content="Book the perfect party plot for your next big event. Premium amenities, stunning locations.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- BoxIcons for icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="nav-brand">
                <i class='bx bxs-party'></i> PartyPlot
            </a>
            <div class="nav-links">
                <a href="index.php#features">Features</a>
                <a href="index.php#about">About</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- If user is logged in -->
                    <a href="user_dashboard.php">My Bookings</a>
                    <a href="logout.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Logout</a>
                <?php else: ?>
                    <!-- If user is NOT logged in -->
                    <a href="login.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
