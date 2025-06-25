<?php
    include '../db_connect.php'; // Include your database connection file
    include '../includes/header.php';
    
    // session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'admin') {
        echo "<script>alert('Access Denied! Admins Only.'); window.location='shoppage.php';</script>";
        exit();
    }

    // Handle Delete Request
    if (isset($_GET['delete'])) {
    $orderIdToDelete = intval($_GET['delete']); // sanitize input

    // Prepare and execute the DELETE query
    $deleteQuery = "DELETE FROM orders WHERE order_id = $orderIdToDelete";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('Order deleted successfully.'); window.location='orderpage.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to delete order.');</script>";
    }
   }

     // --- Pagination Setup ---
     $limit = 5; // Orders per page
     $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
     if ($page < 1) $page = 1;

     $offset = ($page - 1) * $limit;

     // Get total records
     $total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");    
     $total_row = mysqli_fetch_assoc($total_result);
     $total_orders = $total_row['total'];
     $total_pages = ceil($total_orders / $limit);

     // Fetch paginated results
     $result = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

    // Fetch all orders
    // $result = mysqli_query($conn, "SELECT * FROM orders");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>order page </title>
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
</head>
<body>
    <div class="container mt-3">
    <h2  class="text-center mt-2 mb-0">MANAGE ORDER</h2>

    <!-- <h2>Orders List</h2> -->
    <a href="create_order.php" class="btn btn-primary mb-3">Add New Order</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Subtotal</th>
                <th>Tax</th>
                <th>Discount</th>
                <th>Final Total</th>
                <th>Payment Type</th>
                <th>Status</th>
                <th>Card Number</th>
                <th>Expiry</th>
                <th>CVV</th>
                <th>UPI ID</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['order_id'] ?></td>
                <td><?= $row['u_id'] ?></td>
                <td><?= $row['subtotal'] ?></td>
                <td><?= $row['tax'] ?></td>
                <td><?= $row['discount'] ?></td>
                <td><?= $row['final_total'] ?></td>
                <td><?= $row['payment_type'] ?></td>
                <td><?= $row['order_status'] ?></td>
                <td><?= $row['card_number'] ?></td>
                <td><?= $row['expiry_date'] ?></td>
                <td><?= $row['cvv'] ?></td>
                <td><?= $row['upi_id'] ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="editorder.php?order_id=<?= $row['order_id'] ?>" class="btn btn-sm btn-warning m-1">Edit</a>
                    <a href="orderpage.php?delete=<?= $row['order_id'] ?>" class="btn btn-sm btn-danger m-1" onclick="return confirm('Are you sure to delete this order?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <div class="d-flex justify-content-center my-4">
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

</div>
</body>
</html>
<?php
include '../includes/footer.php';
?>