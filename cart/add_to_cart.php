<?php
include '../db_connect.php';
include '../includes/header.php';
// var_dump($_SESSION);
// session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_GET['product_id'];

// print_r($_GET); 
// exit();

// Check if the product is already in the cart
$check_query = "SELECT * FROM cart WHERE u_id = $user_id AND product_id = $product_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // If product already exists, update quantity
    $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE u_id = $user_id AND product_id = $product_id";
    mysqli_query($conn, $update_query);
} else {
    // If not in cart, insert new record
    $insert_query = "INSERT INTO cart (u_id, product_id) VALUES ($user_id, $product_id)";
    mysqli_query($conn, $insert_query);
}

header("Location: /FILEUPLOAD/shoppage.php");
?>
