<?php
session_start(); // Start session to check login status
$logged_in = isset($_SESSION['user_email']);
$user_name = $logged_in ? explode('@', $_SESSION['user_email'])[0] : ''; // Simple name from email
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Suleman Restro | 5-starRoyal Experience</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper for Testimonials -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0a0a0a;
            color: #fff;
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
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

        /* Navbar - Glassmorphism */
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

        /* Hero Section with Video Background */
        .hero {
            height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }
        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.5));
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 20px;
        }
        .hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 5rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 3px;
            animation: fadeInUp 1s ease;
        }
        .hero-content h1 span {
            color: #C5A059;
        }
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #ddd;
            animation: fadeInUp 1s ease 0.2s both;
        }
        .btn-gold {
            background: #C5A059;
            color: #000;
            padding: 14px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 1px;
            display: inline-block;
            transition: 0.3s;
            border: none;
            cursor: pointer;
            animation: fadeInUp 1s ease 0.4s both;
        }
        .btn-gold:hover {
            background: #fff;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(197,160,89,0.3);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Common Section */
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

        /* About Section */
        .about-grid {
            display: flex;
            gap: 60px;
            align-items: center;
            flex-wrap: wrap;
        }
        .about-text {
            flex: 1;
        }
        .about-text p {
            line-height: 1.7;
            margin-bottom: 20px;
            color: #ccc;
        }
        .about-stats {
            display: flex;
            gap: 30px;
            margin-top: 30px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #C5A059;
        }
        .stat-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .about-img {
            flex: 1;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 30px rgba(0,0,0,0.3);
        }
        .about-img img {
            width: 100%;
            display: block;
            transition: transform 0.5s;
        }
        .about-img:hover img {
            transform: scale(1.05);
        }

        /* Signature Dishes */
        .dishes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 30px;
        }
        .dish-card {
            background: #111;
            border-radius: 20px;
            overflow: hidden;
            transition: 0.4s;
            border: 1px solid #222;
        }
        .dish-card:hover {
            transform: translateY(-10px);
            border-color: #C5A059;
            box-shadow: 0 20px 30px rgba(0,0,0,0.5);
        }
        .dish-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .dish-info {
            padding: 25px;
        }
        .dish-info h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #C5A059;
        }
        .dish-info p {
            color: #bbb;
            line-height: 1.5;
        }

        /* Gallery Preview with Lightbox */
        .gallery-preview {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .gallery-item {
            flex: 1;
            min-width: 250px;
            height: 300px;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }
        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 20px;
            transform: translateY(100%);
            transition: 0.3s;
        }
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
        }
        .gallery-overlay h4 {
            color: #C5A059;
        }

        /* Testimonials Swiper */
        .testimonials {
            background: #0f0f0f;
            border-radius: 30px;
            padding: 60px 40px;
            margin-top: 60px;
        }
        .swiper {
            width: 100%;
            padding-bottom: 50px;
        }
        .testimonial-card {
            background: #1a1a1a;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid #222;
        }
        .testimonial-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 2px solid #C5A059;
        }
        .testimonial-card p {
            font-style: italic;
            color: #ccc;
            margin-bottom: 20px;
        }
        .testimonial-card h4 {
            color: #C5A059;
        }
        .testimonial-card span {
            font-size: 0.8rem;
            color: #888;
        }
        .swiper-pagination-bullet {
            background: #C5A059 !important;
        }

        /* Reservation CTA with Parallax */
        .reservation-cta {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('restro_img/home5.jpg') center/cover fixed;
            padding: 100px 20px;
            text-align: center;
            margin: 60px 0;
        }
        .reservation-cta h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .reservation-cta p {
            max-width: 600px;
            margin: 0 auto 30px;
            color: #ddd;
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
        .footer-col p {
            color: #888;
            line-height: 1.6;
        }
        .footer-col ul {
            list-style: none;
        }
        .footer-col ul li {
            margin-bottom: 12px;
        }
        .footer-col ul li a {
            color: #888;
            text-decoration: none;
            transition: 0.3s;
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
            transform: translateY(-3px);
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
            .about-grid { flex-direction: column; text-align: center; }
            .about-stats { justify-content: center; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="home.php" class="logo">SULEMAN<span>RESTRO</span></a>
        <ul class="nav-links">
            <li><a href="home.php" class="active">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
        <div class="user-greeting">
            <?php if ($logged_in): ?>
                <span><i class="fas fa-crown"></i> Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="sign.php">Sign In</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero with Video Background -->
<section class="hero">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="restro_img/hero-video.mp4" type="video/mp4">
        <!-- Fallback image if video fails -->
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Welcome to <span>Suleman Restro</span></h1>
        <p>Experience the pinnacle of 5-starhospitality • Where every moment becomes a cherished memory</p>
        <a href="menu.php" class="btn-gold">Discover Our Menu <i class="fas fa-arrow-right"></i></a>
    </div>
</section>

<!-- About Section -->
<section class="section">
    <h2 class="section-title">Our Legacy</h2>
    <div class="section-sub">A story of passion, tradition, and culinary mastery</div>
    <div class="about-grid">
        <div class="about-text">
            <p>Since 1985, Suleman Restro has been the epitome of royal dining, blending timeless recipes with contemporary elegance. Every dish tells a tale of heritage, crafted by master chefs who pour their soul into each creation.</p>
            <p>Our 11-star commitment means we go beyond expectations — from the finest ingredients to impeccable service, we ensure your experience is nothing short of extraordinary.</p>
            <div class="about-stats">
                <div class="stat-item">
                    <div class="stat-number" data-target="40">0</div>
                    <div class="stat-label">Years of Excellence</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="150">0</div>
                    <div class="stat-label">Signature Dishes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="50000">0</div>
                    <div class="stat-label">Happy Guests</div>
                </div>
            </div>
        </div>
        <div class="about-img">
            <img src="restro_img/home1.jpg" alt="Restaurant Interior">
        </div>
    </div>
</section>

<!-- Signature Dishes -->
<section class="section" style="background: #0f0f0f; border-radius: 40px;">
    <h2 class="section-title">Signature Creations</h2>
    <div class="section-sub">Award‑winning dishes that define our kitchen</div>
    <div class="dishes-grid">
        <div class="dish-card">
            <img src="restro_img/home3.jpg" alt="Hyderabadi Biryani">
            <div class="dish-info">
                <h3>Hyderabadi Dum Biryani</h3>
                <p>Aromatic basmati rice layered with slow‑cooked meat, infused with saffron and secret spices.</p>
            </div>
        </div>
        <div class="dish-card">
            <img src="restro_img/home6.jpg" alt="Galouti Kebab">
            <div class="dish-info">
                <h3>Galouti Kebab</h3>
                <p>Melt‑in‑mouth minced meat kebabs with over 100 spices, a royal Awadhi delicacy.</p>
            </div>
        </div>
        <div class="dish-card">
            <img src="restro_img/ShahiTukda.jpg" alt="Shahi Tukda">
            <div class="dish-info">
                <h3>Shahi Tukda</h3>
                <p>Fried bread soaked in rich rabri, topped with pistachios and silver leaf.</p>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Preview with Lightbox (simple JS alert for demo) -->
<section class="section">
    <h2 class="section-title">Visual Poetry</h2>
    <div class="section-sub">A glimpse of our ambiance and artistry</div>
    <div class="gallery-preview">
        <div class="gallery-item" onclick="alert('Full gallery coming soon!')">
            <img src="restro_img/home7.jpg" alt="Elegant Dining">
            <div class="gallery-overlay">
                <h4>Elegant Dining Hall</h4>
            </div>
        </div>
        <div class="gallery-item" onclick="alert('Full gallery coming soon!')">
            <img src="restro_img/home2.jpg" alt="Impeccable Service">
            <div class="gallery-overlay">
                <h4>Impeccable Service</h4>
            </div>
        </div>
        <div class="gallery-item" onclick="alert('Full gallery coming soon!')">
            <img src="restro_img/home3.jpg" alt="Signature Biryani">
            <div class="gallery-overlay">
                <h4>Signature Biryani</h4>
            </div>
        </div>
    </div>
    <div style="text-align: center; margin-top: 40px;">
        <a href="gallery.php" class="btn-gold">Explore Full Gallery</a>
    </div>
</section>

<!-- Testimonials Carousel -->
<section class="section">
    <h2 class="section-title">Royal Testimonials</h2>
    <div class="section-sub">What our guests say</div>
    <div class="testimonials">
        <div class="swiper testimonials-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <img src="restro_img/avatar1.jpg" alt="Guest">
                        <p>"Absolutely divine! The biryani transported me to Hyderabad. The service is impeccable, truly 5-star."</p>
                        <h4>Chetan Negi</h4>
                        <span>Food Critic</span>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <img src="restro_img/avatar2.jpg" alt="Guest">
                        <p>"A royal experience from start to finish. The ambiance, the flavors, the hospitality – unmatched."</p>
                        <h4>Ryaan Sabri</h4>
                        <span>Regular Guest</span>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <img src="restro_img/avatar3.jpg" alt="Guest">
                        <p>"The Galouti kebabs are legendary. I travel from Delhi just to dine here. Worth every mile."</p>
                        <h4>Amit Kumar</h4>
                        <span>Food Enthusiast</span>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- Reservation CTA -->
<section class="reservation-cta">
    <h2>Reserve Your Royal Table</h2>
    <p>Indulge in an unforgettable journey. Book your table now and let us create magic for you.</p>
    <a href="contact.php" class="btn-gold">Make a Reservation</a>
</section>

<!-- Footer -->
<footer>
    <div class="footer-content">
        <div class="footer-col">
            <h3>SULEMAN<span>RESTRO</span></h3>
            <p>Where heritage meets luxury. Experience the finest dining with 5-starhospitality.</p>
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
                <li><a href="about.html">About Us</a></li>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
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

    // Animated counters
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200;
    const animateCounters = () => {
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const increment = target / speed;
            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(animateCounters, 20);
            } else {
                counter.innerText = target;
            }
        });
    };
    // Start counters when in view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    });
    const statsContainer = document.querySelector('.about-stats');
    if (statsContainer) observer.observe(statsContainer);

    // Testimonials Swiper
    new Swiper('.testimonials-swiper', {
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        }
    });
</script>
</body>
</html>