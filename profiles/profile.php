<?php 
include '../db_connect.php'; // Include your database connection file
include '../includes/header.php';
 
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to view this page!'); window.location='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
<div class="container mt-5">
    <h2>My Profile</h2>
    <ul class="nav nav-tabs" id="profileTabs">
        <li class="nav-item">
            <a class="nav-link active" href="#" data-tab="user_details">User Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-tab="billing_details">Billing Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-tab="order_history">Order History</a>
        </li>
    </ul>

    <div class="tab-content border p-4 mt-3" id="profileContent">
        <!-- AJAX content loads here -->
    </div>
</div>

<script src="/fileupload/profiles/profile_tabs.js"></script>
</body>
</html>