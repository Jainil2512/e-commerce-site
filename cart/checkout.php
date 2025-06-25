<?php
include '../db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location='../login.php';</script>";
    exit();
}

// directly from shop now buttom
$product_data = null;

if (isset($_GET['product_id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['product_id']);
    $product_query = "SELECT * FROM product WHERE product_id = '$product_id'";
    $product_result = mysqli_query($conn, $product_query);

    if ($product_row = mysqli_fetch_assoc($product_result)) {
        $product_data = $product_row; // This will be used to show the product directly on the checkout page
    } else {
        echo "<p class='text-danger'>Product not found for direct checkout.</p>";
    }
}

// from cart
$user_id = $_SESSION['user_id'];
$query = "SELECT c.cart_id, p.product_name, p.price, c.quantity, p.image 
          FROM cart c
          JOIN product p ON c.product_id = p.product_id
          WHERE c.u_id = $user_id";
$result = mysqli_query($conn, $query);

$total_price = 0;
$discounted_price = 0;
$discount_amount = 0;
$coupon_applied = false;
$coupon_message = '';
$applied_coupon = null;

// Fetch active coupons
$coupon_query = "SELECT * FROM coupons WHERE status = 'active' 
                 AND (expiry_date IS NULL OR expiry_date >= NOW())";
$coupon_result = mysqli_query($conn, $coupon_query);

$coupon_names = [];
while ($coupon = mysqli_fetch_assoc($coupon_result)) {
    $coupon_names[$coupon['name']] = $coupon;
    // echo "Coupon Name: " . htmlspecialchars($coupon['name']) . " | Type: " . htmlspecialchars($coupon['type']) . "<br>";
}
// echo "<pre>";
// print_r($coupon_names);
// echo "</pre>";
if (isset($_SESSION['applied_coupon']) && array_key_exists($_SESSION['applied_coupon'], $coupon_names)) {
    $coupon_applied = true;
    $applied_coupon = $coupon_names[$_SESSION['applied_coupon']];
    $coupon_message = "Coupon applied successfully!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    $coupon_applied = false;
    $applied_coupon = null;
    $coupon_message = "Coupon removed!";
}

// Handle coupon submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    // Check if a coupon is already applied
    if (isset($_SESSION['applied_coupon'])) {
        $coupon_message = "You have already applied a coupon! Remove it before adding a new one.";
    } else {
        // Check if coupon code is entered
        if (!empty($_POST['coupon_code'])) {
            $user_coupon = trim($_POST['coupon_code']);

            // Validate the coupon
            if (array_key_exists($user_coupon, $coupon_names)) {
                $coupon = $coupon_names[$user_coupon];

                // Check if the coupon can be used
                if (trim(strtolower($coupon['type'])) === 'general' || 
                   ($coupon['type'] === 'user-based' && $coupon['used_users'] < $coupon['max_users'])) {
                    
                    $_SESSION['applied_coupon'] = $user_coupon; // Store coupon in session
                    $coupon_applied = true;
                    $coupon_message = "Coupon applied successfully!";
                    $applied_coupon = $coupon; // Store coupon details

                } else {
                    $coupon_message = "Coupon usage limit reached!";
                }
            } else {
                $coupon_message = "Invalid coupon code!";
            }
        } else {
            $coupon_message = "Please enter a coupon code!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Checkout</h2>
    
    <table class="table table-bordered-none">
<thead>
    <tr>
        <th>Image</th>
        <th>Product Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
    </tr>
</thead>
<tbody>
    <?php 
    if ($product_data) {
        // Show only the selected product
        $quantity = 1;
        $subtotal = $product_data['price'] * $quantity;
        $total_price += $subtotal;
    ?>
    <tr>
        <td><img src="/FILEUPLOAD/<?php echo $product_data['image']; ?>" width="50"></td>
        <td><?php echo $product_data['product_name']; ?></td>
        <td>$<?php echo number_format($product_data['price'], 2); ?></td>
        <td><?php echo $quantity; ?></td>
        <td>$<?php echo number_format($subtotal, 2); ?></td>
    </tr>
    <?php 
    } else {
        // Show all cart items
        while ($row = mysqli_fetch_assoc($result)) {
            $subtotal = $row['price'] * $row['quantity'];
            $total_price += $subtotal;
    ?>
    <tr>
        <td><img src="/FILEUPLOAD/<?php echo $row['image']; ?>" width="50"></td>
        <td><?php echo $row['product_name']; ?></td>
        <td>$<?php echo number_format($row['price'], 2); ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td>$<?php echo number_format($subtotal, 2); ?></td>
    </tr>
    <?php 
        }
    }    
            // Calculate discount after total_price is computed
            if ($coupon_applied && $applied_coupon) {
                if ($applied_coupon['discount_type'] === 'flat') {
                    $discount_amount = $applied_coupon['discount_value'];
                } elseif ($applied_coupon['discount_type'] === 'percentage') {
                    $discount_amount = ($applied_coupon['discount_value'] * $total_price) / 100;
                }
            }
            
            $discounted_price = $total_price - $discount_amount;
            if ($discounted_price < 0) $discounted_price = 0;
            ?>
        </tbody>
    </table>

    <!-- Coupon Code Input -->
    <div class="mt-3">
    <label for="coupon_code" class="form-label fw-bold">Enter Coupon Code:</label>
    <form action="checkout.php" method="POST">
        <input type="text" id="coupon_code" name="coupon_code" class="form-control" 
               placeholder="Enter coupon name"
               value="<?php echo $coupon_applied ? htmlspecialchars($applied_coupon['name']) : ''; ?>">

        <button type="submit" name="submit" class="btn btn-primary mt-2">Apply Coupon</button>
    </form>

    <?php if ($coupon_applied) { ?>
        <!-- Remove Coupon Button -->
        <form action="checkout.php" method="POST" class="d-inline">
            <button type="submit" name="remove_coupon" class="btn btn-danger mt-2">Remove Coupon</button>
        </form>
    <?php } ?>

    <!-- Display Messages -->
    <?php if (!empty($coupon_message)) { ?>
        <div class="mt-2 <?php echo $coupon_applied ? 'text-success' : 'text-danger'; ?>">
            <?php echo $coupon_message; ?>
        </div>
    <?php } ?>
</div>
 
<?php
// Fetch user details
$user_query = "SELECT first_name, last_name, username, email, phone FROM users WHERE u_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
?>
<!--  -->

<!-- Checkout Form -->
<div class="mt-5">
    <h4>Billing Information</h4>
    <form action="/FILEUPLOAD/order/placeorder.php" method="POST">
         <!-- Price Display -->
      <div class="mt-3">
        <h4>Subtotal: $<span><?php echo number_format($total_price, 2); ?></span></h4>
        <input type="hidden" name="subtotal" value="<?php echo $total_price; ?>">

        <?php if ($coupon_applied) { ?>
            <h4>Discount: $<span><?php echo number_format($discount_amount, 2); ?></span></h4>
            <h4>Final Total: $<span id="total"><?php echo number_format($discounted_price, 2); ?></span></h4>
            <input type="hidden" name="discount" value="<?php echo $discount_amount; ?>">
            <input type="hidden" name="final_total" value="<?php echo $discounted_price; ?>">
        <?php } else { ?>
            <h4>Total: $<span id="total"><?php echo number_format($total_price, 2); ?></span></h4>
            <input type="hidden" name="discount" value="0">
            <input type="hidden" name="final_total" value="<?php echo $total_price; ?>">
        <?php } ?>
     </div>
     </div>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['first_name'] . $user['last_name'] ); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Shipping Address</label>
            <textarea class="form-control" name="address" rows="3" placeholder="Enter your address" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Country</label>
            <input type="text" class="form-control" name="country" placeholder="Enter your Country" required >
        </div>
        <div class="mb-3">
            <label class="form-label">State</label>
            <input type="text" class="form-control" name="state" placeholder="Enter your State" required >
        </div>
        <div class="mb-3">
            <label class="form-label">City</label>
            <input type="text" class="form-control" name="city" placeholder="Enter your City" required >
        </div>
        <div class="mb-3">
            <label class="form-label">ZIP code</label>
            <input type="number" class="form-control" name="zip" placeholder="Enter your ZIp code for example 380001" required >
        </div>
        <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select class="form-select" name="payment_method" id="payment_method" required>
                <option value="">-- Select Payment Option --</option>
                <option value="cod">Cash on Delivery</option>
                <option value="upi">UPI</option>
                <option value="card">Credit/Debit Card</option>
            </select>
        </div>

        <!-- UPI input field -->
        <div id="upi_field" style="display: none;">
            <div class="mb-3">
               <label class="form-label">UPI ID</label>
               <input type="text" class="form-control" name="upi_id" placeholder="Enter your UPI ID (e.g. name@bank)">
            </div>
        </div>

        <!-- Additional fields for card payment -->
        <div id="card_fields" style="display: none;">
           <div class="mb-3">
              <label class="form-label">Card Number</label>
              <input type="text" class="form-control" name="card_number" pattern="\d{16}" maxlength="16" placeholder="Enter 16-digit card number">
            </div>
            <div class="mb-3">
               <label class="form-label">Expiry Date</label>
               <input type="month" class="form-control" name="expiry_date" placeholder="MM/YYYY">
            </div>
            <div class="mb-3">
               <label class="form-label">CVV</label>
               <input type="password" class="form-control" name="cvv" pattern="\d{3}" maxlength="3" placeholder="CVV (3 digits)">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Order Notes (optional)</label>
            <textarea class="form-control" name="order_notes" rows="2" placeholder="Any special instructions?"></textarea>
        </div>

        <!-- Place Order Button -->
        <input type="hidden" name="total_price" id="hidden_total" 
               value="<?php echo $coupon_applied ? $discounted_price : $total_price; ?>" 
               data-original="<?php echo $total_price; ?>">
               <?php if ($coupon_applied) { ?>
                   <input type="hidden" name="coupon_code" value="<?php echo htmlspecialchars($applied_coupon['name']); ?>">
                <?php } ?>
        <button type="submit" name="place_order" class="btn btn-success mt-3" onclick="window.location.href='placeorder.php'">Place Order</button>
        <a href="cart.php" class="btn btn-primary mt-3">Back</a>
    </form>
</div>

<script>
    const paymentSelect = document.getElementById('payment_method');
    const cardFields = document.getElementById('card_fields');
    const upiField = document.getElementById('upi_field');

    paymentSelect.addEventListener('change', function () {
        if (this.value === 'card') {
            cardFields.style.display = 'block';
            upiField.style.display = 'none';
        } else if (this.value === 'upi') {
            upiField.style.display = 'block';
            cardFields.style.display = 'none';
        } else {
            cardFields.style.display = 'none';
            upiField.style.display = 'none';
        }
    });
</script>

<!-- <?php if ($product_data): ?>
    <div class="card p-3 mb-3">
        <h4>Direct Checkout Product</h4>
        <p><strong>Name:</strong> <?php echo $product_data['product_name']; ?></p>
        <p><strong>Price:</strong> $<?php echo $product_data['price']; ?></p>
        <input type="hidden" name="direct_product_id" value="<?php echo $product_data['product_id']; ?>">
        <input type="hidden" name="direct_quantity" value="1">
        <input type="hidden" name="subtotal" value="<?php echo $product_data['price']; ?>">
        <input type="hidden" name="final_total" value="<?php echo $product_data['price']; ?>">
    </div>
<?php endif; ?> -->

</body>
<?php include '../includes/footer.php'; ?>
</html>

