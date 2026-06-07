<?php
// Get the current page name to highlight the active menu item
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- User Sidebar (For Customer Dashboard) -->
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="sidebar-brand">
            <i class='bx bxs-party'></i> My Account
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <a href="user_dashboard.php" class="nav-item <?php echo ($current_page == 'user_dashboard.php') ? 'active' : ''; ?>">
            <i class='bx bxs-dashboard'></i> Dashboard
        </a>
        
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            My Bookings
        </div>
        <a href="my_bookings.php" class="nav-item <?php echo ($current_page == 'my_bookings.php') ? 'active' : ''; ?>">
            <i class='bx bx-calendar-event'></i> All Bookings
        </a>
        <a href="pending_payments.php" class="nav-item <?php echo ($current_page == 'pending_payments.php') ? 'active' : ''; ?>">
            <i class='bx bx-wallet'></i> Pending Payments
        </a>
        
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Settings
        </div>
        <a href="profile.php" class="nav-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class='bx bx-user'></i> Edit Profile
        </a>
        
        <div style="margin-top: auto; padding-top: 2rem;">
            <a href="logout.php" class="nav-item" style="color: #EF4444;">
                <i class='bx bx-log-out'></i> Logout
            </a>
        </div>
    </nav>
</aside>
