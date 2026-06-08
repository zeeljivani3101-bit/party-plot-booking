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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_doc'])) {
    $customer_id = intval($_POST['customer_id']);
    $document_name = $conn->real_escape_string($_POST['document_name']);
    $document_type = $conn->real_escape_string($_POST['document_type']);
    
    // File processing
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['document_file']['tmp_name'];
        $file_name = $_FILES['document_file']['name'];
        $file_size = $_FILES['document_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (!in_array($file_ext, $allowed_exts)) {
            $error = "Invalid file type. Only JPG, PNG, and PDF are allowed.";
        } elseif ($file_size > 5 * 1024 * 1024) { // 5MB limit
            $error = "File size must be less than 5MB.";
        } else {
            // Create directory if not exists
            $upload_dir = '../uploads/documents/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_file_name = "doc_" . $customer_id . "_" . time() . "." . $file_ext;
            $destination = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                $sql = "INSERT INTO customer_documents (customer_id, document_name, document_type, file_path) 
                        VALUES ($customer_id, '$document_name', '$document_type', '$new_file_name')";
                if ($conn->query($sql)) {
                    $message = "Document uploaded successfully!";
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

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    // get file path
    $res = $conn->query("SELECT file_path FROM customer_documents WHERE id = $del_id");
    if ($res && $res->num_rows > 0) {
        $file_to_del = $res->fetch_assoc()['file_path'];
        $full_path = '../uploads/documents/' . $file_to_del;
        if (file_exists($full_path)) {
            unlink($full_path);
        }
        $conn->query("DELETE FROM customer_documents WHERE id = $del_id");
        $message = "Document deleted successfully.";
    }
}

// Fetch all customers for dropdown
$customers = [];
$c_res = $conn->query("SELECT id, customer_name, mobile FROM customers ORDER BY customer_name ASC");
if ($c_res) {
    while($row = $c_res->fetch_assoc()) {
        $customers[] = $row;
    }
}

// Fetch all documents
$docs_sql = "SELECT cd.*, c.customer_name, c.mobile 
             FROM customer_documents cd 
             JOIN customers c ON cd.customer_id = c.id 
             ORDER BY cd.uploaded_at DESC";
$documents = $conn->query($docs_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Documents | PartyPlot Admin</title>
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
                <h1 class="page-title">Customer Documents</h1>
                <p style="color: var(--text-muted);">Manage KYC and ID proofs for customers.</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            
            <!-- Upload Form -->
            <div class="glass" style="padding: 2rem; height: fit-content;">
                <h3 style="margin-bottom: 1.5rem;">Upload Document</h3>
                
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

                <form action="document.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label" for="customer_id">Select Customer</label>
                        <select id="customer_id" name="customer_id" class="form-control" required >
                            <option value="">-- Choose Customer --</option>
                            <?php foreach($customers as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['customer_name'] . ' (' . $c['mobile'] . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="document_type">Document Type</label>
                        <select id="document_type" name="document_type" class="form-control" required >
                            <option value="Aadhar Card">Aadhar Card</option>
                            <option value="PAN Card">PAN Card</option>
                            <option value="Voter ID">Voter ID</option>
                            <option value="Driving License">Driving License</option>
                            <option value="Electricity Bill">Electricity Bill</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="document_name">Document Name/Label</label>
                        <input type="text" id="document_name" name="document_name" class="form-control" placeholder="e.g. Front side of Aadhar" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="document_file">Select File (JPG, PNG, PDF)</label>
                        <input type="file" id="document_file" name="document_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required style="padding: 0.5rem;">
                        <small style="color: var(--text-muted); font-size: 0.8rem;">Max size: 5MB</small>
                    </div>

                    <button type="submit" name="upload_doc" class="btn btn-primary" style="width: 100%;"><i class='bx bx-cloud-upload'></i> Upload Document</button>
                </form>
            </div>

            <!-- Documents List -->
            <div class="glass" style="padding: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">Uploaded Documents</h3>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: var(--text-muted); text-align: left;">
                                <th style="padding: 1rem;">Customer</th>
                                <th style="padding: 1rem;">Doc Type</th>
                                <th style="padding: 1rem;">Name</th>
                                <th style="padding: 1rem;">Date</th>
                                <th style="padding: 1rem; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($documents && $documents->num_rows > 0): ?>
                                <?php while($doc = $documents->fetch_assoc()): ?>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td style="padding: 1rem;">
                                            <div style="font-weight: 500;"><?php echo htmlspecialchars($doc['customer_name']); ?></div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($doc['mobile']); ?></div>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <span class="badge" ><?php echo htmlspecialchars($doc['document_type']); ?></span>
                                        </td>
                                        <td style="padding: 1rem;"><?php echo htmlspecialchars($doc['document_name']); ?></td>
                                        <td style="padding: 1rem; font-size: 0.9rem; color: var(--text-muted);"><?php echo date('d M Y', strtotime($doc['uploaded_at'])); ?></td>
                                        <td style="padding: 1rem; text-align: right;">
                                            <a href="../uploads/documents/<?php echo $doc['file_path']; ?>" target="_blank" class="btn btn-sm"  title="View"><i class='bx bx-show'></i></a>
                                            <a href="document.php?delete=<?php echo $doc['id']; ?>" class="btn btn-sm"  onclick="return confirm('Delete this document?');" title="Delete"><i class='bx bx-trash'></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">No documents uploaded yet.</td>
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
