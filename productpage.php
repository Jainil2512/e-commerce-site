<?php
include 'db_connect.php'; // Include your database connection file
include './includes/header.php';

// session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to view this page!'); window.location='login.php';</script>";
    exit();
}

// Get product ID from URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details from database
    $query = "SELECT * FROM product WHERE product_id = $product_id";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['product_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 text-center">
            <img src="<?php echo $row['image']; ?>" class="img-fluid product-image" alt="Product Image">
        </div>
        <div class="col-md-6">
            <h2><?php echo $row['product_name']; ?></h2>
            <p><strong>Price:</strong> $<?php echo $row['price']; ?></p>
            <?php if ($row['stock'] > 0): ?>
          <p><strong>Stock Available:</strong> 
            <?php echo $row['stock']; ?> 
            <?php if ($row['stock'] <= 7 ): ?>
                <span class="text-warning fw-bold"> (Fast Selling)</span>
            <?php endif; ?>
          </p>
          <?php else: ?>
          <p class="text-danger fw-bold">Out of Stock</p>
          <?php endif; ?>
            <p><strong>Description:</strong> <?php echo $row['description']; ?></p>
            <a 
                 href="/fileupload/cart/checkout.php?product_id=<?php echo $row['product_id']; ?>" 
                 class="btn btn-success w-10 <?php echo ($row['stock'] <= 0) ? 'disabled' : ''; ?>"
                 <?php echo ($row['stock'] <= 0) ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                 Shop Now
            </a><br>           
                <form action="./cart/add_to_cart.php" method="GET">
               <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                <button type="submit" class="btn btn-primary mt-3"
                <?php echo ($row['stock'] <= 0) ? 'disabled' : ''; ?>>
                  Add to Cart</button>
            </form>

            <div class="mt-3">
                <a href="shoppage.php" class="btn btn-secondary mb-2">Back to shop</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php
    } else {
        echo "<p class='text-danger text-center'>Product not found!</p>";
    }
} else {
    echo "<p class='text-danger text-center'> please select a product!</p>";
    echo '<div class="text-center mt-1 mb-1">
           <a href="shoppage.php" class="btn btn-secondary btn-sm mt-3 text-center">Products</a>
           </div>';
}

include './includes/footer.php';
?>