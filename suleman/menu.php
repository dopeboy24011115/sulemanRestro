<?php
session_start();
$logged_in = isset($_SESSION['user_email']);
$user_name = $logged_in ? explode('@', $_SESSION['user_email'])[0] : '';

// Database connection
$conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch menu items
$menu_items = [];
$query = "SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, name";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menu_items[] = $row;
    }
}
mysqli_close($conn);

// Group items by category
$categories = [];
foreach ($menu_items as $item) {
    $cat = $item['category'];
    if (!isset($categories[$cat])) {
        $categories[$cat] = [];
    }
    $categories[$cat][] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | Suleman Restro – 5-Star Culinary Journey</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            overflow-x: hidden;
        }
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }
        ::-webkit-scrollbar-thumb {
            background: #C5A059;
            border-radius: 4px;
        }
        
        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 40px;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(12px);
            z-index: 1000;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(197,160,89,0.2);
        }
        .navbar.scrolled {
            padding: 12px 40px;
            background: rgba(0,0,0,0.9);
            border-bottom-color: rgba(197,160,89,0.5);
        }
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            letter-spacing: 2px;
        }
        .logo span {
            color: #C5A059;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 35px;
        }
        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.3s;
            position: relative;
        }
        .nav-links a:after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 0;
            height: 2px;
            background: #C5A059;
            transition: width 0.3s;
        }
        .nav-links a:hover:after,
        .nav-links a.active:after {
            width: 100%;
        }
        .nav-links a:hover,
        .nav-links a.active {
            color: #C5A059;
        }
        .user-greeting {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-greeting span {
            color: #C5A059;
            font-size: 14px;
        }
        .user-greeting a {
            background: #C5A059;
            padding: 6px 16px;
            border-radius: 30px;
            color: #000;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: 0.3s;
        }
        .user-greeting a:hover {
            background: #fff;
            transform: translateY(-2px);
        }
        
        /* Hero Section */
        .hero {
            height: 50vh;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('restro_img/menu-hero.jpg') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-top: 80px;
        }
        .hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .hero-content h1 span {
            color: #C5A059;
        }
        .hero-content p {
            font-size: 1.2rem;
            color: #ddd;
        }
        
        /* Menu Section */
        .menu-section {
            padding: 80px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        .category-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin: 50px 0 30px;
            position: relative;
            display: inline-block;
        }
        .category-title:after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: #C5A059;
            margin-top: 10px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }
        .menu-card {
            background: #111;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #222;
            transition: 0.3s;
            display: flex;
            flex-direction: column;
        }
        .menu-card:hover {
            transform: translateY(-8px);
            border-color: #C5A059;
            box-shadow: 0 15px 30px rgba(0,0,0,0.5);
        }
        .menu-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .menu-info {
            padding: 20px;
            flex: 1;
        }
        .menu-info h3 {
            font-size: 1.4rem;
            margin-bottom: 8px;
            color: #C5A059;
        }
        .menu-info p {
            color: #bbb;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .price {
            font-size: 1.3rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 15px;
        }
        .price span {
            font-size: 0.9rem;
            color: #C5A059;
        }
        
        /* Global Order Button */
        .global-order-btn {
            text-align: center;
            margin: 60px auto 40px;
        }
        .global-order-btn a {
            display: inline-block;
            background: #C5A059;
            color: #000;
            padding: 14px 40px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            letter-spacing: 1px;
            cursor: pointer;
            border: none;
        }
        .global-order-btn a:hover {
            background: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(197,160,89,0.3);
        }
        
        /* Admin Link (optional) */
        .admin-link {
            text-align: center;
            margin-top: 60px;
        }
        .admin-link a {
            background: rgba(197,160,89,0.2);
            border: 1px solid #C5A059;
            padding: 10px 25px;
            border-radius: 40px;
            color: #C5A059;
            text-decoration: none;
            font-size: 0.8rem;
            transition: 0.3s;
        }
        .admin-link a:hover {
            background: #C5A059;
            color: #000;
        }
        
        /* Footer */
        footer {
            background: #000;
            padding: 60px 5% 30px;
            border-top: 1px solid #111;
        }
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }
        .footer-col h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .footer-col h3 span {
            color: #C5A059;
        }
        .footer-col p, .footer-col ul li a {
            color: #888;
            line-height: 1.6;
            text-decoration: none;
        }
        .footer-col ul {
            list-style: none;
        }
        .footer-col ul li {
            margin-bottom: 12px;
        }
        .footer-col ul li a:hover {
            color: #C5A059;
        }
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .social-icons a {
            background: #222;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #C5A059;
            transition: 0.3s;
        }
        .social-icons a:hover {
            background: #C5A059;
            color: #000;
        }
        .copyright {
            text-align: center;
            padding-top: 40px;
            margin-top: 40px;
            border-top: 1px solid #111;
            color: #666;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-container { flex-direction: column; gap: 15px; }
            .nav-links { gap: 20px; flex-wrap: wrap; justify-content: center; }
            .hero-content h1 { font-size: 2.5rem; }
            .category-title { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="home.php" class="logo">SULEMAN<span>RESTRO</span></a>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="menu.php" class="active">Menu</a></li>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
        <div class="user-greeting">
            <?php if ($logged_in): ?>
                <span><i class="fas fa-crown"></i> Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="index.php">Sign In</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="hero-content">
        <h1>Our <span>Menu</span></h1>
        <p>A symphony of flavors crafted for the discerning palate</p>
    </div>
</section>

<!-- Menu Items -->
<section class="menu-section">
    <?php if (empty($categories)): ?>
        <p style="text-align: center; color: #aaa;">Menu is being curated. Please check back soon.</p>
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
                            <div class="price">₹<?php echo number_format($item['price'], 2); ?> <span>per serving</span></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Single "Order Now" button with login check -->
    <div class="global-order-btn">
        <a id="orderNowBtn"><i class="fas fa-shopping-cart"></i> Order Now</a>
    </div>
    
    <!-- Admin Control Link (commented) -->
    <!-- <div class="admin-link">
        <a href="admin/menucontrol.php"><i class="fas fa-cog"></i> Manage Menu (Admin)</a>
    </div> -->
</section>

<!-- Footer -->
<footer>
    <div class="footer-content">
        <div class="footer-col">
            <h3>SULEMAN<span>RESTRO</span></h3>
            <p>Where heritage meets luxury. Experience the finest dining with 5-star hospitality.</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>Contact Info</h3>
            <ul>
                <li><i class="fas fa-map-marker-alt"></i> 5 Star Avenue, Mumbai, India</li>
                <li><i class="fas fa-phone"></i> +91 98765 43210</li>
                <li><i class="fas fa-envelope"></i> sulemanrestro@gmail.com</li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>Opening Hours</h3>
            <ul>
                <li>Monday – Friday: 12:00 – 23:00</li>
                <li>Saturday – Sunday: 11:00 – 00:00</li>
                <li>Royal Afternoon Tea: 15:00 – 17:00</li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; 2026 Suleman Restro. Crafted for Royalty. All rights reserved.</p>
    </div>
</footer>

<script>
    // Navbar scroll effect (single instance)
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Login status from PHP
    const isLoggedIn = <?php echo json_encode($logged_in); ?>;

    // Order button handler
    const orderBtn = document.getElementById('orderNowBtn');
    if (orderBtn) {
        orderBtn.addEventListener('click', function() {
            if (isLoggedIn) {
                window.location.href = "order.php";
            } else {
                alert("Welcome! Kindly log in to place your order and enjoy a royal dining experience.");
                window.location.href = "sign.php";
            }
        });
    }
</script>
</body>
</html>