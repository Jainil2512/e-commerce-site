<?php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location='login.php';</script>";
    exit();
}

$cart_id = $_GET['cart_id'];
$delete_query = "DELETE FROM cart WHERE cart_id = $cart_id";
mysqli_query($conn, $delete_query);

header("Location: /FILEUPLOAD/cart/cart.php");
?>
