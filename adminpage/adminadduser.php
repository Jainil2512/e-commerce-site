<?php
include '../db_connect.php';
include '../includes/header.php';
// session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $usertype = $_POST['usertype'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (first_name, last_name, email, username, phone, usertype, password, registration_date) 
            VALUES ('$first_name', '$last_name', '$email', '$username', '$phone', '$usertype', '$password', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('User added successfully!'); window.location='dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
    
</head>
<body>

<div class="adduser-container mt-5">
    <h2 class="text-center">Add New User</h2>
    <a href="dashboard.php" class="btn btn-primary mb-3">Back</a>

    <form method="POST">
        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>userName</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>User Type</label>
            <select name="usertype" class="form-control">
                <option value="admin">Admin</option>
                <option value="user">user</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success mb-3">Add User</button>
    </form>
</div>

</body>
</html>
<?php
include '../includes/footer.php';
?>