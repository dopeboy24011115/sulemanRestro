<?php
session_start();
$logged_in = isset($_SESSION['user_email']);
$user_name = $logged_in ? explode('@', $_SESSION['user_email'])[0] : '';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$message = "";
$message_class = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $msg = $_POST['message'];

    $mail = new PHPMailer(true);
    try {
        // SMTP configuration (same as used in signup)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sulemanrestro@gmail.com';
        $mail->Password = 'cthm gdrw dfjo uihw'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom($email, $name);
        $mail->addAddress('sulemanrestro@gmail.com', 'Suleman Restro');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Contact Form: " . $subject;
        $mail->Body = "
            <h3>New message from Suleman Restro website</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($msg)) . "</p>
        ";
        $mail->send();

        $message = "✅ Thank you! Your message has been sent. We'll get back to you soon.";
        $message_class = "success";
    } catch (Exception $e) {
        $message = "❌ Sorry, something went wrong. Please try again later. (Error: {$mail->ErrorInfo})";
        $message_class = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Suleman Restro – 11-Star Hospitality</title>
    
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
        
        /* Navbar (consistent) */
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
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('restro_img/contact-hero.jpg') center/cover no-repeat;
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
        
        /* Contact Section */
        .contact-section {
            padding: 80px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }
        @media (max-width: 992px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Form Styling */
        .contact-form {
            background: #111;
            border: 1px solid #222;
            border-radius: 20px;
            padding: 40px;
        }
        .contact-form h2 {
            font-family: 'Playfair Display', serif;
            color: #C5A059;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #aaa;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            background: #222;
            border: 1px solid #333;
            padding: 12px 15px;
            border-radius: 10px;
            color: #fff;
            font-family: inherit;
            transition: 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #C5A059;
        }
        button {
            background: #C5A059;
            color: #000;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #fff;
            transform: translateY(-2px);
        }
        
        /* Info Cards */
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        .info-card {
            background: #111;
            border: 1px solid #222;
            border-radius: 20px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: 0.3s;
        }
        .info-card:hover {
            border-color: #C5A059;
        }
        .info-icon {
            width: 60px;
            height: 60px;
            background: #1a1a1a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #C5A059;
        }
        .info-text h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: #C5A059;
        }
        .info-text p {
            color: #ccc;
            line-height: 1.4;
        }
        
        /* Map */
        .map-container {
            margin-top: 60px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #222;
        }
        .map-container iframe {
            width: 100%;
            height: 400px;
            border: 0;
        }
        
        /* Alert Messages */
        .alert {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
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
        
        /* Footer (same as previous) */
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
            .contact-form { padding: 25px; }
            .info-card { padding: 20px; }
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
            <li><a href="menu.php">Menu</a></li>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="contact.php" class="active">Contact Us</a></li>
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

<section class="hero">
    <div class="hero-content">
        <h1>Get in <span>Touch</span></h1>
        <p>We're here to make your experience royal</p>
    </div>
</section>

<section class="contact-section">
    <div class="contact-grid">
        <!-- Contact Form -->
        <div class="contact-form">
            <h2>Send a Message</h2>
            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" required></textarea>
                </div>
                <button type="submit" name="send">Send Message <i class="fas fa-paper-plane"></i></button>
            </form>
        </div>

        <!-- Contact Information -->
        <div class="contact-info">
            <div class="info-card">
                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="info-text">
                    <h3>Visit Us</h3>
                    <p>5 Star Avenue, Bandra Kurla Complex<br>Mumbai, Maharashtra 400051, India</p>
                </div>
            </div>
            <div class="info-card">
                <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                <div class="info-text">
                    <h3>Call Us</h3>
                    <p>+91 98765 43210<br>+91 22 1234 5678</p>
                </div>
            </div>
            <div class="info-card">
                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                <div class="info-text">
                    <h3>Email</h3>
                    <p>sulemanrestro@gmail.com<br>reservations@sulemanrestro.com</p>
                </div>
            </div>
            <div class="info-card">
                <div class="info-icon"><i class="fas fa-clock"></i></div>
                <div class="info-text">
                    <h3>Opening Hours</h3>
                    <p>Monday – Friday: 12:00 – 23:00<br>Saturday – Sunday: 11:00 – 00:00</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Map Embed (replace with your actual location) -->
    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d241317.08978828318!2d72.74109922695313!3d19.08252278259549!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c7f1c6f8e9a5%3A0x3b3b3b3b3b3b3b3b!2sMumbai%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1698765432100!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

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