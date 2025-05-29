<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Checkout - toolXpert</title>
    <link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../../includes/css/style.css" rel="stylesheet">
    <link href="coupon.css" rel="stylesheet"> <!-- إضافة ملف coupon.css للتنسيق -->
</head>
<body>
<?php include("../../includes/navbar/index.php"); ?>

<div class="container py-5">
    <h1 class="mb-4">Checkout</h1>

    <form action="checkout.php" method="POST">
        <div class="row">
            <div class="col-md-6">
                <h4>Billing Details</h4>
                <div class="mb-4">
                    <input type="text" class="form-control" name="phone" placeholder="Phone Number" required>
                </div>
                <div class="mb-4">
                    <input type="text" class="form-control" name="address" placeholder="Address" required>
                </div>
            </div>

            <div class="col-md-6">
                <h4>Your Order</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="cartItems"></tbody>
                </table>

                <table class="table">
                    <tbody>
                        <tr><td><strong>Original Total</strong></td><td>JOD <span id="original_price">0.00</span></td></tr>
                        <tr><td><strong>Discount</strong></td><td>-JOD <span id="discount_value">0.00</span></td></tr>
                        <tr><td><strong>Order Total</strong></td><td>JOD <span id="total_price">0.00</span></td></tr>
                    </tbody>
                </table>

                <!-- <div class="input-group mb-4">
                    <input type="text" class="form-control" id="coupon_code" name="coupon_code" placeholder="Coupon Code (Optional)">
                    <button type="button" class="btn btn-dark" onclick="applyCoupon()">Apply</button>
                </div> -->
                <p id="coupon_message" class="form-error" style="display: none;"></p>

                <button type="submit" class="btn btn-success w-100">Place Order</button>
            </div>
        </div>
    </form>
</div>

<?php include("../../includes/footer/index.php") ?>
<script>
    // Load user details when page loads
    fetch('get_user_details.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector('[name="phone"]').value = data.phone;
                document.querySelector('[name="address"]').value = data.address;
            }
        });

    // Load cart details - FIXED
    function loadCart() {
        fetch('get_cart.php')
            .then(response => response.json())
            .then(data => {
                const cartItems = document.getElementById('cartItems');
                cartItems.innerHTML = '';
                if (data.success) {
                    data.cart.forEach(item => {
                        const row = `<tr>
                                        <td>${item.name}</td>
                                        <td>JOD ${parseFloat(item.price).toFixed(2)}</td>
                                        <td>${item.quantity}</td>
                                        <td>JOD ${parseFloat(item.total).toFixed(2)}</td>
                                    </tr>`;
                        cartItems.innerHTML += row;
                    });

                    // استخدام الحقول الصحيحة من get_cart.php
                    document.getElementById('original_price').innerText = parseFloat(data.original_total).toFixed(2);
                    document.getElementById('discount_value').innerText = parseFloat(data.discount_value).toFixed(2);
                    document.getElementById('total_price').innerText = parseFloat(data.total_price).toFixed(2);
                } else {
                    document.getElementById('cartItems').innerHTML = '<tr><td colspan="4">Your cart is empty.</td></tr>';
                    document.getElementById('original_price').innerText = '0.00';
                    document.getElementById('discount_value').innerText = '0.00';
                    document.getElementById('total_price').innerText = '0.00';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('cartItems').innerHTML = '<tr><td colspan="4">Error loading cart.</td></tr>';
            });
    }

    function applyCoupon() {
        const couponCode = document.getElementById('coupon_code').value;
        fetch('apply_coupon.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'coupon_code=' + encodeURIComponent(couponCode)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const couponMessage = document.getElementById('coupon_message');
            couponMessage.innerText = data.message;
            couponMessage.style.color = data.success ? 'green' : 'red';
            couponMessage.style.display = 'block';
            if (data.success) {
                document.getElementById('original_price').innerText = parseFloat(data.original_total).toFixed(2);
                document.getElementById('discount_value').innerText = parseFloat(data.discount_value).toFixed(2);
                document.getElementById('total_price').innerText = parseFloat(data.new_total).toFixed(2);
                loadCart(); // إعادة تحميل بيانات السلة لضمان التحديث
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('coupon_message').innerText = 'Error applying coupon';
            document.getElementById('coupon_message').style.color = 'red';
            document.getElementById('coupon_message').style.display = 'block';
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        loadCart(); // تحميل بيانات السلة عند تحميل الصفحة
        const checkoutForm = document.querySelector("form");
        checkoutForm.addEventListener("submit", function (event) {
            event.preventDefault();
            const formData = new FormData(checkoutForm);
           keyboard: true
            fetch("checkout.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "thankyou.php";
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while placing the order.");
            });
        });
    });
</script>
</body>
</html>