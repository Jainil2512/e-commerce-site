<?php
 include '../db_connect.php'; // Include your database connection file
 include '../includes/header.php';
 
 // session_start();
 if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'admin') {
     echo "<script>alert('Access Denied! Admins Only.'); window.location='/fileupload/shoppage.php';</script>";
     exit();
 }

 // Fetch all users and products
$users = mysqli_query($conn, "SELECT * FROM users WHERE usertype = 'user'");
$products = mysqli_query($conn, "SELECT * FROM product");
$coupons = mysqli_query($conn, "SELECT * FROM coupons WHERE status = 'active' 
                 AND (expiry_date IS NULL OR expiry_date >= NOW())");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["place_order"])) {
    $u_id = $_POST['u_id'];
    $subtotal = $_POST['subtotal'];
    $tax = $_POST['tax'];
    $discount = $_POST['discount'];
    $final_total = $_POST['final_total'];
    $payment_type = $_POST['payment_type'];
    $order_status = $_POST['order_status'];
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $upi_id = $_POST['upi_id'];

    $sql = "INSERT INTO orders (u_id, subtotal, tax, discount, final_total, payment_type, order_status, card_number, expiry_date, cvv, upi_id, created_at)
            VALUES ('$u_id', '$subtotal', '$tax', '$discount', '$final_total', '$payment_type', '$order_status', '$card_number', '$expiry_date', '$cvv', '$upi_id', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "Order placed successfully!";
        // header("Location: orders.php");
        // exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }

// Get the last inserted order ID
$sql_get_max_order = "SELECT MAX(order_id) AS max_order_id FROM orders";
$result_max_order = mysqli_query($conn, $sql_get_max_order);

if ($row = mysqli_fetch_assoc($result_max_order)) {
    $order_id = $row['max_order_id'];
    // echo "Latest Order ID: " . $order_id;
} else {
    echo "Error fetching latest order ID: " . mysqli_error($conn);
}
// Retrieve cart items for the logged-in user
$sql_cart = "SELECT product_id, quantity FROM cart WHERE u_id = '$user_id'";
$result_cart = mysqli_query($conn, $sql_cart);

if (mysqli_num_rows($result_cart) > 0) {
    while ($row = mysqli_fetch_assoc($result_cart)) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];

        // Insert each cart item into order_item_table
        $sql_order_item = "INSERT INTO order_item_table (order_id, product_id, quantity) 
                           VALUES ('$order_id', '$product_id', '$quantity')";
        mysqli_query($conn, $sql_order_item);
    }
    header("Location: order_success.php?order_id=" . $order_id);
    exit;
}else {
    // echo "Error: " . mysqli_error($conn);
}
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Create Order</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
</head>
<body>
<div class="container mt-5">
    <h2>Create New Order</h2>
    <form action="create_order.php" method="post">
        <div class="form-group">
            <label>User Name</label>
            <select name="u_id" class="form-control pb-2" required>
                <option value="">-- Select User --</option>
                <?php while ($user = mysqli_fetch_assoc($users)) { ?>
                    <option value="<?= $user['u_id'] ?>"><?= $user['first_name'] . $user['last_name'] ?></option>
                <?php } ?>
            </select>        
        </div>
        <!-- product displays -->
        <div id="productContainer">
     <div class="form-row product-row mb-3">
        <div class="col">
            <select name="product_id[]" class="form-control productSelect pb-2" required>
                <option value="">-- Select Product --</option>
                <?php 
                mysqli_data_seek($products, 0); // Reset pointer for reuse
                while ($product = mysqli_fetch_assoc($products)) { ?>
                    <option 
                        value="<?= $product['product_id'] ?>" 
                        data-price="<?= $product['price'] ?>"
                        data-stock="<?= $product['stock'] ?>">
                        <?= $product['product_name'] ?> - $<?= $product['price'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col">
            <input type="number" name="quantity[]" class="form-control quantityInput" min="1" value="1" required>
        </div>
        <div class="col">
            <button type="button" class="btn btn-danger remove-product">Remove</button>
        </div>
      </div>
     </div>

  <button type="button" class="btn btn-info mb-3" id="addProduct">+ Add Product</button>

        <div class="form-group">
            <label>Subtotal</label>
            <input type="text" name="subtotal" id="subtotal" class="form-control" readonly>
        </div>
        <div class="form-group">
            <label>Tax</label>
            <input type="text" name="tax" id="tax" class="form-control">
        </div>
        <div class="form-group">
            <label>Apply Coupon</label>
             <select id="couponSelect" class="form-control">
             <option class="pb-2" value="" data-discount="0">-- No Coupon --</option>
            <?php while ($coupon = mysqli_fetch_assoc($coupons)) { ?>
               <option value="<?= $coupon['id'] ?>" 
                    data-discount="<?= $coupon['discount_value'] ?>">
                    <?= $coupon['name'] ?> 
               </option>
              <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Discount</label>
            <input type="text" name="discount" id="discount" class="form-control">
        </div>
        <div class="form-group">
            <label>Final Total</label>
            <input type="text" name="final_total" id="final_total" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Payment Type</label>
            <select name="payment_type" class="form-control pb-2" >
                <option class="mb-2 pb-2" value="cod">COD</option>
                <option class="mb-2 pb-2" value="upi">UPI</option>
                <option class="mb-2 pb-2" value="card">Card</option>
            </select>
        </div>
        <div class="form-group">
            <label>Order Status</label>
            <select name="order_status" class="form-control pb-2">
                <option value="pending pb-2">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    <div id="cardFields" style="display: none;">
        <div class="form-group">
            <label>Card Number</label>
            <input type="text" name="card_number" class="form-control">
        </div>
        <div class="form-group">
            <label>Expiry Date</label>
            <input type="text" name="expiry_date" class="form-control">
        </div>
        <div class="form-group">
            <label>CVV</label>
            <input type="text" name="cvv" class="form-control">
        </div>
    </div>
    <div id="upiField" style="display: none;">
        <div class="form-group">
            <label>UPI ID</label>
            <input type="text" name="upi_id" class="form-control">
        </div>
    </div>
        <button type="submit" name ="place_order" class="btn btn-success">place Order</button>
        <a href="orderpage.php" class="btn btn-secondary">Back</a>
    </form>
</div>
<script>
function updateSubtotal() {
    let subtotal = 0;

    document.querySelectorAll('.product-row').forEach(row => {
        const select = row.querySelector('.productSelect');
        const quantityInput = row.querySelector('.quantityInput');
        const price = parseFloat(select.options[select.selectedIndex]?.dataset.price || 0);
        const qty = parseInt(quantityInput.value || 0);
        subtotal += price * qty;
    });

    document.getElementById('subtotal').value = subtotal.toFixed(2);
    updateFinalTotal(); // Also recalculate total
}

function updateFinalTotal() {
    const subtotal = parseFloat(document.getElementById('subtotal').value || 0);
    const tax = parseFloat(document.querySelector('input[name="tax"]').value || 0);
    const discount = parseFloat(document.querySelector('input[name="discount"]').value || 0);
    const finalTotal = subtotal + tax - discount;
    document.querySelector('input[name="final_total"]').value = finalTotal.toFixed(2);
}

document.getElementById('addProduct').addEventListener('click', () => {
    const container = document.getElementById('productContainer');
    const original = container.querySelector('.product-row');
    const clone = original.cloneNode(true);

    clone.querySelector('.productSelect').selectedIndex = 0;
    clone.querySelector('.quantityInput').value = 1;

    container.appendChild(clone);
    attachEventsToProductRow(clone);
});

function attachEventsToProductRow(row) {
    const productSelect = row.querySelector('.productSelect');
    const quantityInput = row.querySelector('.quantityInput');

    // When product changes
    productSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const stock = parseInt(selectedOption.getAttribute('data-stock')) || 1;
        quantityInput.max = stock;
        if (parseInt(quantityInput.value) > stock) {
            quantityInput.value = stock;
        }
        updateSubtotal();
    });

    // On quantity input
    quantityInput.addEventListener('input', function () {
        const stock = parseInt(productSelect.options[productSelect.selectedIndex].getAttribute('data-stock')) || 1;
        if (parseInt(this.value) > stock) {
            this.value = stock;
            alert("Quantity exceeds available stock!");
        }
        updateSubtotal();
    });

    row.querySelector('.remove-product').addEventListener('click', () => {
        row.remove();
        updateSubtotal();
    });
}

document.getElementById('couponSelect').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const discountAmount = parseFloat(selectedOption.getAttribute('data-discount')) || 0;
    document.getElementById('discount').value = discountAmount.toFixed(2);
    updateFinalTotal();
});

document.getElementById('couponSelect').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const discountAmount = parseFloat(selectedOption.getAttribute('data-discount')) || 0;

    const discountInput = document.getElementById('discount');
    discountInput.value = discountAmount.toFixed(2);
    discountInput.readOnly = discountAmount > 0; // Prevent manual changes
    updateFinalTotal();
});


// Attach initial events
document.querySelectorAll('.product-row').forEach(attachEventsToProductRow);
document.querySelector('input[name="tax"]').addEventListener('input', updateFinalTotal);
document.querySelector('input[name="discount"]').addEventListener('input', updateFinalTotal);
</script>

</body>
</html>
<?php
include '../includes/footer.php';
?>
