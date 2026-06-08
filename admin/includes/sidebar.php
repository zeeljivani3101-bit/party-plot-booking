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

// Determine paths based on current directory
$in_staff = strpos(str_replace('\\', '/', $_SERVER['PHP_SELF']), '/staff mengement/') !== false;
$in_docs = strpos(str_replace('\\', '/', $_SERVER['PHP_SELF']), '/documents model/') !== false;
$in_whatsapp = strpos(str_replace('\\', '/', $_SERVER['PHP_SELF']), '/whatsapp model/') !== false;
$in_events = strpos(str_replace('\\', '/', $_SERVER['PHP_SELF']), '/upcoming_event/') !== false;

// If we are in ANY of the submodules, admin path needs '../admin/'
$in_submodule = $in_staff || $in_docs || $in_whatsapp || $in_events;
$admin_path = $in_submodule ? '../admin/' : '';
$staff_path = $in_staff ? '' : '../staff mengement/';
$doc_path = $in_docs ? '' : '../documents model/';
$whatsapp_path = $in_whatsapp ? '' : '../whatsapp model/';
$event_path = $in_events ? '' : '../upcoming_event/';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo $admin_path; ?>dashbord.php" class="sidebar-brand">
            <i class='bx bxs-party'></i> Admin
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <a href="<?php echo $admin_path; ?>dashbord.php" class="nav-item <?php echo ($current_page == 'dashbord.php') ? 'active' : ''; ?>">
            <i class='bx bxs-dashboard'></i> Dashboard
        </a>
        
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Bookings
        </div>
        <a href="<?php echo $admin_path; ?>add_booking.php" class="nav-item <?php echo ($current_page == 'add_booking.php') ? 'active' : ''; ?>">
            <i class='bx bx-calendar-plus'></i> New Booking
        </a>
        <a href="<?php echo $admin_path; ?>view_bookings.php" class="nav-item <?php echo ($current_page == 'view_bookings.php') ? 'active' : ''; ?>">
            <i class='bx bx-list-ul'></i> All Bookings
        </a>
        
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Customers
        </div>
        <a href="<?php echo $admin_path; ?>add_customer.php" class="nav-item <?php echo ($current_page == 'add_customer.php') ? 'active' : ''; ?>">
            <i class='bx bx-user-plus'></i> Add Customer
        </a>
        <a href="<?php echo $admin_path; ?>view_customers.php" class="nav-item <?php echo ($current_page == 'view_customers.php') ? 'active' : ''; ?>">
            <i class='bx bx-group'></i> All Customers
        </a>
        
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Events Management
        </div>
        <a href="<?php echo $event_path; ?>today_evants.php" class="nav-item <?php echo ($current_page == 'today_evants.php') ? 'active' : ''; ?>">
            <i class='bx bx-calendar-star'></i> Today's Events
        </a>
        <a href="<?php echo $event_path; ?>upcoming_evant.php" class="nav-item <?php echo ($current_page == 'upcoming_evant.php') ? 'active' : ''; ?>">
            <i class='bx bx-calendar-event'></i> Upcoming Events
        </a>
        <a href="<?php echo $event_path; ?>completed_evant.php" class="nav-item <?php echo ($current_page == 'completed_evant.php') ? 'active' : ''; ?>">
            <i class='bx bx-calendar-check'></i> Completed Events
        </a>
        <a href="<?php echo $event_path; ?>cancelled_evant.php" class="nav-item <?php echo ($current_page == 'cancelled_evant.php') ? 'active' : ''; ?>">
            <i class='bx bx-calendar-x'></i> Cancelled Events
        </a>
        <a href="<?php echo $event_path; ?>evant_calender.php" class="nav-item <?php echo ($current_page == 'evant_calender.php') ? 'active' : ''; ?>">
            <i class='bx bx-calendar'></i> Event Calendar
        </a>
        
        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Staff Management
        </div>
        <a href="<?php echo $staff_path; ?>staff_list.php" class="nav-item <?php echo ($current_page == 'staff_list.php' || $current_page == 'addstaff.php' || $current_page == 'staff_profile.php' || $current_page == 'edit_staff.php') ? 'active' : ''; ?>">
            <i class='bx bx-id-card'></i> Staff Members
        </a>
        <a href="<?php echo $staff_path; ?>attendance.php" class="nav-item <?php echo ($current_page == 'attendance.php' || $current_page == 'attendance_report.php') ? 'active' : ''; ?>">
            <i class='bx bx-user-check'></i> Attendance
        </a>
        <a href="<?php echo $staff_path; ?>salary.php" class="nav-item <?php echo ($current_page == 'salary.php' || $current_page == 'salary_report.php' || $current_page == 'salary_deduction.php' || $current_page == 'staff_advance.php' || $current_page == 'advance_history.php') ? 'active' : ''; ?>">
            <i class='bx bx-money-withdraw'></i> Payroll
        </a>

        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Documents & Legal
        </div>
        <a href="<?php echo $doc_path; ?>document.php" class="nav-item <?php echo ($current_page == 'document.php') ? 'active' : ''; ?>">
            <i class='bx bx-id-card'></i> KYC Documents
        </a>
        <a href="<?php echo $doc_path; ?>agrements.php" class="nav-item <?php echo ($current_page == 'agrements.php') ? 'active' : ''; ?>">
            <i class='bx bx-file-blank'></i> Agreements
        </a>

        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            WhatsApp Hub
        </div>
        <a href="<?php echo $whatsapp_path; ?>send_whatsapp.php" class="nav-item <?php echo ($current_page == 'send_whatsapp.php') ? 'active' : ''; ?>">
            <i class='bx bxl-whatsapp'></i> Send Message
        </a>
        <a href="<?php echo $whatsapp_path; ?>whatsapp_templet.php" class="nav-item <?php echo ($current_page == 'whatsapp_templet.php') ? 'active' : ''; ?>">
            <i class='bx bx-message-square-edit'></i> Message Templates
        </a>

        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Finance & Reports
        </div>
        <a href="<?php echo $admin_path; ?>payment.php" class="nav-item <?php echo ($current_page == 'payment.php') ? 'active' : ''; ?>">
            <i class='bx bx-wallet'></i> Payments
        </a>
        <a href="<?php echo $admin_path; ?>reports.php" class="nav-item <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
            <i class='bx bx-bar-chart-alt-2'></i> Reports
        </a>

        <div style="margin-top: 1.5rem; margin-bottom: 0.5rem; padding-left: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 1px;">
            Website Content
        </div>
        <a href="<?php echo $admin_path; ?>inquiries.php" class="nav-item <?php echo ($current_page == 'inquiries.php') ? 'active' : ''; ?>">
            <i class='bx bx-message-square-detail'></i> Inquiries
        </a>
        <a href="<?php echo $admin_path; ?>gallery_manage.php" class="nav-item <?php echo ($current_page == 'gallery_manage.php') ? 'active' : ''; ?>">
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
