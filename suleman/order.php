<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding to cart
if (isset($_GET['add'])) {
    $item_id = intval($_GET['add']);
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]['quantity']++;
    } else {
        // Fetch item details from database to store in cart
        $conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
        if ($conn) {
            $result = mysqli_query($conn, "SELECT id, name, price, image_path FROM menu_items WHERE id = $item_id AND is_available = 1");
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $_SESSION['cart'][$item_id] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'image' => $row['image_path'],
                    'quantity' => 1
                ];
            }
            mysqli_close($conn);
        }
    }
    // Redirect back to same page to avoid re-add on refresh
    header("Location: order.php");
    exit;
}

// Handle removing from cart
if (isset($_GET['remove'])) {
    $item_id = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$item_id])) {
        unset($_SESSION['cart'][$item_id]);
    }
    header("Location: order.php");
    exit;
}

// Handle updating quantity
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $qty) {
        $id = intval($id);
        $qty = intval($qty);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }
    }
    header("Location: order.php");
    exit;
}

// Calculate cart totals
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

// Fetch menu items from database (for display)
$conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$menu_items = [];
$query = "SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, name";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menu_items[] = $row;
    }
}
mysqli_close($conn);

// Group by category
$categories = [];
foreach ($menu_items as $item) {
    $cat = $item['category'];
    if (!isset($categories[$cat])) {
        $categories[$cat] = [];
    }
    $categories[$cat][] = $item;
}

// Check if user is logged in (for pre-filling checkout form)
$logged_in = isset($_SESSION['user_email']);
$user_email = $logged_in ? $_SESSION['user_email'] : '';
// Fetch name from signup if logged in (optional)
$user_name = '';
if ($logged_in) {
    $conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
    if ($conn) {
        $res = mysqli_query($conn, "SELECT NAME FROM signup WHERE EMAIL = '$user_email'");
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $user_name = $row['NAME'];
        }
        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Online | Suleman Restro</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #0a0a0a; color: #fff; overflow-x: hidden; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1a1a1a; }
        ::-webkit-scrollbar-thumb { background: #C5A059; border-radius: 4px; }

        .navbar { position: fixed; top: 0; left: 0; width: 100%; padding: 20px 40px; background: rgba(0,0,0,0.7); backdrop-filter: blur(12px); z-index: 1000; transition: 0.3s; border-bottom: 1px solid rgba(197,160,89,0.2); }
        .navbar.scrolled { padding: 12px 40px; background: rgba(0,0,0,0.9); border-bottom-color: rgba(197,160,89,0.5); }
        .nav-container { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; color: #fff; text-decoration: none; letter-spacing: 2px; }
        .logo span { color: #C5A059; }
        .nav-links { list-style: none; display: flex; gap: 35px; }
        .nav-links li a { color: #fff; text-decoration: none; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; position: relative; }
        .nav-links li a:after { content: ''; position: absolute; bottom: -6px; left: 0; width: 0; height: 2px; background: #C5A059; transition: width 0.3s; }
        .nav-links li a:hover:after, .nav-links li a.active:after { width: 100%; }
        .nav-links li a:hover, .nav-links li a.active { color: #C5A059; }
        
        .user-greeting { display: flex; align-items: center; gap: 15px; }
        .user-greeting span { color: #C5A059; font-size: 14px; }
        .user-greeting a { background: #C5A059; padding: 6px 16px; border-radius: 30px; color: #000; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.3s; }
        .user-greeting a:hover { background: #fff; transform: translateY(-2px); }

        .hero { height: 40vh; background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('restro_img/order-hero.jpg') center/cover no-repeat; display: flex; align-items: center; justify-content: center; text-align: center; margin-top: 80px; }
        .hero-content h1 { font-family: 'Playfair Display', serif; font-size: 3.5rem; margin-bottom: 20px; }
        .hero-content h1 span { color: #C5A059; }

        .order-container { max-width: 1400px; margin: 40px auto; padding: 0 20px; display: flex; gap: 40px; flex-wrap: wrap; }
        .menu-section { flex: 2; min-width: 300px; }
        .cart-section { flex: 1; min-width: 280px; background: #111; border-radius: 20px; border: 1px solid #222; padding: 20px; position: sticky; top: 100px; height: fit-content; }
        
        .category-title { font-family: 'Playfair Display', serif; font-size: 1.8rem; margin: 30px 0 20px; position: relative; display: inline-block; }
        .category-title:after { content: ''; display: block; width: 50px; height: 2px; background: #C5A059; margin-top: 8px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
        .menu-card { background: #111; border-radius: 15px; overflow: hidden; border: 1px solid #222; transition: 0.3s; }
        .menu-card:hover { transform: translateY(-5px); border-color: #C5A059; box-shadow: 0 10px 20px rgba(197, 160, 89, 0.1); }
        .menu-card img { width: 100%; height: 180px; object-fit: cover; }
        .menu-info { padding: 15px; }
        .menu-info h3 { font-size: 1.2rem; margin-bottom: 5px; color: #C5A059; }
        .menu-info p { color: #bbb; font-size: 0.8rem; margin-bottom: 10px; }
        .price { font-size: 1.2rem; font-weight: 600; margin: 10px 0; }
        .add-btn { background: transparent; border: 1px solid #C5A059; color: #C5A059; padding: 6px 15px; border-radius: 25px; cursor: pointer; transition: 0.3s; text-decoration: none; display: inline-block; font-size: 0.8rem; }
        .add-btn:hover { background: #C5A059; color: #000; }

        .cart-section h2 { color: #C5A059; margin-bottom: 20px; font-size: 1.5rem; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #222; }
        .cart-item-info { flex: 1; }
        .cart-item-name { font-weight: 500; }
        .cart-item-price { font-size: 0.8rem; color: #C5A059; }
        .cart-item-qty { display: flex; align-items: center; gap: 8px; }
        .cart-item-qty input { width: 50px; background: #222; border: 1px solid #333; color: #fff; text-align: center; padding: 4px; border-radius: 5px; }
        .cart-item-remove a { color: #ff8888; text-decoration: none; margin-left: 10px; }
        .cart-total { margin: 20px 0; font-size: 1.2rem; text-align: right; border-top: 1px solid #222; padding-top: 15px; font-weight: bold; }
        .checkout-btn { background: #C5A059; color: #000; border: none; width: 100%; padding: 12px; border-radius: 30px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .checkout-btn:hover { background: #fff; }
        .empty-cart { text-align: center; color: #888; padding: 20px; }

        /* MODAL STYLES */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 2000; justify-content: center; align-items: center; }
        .modal-content { background: #111; border-radius: 20px; padding: 30px; max-width: 500px; width: 90%; border: 1px solid #C5A059; position: relative; animation: slideIn 0.3s ease-out forwards; }
        @keyframes slideIn { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-content h2 { color: #C5A059; margin-bottom: 20px; text-align: center; font-family: 'Playfair Display', serif; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 0.8rem; color: #aaa; }
        .form-group input, .form-group textarea { width: 100%; background: #222; border: 1px solid #333; padding: 10px; border-radius: 8px; color: #fff; font-family: 'Poppins', sans-serif; }
        .form-group input:focus, .form-group textarea:focus { border-color: #C5A059; outline: none; }
        .modal-buttons { display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px; }
        .modal-buttons button { padding: 10px 25px; border-radius: 30px; cursor: pointer; font-weight: 600; border: none; transition: 0.3s; }
        .close-modal { background: #333; color: #fff; }
        .close-modal:hover { background: #555; }
        .submit-order { background: #C5A059; color: #000; }
        .submit-order:hover { background: #fff; }

        /* SUCCESS TOAST ANIMATION */
        .success-toast { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.8); background: #111; border: 2px solid #C5A059; padding: 40px; border-radius: 20px; text-align: center; z-index: 3000; opacity: 0; visibility: hidden; transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 10px 50px rgba(0,0,0,0.9); }
        .success-toast.active { opacity: 1; visibility: visible; transform: translate(-50%, -50%) scale(1); }
        .success-toast i { font-size: 5rem; color: #4caf50; margin-bottom: 15px; animation: popIn 0.5s ease 0.2s both; }
        @keyframes popIn { 0% { transform: scale(0); } 80% { transform: scale(1.2); } 100% { transform: scale(1); } }
        .success-toast h2 { color: #C5A059; font-family: 'Playfair Display', serif; font-size: 2rem; margin-bottom: 10px; }
        .success-toast p { color: #ccc; font-size: 0.9rem; }

        footer { background: #000; padding: 40px 5% 20px; margin-top: 60px; text-align: center; color: #888; border-top: 1px solid #111; }
        
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-container { flex-direction: column; gap: 15px; }
            .hero-content h1 { font-size: 2.5rem; }
            .order-container { flex-direction: column; }
            .cart-section { position: static; margin-top: 20px; }
        }
    </style>
</head>
<body>

<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="home.php" class="logo">SULEMAN<span>RESTRO</span></a>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="menu.php" class="active">Menu</a></li>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <?php if ($logged_in): ?>
                <li><a href="my_orders.php"><i class="fas fa-list"></i> My Orders</a></li>
            <?php endif; ?>
        </ul>
        <div class="user-greeting">
            <?php if ($logged_in): ?>
                <span><i class="fas fa-crown"></i> <?php echo htmlspecialchars($user_name); ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="index.php">Sign In</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <h1>Order <span>Online</span></h1>
        <p>Experience royal flavors from the comfort of your home</p>
    </div>
</section>

<div class="order-container">
    <div class="menu-section">
        <?php if (empty($categories)): ?>
            <p>Menu is being curated. Please check back soon.</p>
        <?php else: ?>
            <?php foreach ($categories as $category => $items): ?>
                <h2 class="category-title"><?php echo htmlspecialchars($category); ?></h2>
                <div class="menu-grid">
                    <?php foreach ($items as $item): ?>
                        <div class="menu-card">
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <?php else: ?>
                                <img src="restro_img/menu/placeholder.jpg" alt="Placeholder">
                            <?php endif; ?>
                            <div class="menu-info">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                                <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>
                                <a href="?add=<?php echo $item['id']; ?>" class="add-btn"><i class="fas fa-cart-plus"></i> Add</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="cart-section">
        <h2><i class="fas fa-shopping-bag"></i> Your Cart</h2>
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 10px; color: #444;"></i><br>
                Your royal cart is empty.
            </div>
        <?php else: ?>
            <form method="POST" action="order.php" id="cart-form">
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="cart-item-price">₹<?php echo number_format($item['price'], 2); ?> each</div>
                        </div>
                        <div class="cart-item-qty">
                            <input type="number" name="quantity[<?php echo $id; ?>]" value="<?php echo $item['quantity']; ?>" min="0" step="1">
                            <div class="cart-item-remove">
                                <a href="?remove=<?php echo $id; ?>" title="Remove Item"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div style="text-align: right; margin: 10px 0;">
                    <button type="submit" name="update_cart" class="add-btn" style="background: #222; border-color: #333; color: #fff;">Update Cart</button>
                </div>
                <div class="cart-total">
                    Total Amount: <span style="color: #C5A059;">₹<?php echo number_format($cart_total, 2); ?></span>
                </div>
                <button type="button" class="checkout-btn" id="checkoutBtn"><i class="fas fa-check-circle"></i> Proceed to Checkout</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div id="checkoutModal" class="modal">
    <div class="modal-content">
        <h2>Complete Your Order</h2>
        <form action="process_order.php" method="POST" id="checkoutForm">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user_name); ?>" required placeholder="Enter your name">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Phone *</label>
                <input type="tel" name="phone" required placeholder="Enter your mobile number">
            </div>
            <div class="form-group">
                <label>Delivery Address *</label>
                <textarea name="address" rows="3" required placeholder="Enter full delivery address"></textarea>
            </div>
            <div class="form-group">
                <label>Special Notes (optional)</label>
                <textarea name="notes" rows="2" placeholder="Any specific instructions for the chef?"></textarea>
            </div>
            <div class="modal-buttons">
                <button type="button" class="close-modal" onclick="closeModal()">Cancel</button>
                <button type="submit" class="submit-order" id="placeOrderBtn">Place Order</button>
            </div>
        </form>
    </div>
</div>

<div id="successToast" class="success-toast">
    <i class="fas fa-check-circle"></i>
    <h2>Order Sent!</h2>
    <p>Your royal order is being processed.</p>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Suleman Restro. Crafted for Royalty. All rights reserved.</p>
</footer>

<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });

    // Checkout Modal Logic
    const modal = document.getElementById('checkoutModal');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    if (checkoutBtn) {
        checkoutBtn.onclick = function() {
            modal.style.display = 'flex';
        };
    }
    
    function closeModal() {
        modal.style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    };

    // --- NEW: Animated Success Message on Order Placement ---
    const checkoutForm = document.getElementById('checkoutForm');
    const successToast = document.getElementById('successToast');
    const placeOrderBtn = document.getElementById('placeOrderBtn');

    if(checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Form ko turant submit hone se roko
            e.preventDefault(); 
            
            // Button ko disable kar do taaki user double click na kare
            placeOrderBtn.innerText = "Processing...";
            placeOrderBtn.disabled = true;

            // Checkout form wala modal hide karo
            closeModal();

            // Success animation popup show karo
            successToast.classList.add('active');

            // 2.5 seconds baad automatically data process_order.php par bhej do
            setTimeout(() => {
                this.submit();
            }, 2500); 
        });
    }
</script>

</body>
</html>