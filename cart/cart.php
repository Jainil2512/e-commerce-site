<?php
include '../db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location='../login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = max(1, $_POST['quantity']); // Ensure quantity doesn't go below 1   
        $update_query = "UPDATE cart SET quantity = $quantity WHERE cart_id = $cart_id AND u_id = $user_id";
        mysqli_query($conn, $update_query);
    // Redirect to refresh the page
    header("Location: cart.php");
    exit();
}

$query = "SELECT c.cart_id, p.product_name, p.product_id, p.price, p.stock, c.quantity, p.image 
          FROM cart c
          JOIN product p ON c.product_id = p.product_id
          WHERE c.u_id = $user_id";
$result = mysqli_query($conn, $query);
$cart_count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
</head>
<body>
<?php if (isset($_SESSION['cart_error'])) { ?>
    <div class="alert alert-danger text-center">
        <?php echo $_SESSION['cart_error']; unset($_SESSION['cart_error']); ?>
    </div>
<?php } ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Your Cart</h2>
    <?php if ($cart_count > 0) { ?>
    <table class="table table-bordered-none">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_price = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $total = $row['price'] * $row['quantity'];
                $total_price += $total;
                ?>
                <tr>
                    <td><img src="/FILEUPLOAD/<?php echo $row['image']; ?>" width="50"></td>
                    <td>
                        <a href="/FILEUPLOAD/productpage.php?id=<?php echo $row['product_id']; ?>" class="text-decoration-none">
                            <?php echo $row['product_name']; ?>
                         </a>
                    </td>
                    <td>$<span class="item-price"><?php echo number_format($row['price'], 2); ?></span></td>
                    <td>
                        <form method="POST" class="quantity-form d-inline" data-stock="<?php echo $row['stock']; ?>">
                            <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                            <button type="button" class="btn btn-sm btn-outline-secondary decrease-qty">-</button>
                            <span class="quantity mx-2"><?php echo $row['quantity']; ?></span>
                            <input type="hidden" name="quantity" class="quantity-input" value="<?php echo $row['quantity']; ?>">
                            <button type="button" class="btn btn-sm btn-outline-secondary increase-qty">+</button>
                            <input type="hidden" name="update_cart" value="1">
                        </form>
                    </td>
                    <td>$<span class="item-total"><?php echo number_format($total, 2); ?></span></td>
                    <td>
                        <a href="remove_from_cart.php?cart_id=<?php echo $row['cart_id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="text-center mt-3">
        <h4>Total Price: $<span id="cart-total"><?php echo number_format($total_price, 2); ?></span></h4>
        <a href="checkout.php" class="btn btn-success mb-3">Checkout</a>
    </div>
    <?php } else { ?>
        <div class="text-center">
            <h4 class="text-danger">Your cart is empty! Please select products from the shop.</h4>
            <a href="/fileupload/shoppage.php" class="btn btn-primary mt-3">Go to Shop</a>
        </div>
    <?php } ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateCartTotal = () => {
        let total = 0;
        document.querySelectorAll('.item-total').forEach(item => {
            total += parseFloat(item.textContent.replace('$', ''));
        });
        document.getElementById('cart-total').textContent = total.toFixed(2);
    };

    const updateItemTotal = (row) => {
        const price = parseFloat(row.querySelector('.item-price').textContent.replace('$', ''));
        const quantity = parseInt(row.querySelector('.quantity').textContent);
        const total = price * quantity;
        row.querySelector('.item-total').textContent = total.toFixed(2);
        updateCartTotal();
    };

    document.querySelectorAll('.increase-qty').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.quantity-form');
            const quantitySpan = form.querySelector('.quantity');
            const quantityInput = form.querySelector('.quantity-input');
            const stock = parseInt(form.getAttribute('data-stock')); // Get stock from the dataset
            let currentQty = parseInt(quantitySpan.textContent);
            
            if (currentQty < stock) {
                currentQty++;
                quantitySpan.textContent = currentQty;
                quantityInput.value = currentQty;
                
                updateItemTotal(this.closest('tr'));
                form.submit(); // Submit the form to update the database
            } else {
                alert("Cannot add more! Only " + stock + " items in stock.");
            }
        });
    });

    document.querySelectorAll('.decrease-qty').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.quantity-form');
            const quantitySpan = form.querySelector('.quantity');
            const quantityInput = form.querySelector('.quantity-input');
            let currentQty = parseInt(quantitySpan.textContent);
            
            if (currentQty > 1) {
                currentQty--;
                quantitySpan.textContent = currentQty;
                quantityInput.value = currentQty;
                
                updateItemTotal(this.closest('tr'));
                form.submit(); // Submit the form to update the database
            }
        });
    });
});
</script>
</body>
</html>

<?php include '../includes/footer.php'; ?>