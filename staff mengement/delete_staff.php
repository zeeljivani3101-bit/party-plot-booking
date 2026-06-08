<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Instead of hard deleting, we can update status to 'Inactive' or hard delete.
    // Let's do a hard delete as requested. The foreign keys should have ON DELETE CASCADE.
    $sql = "DELETE FROM staff WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        // Set success message in session if you want, or just redirect
        header("Location: staff_list.php?msg=deleted");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    header("Location: staff_list.php");
}
exit;
?>
