<?php
session_start();
require_once '../config/db.php';

$message = '';
$error = '';
$customer = null;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $customer_name = $conn->real_escape_string(trim($first_name . ' ' . $last_name));
    
    $mobile = $conn->real_escape_string($_POST['phone'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    
    $sql = "UPDATE customers SET customer_name='$customer_name', mobile='$mobile', address='$address' WHERE id=$id";
            
    if ($conn->query($sql) === TRUE) {
        $message = "Customer details updated successfully!";
        // We will fetch the updated data below
    } else {
        $error = "Error updating customer: " . $conn->error;
    }
    $customer_id = $id; // Set ID for fetching below
} else {
    // Handle GET Request
    $customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
}

// Fetch existing data
if ($customer_id > 0) {
    $result = $conn->query("SELECT * FROM customers WHERE id = $customer_id");
    if ($result && $result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        
        // Split name for the form
        $name_parts = explode(' ', $customer['customer_name'], 2);
        $customer['first_name'] = $name_parts[0];
        $customer['last_name'] = isset($name_parts[1]) ? $name_parts[1] : '';
    } else {
        $error = "Customer not found.";
    }
} else {
    if (!$message && !$error) {
        header("Location: view_customers.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Edit Customer</h1>
                <p style="color: var(--text-muted);">Update existing client details.</p>
            </div>
            <div>
                <a href="view_customers.php" class="btn btn-primary" >Back to List</a>
            </div>
        </div>

        <div class="glass" style="padding: 2rem; max-width: 800px;">
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

            <?php if ($customer): ?>
            <form action="edit_customer.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($customer['mobile']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($customer['address']); ?></textarea>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary"><i class='bx bx-save' style="margin-right: 0.5rem;"></i> Update Customer</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
