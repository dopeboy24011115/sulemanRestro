<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "sulemanrestro");  // database name updated

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$message = "";

// SIGNUP LOGIC
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $otp = rand(100000, 999999);

    $_SESSION['temp_otp'] = $otp;
    $_SESSION['temp_name'] = $name;
    $_SESSION['temp_email'] = $email;
    $_SESSION['temp_password'] = $password;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sulemanrestro@gmail.com';
        $mail->Password = 'cthm gdrw dfjo uihw'; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('sulemanrestro@gmail.com', 'Suleman Restro');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'OTP Verification - Suleman Restro';
        $mail->Body = "<h3>Welcome!</h3><p>Your OTP is: <b>$otp</b></p>";
        $mail->send();

        $message = "<div class='alert success'>✨ OTP sent to your email!</div>";
    } catch (Exception $e) {
        $message = "<div class='alert error'>❌ Mail Error. Please try again.</div>";
    }
}

// VERIFY OTP LOGIC
if (isset($_POST['verify'])) {
    $user_otp = $_POST['otp'];
    if ($user_otp == $_SESSION['temp_otp']) {
        $name = $_SESSION['temp_name'];
        $email = $_SESSION['temp_email'];
        $pass = $_SESSION['temp_password'];

        $query = "INSERT INTO signup (NAME, EMAIL, PASSWORD) VALUES ('$name', '$email', '$pass')";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert success'>🎉 Account Created! Please Login.</div>";
            // clear temp data
            unset($_SESSION['temp_otp'], $_SESSION['temp_name'], $_SESSION['temp_email'], $_SESSION['temp_password']);
        }
    } else {
        $message = "<div class='alert error'>🔒 Wrong OTP! Try again.</div>";
    }
}

// LOGIN LOGIC
if (isset($_POST['login'])) {
    $email = $_POST['loginemail'];
    $password = $_POST['loginpassword'];

    $query = "SELECT * FROM signup WHERE EMAIL='$email' AND PASSWORD='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user_email'] = $email;
        header("Location: home.php");
        exit();
    } else {
        $message = "<div class='alert error'>❌ Invalid Credentials!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suleman Restro | 11-Star Access</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at 20% 30%, #0a0a0a, #000000);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
        }

        /* luxury overlay pattern */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.05"><path fill="none" d="M10,50 L90,50 M50,10 L50,90" stroke="%23C5A059" stroke-width="1"/><circle cx="50" cy="50" r="12" stroke="%23C5A059" fill="none"/></svg>');
            background-size: 40px;
            pointer-events: none;
        }

        .container {
            width: 1100px;
            max-width: 100%;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            letter-spacing: 4px;
            background: linear-gradient(135deg, #fff, #C5A059);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .logo span {
            font-size: 0.9rem;
            color: #C5A059;
            letter-spacing: 2px;
            display: block;
        }

        .card {
            background: rgba(20, 20, 20, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 30px;
            border: 1px solid rgba(197, 160, 89, 0.3);
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            transition: transform 0.3s ease;
        }

        .auth-wrapper {
            display: flex;
            flex-wrap: wrap;
        }

        .auth-column {
            flex: 1;
            padding: 2.5rem;
            min-width: 280px;
        }

        .signup-side {
            background: rgba(0,0,0,0.4);
            border-right: 1px solid rgba(197, 160, 89, 0.2);
        }

        h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1.8rem;
            color: #C5A059;
            position: relative;
            display: inline-block;
        }

        h2:after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 2px;
            background: #C5A059;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #aaa;
            margin-bottom: 0.5rem;
        }

        input {
            width: 100%;
            background: rgba(30,30,30,0.8);
            border: 1px solid #333;
            padding: 0.9rem 1rem;
            border-radius: 16px;
            color: white;
            font-size: 0.9rem;
            transition: all 0.2s;
            outline: none;
        }

        input:focus {
            border-color: #C5A059;
            box-shadow: 0 0 0 2px rgba(197,160,89,0.2);
            background: #1e1e1e;
        }

        button, input[type="submit"] {
            background: linear-gradient(135deg, #C5A059, #b38f40);
            border: none;
            padding: 0.9rem;
            border-radius: 40px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0a0a0a;
            cursor: pointer;
            width: 100%;
            font-size: 0.9rem;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }

        button:hover, input[type="submit"]:hover {
            background: linear-gradient(135deg, #d6b15c, #c59a44);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -8px rgba(197,160,89,0.4);
        }

        .otp-box {
            margin-top: 1.8rem;
            padding: 1.2rem;
            border: 1px dashed rgba(197,160,89,0.5);
            border-radius: 20px;
            background: rgba(0,0,0,0.3);
        }

        .alert {
            padding: 0.8rem 1rem;
            border-radius: 50px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            text-align: center;
            backdrop-filter: blur(4px);
        }

        .alert.success {
            background: rgba(0,128,0,0.2);
            border: 1px solid #C5A059;
            color: #C5A059;
        }

        .alert.error {
            background: rgba(255,0,0,0.1);
            border: 1px solid #ff4444;
            color: #ff8888;
        }

        @media (max-width: 800px) {
            .auth-wrapper { flex-direction: column; }
            .signup-side { border-right: none; border-bottom: 1px solid rgba(197,160,89,0.2); }
            body { padding: 1rem; }
            .auth-column { padding: 1.5rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">
        <h1>SULEMAN<span>RESTRO</span></h1>
        <span>✦ 11-Star Gastronomy ✦</span>
    </div>

    <div class="card">
        <?php if (!empty($message)) echo $message; ?>

        <div class="auth-wrapper">
            <!-- SIGNUP SIDE -->
            <div class="auth-column signup-side">
                <h2>Join the Legacy</h2>
                <form method="POST">
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="name" required placeholder="John Carter">
                    </div>
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="hello@example.com">
                    </div>
                    <div class="input-group">
                        <label>Create Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <input type="submit" name="signup" value="Send OTP">
                </form>

                <?php if(isset($_SESSION['temp_otp'])): ?>
                <div class="otp-box">
                    <form method="POST">
                        <div class="input-group">
                            <label style="color: #C5A059;">6‑Digit OTP</label>
                            <input type="text" name="otp" placeholder="123456" required>
                        </div>
                        <input type="submit" name="verify" value="Verify & Register">
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <!-- LOGIN SIDE -->
            <div class="auth-column">
                <h2>Welcome Back</h2>
                <form method="POST">
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="loginemail" required placeholder="Registered Email">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="loginpassword" required placeholder="••••••••">
                    </div>
                    <input type="submit" name="login" value="Sign In">
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>