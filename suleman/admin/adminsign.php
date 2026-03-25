<?php
session_start();

// Handle logout if requested
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: adminsign.php");
    exit;
}

// If already logged in, go to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: adminhome.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Connect to database
        $conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
        if (!$conn) {
            $error = "Database connection failed.";
        } else {
            // Fetch admin with matching username
            $username = mysqli_real_escape_string($conn, $username);
            $query = "SELECT id, username, name, password FROM adminsign WHERE username = '$username'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) == 1) {
                $admin = mysqli_fetch_assoc($result);
                // Direct comparison (no hashing – as per "no security")
                if ($password === $admin['password']) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_username'] = $admin['username'];

                    mysqli_close($conn);
                    header("Location: adminhome.php");
                    exit;
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "Username not found.";
            }
            mysqli_close($conn);
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Suleman Restro</title>
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
            background: radial-gradient(circle at 10% 30%, #0a0a0a, #000000);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.05"><path fill="none" d="M10,50 L90,50 M50,10 L50,90" stroke="%23C5A059" stroke-width="1"/><circle cx="50" cy="50" r="12" stroke="%23C5A059" fill="none"/></svg>');
            background-size: 40px;
            pointer-events: none;
        }
        .login-container {
            background: rgba(17, 17, 17, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(197, 160, 89, 0.3);
            border-radius: 24px;
            padding: 45px 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.8);
            transition: transform 0.3s;
        }
        .login-container:hover {
            transform: translateY(-5px);
            border-color: rgba(197, 160, 89, 0.6);
        }
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        .login-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #fff, #C5A059);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .login-header p {
            color: #aaa;
            font-size: 0.85rem;
            margin-top: 8px;
        }
        .form-group {
            margin-bottom: 22px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #C5A059;
        }
        .form-group input {
            width: 100%;
            background: #222;
            border: 1px solid #333;
            padding: 12px 16px;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #C5A059;
            box-shadow: 0 0 0 2px rgba(197,160,89,0.2);
            background: #2a2a2a;
        }
        button {
            width: 100%;
            background: linear-gradient(135deg, #C5A059, #b38f40);
            border: none;
            padding: 12px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 8px;
            color: #0a0a0a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        button:hover {
            background: linear-gradient(135deg, #d6b15c, #c59a44);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(197,160,89,0.3);
        }
        .error-message {
            background: rgba(255,0,0,0.1);
            border: 1px solid #ff4444;
            color: #ff8888;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 0.85rem;
        }
        .back-link, .forgot-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a, .forgot-link a {
            color: #C5A059;
            text-decoration: none;
            font-size: 0.8rem;
            transition: 0.2s;
        }
        .back-link a:hover, .forgot-link a:hover {
            color: #fff;
            text-decoration: underline;
        }
        .forgot-link {
            margin-top: 10px;
        }
        hr {
            margin: 25px 0 15px;
            border: 0;
            height: 1px;
            background: #222;
        }
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <h1>Admin Access</h1>
        <p>Suleman Restro – 5-starManagement</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login <i class="fas fa-arrow-right"></i></button>
    </form>

    <div class="forgot-link">
        <a href="#" onclick="alert('Contact the main administrator to reset your password.'); return false;"><i class="fas fa-key"></i> Forgot Password?</a>
    </div>

    <hr>

    <div class="back-link">
        <a href="../index.php"><i class="fas fa-home"></i> Return to Website</a>
    </div>
</div>
</body>
</html>