<?php
session_start();

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_query = "SELECT COUNT(*) AS cart_count FROM cart WHERE u_id = $user_id";
    $cart_result = mysqli_query($conn, $cart_query);
    $cart_data = mysqli_fetch_assoc($cart_result);
    $cart_count = $cart_data['cart_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="nav-container">
        <a class="navbar-brand" href="shoppage.php">E-commerce site</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="/FILEUPLOAD/productpage.php">Shop Products</a>
                    </li> -->

                    <!-- If Admin is logged in -->
                    <?php if ($_SESSION['usertype'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/FILEUPLOAD/adminpage/dashboard.php"> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/FILEUPLOAD/adminpage/manageproduct.php">Manage Products</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/FILEUPLOAD/uploadfile.php">Upload File</a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link" href="/FILEUPLOAD/adminpage/coupon.php">Coupons</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/FILEUPLOAD/adminpage/orderpage.php"> Order</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                    <a class="nav-link" href="/FILEUPLOAD/shoppage.php">Shop </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/FILEUPLOAD/cart/cart.php">
                        🛒 Cart 
                        <?php if ($cart_count > 0): ?>
                     <span class="badge start-100 translate-middle">
                        <?php echo $cart_count; ?>
                      </span>
                       <?php endif; ?>
                     </a>
                     </li>

                     <li class="nav-item">
                     <a class="nav-link" href="/FILEUPLOAD/profiles/profile.php">Profile</a>
                    </li>
                    <!-- Show Logout for Logged-in Users -->
                    <li class="nav-item">
                        <a class="nav-link" href="/FILEUPLOAD/logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <!-- Show Login & Register for Non-logged-in Users -->
                    <li class="nav-item">
                        <a class="nav-link" href="/FILEUPLOAD/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/FILEUPLOAD/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
