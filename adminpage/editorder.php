<?php
include '../db_connect.php';
include '../includes/header.php';

if (!isset($_GET['order_id'])) {
    echo "<script>alert('Order ID is missing.'); window.location='orderpage.php';</script>";
    exit();
}

$order_id = intval($_GET['order_id']);
$result = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = $order_id");
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "<script>alert('Order not found.'); window.location='orderpage.php';</script>";
    exit();
}

 // Get current coupon info if applied
 $current_coupon = null;
 if (!empty($order['id'])) {
     $coupon_id = $order['id'];  // 'id' is used to store coupon_id
     $coupon_result = mysqli_query($conn, "SELECT * FROM coupons WHERE id = $coupon_id");
     $current_coupon = mysqli_fetch_assoc($coupon_result);
 }
 
 // Fetch all available coupons
 $all_coupons = mysqli_query($conn, "SELECT * FROM coupons WHERE status = 'Active'");
 
// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $u_id = $_POST['u_id'];
    $subtotal = floatval($_POST['subtotal']);
    $tax = floatval($_POST['tax']);
    $payment_type = $_POST['payment_type'];
    $order_status = $_POST['order_status'];

    $discount = 0;
    $final_total = $subtotal + $tax;

    // Handle coupon application/removal
    if (!empty($_POST['id'])) {
        $coupon_id = intval($_POST['id']);
        
        // Fetch selected coupon details
        $coupon_result = mysqli_query($conn, "SELECT * FROM coupons WHERE id = $coupon_id");
        if ($coupon_row = mysqli_fetch_assoc($coupon_result)) {
            $discount_value = floatval($coupon_row['discount_value']);
            $discount_type = $coupon_row['discount_type'];

            if ($discount_type === 'percentage') {
                $discount = ($subtotal * $discount_value) / 100;
            } elseif ($discount_type === 'flat') {
                $discount = $discount_value;
            }

            $final_total = ($subtotal + $tax) - $discount;
        }
    } elseif (isset($_POST['remove_coupon']) && $_POST['remove_coupon'] === '1') {
        $coupon_id = "NULL";
    } else {
        $coupon_id = !empty($order['id']) ? $order['id'] : "NULL";
    }

    // Optional fields
    $card_number = $payment_type == 'card' ? $_POST['card_number'] : '';
    $expiry_date = $payment_type == 'card' ? $_POST['expiry_date'] : '';
    $cvv = $payment_type == 'card' ? $_POST['cvv'] : '';
    $upi_id = $payment_type == 'upi' ? $_POST['upi_id'] : '';

    $updateQuery = "UPDATE orders SET 
        u_id='$u_id', subtotal='$subtotal', tax='$tax', discount='$discount', final_total='$final_total', 
        payment_type='$payment_type', order_status='$order_status',
        card_number='$card_number', expiry_date='$expiry_date', cvv='$cvv',
        upi_id='$upi_id', id=" . ($coupon_id === "NULL" ? "NULL" : "'$coupon_id'") . "
        WHERE order_id = $order_id";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Order updated successfully.'); window.location='orderpage.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update order.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit order</title>
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
</head>
<body>
<div class="container mt-4">
    <h3>Edit Order #<?= $order_id ?></h3>
    <form method="POST">
        <div class="form-group">
            <label>User ID:</label>
            <input type="number" name="u_id" class="form-control" value="<?= $order['u_id'] ?>" required>
        </div>
        <div class="form-group">
            <label>Subtotal:</label>
            <input type="number" step="0.01" name="subtotal" class="form-control" value="<?= $order['subtotal'] ?>" readonly required>
        </div>
        <div class="form-group">
            <label>Tax:</label>
            <input type="number" step="0.01" name="tax" class="form-control" value="<?= $order['tax'] ?>" required>
        </div>
        <div class="form-group">
            <label>Discount:</label>
            <input type="number" name="discount" class="form-control" value="<?= $order['discount'] ?>" readonly>
            </div>
        <div class="form-group">
            <label>Final Total:</label>
            <input type="number" name="final_total" class="form-control" value="<?= $order['final_total'] ?>" readonly>
            </div>
        <div class="form-group">
            <label>Payment Type:</label>
            <select name="payment_type" class="form-control" id="payment_type" required onchange="toggleFields()">
                <option value="card" <?= $order['payment_type'] == 'card' ? 'selected' : '' ?>>Card</option>
                <option value="upi" <?= $order['payment_type'] == 'upi' ? 'selected' : '' ?>>UPI</option>
                <option value="cod" <?= $order['payment_type'] == 'cod' ? 'selected' : '' ?>>Cash on Delivery</option>
            </select>
        </div>

        <!-- Card Fields -->
        <div id="card_fields" style="display: none;">
            <div class="form-group">
                <label>Card Number:</label>
                <input type="text" name="card_number" class="form-control" value="<?= $order['card_number'] ?>">
            </div>
            <div class="form-group">
                <label>Expiry Date:</label>
                <input type="text" name="expiry_date" class="form-control" value="<?= $order['expiry_date'] ?>">
            </div>
            <div class="form-group">
                <label>CVV:</label>
                <input type="text" name="cvv" class="form-control" value="<?= $order['cvv'] ?>">
            </div>
        </div>

        <!-- UPI Field -->
        <div id="upi_field" style="display: none;">
            <div class="form-group">
                <label>UPI ID:</label>
                <input type="text" name="upi_id" class="form-control" value="<?= $order['upi_id'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Order Status:</label>
            <select name="order_status" class="form-control" required>
            <!-- <option value="">-- Select order status --</option> -->
                <option value="Pending" <?= $order['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Completed" <?= $order['order_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Cancelled" <?= $order['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="processing" <?= $order['order_status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                <option value="shipped" <?= $order['order_status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="delivered" <?= $order['order_status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
            </select>
        </div>
        <?php if ($current_coupon): ?>
     <div class="form-group">
        <label>Applied Coupon:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($current_coupon['name']) ?>" readonly>
        <?php if ($current_coupon): ?>
            <input type="hidden" name="id" value="<?= $current_coupon['id'] ?>">
        <?php endif; ?>

        <button type="button" onclick="removeCoupon()" class="btn btn-danger btn-sm mt-2">Remove Coupon</button>
        <input type="hidden" name="remove_coupon" id="remove_coupon" value="0">
     </div>

      <?php else: ?>
      <div class="form-group">
        <label>Apply Coupon:</label>
        <select name="id" class="form-control">
            <option value="">-- Select a Coupon --</option>
            <?php while ($row = mysqli_fetch_assoc($all_coupons)) : ?>
                <option value="<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
     </div>
      <?php endif; ?>

        <button type="submit" name="submit" class="btn btn-success mt-3">Update Order</button>
        <a href="orderpage.php" class="btn btn-secondary mx-2 mt-3">Cancel</a>
    </form>
</div>

<script>
function removeCoupon() {
    const couponGroup = document.querySelector('[name="remove_coupon"]').closest('.form-group');
    couponGroup.style.display = 'none';

    // Remove old hidden coupon id input if any
    const hiddenIdInput = document.querySelector('input[name="id"]');
    if (hiddenIdInput) hiddenIdInput.remove();

    const selectGroup = document.createElement('div');
    selectGroup.className = 'form-group';
    selectGroup.innerHTML = `
        <label>Apply Coupon:</label>
        <select name="id" class="form-control">
            <option value="">-- Select a Coupon --</option>
            <?php
            mysqli_data_seek($all_coupons, 0);
            while ($row = mysqli_fetch_assoc($all_coupons)) {
                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
            }
            ?>
        </select>
    `;
    couponGroup.parentNode.insertBefore(selectGroup, couponGroup.nextSibling);

    document.getElementById('remove_coupon').value = '1';
}


function toggleFields() {
    var type = document.getElementById('payment_type').value;
    document.getElementById('card_fields').style.display = (type === 'card') ? 'block' : 'none';
    document.getElementById('upi_field').style.display = (type === 'upi') ? 'block' : 'none';
}
toggleFields(); // Trigger on page load
</script>
</body>
<?php include '../includes/footer.php'; ?>
</html>