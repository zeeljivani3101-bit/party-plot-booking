<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    if (!headers_sent()) {
        header("Location: ../login.php");
    } else {
        echo '<script>window.location.href="../login.php";</script>';
    }
    exit;
}

// Get the current page name to highlight the active menu item
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="dashbord.php" class="sidebar-brand">
            <i class='bx bxs-party'></i> Admin
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashbord.php" class="nav-item <?php echo ($current_page == 'dashbord.php') ? 'active' : ''; ?>">
            <i class='bx bxs-dashboard'></i> Dashboard
        </a>
        
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Bookings
        </div>
        <a href="add_booking.php" class="nav-item <?php echo ($current_page == 'add_booking.php') ? 'active' : ''; ?>">
            <i class='bx bx-calendar-plus'></i> New Booking
        </a>
        <a href="view_bookings.php" class="nav-item <?php echo ($current_page == 'view_bookings.php') ? 'active' : ''; ?>">
            <i class='bx bx-list-ul'></i> All Bookings
        </a>
        
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Customers
        </div>
        <a href="add_customer.php" class="nav-item <?php echo ($current_page == 'add_customer.php') ? 'active' : ''; ?>">
            <i class='bx bx-user-plus'></i> Add Customer
        </a>
        <a href="view_customers.php" class="nav-item <?php echo ($current_page == 'view_customers.php') ? 'active' : ''; ?>">
            <i class='bx bx-group'></i> All Customers
        </a>
        
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Finance & Reports
        </div>
        <a href="payment.php" class="nav-item <?php echo ($current_page == 'payment.php') ? 'active' : ''; ?>">
            <i class='bx bx-wallet'></i> Payments
        </a>
        <a href="reports.php" class="nav-item <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
            <i class='bx bx-bar-chart-alt-2'></i> Reports
        </a>
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Website Content
        </div>
        <a href="inquiries.php" class="nav-item <?php echo ($current_page == 'inquiries.php') ? 'active' : ''; ?>">
            <i class='bx bx-message-square-detail'></i> Inquiries
        </a>
        <a href="gallery_manage.php" class="nav-item <?php echo ($current_page == 'gallery_manage.php') ? 'active' : ''; ?>">
            <i class='bx bx-images'></i> Gallery
        </a>
        
        <div style="margin-top: auto; padding-top: 2rem; display: flex; flex-direction: column; gap: 0.5rem;">
            <!-- Theme Toggle -->
            <button id="themeToggle" class="nav-item" style="background: none; border: none; cursor: pointer; text-align: left; width: 100%; font-size: 1rem; color: var(--text-muted);">
                <i class='bx bx-moon'></i> Theme
            </button>
            
            <a href="../logout.php" class="nav-item" style="color: #EF4444;">
                <i class='bx bx-log-out'></i> Logout
            </a>
        </div>
    </nav>
</aside>

<!-- Include Theme JS globally for Admin pages -->
<script src="../assets/js/theme.js"></script>
