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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <!-- BoxIcons for icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
</head>
<body style="margin: 0; padding: 0; background: #000; overflow-x: hidden;">

    <!-- Minimal Transparent Nav -->
    <nav class="cinematic-nav animate-fade-up">
        <a href="index.php" class="logo">PartyPlot</a>
        <div class="links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="gallery.php">Gallery</a>
        </div>
    </nav>

    <!-- Fullscreen Immersive Hero -->
    <section class="cinematic-hero">
        <img src="assets/images/ratan_farm.png" alt="Ratan Farm" class="cinematic-bg">
        <div class="cinematic-overlay"></div>
        
        <div class="emblem-glass animate-fade-up">
            <span class="luxury-accent">The Royal Experience</span>
            <h1 class="cinematic-title" style="color: #d4af37; text-shadow: 0 0 15px rgba(212, 175, 55, 0.8), 0 0 30px rgba(212, 175, 55, 0.4);">RATAN FARM</h1>
            <p class="cinematic-subtitle">Where timeless elegance meets unforgettable celebrations.</p>
            
            <a href="gallery.php" class="luxury-btn">Enter Venue</a>
        </div>
        

    </section>

</body>
</html>
