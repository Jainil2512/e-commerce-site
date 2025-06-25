<?php
include 'db_connect.php'; // Include your database connection file
include './includes/header.php';

$query = "SELECT * FROM product  WHERE status = 'approved'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./includes/styles.css"> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-4">
     <h2 class="text-center mb-4">Shop here</h2>  
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <a href="productpage.php?id=<?php echo $row['product_id']; ?>">
                        <img src="<?php echo $row['image']; ?>" class="card-img-top" alt="Product Image">
                    </a>
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <a href="productpage.php?id=<?php echo $row['product_id']; ?>" class="text-decoration-none">
                                <?php echo $row['product_name']; ?>
                            </a>
                        </h5>
                        <p class="card-text">
                            <a href="productpage.php?id=<?php echo $row['product_id']; ?>" class="text-decoration-none fw-bold text-primary">
                               $<?php echo number_format($row['price'], 2); ?>
                            </a>
                        </p>
                        <form action="./cart/add_to_cart.php" method="GET">
                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                            <button  type="submit" class="add-to-cart"
                            <?php echo ($row['stock'] <= 0) ? 'disabled' : ''; ?>>
                            Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>

<?php include './includes/footer.php'; ?>
