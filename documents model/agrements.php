<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$message = '';
$error = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_agreement'])) {
    $booking_id = intval($_POST['booking_id']);
    $agreement_title = $conn->real_escape_string($_POST['agreement_title']);
    $status = $conn->real_escape_string($_POST['status']);
    
    if (isset($_FILES['agreement_file']) && $_FILES['agreement_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['agreement_file']['tmp_name'];
        $file_name = $_FILES['agreement_file']['name'];
        $file_size = $_FILES['agreement_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (!in_array($file_ext, $allowed_exts)) {
            $error = "Invalid file type. Only JPG, PNG, and PDF are allowed.";
        } elseif ($file_size > 5 * 1024 * 1024) {
            $error = "File size must be less than 5MB.";
        } else {
            $upload_dir = '../uploads/agreements/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_file_name = "agr_" . $booking_id . "_" . time() . "." . $file_ext;
            $destination = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                $sql = "INSERT INTO booking_agreements (booking_id, agreement_title, status, file_path) 
                        VALUES ($booking_id, '$agreement_title', '$status', '$new_file_name')";
                if ($conn->query($sql)) {
                    $message = "Agreement uploaded successfully!";
                } else {
                    $error = "Database error: " . $conn->error;
                }
            } else {
                $error = "Failed to upload file.";
            }
        }
    } else {
        $error = "Please select a valid file to upload.";
    }
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $agr_id = intval($_POST['agreement_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);
    if ($conn->query("UPDATE booking_agreements SET status = '$new_status' WHERE id = $agr_id")) {
        $message = "Status updated successfully!";
    } else {
        $error = "Database error: " . $conn->error;
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $res = $conn->query("SELECT file_path FROM booking_agreements WHERE id = $del_id");
    if ($res && $res->num_rows > 0) {
        $file_to_del = $res->fetch_assoc()['file_path'];
        $full_path = '../uploads/agreements/' . $file_to_del;
        if (file_exists($full_path)) {
            unlink($full_path);
        }
        $conn->query("DELETE FROM booking_agreements WHERE id = $del_id");
        $message = "Agreement deleted successfully.";
    }
}

// Fetch bookings for dropdown
$bookings = [];
$b_sql = "SELECT b.id, c.customer_name, b.event_type, b.booking_date 
          FROM bookings b 
          JOIN customers c ON b.customer_id = c.id 
          ORDER BY b.id DESC LIMIT 100";
$b_res = $conn->query($b_sql);
if ($b_res) {
    while($row = $b_res->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Fetch all agreements
$agr_sql = "SELECT ba.*, b.event_type, b.booking_date, c.customer_name 
            FROM booking_agreements ba 
            JOIN bookings b ON ba.booking_id = b.id 
            JOIN customers c ON b.customer_id = c.id 
            ORDER BY ba.uploaded_at DESC";
$agreements = $conn->query($agr_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Agreements | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Booking Agreements</h1>
                <p style="color: var(--text-muted);">Manage rental agreements and legal documents for bookings.</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            
            <!-- Upload Form -->
            <div class="glass" style="padding: 2rem; height: fit-content;">
                <h3 style="margin-bottom: 1.5rem;">Upload Agreement</h3>
                
                <?php if ($message): ?>
                    <div >
                        <i class='bx bx-check-circle'></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div >
                        <i class='bx bx-error-circle'></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="agrements.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label" for="booking_id">Select Booking</label>
                        <select id="booking_id" name="booking_id" class="form-control" required >
                            <option value="">-- Choose Booking --</option>
                            <?php foreach($bookings as $b): ?>
                                <option value="<?php echo $b['id']; ?>">
                                    #BKG-<?php echo $b['id']; ?> | <?php echo htmlspecialchars($b['customer_name']); ?> | <?php echo date('d M', strtotime($b['booking_date'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="agreement_title">Agreement Title</label>
                        <input type="text" id="agreement_title" name="agreement_title" class="form-control" placeholder="e.g. Terms & Conditions Signed" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status">Current Status</label>
                        <select id="status" name="status" class="form-control" required >
                            <option value="Pending Signature">Pending Signature</option>
                            <option value="Signed">Signed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="agreement_file">Scanned File (JPG, PNG, PDF)</label>
                        <input type="file" id="agreement_file" name="agreement_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required style="padding: 0.5rem;">
                        <small style="color: var(--text-muted); font-size: 0.8rem;">Max size: 5MB</small>
                    </div>

                    <button type="submit" name="upload_agreement" class="btn btn-primary" style="width: 100%;"><i class='bx bx-file-blank'></i> Upload Agreement</button>
                </form>
            </div>

            <!-- Agreements List -->
            <div class="glass" style="padding: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">Uploaded Agreements</h3>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: var(--text-muted); text-align: left;">
                                <th style="padding: 1rem;">Booking</th>
                                <th style="padding: 1rem;">Title</th>
                                <th style="padding: 1rem;">Status</th>
                                <th style="padding: 1rem; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($agreements && $agreements->num_rows > 0): ?>
                                <?php while($agr = $agreements->fetch_assoc()): ?>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td style="padding: 1rem;">
                                            <div style="font-weight: 500;">#BKG-<?php echo $agr['booking_id']; ?></div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($agr['customer_name']); ?></div>
                                        </td>
                                        <td style="padding: 1rem;"><?php echo htmlspecialchars($agr['agreement_title']); ?></td>
                                        <td style="padding: 1rem;">
                                            <form action="agrements.php" method="POST" style="display: flex; align-items: center; gap: 0.5rem;">
                                                <input type="hidden" name="agreement_id" value="<?php echo $agr['id']; ?>">
                                                <select name="new_status" class="form-control" style="padding: 0.3rem; font-size: 0.8rem; width: auto;" onchange="this.form.submit()">
                                                    <option value="Pending Signature" <?php echo $agr['status']=='Pending Signature'?'selected':''; ?>>Pending</option>
                                                    <option value="Signed" <?php echo $agr['status']=='Signed'?'selected':''; ?>>Signed</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                        <td style="padding: 1rem; text-align: right; white-space: nowrap;">
                                            <a href="../uploads/agreements/<?php echo $agr['file_path']; ?>" target="_blank" class="btn btn-sm"  title="View"><i class='bx bx-show'></i></a>
                                            <a href="agrements.php?delete=<?php echo $agr['id']; ?>" class="btn btn-sm"  onclick="return confirm('Delete this agreement?');" title="Delete"><i class='bx bx-trash'></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-muted);">No agreements uploaded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>
