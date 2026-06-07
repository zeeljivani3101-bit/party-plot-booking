<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | PartyPlot</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .about-layout {
            padding-top: 100px;
            padding-bottom: 5rem;
            min-height: 80vh;
        }
        .theme-toggle-btn {
            background: transparent;
            border: none;
            color: var(--primary);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
        }
        .theme-toggle-btn:hover {
            color: var(--primary-hover);
        }
    </style>
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
                <a href="gallery.php">Gallery</a>
                <a href="inquiry.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Inquiry Form</a>
                <button id="themeToggle" class="theme-toggle-btn" title="Toggle Theme"><i class='bx bx-moon'></i></button>
            </div>
        </div>
    </nav>

    <div class="container about-layout">
        <!-- About Us Section -->
        <section id="about" style="padding: 3rem 0;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 4rem;">
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="font-size: 3rem; margin-bottom: 2rem;">The Legacy of <br><span class="text-gradient">The Royal Party Plot</span></h2>
                    <p style="color: var(--text-muted); font-size: 1.15rem; line-height: 1.8; margin-bottom: 1.5rem; font-family: 'Inter', sans-serif; font-weight: 300;">
                        Located in the heart of the city, The Royal Party Plot is the most prestigious destination for hosting your grand events. From lavish weddings and grand receptions to high-profile corporate events and intimate birthday celebrations, we provide a magnificent setting that adds a touch of royalty to your special moments.
                    </p>
                    <p style="color: var(--text-muted); font-size: 1.15rem; line-height: 1.8; font-family: 'Inter', sans-serif; font-weight: 300;">
                        With over a decade of experience in hospitality, our dedicated team ensures that every detail is meticulously planned and executed. Our sprawling green lawns, elegant architecture, and state-of-the-art facilities guarantee an unforgettable experience for you and your guests.
                    </p>
                </div>
                <div style="flex: 1; min-width: 300px;">
                    <div style="position: relative; padding-bottom: 2rem; padding-right: 2rem;">
                        <img src="https://images.unsplash.com/photo-1530103862676-de8892bf084d?q=80&w=2070&auto=format&fit=crop" alt="About Party Plot" style="width: 100%; border-radius: 24px; box-shadow: var(--shadow-lg); position: relative; z-index: 2;">
                        <div style="position: absolute; bottom: 0; right: 0; width: 80%; height: 80%; background: var(--primary); border-radius: 24px; opacity: 0.1; z-index: 1;"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" style="padding: 5rem 0; margin-top: 3rem;">
            <div class="text-center" style="margin-bottom: 4rem;">
                <h2 style="font-size: 2.5rem;">Premium <span class="text-gradient">Facilities</span></h2>
                <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 600px; margin: 0 auto; font-weight: 300;">We offer everything you need to make your event a resounding success without any hassle.</p>
            </div>
            
            <div class="features-grid" style="margin-top: 2rem;">
                <div class="feature-card">
                    <i class='bx bx-landscape feature-icon'></i>
                    <h3 style="margin-bottom: 1rem; font-size: 1.5rem;">Huge Green Lawn</h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem; font-weight: 300;">Spacious, beautifully maintained lawns capable of hosting up to 2,000 guests comfortably.</p>
                </div>
                
                <div class="feature-card">
                    <i class='bx bx-car feature-icon'></i>
                    <h3 style="margin-bottom: 1rem; font-size: 1.5rem;">Valet Parking</h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem; font-weight: 300;">Massive dedicated parking space with professional valet services for your guests' convenience.</p>
                </div>
                
                <div class="feature-card">
                    <i class='bx bx-restaurant feature-icon'></i>
                    <h3 style="margin-bottom: 1rem; font-size: 1.5rem;">Exquisite Catering</h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem; font-weight: 300;">Partnered with top-tier culinary experts to deliver a gastronomic experience your guests won't forget.</p>
                </div>
                
                <div class="feature-card">
                    <i class='bx bx-home-heart feature-icon'></i>
                    <h3 style="margin-bottom: 1rem; font-size: 1.5rem;">VIP Rooms</h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem; font-weight: 300;">Fully air-conditioned luxury changing rooms and relaxing suites for the host family.</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer style="text-align: center; padding: 2.5rem; color: var(--text-muted); border-top: 1px solid var(--border-color); background: var(--surface);">
        <p style="font-weight: 500; font-size: 0.9rem;">&copy; <?php echo date('Y'); ?> The Royal Party Plot. All rights reserved.</p>
    </footer>

    <!-- Theme JS -->
    <script src="assets/js/theme.js"></script>
</body>
</html>
