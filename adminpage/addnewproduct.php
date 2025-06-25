<?php
include '../db_connect.php';
include '../includes/header.php'; 

$imageDir = '../images/'; // folder to store images
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0777, true); // Create folder if it doesn't exist
}

if (isset($_POST['add'])) {
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = $_POST['description'];
    $status = $_POST['status'];


   // Handle image upload
   $imageName = basename($_FILES['image']['name']);
   $imagePath = $imageDir . $imageName;
   $imageRelativePath = 'images/' . $imageName; // path to store in DB

   if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
       $insertQuery = "INSERT INTO product (product_name, price, stock, description, image, status)
                       VALUES ('$name', '$price', '$stock', '$desc', '$imageRelativePath', '$status')";

       if (mysqli_query($conn, $insertQuery)) {
           echo "<script>alert('Product added successfully!'); window.location='manageproduct.php';</script>";
       } else {
           echo "Database error: " . mysqli_error($conn);
       }
   } else {
       echo "Image upload failed!";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Add New Product</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="product_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
            </select>
        </div>
        <button type="submit" name="add" class="btn btn-primary mb-3">Add Product</button>
        <a href="manageproduct.php" class="btn btn-secondary mb-3">Cancel</a>
    </form>
</div>

</body>
</html>

<?php include '../includes/footer.php'; ?>
