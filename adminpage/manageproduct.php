<?php
include '../db_connect.php'; // Database connection
include '../includes/header.php'; // Header file
// var_dump($_SESSION);
// session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'admin') {
    echo "<script>alert('Access Denied! Admins Only.'); window.location='shoppage.php';</script>";
    exit();
}

// Handle DELETE Request
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $query = "DELETE FROM product WHERE product_id = $product_id";
    mysqli_query($conn, $query);
    header("Location: manageproduct.php"); // Refresh page
}

// ** Pagination Settings **
$records_per_page = 4; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of users
$total_records_query = "SELECT COUNT(*) AS total FROM users";
$total_records_result = $conn->query($total_records_query);
$total_records = $total_records_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);


// Fetch Products
$query = "SELECT * FROM product LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
    
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Products</h2>
    <div class="d-flex mb-3">
        <a href="../adminpage/addnewproduct.php" class="btn btn-primary mb-3">Add New Product</a>
        <a href="../uploadfile.php" class="btn btn-primary mx-3 mb-3">Add bulk Product</a>
    </div>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Description</th>
                <th>Image</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['product_id']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td>$<?php echo $row['price']; ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                        <img src="/FILEUPLOAD/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" width="110" height="150" style="object-fit: cover; border-radius: 5px;">
                    </td>
                    <td><?php echo ucfirst($row['status']); ?></td>
                    <td>
                        <a href="editproduct.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning btn-sm d-inline-block mb-1">Edit</a>
                        <a href="?delete=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm d-inline-block" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <!-- Pagination Links -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next&raquo;</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

</body>
</html>

<?php include '../includes/footer.php'; ?>