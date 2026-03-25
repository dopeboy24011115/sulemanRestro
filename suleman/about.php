<?php
session_start();
$logged_in = isset($_SESSION['user_email']);
$user_name = $logged_in ? explode('@', $_SESSION['user_email'])[0] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Suleman Restro – 5-Star Legacy</title>
    
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
        
        /* Navbar (same as homepage) */
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
            height: 60vh;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('restro_img/about-hero.jpg') center/cover no-repeat;
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
        
        /* Common Sections */
        .section {
            padding: 100px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            text-align: center;
            margin-bottom: 15px;
            position: relative;
        }
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: #C5A059;
            margin: 15px auto 0;
        }
        .section-sub {
            text-align: center;
            color: #aaa;
            margin-bottom: 60px;
            font-size: 1rem;
        }
        
        /* Story Grid */
        .story-grid {
            display: flex;
            gap: 60px;
            align-items: center;
            flex-wrap: wrap;
        }
        .story-text {
            flex: 1;
        }
        .story-text p {
            line-height: 1.8;
            margin-bottom: 20px;
            color: #ccc;
        }
        .story-img {
            flex: 1;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 30px rgba(0,0,0,0.3);
        }
        .story-img img {
            width: 100%;
            display: block;
            transition: 0.5s;
        }
        .story-img:hover img {
            transform: scale(1.05);
        }
        
        /* Philosophy Cards */
        .philosophy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            margin-top: 40px;
        }
        .philosophy-card {
            background: #111;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid #222;
            transition: 0.3s;
        }
        .philosophy-card:hover {
            transform: translateY(-10px);
            border-color: #C5A059;
        }
        .philosophy-card i {
            font-size: 3rem;
            color: #C5A059;
            margin-bottom: 20px;
        }
        .philosophy-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .philosophy-card p {
            color: #bbb;
            line-height: 1.6;
        }
        
        /* Team Section */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }
        .team-card {
            background: #111;
            border-radius: 20px;
            overflow: hidden;
            text-align: center;
            border: 1px solid #222;
            transition: 0.3s;
        }
        .team-card:hover {
            transform: translateY(-10px);
            border-color: #C5A059;
        }
        .team-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .team-info {
            padding: 20px;
        }
        .team-info h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: #C5A059;
        }
        .team-info p {
            color: #888;
            font-size: 0.9rem;
        }
        
        /* Video Showcase (replacing timeline) */
        .video-showcase {
            background: #0f0f0f;
            border-radius: 40px;
            padding: 60px 5%;
        }
        .video-container {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 40px rgba(0,0,0,0.5);
            background: #000;
            margin-top: 30px;
        }
        .restro-video {
            width: 100%;
            display: block;
            transition: transform 0.4s ease;
        }
        .video-caption {
            text-align: center;
            margin-top: 25px;
            font-size: 0.95rem;
            color: #aaa;
            letter-spacing: 0.5px;
        }
        .video-caption i {
            color: #C5A059;
            margin-right: 8px;
        }
        @media (max-width: 768px) {
            .video-showcase {
                padding: 40px 5%;
            }
        }
        
        /* Footer (same as homepage) */
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
            .section-title { font-size: 2rem; }
            .story-grid { flex-direction: column; text-align: center; }
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
            <li><a href="about.php" class="active">About Us</a></li>
            <li><a href="menu.php">Menu</a></li>
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
        <h1>Our <span>Legacy</span></h1>
        <p>Since 1985, crafting memories fit for royalty</p>
    </div>
</section>

<!-- Story Section -->
<section class="section">
    <div class="story-grid">
        <div class="story-text">
            <h2 class="section-title" style="text-align: left; margin-bottom: 30px;">The Suleman Story</h2>
            <p>What began as a humble family kitchen in the heart of Old Delhi has blossomed into an 5-star culinary destination. Founded by the late Suleman Khan, our restaurant was built on a simple philosophy: every guest deserves to feel like royalty.</p>
            <p>Today, the third generation of the Suleman family continues this legacy, blending traditional recipes with modern innovation. Our chefs are guardians of time‑honored techniques, using spices sourced directly from the finest farms and ingredients handpicked for purity.</p>
            <p>We don't just serve food — we curate experiences. From the moment you step through our doors, you become part of a story that celebrates heritage, flavor, and unparalleled hospitality.</p>
        </div>
        <div class="story-img">
            <img src="restro_img/about-story.jpg" alt="Family Legacy">
        </div>
    </div>
</section>

<!-- Philosophy Section -->
<section class="section" style="background: #0f0f0f; border-radius: 40px;">
    <h2 class="section-title">Our Philosophy</h2>
    <div class="section-sub">The pillars that define our 5-star commitment</div>
    <div class="philosophy-grid">
        <div class="philosophy-card">
            <i class="fas fa-seedling"></i>
            <h3>Authenticity</h3>
            <p>We honor age‑old recipes passed down through generations, using traditional techniques and premium ingredients.</p>
        </div>
        <div class="philosophy-card">
            <i class="fas fa-hand-sparkles"></i>
            <h3>Excellence</h3>
            <p>Every plate is a masterpiece. Our pursuit of perfection is relentless, from kitchen to table.</p>
        </div>
        <div class="philosophy-card">
            <i class="fas fa-heart"></i>
            <h3>Hospitality</h3>
            <p>We believe in treating every guest as family, with warmth, attention, and genuine care.</p>
        </div>
    </div>
</section>

<!-- Meet the Team -->
<section class="section">
    <h2 class="section-title">Our Masters</h2>
    <div class="section-sub">The artisans behind the magic</div>
    <div class="team-grid">
        <div class="team-card">
            <img src="restro_img/chef1.jpg" alt="Chef">
            <div class="team-info">
                <h3>Chef Arjun Singh</h3>
                <p>Executive Chef • 25 Years Experience</p>
            </div>
        </div>
        <div class="team-card">
            <img src="restro_img/chef2.jpg" alt="Chef">
            <div class="team-info">
                <h3>Chef Imtiaz Qureshi</h3>
                <p>Master of Dum Pukht Cuisine</p>
            </div>
        </div>
        <div class="team-card">
            <img src="restro_img/chef3.jpg" alt="Chef">
            <div class="team-info">
                <h3>Pastry Chef Meera</h3>
                <p>Dessert Artisan</p>
            </div>
        </div>
        <div class="team-card">
            <img src="restro_img/chef4.jpg" alt="Sommelier">
            <div class="team-info">
                <h3>Rahul Mehta</h3>
                <p>Head Sommelier</p>
            </div>
        </div>
    </div>
</section>

<!-- VIDEO SHOWCASE SECTION - Replaces Timeline -->
<div class="video-showcase">
    <h2 class="section-title">5-Star Visual Journey</h2>
    <div class="section-sub">Experience the elegance & grandeur of our royal dining spaces</div>
    
    <div class="video-container">
        <video autoplay muted loop playsinline class="restro-video">
            <source src="videorestro.mp4" type="video/mp4">
            Your browser does not support the video tag. Please upgrade to a modern browser.
        </video>
    </div>
    <div class="video-caption">
        <i class="fas fa-video"></i> Step into a world of luxury — where every detail tells a story of excellence (video loops)
    </div>
</div>

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
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>
</body>
</html>