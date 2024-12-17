<?php
session_start(); // Start the session to access session variables

if (!isset($_SESSION['user_id'])) {
    // Handle the case where the user is not logged in
    header('Location: login.php'); // Redirect to login page if no session
    exit;
}

// Use the correct session key
$user_id = $_SESSION['user_id'];

include_once "../db/database.php";

// SQL to fetch cart data, excluding checked-out items
$sql = "SELECT 
            c.cart_id,
            p.id, 
            p.name, 
            p.image_url, 
            p.price, 
            p.discounted_price,
            c.quantity, 
            (COALESCE(p.discounted_price, p.price) * c.quantity) AS total_price
        FROM 
            AfriqueBotique_Cart c
        JOIN 
            AfriqueBotique_Products p ON c.product_id = p.id
        WHERE 
            c.user_id = ? 
            AND c.checkout = 'No'"; // Exclude checked-out items

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

$cart_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - BotiqueAfrique</title>
    <link rel="stylesheet" href="../assets/css/newCart.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Shopping Cart</h1>
    </header>

    <div class="shopping-cart">
        <div class="column-labels">
            <label class="product-image">Image</label>
            <label class="product-details">Product</label>
            <label class="product-price">Price</label>
            <label class="product-quantity">Quantity</label>
            <label class="product-removal">Remove</label>
            <label class="product-total">Total</label>
        </div>

        <?php foreach ($cart_items as $item): ?>
            <div class="product" data-product-id="<?= $item['cart_id']; ?>">
                <div class="product-image">
                    <img src="<?= htmlspecialchars($item['image_url']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                </div>
                <div class="product-details">
                    <div class="product-title"><?= htmlspecialchars($item['name']); ?></div>
                    <p class="product-description"></p>
                </div>
                <div class="product-price">
                    $<?= number_format($item['discounted_price'] ?? $item['price'], 2); ?>
                </div>
                <div class="product-quantity">
                    <input type="number" value="<?= $item['quantity']; ?>" min="1" aria-label="Quantity of <?= htmlspecialchars($item['name']); ?>"
                           oninput="updateQuantity(<?= $item['cart_id']; ?>, this.value, <?= $item['discounted_price'] ?? $item['price']; ?>)">
                </div>
                <div class="product-removal">
                    <button class="remove-product" aria-label="Remove <?= htmlspecialchars($item['name']); ?>" onclick="removeFromCart(<?= $item['cart_id']; ?>)">
                        Remove
                    </button>
                </div>
                <div class="product-total">
                    $<?= number_format($item['total_price'], 2); ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="totals">
            <div class="totals-item">
                <label>Subtotal</label>
                <div class="totals-value" id="cart-subtotal">
                    $<?php
                    // Calculate subtotal dynamically
                    $subtotal = array_sum(array_column($cart_items, 'total_price'));
                    echo number_format($subtotal, 2);
                    ?>
                </div>
            </div>
            <div class="totals-item">
                <label>Tax (5%)</label>
                <div class="totals-value" id="cart-tax">$<?php echo number_format($subtotal * 0.05, 2); ?></div>
            </div>
            <div class="totals-item">
                <label>Shipping</label>
                <div class="totals-value" id="cart-shipping">$15.00</div>
            </div>
            <div class="totals-item totals-item-total">
                <label>Grand Total</label>
                <div class="totals-value" id="cart-total">
                    $<?php echo number_format($subtotal + ($subtotal * 0.05) + 15, 2); ?>
                </div>
            </div>
        </div>
        
        <button class="checkout" aria-label="Back to Homepage" onclick="location.href='homepage.php';">Back to Homepage</button>
        <button class="checkout" aria-label="Proceed to checkout" onclick="location.href='checkout.php';">Checkout</button>
        

    </div>

<script>
    // Function to remove an item from the cart and update totals dynamically
    function removeFromCart(cartId) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `../functions/remove_from_cart.php?cart_id=${cartId}`, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText === 'success') {
                    alert('Item removed from cart');
                    const productRow = document.querySelector(`[data-product-id='${cartId}']`);
                    if (productRow) {
                        productRow.remove();
                    }
                    updateTotals(); // Recalculate totals
                } else {
                    alert('Failed to remove item from cart');
                }
            }
        };
        xhr.send();
    }

    // Function to update the totals dynamically
    function updateTotals() {
        const subtotalElem = document.getElementById('cart-subtotal');
        const taxElem = document.getElementById('cart-tax');
        const totalElem = document.getElementById('cart-total');
        
        let subtotal = 0;
        const totalPrices = document.querySelectorAll('.product-total');
        totalPrices.forEach(function (totalElem) {
            // Use parseFloat with more robust parsing
            const price = parseFloat(totalElem.textContent.replace(/[^0-9.-]+/g, ''));
            if (!isNaN(price)) {
                subtotal += price;
            }
        });

        const tax = subtotal * 0.05;
        const total = subtotal + tax + 15; // Include shipping cost

        // Update totals on the page
        subtotalElem.textContent = `$${subtotal.toFixed(2)}`;
        taxElem.textContent = `$${tax.toFixed(2)}`;
        totalElem.textContent = `$${total.toFixed(2)}`;
    }

    // Function to update the quantity of an item and reflect the changes in the cart
    function updateQuantity(cartId, newQuantity, productPrice) {
        newQuantity = parseInt(newQuantity, 10);
        if (isNaN(newQuantity) || newQuantity <= 0) {
            newQuantity = 1;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../functions/update_quantities.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText === 'success') {
                    // Update the total price for the item
                    const totalPriceElem = document.querySelector(`[data-product-id='${cartId}'] .product-total`);
                    const newTotalPrice = newQuantity * productPrice;
                    totalPriceElem.textContent = `$${newTotalPrice.toFixed(2)}`;
                    
                    // Update the quantity input value
                    const quantityInput = document.querySelector(`[data-product-id='${cartId}'] .product-quantity input`);
                    quantityInput.value = newQuantity;

                    // Recalculate subtotal
                    let subtotal = 0;
                    const totalPrices = document.querySelectorAll('.product-total');
                    totalPrices.forEach(function (totalElem) {
                        // Use parseFloat with more robust parsing
                        const price = parseFloat(totalElem.textContent.replace(/[^0-9.-]+/g, ''));
                        if (!isNaN(price)) {
                            subtotal += price;
                        }
                    });

                    // Update subtotal, tax, and total
                    const subtotalElem = document.getElementById('cart-subtotal');
                    const taxElem = document.getElementById('cart-tax');
                    const totalElem = document.getElementById('cart-total');

                    const tax = subtotal * 0.05;
                    const total = subtotal + tax + 15; // Include shipping cost

                    subtotalElem.textContent = `$${subtotal.toFixed(2)}`;
                    taxElem.textContent = `$${tax.toFixed(2)}`;
                    totalElem.textContent = `$${total.toFixed(2)}`;
                } else {
                    alert('Failed to update quantity');
                }
            }
        };
        xhr.send(`cart_id=${cartId}&quantity=${newQuantity}`);
    }
</script>

</body>
</html>
