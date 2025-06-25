<?php
include '../db_connect.php'; // Include your database connection file
include '../includes/header.php';

// session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'admin') {
    echo "<script>alert('Access Denied! Admins Only.'); window.location='shoppage.php';</script>";
    exit();
}

// DELETE USER
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM users WHERE u_id=$delete_id");
    echo "<script>alert('User deleted successfully!'); window.location='admindashboard.php';</script>";
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 

</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Admin Dashboard</h2>
    <a href="adminadduser.php" class="btn btn-primary mb-3">Add New User</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Username</th>
                <th>User Type</th>
                <th>Registration date</th>
                <th>last-logged-in</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM users LIMIT $offset, $records_per_page";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['u_id']}</td>
                        <td>{$row['first_name']}</td>
                        <td>{$row['last_name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['usertype']}</td>
                        <td>{$row['registration_date']}</td>
                        <td>{$row['last_loggedin_time']}</td>
                        <td>
                            <a href='edituser.php?id={$row['u_id']}' class='btn btn-warning btn-sm d-inline-block mb-1'>Edit</a>
                            <a href='admindashboard.php?delete_id={$row['u_id']}' class='btn btn-danger btn-sm d-inline-block' onclick='return confirm(\"Are you sure?\")'>Delete</a>

                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No users found</td></tr>";
            }
            ?>
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
<?php
include '../includes/footer.php';
?>