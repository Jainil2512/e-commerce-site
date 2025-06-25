<?php
include '../db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location='../login.php';</script>";
    exit();
}
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST['place_order'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST["full_name"]);
    $user_id = $_SESSION['user_id'];
    $subtotal = isset($_POST['subtotal']) ? $_POST['subtotal'] : 0;
    $discount = isset($_POST['discount']) ? $_POST['discount'] : 0;
    $final_total = isset($_POST['final_total']) ? $_POST['final_total'] : 0;
    // $coupon_id = isset($_SESSION['applied_coupon']) ? "'" . mysqli_real_escape_string($conn, $_SESSION['applied_coupon']) . "'" : "NULL";
    $payment_type = mysqli_real_escape_string($conn, $_POST['payment_method']);

    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);


    $coupon_id = "NULL"; // Default to NULL if no coupon is applied
    if (isset($_SESSION['applied_coupon'])) {
    $coupon_name = mysqli_real_escape_string($conn, $_SESSION['applied_coupon']);
    // Fetch the coupon ID from the coupons table
    $coupon_query = "SELECT id FROM coupons WHERE name = '$coupon_name' LIMIT 1";
    $coupon_result = mysqli_query($conn, $coupon_query);
    if ($coupon_row = mysqli_fetch_assoc($coupon_result)) {
        $coupon_id = $coupon_row['id']; // Get the actual ID
    }
    }

    $card_number = $expiry_date = $cvv = $upi_id = "NULL";

    if ($payment_type === "card") {
        $card_number = !empty($_POST['card_number']) ? "'" . mysqli_real_escape_string($conn, $_POST['card_number']) . "'" : "NULL";
        $expiry_date = !empty($_POST['expiry_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['expiry_date']) . "'" : "NULL";
        $cvv = !empty($_POST['cvv']) ? "'" . mysqli_real_escape_string($conn, $_POST['cvv']) . "'" : "NULL";
    } elseif ($payment_type === "upi") {
        $upi_id = !empty($_POST['upi_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['upi_id']) . "'" : "NULL";
    }

    // Fixed SQL query with proper NULL handling
    $sql = "INSERT INTO orders (u_id, subtotal, discount, final_total, id, payment_type, card_number, expiry_date, cvv, upi_id, address, city, zipcode, order_status) 
            VALUES ('$user_id', '$subtotal', '$discount', '$final_total', $coupon_id, '$payment_type', $card_number, $expiry_date, $cvv, $upi_id, '$address', '$city', '$zip', 'pending')";

    if (mysqli_query($conn, $sql)) {
        echo "Order placed successfully!";
        //header("Location: order_success.php"); Redirect to success page
        // exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // mysqli_close($conn);
}

// Get the last inserted order ID
$sql_get_max_order = "SELECT MAX(order_id) AS max_order_id FROM orders";
$result_max_order = mysqli_query($conn, $sql_get_max_order);

if ($row = mysqli_fetch_assoc($result_max_order)) {
    $order_id = $row['max_order_id'];
    // echo "Latest Order ID: " . $order_id;
} else {
    echo "Error fetching latest order ID: " . mysqli_error($conn);
}


// Retrieve cart items for the logged-in user
$sql_cart = "SELECT product_id, quantity FROM cart WHERE u_id = '$user_id'";
$result_cart = mysqli_query($conn, $sql_cart);

if (mysqli_num_rows($result_cart) > 0) {
    while ($row = mysqli_fetch_assoc($result_cart)) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];

        // Insert each cart item into order_item_table
        $sql_order_item = "INSERT INTO order_item_table (order_id, product_id, quantity) 
                           VALUES ('$order_id', '$product_id', '$quantity')";
        mysqli_query($conn, $sql_order_item);
    }
}

// Clear cart after order is placed
mysqli_query($conn, "DELETE FROM cart WHERE u_id = '$user_id'");


//Fetch the order status for the recently placed order
$sql_status = "SELECT order_status FROM orders WHERE order_id = '$order_id' LIMIT 1";
$result_status = mysqli_query($conn, $sql_status);

$order_status = "Unknown"; // default fallback

if ($row_status = mysqli_fetch_assoc($result_status)) {
    $order_status = $row_status['order_status'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>order placed</title>
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
</head>
<body>
    <h2> ORDER STATUS</h2>
    <h3>Your order is currently: <strong><?php echo ucfirst($order_status); ?></strong></h3>
</body>
</html>