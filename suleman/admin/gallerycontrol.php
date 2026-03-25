<?php
// admin/gallerycontrol.php
// No security – direct access, for development/demo only

$conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// Ensure the upload directory exists
$upload_dir = "../restro_img/gallery/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle upload
if (isset($_POST['upload'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $file = $_FILES['image'];
    
    if ($file['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_name = time() . "_" . rand(1000, 9999) . "." . $ext;
            $destination = $upload_dir . $new_name;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $db_path = "restro_img/gallery/" . $new_name;
                $query = "INSERT INTO gallery_images (image_path, title, description) VALUES ('$db_path', '$title', '$desc')";
                if (mysqli_query($conn, $query)) {
                    $message = "<div class='alert success'>✅ Image uploaded successfully.</div>";
                } else {
                    $message = "<div class='alert error'>❌ DB error: " . mysqli_error($conn) . "</div>";
                }
            } else {
                $message = "<div class='alert error'>❌ Failed to move uploaded file.</div>";
            }
        } else {
            $message = "<div class='alert error'>❌ Invalid file type. Allowed: jpg, jpeg, png, gif, webp</div>";
        }
    } else {
        $message = "<div class='alert error'>❌ Upload error.</div>";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Get image path to delete file
    $res = mysqli_query($conn, "SELECT image_path FROM gallery_images WHERE id=$id");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $file_path = "../" . $row['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        mysqli_query($conn, "DELETE FROM gallery_images WHERE id=$id");
        $message = "<div class='alert success'>🗑️ Image deleted.</div>";
    } else {
        $message = "<div class='alert error'>❌ Image not found.</div>";
    }
}

// Handle reorder (move up/down)
if (isset($_GET['move']) && isset($_GET['id'])) {
    $move = $_GET['move']; // 'up' or 'down'
    $id = intval($_GET['id']);
    
    // Get current order of the target
    $res = mysqli_query($conn, "SELECT display_order FROM gallery_images WHERE id=$id");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $current = $row['display_order'];
        if ($move == 'up') {
            $new = $current - 1;
            // Find item with display_order = $new and swap
            $res2 = mysqli_query($conn, "SELECT id FROM gallery_images WHERE display_order=$new");
            if ($res2 && $row2 = mysqli_fetch_assoc($res2)) {
                $other_id = $row2['id'];
                mysqli_query($conn, "UPDATE gallery_images SET display_order=$current WHERE id=$other_id");
                mysqli_query($conn, "UPDATE gallery_images SET display_order=$new WHERE id=$id");
            } else {
                // If no item with that order, just set to 0
                mysqli_query($conn, "UPDATE gallery_images SET display_order=0 WHERE id=$id");
                // Rebuild orders to avoid gaps
                rebuild_orders($conn);
            }
        } elseif ($move == 'down') {
            $new = $current + 1;
            $res2 = mysqli_query($conn, "SELECT id FROM gallery_images WHERE display_order=$new");
            if ($res2 && $row2 = mysqli_fetch_assoc($res2)) {
                $other_id = $row2['id'];
                mysqli_query($conn, "UPDATE gallery_images SET display_order=$current WHERE id=$other_id");
                mysqli_query($conn, "UPDATE gallery_images SET display_order=$new WHERE id=$id");
            } else {
                // Get max order and set to max+1
                $max = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(display_order) as max FROM gallery_images"))['max'];
                mysqli_query($conn, "UPDATE gallery_images SET display_order=" . ($max+1) . " WHERE id=$id");
                rebuild_orders($conn);
            }
        }
        $message = "<div class='alert success'>↕️ Order updated.</div>";
    }
    // Redirect to avoid re‑execution on refresh
    header("Location: gallerycontrol.php");
    exit;
}

function rebuild_orders($conn) {
    // Reassign sequential display_order starting from 0
    $result = mysqli_query($conn, "SELECT id FROM gallery_images ORDER BY display_order ASC, uploaded_at DESC");
    $order = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        mysqli_query($conn, "UPDATE gallery_images SET display_order=$order WHERE id={$row['id']}");
        $order++;
    }
}

// Fetch all images for listing
$images = [];
$result = mysqli_query($conn, "SELECT * FROM gallery_images ORDER BY display_order ASC, uploaded_at DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gallery Control | Suleman Restro</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #0a0a0a;
            color: #fff;
            padding: 2rem;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        h1 span { color: #C5A059; }
        .admin-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .back-link a {
            color: #C5A059;
            text-decoration: none;
            border: 1px solid #C5A059;
            padding: 8px 16px;
            border-radius: 40px;
            transition: 0.3s;
        }
        .back-link a:hover {
            background: #C5A059;
            color: #000;
        }
        .alert {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert.success {
            background: rgba(197,160,89,0.2);
            border: 1px solid #C5A059;
            color: #C5A059;
        }
        .alert.error {
            background: rgba(255,0,0,0.1);
            border: 1px solid #ff4444;
            color: #ff8888;
        }
        .form-card {
            background: #111;
            border: 1px solid #222;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 40px;
        }
        .form-card h2 {
            color: #C5A059;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .input-group {
            display: flex;
            flex-direction: column;
        }
        .input-group label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            color: #aaa;
        }
        .input-group input, .input-group textarea {
            background: #222;
            border: 1px solid #333;
            padding: 10px 12px;
            border-radius: 8px;
            color: #fff;
            font-family: inherit;
        }
        .input-group input:focus, .input-group textarea:focus {
            outline: none;
            border-color: #C5A059;
        }
        input[type="file"] {
            background: #222;
            padding: 8px;
        }
        button {
            background: #C5A059;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #fff;
            transform: translateY(-2px);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #111;
            border-radius: 20px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #222;
        }
        th {
            background: #1a1a1a;
            color: #C5A059;
            font-weight: 600;
        }
        tr:hover {
            background: #1a1a1a;
        }
        .gallery-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .action-buttons a, .action-buttons form {
            display: inline-block;
            margin: 0 2px;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 20px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-edit {
            background: #2c3e66;
            color: #fff;
        }
        .btn-delete {
            background: #8b0000;
            color: #fff;
        }
        .btn-move {
            background: #444;
            color: #fff;
        }
        @media (max-width: 768px) {
            body { padding: 1rem; }
            th, td { padding: 8px; font-size: 12px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="admin-bar">
        <h1>Gallery <span>Control</span></h1>
        <div class="back-link">
            <a href="adminhome.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../home.php"><i class="fas fa-arrow-left"></i> Back to Site</a>
        </div>
    </div>
    
    <?php echo $message; ?>
    
    <!-- Upload Form -->
    <div class="form-card">
        <h2><i class="fas fa-upload"></i> Upload New Image</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="input-group">
                    <label>Title *</label>
                    <input type="text" name="title" required>
                </div>
                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" rows="2"></textarea>
                </div>
                <div class="input-group">
                    <label>Image File *</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>
            </div>
            <button type="submit" name="upload">Upload</button>
        </form>
    </div>
    
    <!-- List Images with Reorder & Delete -->
    <div class="form-card">
        <h2><i class="fas fa-images"></i> Manage Gallery Images</h2>
        <?php if (count($images) == 0): ?>
            <p>No images yet. Upload some above.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($images as $idx => $img): ?>
                        <tr>
                            <td><?php echo $img['display_order']; ?></td>
                            <td><img src="../<?php echo htmlspecialchars($img['image_path']); ?>" class="gallery-thumb" alt="thumb"></td>
                            <td><?php echo htmlspecialchars($img['title']); ?></td>
                            <td><?php echo htmlspecialchars($img['description']); ?></td>
                            <td class="action-buttons">
                                <a href="?move=up&id=<?php echo $img['id']; ?>" class="btn-sm btn-move" title="Move Up"><i class="fas fa-arrow-up"></i></a>
                                <a href="?move=down&id=<?php echo $img['id']; ?>" class="btn-sm btn-move" title="Move Down"><i class="fas fa-arrow-down"></i></a>
                                <a href="?delete=<?php echo $img['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Delete this image?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>