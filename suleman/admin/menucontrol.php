<?php
// admin/menucontrol.php
// No security – direct access, only for demonstration/development

// Database connection
$conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = ""; // For feedback messages

// Handle Add/Edit/Delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $desc = mysqli_real_escape_string($conn, $_POST['description']);
        $price = floatval($_POST['price']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $image = mysqli_real_escape_string($conn, $_POST['image_path']);
        
        $query = "INSERT INTO menu_items (name, description, price, category, image_path) 
                  VALUES ('$name', '$desc', '$price', '$category', '$image')";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert success'>✅ Item added successfully.</div>";
        } else {
            $message = "<div class='alert error'>❌ Error: " . mysqli_error($conn) . "</div>";
        }
    }
    
    elseif (isset($_POST['edit'])) {
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $desc = mysqli_real_escape_string($conn, $_POST['description']);
        $price = floatval($_POST['price']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $image = mysqli_real_escape_string($conn, $_POST['image_path']);
        
        $query = "UPDATE menu_items SET 
                    name='$name', 
                    description='$desc', 
                    price='$price', 
                    category='$category', 
                    image_path='$image' 
                  WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert success'>✅ Item updated.</div>";
        } else {
            $message = "<div class='alert error'>❌ Error: " . mysqli_error($conn) . "</div>";
        }
    }
    
    elseif (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $query = "DELETE FROM menu_items WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert success'>🗑️ Item deleted.</div>";
        } else {
            $message = "<div class='alert error'>❌ Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Fetch all menu items for listing
$items = [];
$result = mysqli_query($conn, "SELECT * FROM menu_items ORDER BY category, name");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel – Manage Menu | Suleman Restro</title>
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
        .input-group input, .input-group textarea, .input-group select {
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
        .action-buttons form {
            display: inline-block;
            margin: 0 2px;
        }
        .edit-btn, .delete-btn {
            padding: 5px 12px;
            font-size: 12px;
        }
        .delete-btn {
            background: #8b0000;
            color: #fff;
        }
        .delete-btn:hover {
            background: #ff4444;
        }
        .edit-btn {
            background: #2c3e66;
            color: #fff;
        }
        .edit-btn:hover {
            background: #3a5a8f;
        }
        .image-preview {
            max-width: 60px;
            max-height: 60px;
            border-radius: 8px;
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
        <h1>Admin <span>Menu Control</span></h1>
        <div class="back-link">
            <a href="adminhome.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../home.php"><i class="fas fa-arrow-left"></i> Back to Site</a>
        </div>
    </div>
    
    <?php echo $message; ?>
    
    <!-- Add New Item Form -->
    <div class="form-card">
        <h2><i class="fas fa-plus-circle"></i> Add New Menu Item</h2>
        <form method="POST">
            <div class="form-grid">
                <div class="input-group">
                    <label>Item Name *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="input-group">
                    <label>Category *</label>
                    <select name="category" required>
                        <option value="Appetizers">Appetizers</option>
                        <option value="Main Course">Main Course</option>
                        <option value="Desserts">Desserts</option>
                        <option value="Beverages">Beverages</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Price (₹) *</label>
                    <input type="number" step="0.01" name="price" required>
                </div>
                <div class="input-group">
                    <label>Image Path</label>
                    <input type="text" name="image_path" placeholder="restro_img/menu/yourimage.jpg">
                </div>
                <div class="input-group" style="grid-column: span 2;">
                    <label>Description</label>
                    <textarea name="description" rows="2"></textarea>
                </div>
            </div>
            <button type="submit" name="add">Add Item</button>
        </form>
    </div>
    
    <!-- List Existing Items -->
    <div class="form-card">
        <h2><i class="fas fa-list"></i> Current Menu Items</h2>
        <?php if (count($items) == 0): ?>
            <p>No items found. Add some using the form above.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td>
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" class="image-preview" alt="image">
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($item['description'])); ?></td>
                        <td class="action-buttons">
                            <!-- Edit Button triggers modal or inline form? We'll use a separate edit form below each row? Simpler: edit via separate GET form with pre-filled fields. We'll add a small edit section per row? For simplicity, we'll use the same edit form after clicking "Edit" which redirects with GET? But to keep it all in one page, we can show an inline edit form when clicking "Edit". Alternatively, we can use a separate edit page. I'll use a modal-like inline row edit toggling with JavaScript, but for simplicity, I'll implement a basic edit form that appears when "Edit" is clicked (with a GET parameter). However, since we need to support many items, it's easier to use a separate edit page. I'll create a separate edit form in the same page but only shown when "Edit" is clicked? That becomes messy. Better to use a GET parameter to show an edit form for a specific item. I'll implement a simple GET edit mode.

                            We'll modify the code: when "Edit" link is clicked, we pass ?edit=id, and then display an edit form below the list. That's cleaner. Let's do that.
                        -->
                        <a href="?edit=<?php echo $item['id']; ?>" class="edit-btn" style="background:#2c3e66; color:#fff; padding:5px 12px; border-radius:20px; text-decoration:none; display:inline-block; margin-right:5px;"><i class="fas fa-edit"></i> Edit</a>
                        <form method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this item?');">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="delete" class="delete-btn" style="background:#8b0000; padding:5px 12px;"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Edit Form (if edit parameter is set) -->
    <?php
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $edit_id = intval($_GET['edit']);
        $result = mysqli_query($conn, "SELECT * FROM menu_items WHERE id=$edit_id");
        if ($result && $row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="form-card">
                <h2><i class="fas fa-edit"></i> Edit Item #<?php echo $row['id']; ?></h2>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Item Name *</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Category *</label>
                            <select name="category" required>
                                <option value="Appetizers" <?php if($row['category']=='Appetizers') echo 'selected'; ?>>Appetizers</option>
                                <option value="Main Course" <?php if($row['category']=='Main Course') echo 'selected'; ?>>Main Course</option>
                                <option value="Desserts" <?php if($row['category']=='Desserts') echo 'selected'; ?>>Desserts</option>
                                <option value="Beverages" <?php if($row['category']=='Beverages') echo 'selected'; ?>>Beverages</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Price (₹) *</label>
                            <input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Image Path</label>
                            <input type="text" name="image_path" value="<?php echo htmlspecialchars($row['image_path']); ?>">
                        </div>
                        <div class="input-group" style="grid-column: span 2;">
                            <label>Description</label>
                            <textarea name="description" rows="2"><?php echo htmlspecialchars($row['description']); ?></textarea>
                        </div>
                    </div>
                    <button type="submit" name="edit">Update Item</button>
                    <a href="menucontrol.php" style="margin-left:10px; color:#C5A059;">Cancel</a>
                </form>
            </div>
            <?php
        } else {
            echo "<div class='alert error'>Item not found.</div>";
        }
    }
    ?>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>