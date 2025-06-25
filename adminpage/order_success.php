<?php
include '../db_connect.php';
include '../includes/header.php';

if (!isset($_GET['order_id'])) {
    echo "Invalid Order!";
    exit;
}

$order_id = $_GET['order_id'];

$order_query = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = '$order_id'");
$order = mysqli_fetch_assoc($order_query);

if (!$order) {
    echo "Order not found!";
    exit;
}
?>

<div class="container mt-5">
    <div class="alert alert-success">
        <h4>🎉 Order Placed Successfully!</h4>
        <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
        <p><strong>Status:</strong> <?= ucfirst($order['order_status']) ?></p>
        <p><strong>Payment Type:</strong> <?= strtoupper($order['payment_type']) ?></p>
        <p><strong>Total:</strong> $<?= $order['final_total'] ?></p>
        <a href="orderpage.php" class="btn btn-primary mt-3">Go to Orders</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
