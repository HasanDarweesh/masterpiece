<?php

require "../../includes/database/config.php";
require "../../includes/functions/index.php";
include("../../includes/navbar/index.php");

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$sql = "SELECT name, email, phone, address, profile_picture, role FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


$sqlo = "SELECT o.id AS order_id, o.total_price, o.status, o.created_at,
                GROUP_CONCAT(
                    CONCAT(
                        p.name, '|',
                        oi.quantity, '|',
                        oi.price, '|',
                        p.image, '|',
                        p.description
                    ) SEPARATOR '||'
                ) AS products
         FROM orders o
         JOIN order_items oi ON o.id = oi.order_id
         JOIN products p ON oi.product_id = p.id
         WHERE o.user_id = :user_id
         GROUP BY o.id
         ORDER BY o.created_at DESC";

$stmto = $pdo->prepare($sqlo);
$stmto->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmto->execute();
$orders = $stmto->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $updated_name = $_POST['name'];
        $updated_email = $_POST['email'];
        $updated_phone = $_POST['phone'];
        $updated_address = $_POST['address'];

        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $profile_picture_name = $_FILES['image']['name'];
            $profile_picture_tmp_name = $_FILES['image']['tmp_name'];
            $image_directory = '../../uploads/avatars/';
            $profile_picture_path = $image_directory . basename($profile_picture_name);

            if (move_uploaded_file($profile_picture_tmp_name, $profile_picture_path)) {
                $update_sql = "UPDATE users SET name = :name, email = :email, phone = :phone, address = :address, profile_picture = :profile_picture WHERE id = :user_id";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->bindParam(':name', $updated_name);
                $update_stmt->bindParam(':email', $updated_email);
                $update_stmt->bindParam(':phone', $updated_phone);
                $update_stmt->bindParam(':address', $updated_address);
                $update_stmt->bindParam(':profile_picture', $profile_picture_path);
                $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $update_stmt->execute();
            }
        } else {
            $update_sql = "UPDATE users SET name = :name, email = :email, phone = :phone, address = :address WHERE id = :user_id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->bindParam(':name', $updated_name);
            $update_stmt->bindParam(':email', $updated_email);
            $update_stmt->bindParam(':phone', $updated_phone);
            $update_stmt->bindParam(':address', $updated_address);
            $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $update_stmt->execute();
        }

        header("Location: profile.php");
        exit();
    }

    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $repeat_new_password = $_POST['repeat_new_password'];

        $sql = "SELECT password FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user_data['password'])) {
            if ($new_password === $repeat_new_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = :password WHERE id = :user_id";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->bindParam(':password', $hashed_password);
                $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $update_stmt->execute();

                header("Location: profile.php#account-change-password");
                exit();
            } else {
                $password_error = "New passwords do not match.";
            }
        } else {
            $password_error = "Current password is incorrect.";
        }
    }

    // Handle Remove Photo
    if (isset($_POST['remove_photo'])) {
        $profile_picture_path = $user['profile_picture'];

        if ($profile_picture_path && file_exists($profile_picture_path)) {
            unlink($profile_picture_path);
        }

        $update_sql = "UPDATE users SET profile_picture = default WHERE id = :user_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $update_stmt->execute();

        // Reload the page to reflect changes
        header("Location: profile.php");
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="../../includes/css/style.css" rel="stylesheet">
</head>

<body>
    <img src="" alt="">
    <div class="container light-style flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-4">
            Profile
        </h4>
        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light">
                <div class="col-md-3 pt-0">
                    <div class="list-group list-group-flush account-settings-links">
                        <a class="list-group-item list-group-item-action active" data-toggle="list"
                            href="#account-general">General</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list"
                            href="#account-change-password">Change password</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list"
                            href="#History">History</a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="account-general">
                            <div class="card-body media align-items-center">
                                <img src="<?php echo $user['profile_picture'] ? $user['profile_picture'] : '../../uploads/default-avatar.png'; ?>" alt="Profile Image" class="d-block ui-w-80">
                                <div class="media-body ml-4">
                                    <label style="font-size: 14px;" class="btn btn-outline-primary">
                                        Upload new photo
                                        <input type="file" name="image" class="account-settings-fileinput" form="profile-form" style="display: none;" onchange="previewFile()">
                                    </label>

                                    <form action="profile.php" method="POST" id="remove-photo-form">
                                        <button type="submit" class="btn btn-danger mt-3" name="remove_photo" style="font-size: 14px;" >Remove Photo</button>
                                    </form>
                                </div>
                            </div>
                            <hr class="border-light m-0">
                            <form action="profile.php" method="POST" id="profile-form" enctype="multipart/form-data" onsubmit="return validateForm()">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" class="form-control" name="name" id="nameField" value="<?php echo $user['name']; ?>">
                                        <small id="nameError" class="text-danger"></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="email">E-mail</label>
                                        <input type="text" class="form-control mb-1" name="email" id="emailField" value="<?php echo $user['email']; ?>">
                                        <small id="emailError" class="text-danger"></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="phone">Phone</label>
                                        <input type="text" class="form-control" name="phone" id="phoneField" value="<?php echo $user['phone']; ?>">
                                        <small id="phoneError" class="text-danger"></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="address">Address</label>
                                        <input type="text" class="form-control" name="address" id="locationField" value="<?php echo $user['address']; ?>">
                                        <small id="locationError" class="text-danger"></small>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="update_profile">Save Changes</button>
                                </div>
                            </form>

                        </div>
                        <div class="tab-pane fade" id="account-change-password">
                            <div class="card-body pb-2">
                                <?php if (isset($password_error)) {
                                    echo "<p class='text-danger'>$password_error</p>";
                                } ?>
                                <form action="profile.php" method="POST" id="password-form" onsubmit="return validatePasswordForm()">
                                    <div class="form-group">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control" name="current_password" id="currentPassword" required>
                                        <small id="currentPasswordError" class="text-danger"></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="new_password" id="newPassword" required>
                                        <small id="newPasswordError" class="text-danger"></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Repeat New Password</label>
                                        <input type="password" class="form-control" name="repeat_new_password" id="repeatNewPassword" required>
                                        <small id="repeatNewPasswordError" class="text-danger"></small>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="change_password">Save Changes</button>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="History">
                            <div class="card-body pb-2">
                                <h5 class="font-weight-bold">Order History</h5>
                                <?php if (empty($orders)) { ?>
                                    <p>You have no orders yet.</p>
                                <?php } else { ?>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Total Price</th>
                                                <th>Status</th>
                                                <th>Created At</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td><?php echo number_format($order['total_price'], 2); ?></td>
                                                    <td><?php echo ucfirst($order['status']); ?></td>
                                                    <td><?php echo $order['created_at']; ?></td>
                                                    <td>
                                                        <button class="btn btn-info view-products-btn"
                                                            data-toggle="modal"
                                                            data-target="#productModal"
                                                            data-products="<?php echo htmlspecialchars($order['products'], ENT_QUOTES, 'UTF-8'); ?>">
                                                            View Products
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Bootstrap Modal -->
                        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="productModalLabel">Order Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="productDetails"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="./assets/js/profile.js"></script>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.view-products-btn').on('click', function() {
                var productsString = $(this).data('products');
                var productHTML = '';

                if (!productsString) {
                    productHTML = '<p>No products found.</p>';
                } else {
                    var productList = productsString.split('||'); // Split each product

                    productList.forEach(function(product) {
                        var details = product.split('|'); // Extract details

                        if (details.length === 5) {
                            var name = details[0];
                            var quantity = details[1];
                            var price = details[2];
                            var image = details[3];
                            var description = details[4];

                            productHTML += `
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <img src="../../admin/product/uploads/product_images/${image}" alt="${name}" class="img-fluid rounded">
                                </div>
                                <div class="col-md-9">
                                    <h6>${name}</h6>
                                    <p><strong>Quantity:</strong> ${quantity}</p>
                                    <p><strong>Price:</strong> ${price} $</p>
                                    <p><strong>Description:</strong> ${description}</p>
                                </div>
                            </div>
                            <hr>
                        `;
                        }
                    });
                }

                $('#productDetails').html(productHTML);
            });
        });
    </script>
</body>

</html>