<?php
session_start();
require_once '../config/db.php';

$message = '';
$error = '';

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gallery_image'])) {
    $caption = $conn->real_escape_string($_POST['caption'] ?? '');
    
    $target_dir = "../uploads/";
    $file_extension = strtolower(pathinfo($_FILES["gallery_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (in_array($file_extension, $allowed_types)) {
        if (move_uploaded_file($_FILES["gallery_image"]["tmp_name"], $target_file)) {
            // Save to DB
            $db_path = "uploads/" . $new_filename;
            $sql = "INSERT INTO gallery (image_path, caption) VALUES ('$db_path', '$caption')";
            if ($conn->query($sql) === TRUE) {
                $message = "Image uploaded successfully!";
            } else {
                $error = "Database error: " . $conn->error;
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    } else {
        $error = "Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.";
    }
}

// Handle Image Delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Fetch image path to delete file
    $res = $conn->query("SELECT image_path FROM gallery WHERE id = $delete_id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $file_path = "../" . $row['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $conn->query("DELETE FROM gallery WHERE id = $delete_id");
        $message = "Image deleted successfully!";
    }
}

// Fetch all images
$images = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .gallery-item {
            background: rgba(30, 41, 59, 0.7);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .gallery-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }
        .gallery-info {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Manage Gallery</h1>
                <p style="color: var(--text-muted);">Upload photos that customers will see on the front page.</p>
            </div>
        </div>

        <div class="glass" style="padding: 2rem; max-width: 600px; margin-bottom: 2rem;">
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

            <form action="gallery_manage.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label" for="gallery_image">Select Image File</label>
                    <input type="file" id="gallery_image" name="gallery_image" class="form-control" accept="image/*" required style="padding: 0.5rem;">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="caption">Image Caption (Optional)</label>
                    <input type="text" id="caption" name="caption" class="form-control" placeholder="e.g. Beautiful Evening Setup">
                </div>

                <div style="margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary"><i class='bx bx-upload' style="margin-right: 0.5rem;"></i> Upload Image</button>
                </div>
            </form>
        </div>

        <h3>Uploaded Photos</h3>
        <div class="gallery-grid">
            <?php if ($images && $images->num_rows > 0): ?>
                <?php while($img = $images->fetch_assoc()): ?>
                    <div class="gallery-item">
                        <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" alt="Gallery Image" class="gallery-img">
                        <div class="gallery-info">
                            <span style="font-size: 0.875rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($img['caption']) ?: 'No caption'; ?>
                            </span>
                            <a href="gallery_manage.php?delete_id=<?php echo $img['id']; ?>" class="btn btn-sm btn-danger" style="padding: 0.3rem 0.5rem;" onclick="return confirm('Are you sure you want to delete this photo?')"><i class='bx bx-trash'></i></a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; color: var(--text-muted);">No images uploaded yet.</div>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
