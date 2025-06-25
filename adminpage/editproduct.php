<?php
include '../db_connect.php';
include '../includes/header.php'; // Header file

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $query = "SELECT * FROM product WHERE product_id = $product_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
}

if (isset($_POST['update'])) {
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = $_POST['description'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE product SET product_name='$name', price='$price', stock='$stock', description='$desc', status='$status' WHERE product_id=$product_id";
    mysqli_query($conn, $updateQuery);
    header("Location: manageproduct.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Product</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="product_name" class="form-control" value="<?php echo $row['product_name']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control" value="<?php echo $row['price']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" value="<?php echo $row['stock']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required><?php echo $row['description']; ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="approved" <?php if ($row['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
            </select>
        </div>
        <button type="submit" name="update" class="btn btn-success mb-3">Update Product</button>
        <a href="manageproduct.php" class="btn btn-secondary mb-3">Cancel</a>
    </form>
</div>

</body>
</html>
<?php include '../includes/footer.php'; ?>