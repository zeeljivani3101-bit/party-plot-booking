<?php
session_start();
require_once 'config/db.php';

// Fetch Gallery Images
$images = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PartyPlot Gallery & Booking Inquiry</title>
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
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 4rem;
        }
        .gallery-item {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
            transition: transform 0.3s ease;
        }
        .gallery-item:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }
        .gallery-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }
        .gallery-caption {
            background: rgba(15, 23, 42, 0.8);
            padding: 1rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-main);
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
            <a href="inquiry.php" class="btn btn-primary" style="padding: 0.5rem 1.5rem; color: white;">Inquiry Form</a>
            <button id="themeToggle" style="background: transparent; border: none; color: var(--primary); font-size: 1.5rem; cursor: pointer; transition: color 0.3s ease; display: flex; align-items: center;" title="Toggle Theme"><i class='bx bx-moon'></i></button>
        </nav>
    </header>

    <div style="text-align: center; margin-bottom: 4rem;">
        <h1 style="font-size: 4rem; margin-bottom: 1rem; color: var(--secondary);">Our Beautiful Venues</h1>
        <p style="color: var(--text-muted); font-size: 1.2rem; max-width: 600px; margin: 0 auto; font-family: 'Inter', sans-serif; font-weight: 300;">Take a look at our stunning party plot setups. Once you are ready, fill out the inquiry form below to check availability.</p>
    </div>

    <!-- Gallery Section -->
    <div class="gallery-grid">
        <?php if ($images && $images->num_rows > 0): ?>
            <?php while($img = $images->fetch_assoc()): ?>
                <div class="gallery-item" style="border-radius: 24px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); overflow: hidden; transition: transform 0.4s ease, box-shadow 0.4s ease;">
                    <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="Gallery Image" class="gallery-img" style="height: 250px; transition: transform 0.5s ease;">
                    <?php if($img['caption']): ?>
                        <div class="gallery-caption" style="background: var(--surface); color: var(--secondary); font-family: 'Inter', sans-serif; font-weight: 500; font-size: 1rem; border-top: 1px solid var(--border-color);"><?php echo htmlspecialchars($img['caption']); ?></div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: var(--text-muted); background: var(--surface); border-radius: 24px; border: 1px dashed var(--border-color); box-shadow: var(--shadow-sm);">
                <i class='bx bx-images' style="font-size: 3rem; margin-bottom: 1rem; color: var(--primary); opacity: 0.5;"></i>
                <p style="font-family: 'Inter', sans-serif;">Photos will be updated soon. Stay tuned!</p>
            </div>
        <?php endif; ?>
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
