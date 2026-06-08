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
    <title>PartyPlot | The Royal Galleries</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .gallery-page-container {
            position: relative;
            min-height: 100vh;
            width: 100vw;
            background-color: #000;
            overflow-x: hidden;
            padding-bottom: 5rem;
        }

        .gallery-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100vh;
            object-fit: cover;
            opacity: 0.3;
            z-index: 0;
            filter: blur(10px);
        }

        .gallery-content {
            position: relative;
            z-index: 10;
            padding-top: 150px;
            max-width: 1400px;
            margin: 0 auto;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .gallery-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2.5rem;
            margin-bottom: 5rem;
        }

        .gallery-item {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(212, 175, 55, 0.3);
            background: rgba(20, 20, 20, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .gallery-item::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.9) 100%);
            z-index: 1;
            transition: all 0.5s ease;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            border-color: rgba(212, 175, 55, 0.8);
            box-shadow: 0 25px 50px rgba(0,0,0,0.8), 0 0 30px rgba(212, 175, 55, 0.1);
        }

        .gallery-img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            display: block;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gallery-item:hover .gallery-img {
            transform: scale(1.08);
        }

        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 2rem;
            z-index: 2;
            text-align: left;
            transform: translateY(10px);
            opacity: 0.9;
            transition: all 0.5s ease;
        }

        .gallery-item:hover .gallery-caption {
            transform: translateY(0);
            opacity: 1;
        }

        .gallery-caption p {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #fff;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
        }

        .empty-gallery {
            grid-column: 1 / -1;
            text-align: center;
            padding: 6rem 2rem;
            background: rgba(20, 20, 20, 0.4);
            backdrop-filter: blur(15px);
            border: 1px dashed rgba(212, 175, 55, 0.3);
            border-radius: 20px;
        }

        .empty-gallery i {
            font-size: 4rem;
            color: rgba(212, 175, 55, 0.5);
            margin-bottom: 1.5rem;
        }

        .empty-gallery p {
            font-family: 'Outfit', sans-serif;
            font-size: 1.2rem;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .inquiry-banner {
            text-align: center;
            padding: 5rem 2rem;
            background: linear-gradient(to bottom, rgba(20,20,20,0.2), rgba(0,0,0,0.8));
            border-top: 1px solid rgba(212, 175, 55, 0.1);
        }
    </style>
</head>
<body style="margin: 0; padding: 0;">

<div class="gallery-page-container">
    <img src="assets/images/ratan_farm.png" alt="Background" class="gallery-bg">

    <!-- Minimal Transparent Nav from Cinematic Design -->
    <nav class="cinematic-nav animate-fade-up">
        <a href="index.php" class="logo">PartyPlot</a>
        <div class="links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="gallery.php" style="color: #D4AF37; border-bottom: 1px solid #D4AF37;">Gallery</a>
        </div>
    </nav>

    <div class="gallery-content animate-fade-up">
        <div class="gallery-header">
            <span class="luxury-accent">Explore</span>
            <h1 class="cinematic-title" style="font-size: 5rem; margin-bottom: 1rem;">Our Beautiful Venues</h1>
            <p class="cinematic-subtitle" style="font-size: 1.2rem;">Take a look at our stunning setups. Once you are ready, check our availability.</p>
        </div>

        <div class="gallery-grid">
            <?php if ($images && $images->num_rows > 0): ?>
                <?php while($img = $images->fetch_assoc()): ?>
                    <div class="gallery-item">
                        <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="Gallery Image" class="gallery-img">
                        <?php if($img['caption']): ?>
                            <div class="gallery-caption">
                                <p><?php echo htmlspecialchars($img['caption']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-gallery">
                    <i class='bx bx-images'></i>
                    <p>Photos will be updated soon.<br>Stay tuned!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="inquiry-banner">
            <h2 class="cinematic-title" style="font-size: 3rem; margin-bottom: 2rem;">Ready to Book?</h2>
            <a href="inquiry.php" class="luxury-btn">Inquiry Form</a>
        </div>
    </div>
</div>

<footer style="position: relative; z-index: 10; text-align: center; padding: 2.5rem; color: rgba(255,255,255,0.4); border-top: 1px solid rgba(255,255,255,0.05); background: #000; font-family: 'Outfit', sans-serif; font-size: 0.85rem; letter-spacing: 0.1em;">
    <p>&copy; <?php echo date('Y'); ?> The Royal Party Plot. All rights reserved.</p>
</footer>

</body>
</html>
