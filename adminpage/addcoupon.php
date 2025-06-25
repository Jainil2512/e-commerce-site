<?php
include '../db_connect.php'; 
include '../includes/header.php';

// session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'admin') {
    echo "<script>alert('Access Denied! Admins Only.'); window.location='shoppage.php';</script>";
    exit();
}
// Disable expired coupons
$now = date('Y-m-d H:i:s');
mysqli_query($conn, "UPDATE coupons SET status = 'inactive' WHERE expiry_date IS NOT NULL AND expiry_date <= '$now'");

// Handle coupon creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_coupon'])) {
    $name = $_POST['coupon_name'];
    $description = $_POST['coupon_desc'];
    $type = $_POST['coupon_type'];
    $limit = $_POST['time_limit'] ?? null;
    $max_users = $_POST['max_users'] ?? null;
    $discount_value = $_POST['discount_value'];
    $discount_type = $_POST['discount_type'];

    // Calculate expiration date if time-limited
    $expiry_date = null;

    if ($type === 'time-limited' && !empty($limit)) {
        $days = 0;
        $hours = 0;
    
        if (preg_match('/(\d+)\s*day/i', $limit, $matchDay)) {
            $days = (int)$matchDay[1];
        }
    
        if (preg_match('/(\d+)\s*hour/i', $limit, $matchHour)) {
            $hours = (int)$matchHour[1];
        }
    
        $interval = '';
        if ($days > 0) $interval .= "+$days days ";
        if ($hours > 0) $interval .= "+$hours hours";
    
        if (!empty($interval)) {
            date_default_timezone_set('Asia/Kolkata');
            $expiry_date = date('Y-m-d H:i:s', strtotime($interval));
        }
    }
    
    $sql = "INSERT INTO coupons (name, description, type, expiry_date, max_users, discount_value, discount_type, status)
            VALUES ('$name', '$description', '$type'," . ($expiry_date ? "'$expiry_date'" : "NULL") . ", '$max_users', '$discount_value', '$discount_type', 'active')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Coupon created successfully!');</script>";
    } else {
        echo "<script>alert('Error creating coupon');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Coupon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adcoupon.css"> 
</head>
<body>

<div class="container mt-5">
    
        <h4 class="text-center text-bold mb-3">Create New Coupon</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Coupon Name</label>
                <input type="text" class="form-control" name="coupon_name" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea class="form-control" name="coupon_desc" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Coupon Type</label>
                <select class="form-select" name="coupon_type" id="coupon_type" required>
                    <option value="time-limited">Time-Limited</option>
                    <option value="user-based">User-Based</option>
                    <option value="general">General</option>
                </select>
            </div>
            <div class="mb-3 time-limit">
                <label class="form-label fw-bold">Time Limit </label>
                <input type="text" class="form-control" name="time_limit" placeholder="e.g. 3 hours or 1 day 4 hours">
            </div>
            <div class="mb-3 user-limit">
                <label class="form-label fw-bold">Max Users</label>
                <input type="number" class="form-control" name="max_users">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Discount Value</label>
                <input type="number" step="0.01" class="form-control" name="discount_value" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Discount Type</label>
                <select class="form-select" name="discount_type" required>
                    <option value="percentage">Percentage (%)</option>
                    <option value="flat">Flat Amount ($)</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" name="create_coupon" class="btn btn-primary">Create Coupon</button>
                <a href="coupon.php" class="btn btn-primary ">Back to Coupons</a>
            </div>
        </form>
    
</div>

<script>
    // Hide/Show Time Limit and Max Users fields based on Coupon Type
    document.getElementById('coupon_type').addEventListener('change', function() {
        let timeLimitField = document.querySelector('.time-limit');
        let userLimitField = document.querySelector('.user-limit');
        timeLimitField.style.display = (this.value === 'time-limited') ? 'block' : 'none';
        userLimitField.style.display = (this.value === 'user-based') ? 'block' : 'none';
    });
</script>

</body>
</html>
