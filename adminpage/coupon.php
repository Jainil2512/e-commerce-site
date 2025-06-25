<?php
include '../db_connect.php'; // Include your database connection file
include '../includes/header.php';

// session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'admin') {
    echo "<script>alert('Access Denied! Admins Only.'); window.location='shoppage.php';</script>";
    exit();
}

// Handle activation/deactivation
if (isset($_GET['toggle_id'])) {
    $coupon_id = $_GET['toggle_id'];
    $current_status = $_GET['status'];
    $new_status = ($current_status == 'active') ? 'inactive' : 'active';
    
    $update_sql = "UPDATE coupons SET status='$new_status' WHERE id=$coupon_id";
    mysqli_query($conn, $update_sql);
    
    echo "<script>window.location='coupon.php';</script>";
}

// Fetch coupons
$result = mysqli_query($conn, "SELECT * FROM coupons");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coupons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 

</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Manage Coupons</h2>
    <a href="addcoupon.php" class="btn btn-primary  p-2">Add New coupons</a>

    <!-- Coupons Table -->
    <table class="table table-bordered-none">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Type</th>
                <th>Expiry Date</th>
                <th>Discount value</th>
                <th>Discount type</th>
                <th>Max Users</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><?= ucfirst($row['type']) ?></td>
                    <td><?= $row['expiry_date'] ?? 'N/A' ?></td>
                    <td><?= $row['discount_value'] ?></td>
                    <td><?= ucfirst($row['discount_type']) ?></td>
                    <td><?= $row['max_users'] ?? 'N/A' ?></td>
                    <td>
                        <span class="badge bg-<?= $row['status'] == 'active' ? 'success' : 'danger' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="coupon.php?toggle_id=<?= $row['id'] ?>&status=<?= $row['status'] ?>" 
                           class="btn btn-<?= $row['status'] == 'active' ? 'danger' : 'success' ?> btn-sm">
                           <?= $row['status'] == 'active' ? 'Deactivate' : 'Activate' ?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    document.getElementById('coupon_type').addEventListener('change', function() {
        document.querySelector('.time-limit').style.display = this.value === 'time-limited' ? 'block' : 'none';
        document.querySelector('.user-limit').style.display = this.value === 'user-based' ? 'block' : 'none';
    });
</script>
</body>
</html>

<?php include '../includes/footer.php'; ?>
