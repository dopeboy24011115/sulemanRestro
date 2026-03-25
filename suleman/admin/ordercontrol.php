<?php
session_start();

// Admin session check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminsign.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle status update
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = $order_id");
    header("Location: ordercontrol.php" . (isset($_GET['status']) ? "?status=" . urlencode($_GET['status']) : ""));
    exit;
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $order_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM orders WHERE id = $order_id");
    header("Location: ordercontrol.php" . (isset($_GET['status']) ? "?status=" . urlencode($_GET['status']) : ""));
    exit;
}

// Determine filter
$filter_status = isset($_GET['status']) && $_GET['status'] != '' ? $_GET['status'] : 'all';
$where = "";
if ($filter_status != 'all') {
    $where = "WHERE status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
}

// Fetch orders with filter
$orders = [];
$result = mysqli_query($conn, "SELECT * FROM orders $where ORDER BY order_date DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Get item count for this order
        $order_id = $row['id'];
        $count_res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM order_items WHERE order_id = $order_id");
        $cnt = 0;
        if ($count_res && $c = mysqli_fetch_assoc($count_res)) {
            $cnt = $c['cnt'];
        }
        $row['item_count'] = $cnt;
        $orders[] = $row;
    }
}

// Get counts for each status (for filter tabs)
$counts = ['all' => 0, 'pending' => 0, 'confirmed' => 0, 'preparing' => 0, 'delivered' => 0, 'cancelled' => 0];
$count_res = mysqli_query($conn, "SELECT status, COUNT(*) as cnt FROM orders GROUP BY status");
if ($count_res) {
    while ($row = mysqli_fetch_assoc($count_res)) {
        $counts[$row['status']] = $row['cnt'];
        $counts['all'] += $row['cnt'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order Management | Suleman Restro</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
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
        .admin-links {
            display: flex;
            gap: 15px;
        }
        .admin-links a {
            color: #C5A059;
            text-decoration: none;
            border: 1px solid #C5A059;
            padding: 8px 16px;
            border-radius: 40px;
            transition: 0.3s;
        }
        .admin-links a:hover {
            background: #C5A059;
            color: #000;
        }
        .filter-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
            background: #111;
            padding: 15px;
            border-radius: 50px;
            border: 1px solid #222;
        }
        .filter-tab {
            padding: 8px 20px;
            border-radius: 40px;
            background: #222;
            color: #fff;
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.85rem;
        }
        .filter-tab.active {
            background: #C5A059;
            color: #000;
        }
        .filter-tab:hover:not(.active) {
            background: #333;
        }
        .badge-count {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 0 8px;
            margin-left: 8px;
            font-size: 0.7rem;
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
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-pending { background: #ff9800; color: #000; }
        .status-confirmed { background: #2196f3; color: #fff; }
        .status-preparing { background: #9c27b0; color: #fff; }
        .status-delivered { background: #4caf50; color: #fff; }
        .status-cancelled { background: #f44336; color: #fff; }
        .status-select {
            background: #222;
            border: 1px solid #333;
            color: #fff;
            padding: 5px;
            border-radius: 5px;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .view-btn, .delete-btn {
            padding: 5px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.75rem;
            border: none;
            cursor: pointer;
        }
        .view-btn {
            background: #2c3e66;
            color: #fff;
        }
        .delete-btn {
            background: #8b0000;
            color: #fff;
        }
        .delete-btn:hover {
            background: #ff4444;
        }
        .item-count-badge {
            display: inline-block;
            background: #333;
            color: #C5A059;
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 0.7rem;
            margin-left: 8px;
        }
        /* UPDATED: Removed display: none; from here */
        .order-details {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .order-details table {
            margin-top: 10px;
            background: #222;
        }
        .order-details p {
            margin-top: 10px;
        }
        .debug-info {
            background: #2a2a2a;
            border-left: 4px solid #C5A059;
            padding: 10px;
            font-family: monospace;
            font-size: 0.75rem;
            color: #aaa;
            margin-top: 10px;
            overflow-x: auto;
        }
        @media (max-width: 768px) {
            body { padding: 1rem; }
            th, td { padding: 8px; font-size: 12px; }
            .filter-tabs { border-radius: 20px; padding: 10px; }
            .filter-tab { padding: 5px 12px; font-size: 0.75rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="admin-bar">
        <h1>Order <span>Management</span></h1>
        <div class="admin-links">
            <a href="adminhome.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../home.php"><i class="fas fa-arrow-left"></i> Back to Site</a>
        </div>
    </div>

    <div class="filter-tabs">
        <a href="?status=all" class="filter-tab <?php echo $filter_status == 'all' ? 'active' : ''; ?>">
            All <span class="badge-count"><?php echo $counts['all']; ?></span>
        </a>
        <a href="?status=pending" class="filter-tab <?php echo $filter_status == 'pending' ? 'active' : ''; ?>">
            Pending <span class="badge-count"><?php echo $counts['pending']; ?></span>
        </a>
        <a href="?status=confirmed" class="filter-tab <?php echo $filter_status == 'confirmed' ? 'active' : ''; ?>">
            Confirmed <span class="badge-count"><?php echo $counts['confirmed']; ?></span>
        </a>
        <a href="?status=preparing" class="filter-tab <?php echo $filter_status == 'preparing' ? 'active' : ''; ?>">
            Preparing <span class="badge-count"><?php echo $counts['preparing']; ?></span>
        </a>
        <a href="?status=delivered" class="filter-tab <?php echo $filter_status == 'delivered' ? 'active' : ''; ?>">
            Delivered <span class="badge-count"><?php echo $counts['delivered']; ?></span>
        </a>
        <a href="?status=cancelled" class="filter-tab <?php echo $filter_status == 'cancelled' ? 'active' : ''; ?>">
            Cancelled <span class="badge-count"><?php echo $counts['cancelled']; ?></span>
        </a>
    </div>

    <?php if (empty($orders)): ?>
        <p>No orders found for this status.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
         <table>
            <thead>
                 <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                 </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                 <tr>
                     <td>#<?php echo $order['id']; ?>
                        <?php if ($order['item_count'] > 0): ?>
                            <span class="item-count-badge"><?php echo $order['item_count']; ?> item<?php echo $order['item_count'] != 1 ? 's' : ''; ?></span>
                        <?php endif; ?>
                     </td>
                     <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                     <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                     <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                     <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                     <td>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                     </td>
                    <td class="action-buttons">
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <option value="pending" <?php if($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="confirmed" <?php if($order['status'] == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                                <option value="preparing" <?php if($order['status'] == 'preparing') echo 'selected'; ?>>Preparing</option>
                                <option value="delivered" <?php if($order['status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                                <option value="cancelled" <?php if($order['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                        </form>
                        <button type="button" onclick="toggleDetails(<?php echo $order['id']; ?>)" class="view-btn">View Items</button>
                        <a href="?delete=<?php echo $order['id']; ?>&status=<?php echo $filter_status; ?>" class="delete-btn" onclick="return confirm('Delete this order permanently?')">Delete</a>
                     </td>
                 </tr>
                <tr id="details-<?php echo $order['id']; ?>" style="display: none;">
                    <td colspan="7">
                        <div class="order-details">
                            <strong>Order Items:</strong>
                            <?php
                            $order_id = $order['id'];
                            // Build the query
                            $sql = "SELECT oi.*, mi.name as item_name, mi.image_path 
                                    FROM order_items oi 
                                    LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id 
                                    WHERE oi.order_id = $order_id";
                            $items_res = mysqli_query($conn, $sql);
                            
                            if ($items_res === false) {
                                echo '<p style="color:#ff8888;">⚠️ SQL Error: ' . mysqli_error($conn) . '</p>';
                                echo '<div class="debug-info">Query: ' . htmlspecialchars($sql) . '</div>';
                            } elseif (mysqli_num_rows($items_res) > 0) {
                                echo '<table style="width:100%; margin-top:10px;">';
                                echo '<tr><th>Item</th><th>Image</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>';
                                while ($item = mysqli_fetch_assoc($items_res)) {
                                    $sub = $item['quantity'] * $item['price_at_time'];
                                    $item_name = !empty($item['item_name']) ? $item['item_name'] : 'Item #' . $item['menu_item_id'] . ' (deleted)';
                                    $img = !empty($item['image_path']) ? $item['image_path'] : 'restro_img/menu/placeholder.jpg';
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($item_name) . '</td>';
                                    echo '<td><img src="../' . $img . '" style="width:50px; height:50px; object-fit:cover; border-radius:8px;"></td>';
                                    echo '<td>' . $item['quantity'] . '</td>';
                                    echo '<td>₹' . number_format($item['price_at_time'], 2) . '</td>';
                                    echo '<td>₹' . number_format($sub, 2) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</table>';
                            } else {
                                echo '<p style="color:#ff8888;">⚠️ No items found in order_items for order #' . $order_id . '.</p>';
                                echo '<div class="debug-info">Query: ' . htmlspecialchars($sql) . '<br>Rows returned: 0</div>';
                            }
                            ?>
                            <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></p>
                            <?php if (!empty($order['notes'])): ?>
                                <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                            <?php endif; ?>
                        </div>
                     </td>
                 </tr>
                <?php endforeach; ?>
            </tbody>
         </table>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleDetails(orderId) {
        console.log("Toggle details for order " + orderId);
        var row = document.getElementById('details-' + orderId);
        if (row) {
            if (row.style.display === 'none') {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        } else {
            console.error("Row not found for order " + orderId);
            alert("Could not find details for order " + orderId + ". Please refresh the page.");
        }
    }
</script>
</body>
</html>
<?php mysqli_close($conn); ?>