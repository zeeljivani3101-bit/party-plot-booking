<?php
session_start();
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
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a>
                <button id="themeToggle" style="background: transparent; border: none; color: var(--primary); font-size: 1.5rem; cursor: pointer; transition: color 0.3s ease; display: flex; align-items: center;" title="Toggle Theme"><i class='bx bx-moon'></i></button>
            </div>
        </div>
    </nav>

    <!-- Centered Farm Display -->
    <section class="hero text-center" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; flex-direction: column;">
        <div class="container" style="max-width: 800px; padding: 2rem;">
            
            <!-- Farm Image -->
            <div style="width: 100%; height: 400px; border-radius: 24px; overflow: hidden; margin-bottom: 2.5rem; box-shadow: var(--shadow-lg); border: 1px solid var(--border-color); position: relative;">
                <img src="assets/images/ratan_farm.png" alt="RATAN FARM" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
            </div>

            <!-- Farm Name -->
            <h1 style="font-size: 4.5rem; margin-bottom: 1rem; color: var(--secondary);">RATAN FARM</h1>
            <p style="color: var(--text-muted); font-size: 1.25rem; margin-bottom: 3.5rem; font-weight: 300;">Experience the most beautiful and luxurious outdoor venue for your unforgettable events.</p>

            <!-- See More Button linking to Gallery -->
            <a href="gallery.php" class="btn btn-primary" style="font-size: 1.15rem; padding: 1.25rem 3.5rem;">
                See More Photos <i class='bx bx-images' style="margin-left: 0.5rem; font-size: 1.4rem;"></i>
            </a>

        </div>
    </section>

    <!-- Footer -->
    <footer style="text-align: center; padding: 2.5rem; color: var(--text-muted); border-top: 1px solid var(--border-color); background: var(--surface);">
        <p style="font-weight: 500; font-size: 0.9rem;">&copy; <?php echo date('Y'); ?> The Royal Party Plot. All rights reserved.</p>
    </footer>

    <!-- Theme JS -->
    <script src="assets/js/theme.js"></script>

</body>
</html>
