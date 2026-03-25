<?php
// admin/adminhome.php - Admin Dashboard with session check
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminsign.php");
    exit;
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch statistics
$stats = [];

// Total orders
$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $res ? mysqli_fetch_assoc($res)['count'] : 0;

// Pending orders
$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
$stats['pending_orders'] = $res ? mysqli_fetch_assoc($res)['count'] : 0;

// Total menu items
$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM menu_items WHERE is_available = 1");
$stats['total_menu'] = $res ? mysqli_fetch_assoc($res)['count'] : 0;

// Total gallery images
$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM gallery_images");
$stats['total_gallery'] = $res ? mysqli_fetch_assoc($res)['count'] : 0;

// Total registered users
$res = mysqli_query($conn, "SELECT COUNT(*) as count FROM signup");
$stats['total_users'] = $res ? mysqli_fetch_assoc($res)['count'] : 0;

// Recent orders (last 5)
$recent_orders = [];
$res = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $recent_orders[] = $row;
    }
}

// Top 5 selling items
$top_items = [];
$res = mysqli_query($conn, "SELECT mi.name, SUM(oi.quantity) as total_sold 
                            FROM order_items oi 
                            JOIN menu_items mi ON oi.menu_item_id = mi.id 
                            GROUP BY oi.menu_item_id 
                            ORDER BY total_sold DESC 
                            LIMIT 5");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $top_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Suleman Restro</title>
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
        .admin-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .admin-info span {
            color: #C5A059;
            font-size: 0.9rem;
        }
        .back-link a, .logout-link a {
            color: #C5A059;
            text-decoration: none;
            border: 1px solid #C5A059;
            padding: 8px 16px;
            border-radius: 40px;
            transition: 0.3s;
        }
        .back-link a:hover, .logout-link a:hover {
            background: #C5A059;
            color: #000;
        }
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: #111;
            border: 1px solid #222;
            border-radius: 20px;
            padding: 25px 20px;
            text-align: center;
            transition: 0.3s;
        }
        .stat-card:hover {
            border-color: #C5A059;
            transform: translateY(-5px);
        }
        .stat-card i {
            font-size: 2.5rem;
            color: #C5A059;
            margin-bottom: 15px;
        }
        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 10px 0;
        }
        .stat-label {
            color: #aaa;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        /* Sections */
        .dashboard-section {
            background: #111;
            border: 1px solid #222;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #C5A059;
            border-left: 3px solid #C5A059;
            padding-left: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #222;
        }
        th {
            color: #C5A059;
            font-weight: 600;
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
        .btn-link {
            background: #222;
            color: #C5A059;
            padding: 5px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: 0.3s;
        }
        .btn-link:hover {
            background: #C5A059;
            color: #000;
        }
        .quick-links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .quick-link {
            background: #222;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            color: #C5A059;
            transition: 0.3s;
        }
        .quick-link:hover {
            background: #C5A059;
            color: #000;
        }
        @media (max-width: 768px) {
            body { padding: 1rem; }
            .stat-number { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="admin-bar">
        <h1>Admin <span>Dashboard</span></h1>
        <div class="admin-info">
            <span><i class="fas fa-user-shield"></i> Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
            <div class="logout-link">
                <a href="adminsign.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <div class="back-link">
                <a href="../home.php"><i class="fas fa-arrow-left"></i> Back to Site</a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-shopping-cart"></i>
            <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <div class="stat-number"><?php echo $stats['pending_orders']; ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-utensils"></i>
            <div class="stat-number"><?php echo $stats['total_menu']; ?></div>
            <div class="stat-label">Menu Items</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-images"></i>
            <div class="stat-number"><?php echo $stats['total_gallery']; ?></div>
            <div class="stat-label">Gallery Images</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div class="stat-number"><?php echo $stats['total_users']; ?></div>
            <div class="stat-label">Registered Users</div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="dashboard-section">
        <div class="section-title"><i class="fas fa-receipt"></i> Recent Orders</div>
        <?php if (empty($recent_orders)): ?>
            <p>No orders yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                        <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                        <td><a href="ordercontrol.php" class="btn-link">Manage</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Top Selling Items -->
    <div class="dashboard-section">
        <div class="section-title"><i class="fas fa-chart-line"></i> Top Selling Items</div>
        <?php if (empty($top_items)): ?>
            <p>No sales data yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Total Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['total_sold']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Quick Admin Links -->
    <div class="dashboard-section">
        <div class="section-title"><i class="fas fa-cog"></i> Quick Actions</div>
        <div class="quick-links">
            <a href="menucontrol.php" class="quick-link"><i class="fas fa-utensils"></i> Manage Menu</a>
            <a href="gallerycontrol.php" class="quick-link"><i class="fas fa-images"></i> Manage Gallery</a>
            <a href="ordercontrol.php" class="quick-link"><i class="fas fa-truck"></i> Manage Orders</a>
        </div>
    </div>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>