<?php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to view this page!'); window.location='login.php';</script>";
    exit();
}




$user_id = $_SESSION['user_id'];
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $update_query = "UPDATE users SET 
        first_name = '$first_name', 
        last_name = '$last_name', 
        username = '$username',
        email = '$email', 
        phone = '$phone', 
        address = '$address' 
        WHERE u_id = '$user_id'";

 


    if (mysqli_query($conn, $update_query)) {
        echo "success";
        exit();
    } else {
        echo "error";
        echo "MySQL Error: " . mysqli_error($conn);

    }
}

// Fetch user data
$query = "SELECT * FROM users WHERE u_id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
        <h4 class="mb-4">User Details</h4>
        <form method="POST" id="userDetailsForm" action="">
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" >
                </div>
                <div class="col">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" >
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="number" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>

            <input type="submit" name="submit" value="Update Details" class="btn btn-primary">
            </form>
    
</div>
<script>
document.getElementById('userDetailsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('user_details.php', {
        method: 'POST',
        body: formData
        console.log(formData);
    })
    .then(res => res.text())
    .then(response => {
        if (response.trim() === "success") {
            alert('Details updated successfully');
        } else {
            alert('Error updating details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong');
    });
});
</script>

</body>
</html>
