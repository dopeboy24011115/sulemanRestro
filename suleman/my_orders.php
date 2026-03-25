<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Database Connection
$conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle Form Submissions (Cancel / Reorder / Review) - Demo Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action_order_id = $_POST['order_id'];
        if ($_POST['action'] == 'cancel') {
            // Update query here
            mysqli_query($conn, "UPDATE orders SET status = 'cancelled' WHERE id = '$action_order_id' AND user_email = '$user_email'");
            $_SESSION['toast'] = "Order #$action_order_id has been cancelled successfully.";
        } elseif ($_POST['action'] == 'reorder') {
            // Reorder logic here
            $_SESSION['toast'] = "Items from Order #$action_order_id added to cart.";
        }
        header("Location: my_orders.php");
        exit;
    }
}

// Fetch user's name
$user_name = '';
$res = mysqli_query($conn, "SELECT NAME FROM signup WHERE EMAIL = '$user_email'");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $user_name = $row['NAME'];
}

// Fetch all orders for this user
$orders = [];
$query = "SELECT * FROM orders WHERE user_email = '$user_email' ORDER BY order_date DESC";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Premium Experience | Suleman Restro</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* =========================================
           CSS VARIABLES & RESET
           ========================================= */
        :root {
            --primary-gold: #C5A059;
            --gold-hover: #e0bb6c;
            --dark-bg: #0a0a0a;
            --card-bg: #141414;
            --card-border: #2a2a2a;
            --text-main: #ffffff;
            --text-muted: #888888;
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
            --info: #2196f3;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            color: var(--text-main);
            overflow-x: hidden;
            line-height: 1.6;
        }

        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: var(--primary-gold); border-radius: 5px; border: 2px solid #000; }

        /* =========================================
           NAVBAR (ENHANCED)
           ========================================= */
        .navbar {
            position: fixed; top: 0; left: 0; width: 100%;
            padding: 20px 5%; background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
            z-index: 1000; transition: var(--transition);
            border-bottom: 1px solid rgba(197, 160, 89, 0.1);
        }
        .navbar.scrolled { padding: 12px 5%; background: rgba(0,0,0,0.95); border-bottom-color: rgba(197,160,89,0.3); }
        .nav-container { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; color: #fff; text-decoration: none; letter-spacing: 2px; }
        .logo span { color: var(--primary-gold); }
        .nav-links { list-style: none; display: flex; gap: 30px; }
        .nav-links a { color: #fff; text-decoration: none; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; transition: var(--transition); position: relative; }
        .nav-links a:after { content: ''; position: absolute; bottom: -5px; left: 0; width: 0; height: 2px; background: var(--primary-gold); transition: width 0.3s ease; }
        .nav-links a:hover:after, .nav-links a.active:after { width: 100%; }
        .nav-links a:hover, .nav-links a.active { color: var(--primary-gold); }
        .user-greeting { display: flex; align-items: center; gap: 15px; }
        .user-greeting span { color: var(--primary-gold); font-size: 14px; font-weight: 500;}
        .btn-nav { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 30px; color: #fff; text-decoration: none; font-size: 13px; font-weight: 600; transition: var(--transition); }
        .btn-nav:hover { background: var(--primary-gold); color: #000; border-color: var(--primary-gold); transform: translateY(-2px); }

        /* =========================================
           HERO SECTION
           ========================================= */
        .hero {
            height: 45vh; min-height: 350px;
            background: linear-gradient(to top, var(--dark-bg), transparent), linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('restro_img/orders-hero.jpg') center/cover fixed;
            display: flex; align-items: center; justify-content: center; text-align: center; margin-top: 0; padding-top: 80px;
        }
        .hero-content h1 { font-family: 'Playfair Display', serif; font-size: 3.5rem; margin-bottom: 15px; text-shadow: 2px 2px 10px rgba(0,0,0,0.5); }
        .hero-content h1 span { color: var(--primary-gold); }
        .hero-content p { font-size: 1.1rem; color: #ccc; max-width: 600px; margin: 0 auto; }

        /* =========================================
           DASHBOARD & FILTERS
           ========================================= */
        .orders-container { max-width: 1200px; margin: -40px auto 50px; padding: 0 20px; position: relative; z-index: 10; }
        .dashboard-header { background: var(--card-bg); border: 1px solid var(--card-border); padding: 20px 30px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); flex-wrap: wrap; gap: 20px; }
        .stat-box { display: flex; align-items: center; gap: 15px; }
        .stat-icon { width: 50px; height: 50px; background: rgba(197, 160, 89, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary-gold); font-size: 1.5rem; }
        .stat-text h4 { font-size: 0.9rem; color: var(--text-muted); font-weight: 400; }
        .stat-text h2 { font-size: 1.5rem; color: #fff; line-height: 1.2; }
        
        .filter-tabs { display: flex; gap: 10px; background: #000; padding: 5px; border-radius: 30px; border: 1px solid #222; }
        .filter-btn { background: transparent; color: var(--text-muted); border: none; padding: 8px 20px; border-radius: 25px; cursor: pointer; font-family: inherit; font-size: 0.9rem; font-weight: 500; transition: var(--transition); }
        .filter-btn:hover { color: #fff; }
        .filter-btn.active { background: var(--primary-gold); color: #000; box-shadow: 0 4px 15px rgba(197, 160, 89, 0.3); }

        /* =========================================
           ORDER CARDS
           ========================================= */
        .order-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 20px; margin-bottom: 30px; transition: var(--transition); box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
        .order-card:hover { border-color: rgba(197, 160, 89, 0.5); transform: translateY(-5px); box-shadow: 0 10px 30px rgba(197, 160, 89, 0.1); }
        
        .order-header { background: rgba(0,0,0,0.3); padding: 20px 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-bottom: 1px solid var(--card-border); border-radius: 20px 20px 0 0; }
        .order-meta { display: flex; flex-direction: column; gap: 5px; }
        .order-id { font-size: 1.2rem; font-weight: 600; font-family: 'Playfair Display', serif; letter-spacing: 1px; }
        .order-id span { color: var(--primary-gold); }
        .order-date { color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center; gap: 5px; }
        
        .status-badge { padding: 6px 18px; border-radius: 30px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 8px; }
        .status-badge i { font-size: 1rem; }
        .status-pending { background: rgba(255, 152, 0, 0.1); color: var(--warning); border: 1px solid rgba(255, 152, 0, 0.3); }
        .status-confirmed { background: rgba(33, 150, 243, 0.1); color: var(--info); border: 1px solid rgba(33, 150, 243, 0.3); }
        .status-preparing { background: rgba(156, 39, 176, 0.1); color: #ce93d8; border: 1px solid rgba(156, 39, 176, 0.3); }
        .status-delivered { background: rgba(76, 175, 80, 0.1); color: var(--success); border: 1px solid rgba(76, 175, 80, 0.3); }
        .status-cancelled { background: rgba(244, 67, 54, 0.1); color: var(--danger); border: 1px solid rgba(244, 67, 54, 0.3); }

        /* =========================================
           PROGRESS TRACKER (ZOMATO STYLE)
           ========================================= */
        .progress-tracker { padding: 25px; border-bottom: 1px solid var(--card-border); background: rgba(255,255,255,0.01); }
        .tracker-steps { display: flex; justify-content: space-between; position: relative; max-width: 800px; margin: 0 auto; }
        .tracker-steps::before { content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 2px; background: #333; z-index: 1; }
        .step { position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; gap: 10px; width: 25%; }
        .step-icon { width: 32px; height: 32px; background: #222; border: 2px solid #444; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #666; font-size: 0.8rem; transition: var(--transition); }
        .step-label { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; text-align: center; }
        
        .step.completed .step-icon { background: var(--success); border-color: var(--success); color: #fff; }
        .step.completed .step-label { color: var(--success); }
        .step.active .step-icon { background: var(--primary-gold); border-color: var(--primary-gold); color: #000; box-shadow: 0 0 15px rgba(197, 160, 89, 0.5); }
        .step.active .step-label { color: var(--primary-gold); }

        /* Tracker Cancelled State */
        .tracker-steps.cancelled::before { background: rgba(244, 67, 54, 0.2); }
        .step.cancelled .step-icon { background: var(--danger); border-color: var(--danger); color: #fff; }
        .step.cancelled .step-label { color: var(--danger); }

        /* =========================================
           ORDER DETAILS & ITEMS
           ========================================= */
        .order-body { padding: 25px; display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        @media (max-width: 992px) { .order-body { grid-template-columns: 1fr; } }
        
        .order-items { display: flex; flex-direction: column; gap: 15px; }
        .order-item { display: flex; align-items: center; gap: 15px; padding: 15px; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); }
        .order-item-img { width: 70px; height: 70px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .order-item-info { flex: 1; }
        .order-item-name { font-weight: 600; font-size: 1.05rem; margin-bottom: 5px; }
        .order-item-price { color: var(--primary-gold); font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.1rem; }
        .order-item-qty { background: #222; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; color: #ccc; margin-left: auto; }

        .order-summary-box { background: rgba(255,255,255,0.02); padding: 20px; border-radius: 15px; border: 1px dashed rgba(197, 160, 89, 0.3); height: fit-content; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; color: #bbb; }
        .summary-row.total { border-top: 1px solid #333; padding-top: 15px; margin-top: 10px; font-size: 1.2rem; color: #fff; font-weight: 600; }
        .summary-row.total span { color: var(--primary-gold); font-family: 'Playfair Display', serif; font-size: 1.4rem; }
        
        .order-details-extra { margin-top: 20px; font-size: 0.85rem; color: var(--text-muted); }
        .order-details-extra p { margin-bottom: 8px; display: flex; gap: 10px; }
        .order-details-extra i { color: var(--primary-gold); width: 15px; text-align: center; margin-top: 3px; }

        /* =========================================
           ACTION BUTTONS
           ========================================= */
        .order-actions { padding: 20px 25px; background: rgba(0,0,0,0.2); border-top: 1px solid var(--card-border); display: flex; justify-content: flex-end; gap: 15px; flex-wrap: wrap; border-radius: 0 0 20px 20px; }
        .btn { padding: 10px 20px; border-radius: 30px; font-size: 0.85rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; border: none; font-family: inherit; }
        
        .btn-outline { background: transparent; color: #fff; border: 1px solid #444; }
        .btn-outline:hover { background: #fff; color: #000; border-color: #fff; }
        
        .btn-gold { background: var(--primary-gold); color: #000; border: 1px solid var(--primary-gold); }
        .btn-gold:hover { background: var(--gold-hover); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(197, 160, 89, 0.4); }
        
        .btn-danger-outline { background: transparent; color: var(--danger); border: 1px solid var(--danger); }
        .btn-danger-outline:hover { background: var(--danger); color: #fff; }

        /* =========================================
           MODALS & TOASTS
           ========================================= */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 2000; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-content { background: var(--card-bg); border: 1px solid var(--primary-gold); padding: 40px; border-radius: 20px; max-width: 500px; width: 90%; position: relative; transform: translateY(20px); transition: transform 0.3s ease; text-align: center; }
        .modal-overlay.active .modal-content { transform: translateY(0); }
        .close-modal { position: absolute; top: 15px; right: 20px; font-size: 1.5rem; color: #888; cursor: pointer; transition: 0.3s; background: none; border: none; }
        .close-modal:hover { color: var(--danger); }
        
        /* Star Rating */
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; gap: 10px; margin: 20px 0; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 2.5rem; color: #444; cursor: pointer; transition: 0.2s; }
        .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: var(--primary-gold); text-shadow: 0 0 10px rgba(197, 160, 89, 0.5); }
        .review-textarea { width: 100%; background: rgba(0,0,0,0.5); border: 1px solid #333; color: #fff; padding: 15px; border-radius: 10px; resize: vertical; min-height: 100px; margin-bottom: 20px; font-family: inherit; }
        .review-textarea:focus { outline: none; border-color: var(--primary-gold); }

        /* Toast */
        .toast { position: fixed; bottom: 30px; right: 30px; background: #fff; color: #000; padding: 15px 25px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 15px; z-index: 9999; transform: translateX(120%); transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); border-left: 5px solid var(--success); }
        .toast.show { transform: translateX(0); }
        .toast i { font-size: 1.5rem; color: var(--success); }

        /* Empty State */
        .empty-state { text-align: center; padding: 80px 20px; background: var(--card-bg); border-radius: 20px; border: 1px dashed #444; }
        .empty-state i { font-size: 4rem; color: #333; margin-bottom: 20px; }
        .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 2rem; margin-bottom: 10px; color: var(--primary-gold); }

        /* Footer */
        footer { background: #000; padding: 30px; text-align: center; color: #666; font-size: 0.9rem; border-top: 1px solid #111; margin-top: 50px; }
    </style>
</head>
<body>

<?php
// Display Toast if exists
if (isset($_SESSION['toast'])) {
    echo "<div class='toast show' id='dynamicToast'><i class='fas fa-check-circle'></i> <div>" . $_SESSION['toast'] . "</div></div>";
    unset($_SESSION['toast']);
}
?>

<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="home.php" class="logo">SULEMAN<span>RESTRO</span></a>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
        <div class="user-greeting">
            <span><i class="fas fa-crown"></i> <?php echo htmlspecialchars($user_name); ?></span>
            <a href="my_orders.php" class="btn-nav active"><i class="fas fa-box-open"></i> Orders</a>
            <a href="logout.php" class="btn-nav" style="background: rgba(244, 67, 54, 0.1); color: var(--danger); border-color: rgba(244,67,54,0.3);"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <h1>My <span>Royal</span> Journey</h1>
        <p>Track your exquisite culinary experiences and past indulgences with us.</p>
    </div>
</section>

<div class="orders-container">
    
    <div class="dashboard-header">
        <div class="stat-box">
            <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
            <div class="stat-text">
                <h4>Total Orders</h4>
                <h2><?php echo count($orders); ?></h2>
            </div>
        </div>
        
        <div class="filter-tabs">
            <button class="filter-btn active" onclick="filterOrders('all')">All Orders</button>
            <button class="filter-btn" onclick="filterOrders('pending')">Active</button>
            <button class="filter-btn" onclick="filterOrders('delivered')">Completed</button>
            <button class="filter-btn" onclick="filterOrders('cancelled')">Cancelled</button>
        </div>
    </div>

    <div id="ordersWrapper">
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <i class="fas fa-utensils"></i>
                <h3>Your plate is empty</h3>
                <p>Looks like you haven't placed any orders yet. Explore our royal menu!</p>
                <br>
                <a href="menu.php" class="btn btn-gold">Browse Menu</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): 
                $status = strtolower($order['status']);
                // Determine Tracker Steps based on status
                $step1 = $step2 = $step3 = $step4 = "";
                if($status == 'cancelled') {
                    // special handling in CSS
                } else {
                    $step1 = "completed"; // Pending is always completed if it exists
                    if(in_array($status, ['confirmed', 'preparing', 'delivered'])) $step2 = "completed";
                    if(in_array($status, ['preparing', 'delivered'])) $step3 = "completed";
                    if($status == 'delivered') $step4 = "completed";
                    
                    // Set Active Step
                    if($status == 'pending') $step1 = "active";
                    if($status == 'confirmed') $step2 = "active";
                    if($status == 'preparing') $step3 = "active";
                    if($status == 'delivered') $step4 = "active";
                }

                // Fetch Items
                $conn_items = mysqli_connect("localhost", "root", "", "sulemanrestro");
                $order_id = $order['id'];
                $items = [];
                $items_res = mysqli_query($conn_items, "SELECT oi.*, mi.name, mi.image_path FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = $order_id");
                if ($items_res) {
                    while ($item = mysqli_fetch_assoc($items_res)) { $items[] = $item; }
                }
            ?>
            <div class="order-card order-item-card" data-status="<?php echo $status; ?>">
                
                <div class="order-header">
                    <div class="order-meta">
                        <div class="order-id">Order #<span><?php echo $order['id']; ?></span></div>
                        <div class="order-date"><i class="far fa-clock"></i> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></div>
                    </div>
                    <?php
                        $icon = 'fa-clock';
                        if($status == 'delivered') $icon = 'fa-check-circle';
                        if($status == 'cancelled') $icon = 'fa-times-circle';
                        if($status == 'preparing') $icon = 'fa-fire';
                    ?>
                    <div class="status-badge status-<?php echo $status; ?>">
                        <i class="fas <?php echo $icon; ?>"></i> <?php echo ucfirst($status); ?>
                    </div>
                </div>

                <div class="progress-tracker">
                    <div class="tracker-steps <?php echo ($status == 'cancelled') ? 'cancelled' : ''; ?>">
                        <?php if($status == 'cancelled'): ?>
                            <div class="step cancelled"><div class="step-icon"><i class="fas fa-times"></i></div><div class="step-label">Order Cancelled</div></div>
                        <?php else: ?>
                            <div class="step <?php echo $step1; ?>"><div class="step-icon"><i class="fas fa-clipboard-list"></i></div><div class="step-label">Order Placed</div></div>
                            <div class="step <?php echo $step2; ?>"><div class="step-icon"><i class="fas fa-check"></i></div><div class="step-label">Confirmed</div></div>
                            <div class="step <?php echo $step3; ?>"><div class="step-icon"><i class="fas fa-fire"></i></div><div class="step-label">Preparing</div></div>
                            <div class="step <?php echo $step4; ?>"><div class="step-icon"><i class="fas fa-home"></i></div><div class="step-label">Delivered</div></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="order-body">
                    <div class="order-items">
                        <?php foreach ($items as $item): 
                            $img_path = !empty($item['image_path']) ? $item['image_path'] : 'restro_img/menu/placeholder.jpg';
                        ?>
                        <div class="order-item">
                            <img src="<?php echo $img_path; ?>" class="order-item-img" alt="Item">
                            <div class="order-item-info">
                                <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="order-item-price">₹<?php echo number_format($item['price_at_time'], 2); ?></div>
                            </div>
                            <div class="order-item-qty">Qty: <?php echo $item['quantity']; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-summary-box">
                        <h4 style="margin-bottom: 15px; color: #fff; font-family: 'Playfair Display', serif;">Order Summary</h4>
                        <div class="summary-row"><span>Subtotal</span><span>₹<?php echo number_format($order['total_amount'] * 0.95, 2); ?></span></div>
                        <div class="summary-row"><span>Taxes & Fees</span><span>₹<?php echo number_format($order['total_amount'] * 0.05, 2); ?></span></div>
                        <div class="summary-row total"><span>Total</span><span>₹<?php echo number_format($order['total_amount'], 2); ?></span></div>
                        
                        <div class="order-details-extra">
                            <p><i class="fas fa-map-marker-alt"></i> <span><?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></span></p>
                            <?php if (!empty($order['notes'])): ?>
                                <p><i class="fas fa-comment-dots"></i> <span><?php echo htmlspecialchars($order['notes']); ?></span></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="order-actions">
                    <button class="btn btn-outline" onclick="alert('Help center is currently under maintenance. Please call support.')"><i class="fas fa-headset"></i> Need Help?</button>
                    
                    <?php if ($status == 'delivered'): ?>
                        <button class="btn btn-outline" onclick="openInvoice(<?php echo $order['id']; ?>)"><i class="fas fa-file-invoice"></i> Receipt</button>
                        <button class="btn btn-gold" onclick="openReviewModal(<?php echo $order['id']; ?>)"><i class="fas fa-star"></i> Rate Order</button>
                        
                        <form action="" method="POST" style="margin:0;">
                            <input type="hidden" name="action" value="reorder">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" class="btn btn-gold"><i class="fas fa-sync-alt"></i> Reorder</button>
                        </form>

                    <?php elseif ($status == 'pending'): ?>
                        <form action="" method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to cancel this delicious order?');">
                            <input type="hidden" name="action" value="cancel">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" class="btn btn-danger-outline"><i class="fas fa-times"></i> Cancel Order</button>
                        </form>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal-overlay" id="reviewModal">
    <div class="modal-content">
        <button class="close-modal" onclick="closeModal('reviewModal')">&times;</button>
        <h2 style="font-family: 'Playfair Display', serif; color: var(--primary-gold); margin-bottom: 10px;">Rate Your Experience</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem;">How was the food from Order #<span id="reviewOrderId"></span>?</p>
        
        <form onsubmit="submitReview(event)">
            <div class="star-rating">
                <input type="radio" id="star5" name="rating" value="5"><label for="star5" class="fas fa-star"></label>
                <input type="radio" id="star4" name="rating" value="4"><label for="star4" class="fas fa-star"></label>
                <input type="radio" id="star3" name="rating" value="3"><label for="star3" class="fas fa-star"></label>
                <input type="radio" id="star2" name="rating" value="2"><label for="star2" class="fas fa-star"></label>
                <input type="radio" id="star1" name="rating" value="1"><label for="star1" class="fas fa-star"></label>
            </div>
            <textarea class="review-textarea" placeholder="Tell us what you loved (or what we can improve)..." required></textarea>
            <button type="submit" class="btn btn-gold" style="width: 100%; justify-content: center; font-size: 1rem; padding: 12px;">Submit Review</button>
        </form>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Suleman Restro. Crafted for Royalty. All rights reserved.</p>
</footer>

<script>
    // Navbar Scroll Effect
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });

    // Filtering Logic
    function filterOrders(filterType) {
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        // Filter cards
        const cards = document.querySelectorAll('.order-item-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const status = card.getAttribute('data-status');
            let shouldShow = false;

            if (filterType === 'all') shouldShow = true;
            else if (filterType === 'pending' && (status === 'pending' || status === 'confirmed' || status === 'preparing')) shouldShow = true;
            else if (filterType === 'delivered' && status === 'delivered') shouldShow = true;
            else if (filterType === 'cancelled' && status === 'cancelled') shouldShow = true;

            if (shouldShow) {
                card.style.display = 'block';
                // Add a small fade-in animation
                card.style.opacity = '0';
                setTimeout(() => card.style.opacity = '1', 50);
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show empty state if no orders match filter
        let emptyState = document.getElementById('filterEmptyState');
        if(visibleCount === 0 && cards.length > 0) {
            if(!emptyState) {
                emptyState = document.createElement('div');
                emptyState.id = 'filterEmptyState';
                emptyState.className = 'empty-state';
                emptyState.innerHTML = '<i class="fas fa-search"></i><h3>No orders found</h3><p>No orders match this status.</p>';
                document.getElementById('ordersWrapper').appendChild(emptyState);
            }
            emptyState.style.display = 'block';
        } else if (emptyState) {
            emptyState.style.display = 'none';
        }
    }

    // Modal Logic
    function openReviewModal(orderId) {
        document.getElementById('reviewOrderId').innerText = orderId;
        document.getElementById('reviewModal').classList.add('active');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    function submitReview(e) {
        e.preventDefault();
        closeModal('reviewModal');
        showToast("Thank you! Your royal feedback has been recorded.");
    }

    // PDF Simulation
    function openInvoice(orderId) {
        showToast("Generating PDF Invoice for Order #" + orderId + "...");
        setTimeout(() => { showToast("Invoice downloaded successfully."); }, 2000);
    }

    // Custom Toast Notification
    function showToast(message) {
        // Remove existing toast if any
        const existing = document.getElementById('customToast');
        if(existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'customToast';
        toast.className = 'toast';
        toast.innerHTML = `<i class='fas fa-info-circle' style='color:var(--primary-gold)'></i> <div style='font-weight:500'>${message}</div>`;
        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('show'), 100);
        // Animate out
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }

    // Auto-hide PHP generated toast
    document.addEventListener("DOMContentLoaded", () => {
        const phpToast = document.getElementById('dynamicToast');
        if(phpToast) {
            setTimeout(() => {
                phpToast.classList.remove('show');
            }, 4000);
        }
    });
</script>
</body>
</html>