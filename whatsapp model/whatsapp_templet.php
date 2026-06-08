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

// Handle Create / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_template'])) {
    $template_name = $conn->real_escape_string($_POST['template_name']);
    $content = $conn->real_escape_string($_POST['content']);
    $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
    
    if ($template_id > 0) {
        $sql = "UPDATE whatsapp_templates SET template_name = '$template_name', content = '$content' WHERE id = $template_id";
        if ($conn->query($sql)) {
            $message = "Template updated successfully!";
        } else {
            $error = "Error updating template: " . $conn->error;
        }
    } else {
        $sql = "INSERT INTO whatsapp_templates (template_name, content) VALUES ('$template_name', '$content')";
        if ($conn->query($sql)) {
            $message = "Template created successfully!";
        } else {
            $error = "Error creating template: " . $conn->error;
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM whatsapp_templates WHERE id = $del_id")) {
        $message = "Template deleted successfully.";
    }
}

// Fetch templates
$templates = $conn->query("SELECT * FROM whatsapp_templates ORDER BY created_at DESC");

// Check if editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM whatsapp_templates WHERE id = $edit_id");
    if ($res && $res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Templates | PartyPlot Admin</title>
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
                <h1 class="page-title">WhatsApp Templates</h1>
                <p style="color: var(--text-muted);">Manage standard messages to send to customers.</p>
            </div>
            <div>
                <a href="send_whatsapp.php" class="btn btn-primary" ><i class='bx bxl-whatsapp'></i> Send Message</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            
            <!-- Editor Form -->
            <div class="glass" style="padding: 2rem; height: fit-content;">
                <h3 style="margin-bottom: 1.5rem;"><?php echo $edit_data ? 'Edit Template' : 'Create New Template'; ?></h3>
                
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

                <form action="whatsapp_templet.php" method="POST">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="template_id" value="<?php echo $edit_data['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label" for="template_name">Template Name</label>
                        <input type="text" id="template_name" name="template_name" class="form-control" required placeholder="e.g. Payment Reminder" value="<?php echo $edit_data ? htmlspecialchars($edit_data['template_name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="content">Message Content</label>
                        <textarea id="content" name="content" class="form-control" rows="8" required placeholder="Type your message here..."><?php echo $edit_data ? htmlspecialchars($edit_data['content']) : ''; ?></textarea>
                        
                        <div >
                            <strong>Available Variables:</strong><br>
                            <span style="color: #60A5FA;">{customer_name}</span> - Customer's full name<br>
                            <span style="color: #60A5FA;">{booking_date}</span> - Event Date<br>
                            <span style="color: #60A5FA;">{event_type}</span> - Type of Event<br>
                            <span style="color: #60A5FA;">{total_amount}</span> - Total Booking Price<br>
                            <span style="color: #60A5FA;">{advance_amount}</span> - Advance Paid<br>
                            <span style="color: #60A5FA;">{balance_amount}</span> - Balance Due
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" name="save_template" class="btn btn-primary" style="flex: 1;"><i class='bx bx-save'></i> Save Template</button>
                        <?php if ($edit_data): ?>
                            <a href="whatsapp_templet.php" class="btn btn-primary" >Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Templates List -->
            <div class="glass" style="padding: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">Saved Templates</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if ($templates && $templates->num_rows > 0): ?>
                        <?php while($tpl = $templates->fetch_assoc()): ?>
                            <div >
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                    <h4 style="margin: 0; font-size: 1.1rem;"><?php echo htmlspecialchars($tpl['template_name']); ?></h4>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="whatsapp_templet.php?edit=<?php echo $tpl['id']; ?>" class="btn btn-sm"  title="Edit"><i class='bx bx-edit-alt'></i></a>
                                        <a href="whatsapp_templet.php?delete=<?php echo $tpl['id']; ?>" class="btn btn-sm"  onclick="return confirm('Delete this template?');" title="Delete"><i class='bx bx-trash'></i></a>
                                    </div>
                                </div>
                                <div ><?php echo htmlspecialchars($tpl['content']); ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">No templates saved yet.</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>
