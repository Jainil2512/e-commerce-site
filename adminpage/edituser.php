<?php
include '../db_connect.php'; // Database connection
include '../includes/header.php';
session_start();

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid user ID'); window.location='admindashboard.php';</script>";
    exit;
}

$user_id = $_GET['id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE u_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<script>alert('User not found'); window.location='admindashboard.php';</script>";
    exit;
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $usertype = $_POST['usertype'];

    $update_sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', phone='$phone', username='$username', usertype='$usertype' WHERE u_id=$user_id";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('User updated successfully!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating user');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Edit User</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" name="first_name" value="<?php echo $user['first_name']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" name="last_name" value="<?php echo $user['last_name']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?php echo $user['phone']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">username</label>
            <input type="text" class="form-control" name="username" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">User Type</label>
            <select class="form-select" name="usertype" required>
                <option value="admin" <?php if ($user['usertype'] == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="user" <?php if ($user['usertype'] == 'user') echo 'selected'; ?>>User</option>
            </select>
        </div>
        <button type="submit" name="submit" class="btn btn-success mb-2">Update User</button>
        <a href="dashboard.php" class="btn btn-secondary mb-2">Cancel</a>
    </form>
</div>
</body>
</html>

<?php include '../includes/footer.php'; ?>