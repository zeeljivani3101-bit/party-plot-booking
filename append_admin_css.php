<?php
$file = 'c:/xampp/htdocs/partyplot/assets/css/style.css';
$css = <<<EOD

/* --- Ultra-Premium Admin Dashboard Overrides --- */
body.admin-body {
    background-color: var(--background);
    background-image: var(--bg-mesh);
    background-attachment: fixed;
}
.sidebar {
    background: rgba(15, 20, 25, 0.6) !important;
    backdrop-filter: blur(25px) !important;
    -webkit-backdrop-filter: blur(25px) !important;
    border: 1px solid rgba(212, 175, 55, 0.3) !important;
    box-shadow: 0 15px 35px rgba(0,0,0,0.8), inset 0 0 20px rgba(212,175,55,0.05) !important;
}
.main-content {
    background: rgba(15, 20, 25, 0.6) !important;
    backdrop-filter: blur(25px) !important;
    -webkit-backdrop-filter: blur(25px) !important;
    border: 1px solid rgba(212, 175, 55, 0.3) !important;
    box-shadow: 0 15px 35px rgba(0,0,0,0.8) !important;
}
.stat-card {
    background: rgba(20, 25, 30, 0.8) !important;
    border: 1px solid rgba(212, 175, 55, 0.3) !important;
    box-shadow: 0 10px 20px rgba(0,0,0,0.6) !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.stat-card:hover {
    transform: translateY(-8px) scale(1.02) !important;
    border-color: rgba(212, 175, 55, 0.8) !important;
    box-shadow: 0 20px 40px rgba(0,0,0,0.8), 0 0 30px rgba(212, 175, 55, 0.2) !important;
}
.stat-icon {
    background: linear-gradient(135deg, rgba(212,175,55,0.1), rgba(212,175,55,0.3)) !important;
    border: 1px solid rgba(212, 175, 55, 0.5) !important;
    color: #D4AF37 !important;
    box-shadow: 0 0 15px rgba(212, 175, 55, 0.2) !important;
}
.stat-info h3 {
    color: #FFF !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5) !important;
}
.stat-info p {
    color: #D4AF37 !important;
    letter-spacing: 0.15em !important;
}
/* Table Overrides */
td, th {
    background: rgba(20, 25, 30, 0.6) !important;
    border-color: rgba(212, 175, 55, 0.15) !important;
    color: #E2E8F0 !important;
}
tr:hover td {
    background: rgba(30, 35, 45, 0.8) !important;
    border-color: rgba(212, 175, 55, 0.5) !important;
    box-shadow: 0 5px 15px rgba(0,0,0,0.5) !important;
}
/* Form Control in Admin */
.form-control {
    background: rgba(10, 15, 20, 0.8) !important;
    border: 1px solid rgba(212, 175, 55, 0.3) !important;
    color: #FFF !important;
}
.form-control:focus {
    border-color: #D4AF37 !important;
    box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.2) !important;
}
EOD;

file_put_contents($file, "\n" . $css, FILE_APPEND);
echo "Added Admin Overrides";
?>
