<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SESSION['cart'])) {
    header("Location: order.php");
    exit;
}

// Get form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$notes = $_POST['notes'] ?? '';

if (empty($name) || empty($email) || empty($phone) || empty($address)) {
    header("Location: order.php?error=missing_fields");
    exit;
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "sulemanrestro");
if (!$conn) {
    die("Database connection failed");
}

// Insert order
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
$query = "INSERT INTO orders (user_email, customer_name, customer_email, customer_phone, customer_address, total_amount, notes) 
          VALUES ('$user_email', '$name', '$email', '$phone', '$address', '$total', '$notes')";
if (!mysqli_query($conn, $query)) {
    die("Error inserting order: " . mysqli_error($conn));
}
$order_id = mysqli_insert_id($conn);

// Insert order items
foreach ($_SESSION['cart'] as $item) {
    $menu_id = $item['id'];
    $qty = $item['quantity'];
    $price = $item['price'];
    $query2 = "INSERT INTO order_items (order_id, menu_item_id, quantity, price_at_time) 
               VALUES ($order_id, $menu_id, $qty, $price)";
    if (!mysqli_query($conn, $query2)) {
        // If one fails, we might want to rollback, but for simplicity, we'll show error
        die("Error inserting order item: " . mysqli_error($conn));
    }
}

mysqli_close($conn);

// Clear cart
unset($_SESSION['cart']);

// Redirect to success page
header("Location: order.php?success=order_placed");
exit;