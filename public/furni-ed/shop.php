<?php
require_once "../../includes/database/config.php";

$query = "SELECT 
            products.id, 
            products.category_id, 
            products.name AS name, 
            products.description, 
            products.stock AS stock, 
            products.image AS product_image, 
            products.price, 
            products.is_active, 
            categories.name AS category_name
          FROM products 
          INNER JOIN categories ON products.category_id = categories.id 
          WHERE products.is_active = 1";

if (isset($_GET['categories']) && !empty($_GET['categories'])) {
    $categoryIds = implode(",", $_GET['categories']); 
    $query .= " AND products.category_id IN ($categoryIds)"; 
}

try {
    $stmt = $pdo->query($query);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching products: " . $e->getMessage();
    $products = [];
}
?>



<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Untree.co">
  <link rel="shortcut icon" href="store.png">

  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap4" />

		<!-- Bootstrap CSS -->
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
		<link href="css/tiny-slider.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
		<link href="css/Filter.css" rel="stylesheet">

		<title>Craftify Free Bootstrap 5 Template for Craftifyture and Interior Design Websites by Untree.co </title>
	</head>

	<body>

		<!-- Start Header/Navigation -->
		<nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="Craftify navigation bar">

			<div class="container">
				<a class="navbar-brand" href="index.html">Craftify<span>.</span></a>

				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsCraftify" aria-controls="navbarsCraftify" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarsCraftify">
					<ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
						<li class="nav-item">
							<a class="nav-link" href="index.html">Home</a>
						</li>
						<li class="active"><a class="nav-link" href="shop.php">Shop</a></li>

						<li><a class="nav-link" href="about.html">About us</a></li>
						<li><a class="nav-link" href="services.html">Services</a></li>
						<li><a class="nav-link" href="Singin.html">Sing in</a></li>
						<li><a class="nav-link" href="contact.php">Contact us</a></li>
					</ul>

					<ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">
						<li><a class="nav-link" href="#"><img src="images/user.svg"></a></li>
						<li><a class="nav-link" href="cart.html"><img src="images/cart.svg"></a></li>
					</ul>
				</div>
			</div>
				
		</nav>
		<!-- End Header/Navigation -->

		<!-- Start Hero Section -->
			<div class="hero">
				<div class="container">
					<div class="row justify-content-between">
						<div class="col-lg-5">
							<div class="intro-excerpt">
								<h1>Shop</h1>
								<p class="mb-4">Custom products that reflect your unique style, for personal use or gifts. Let us bring your ideas to life.</p>
								<p><a href="" class="btn btn-secondary me-2">Shop Now</a><a href="#" class="btn btn-white-outline">Explore</a></p>
							</div>
						</div>
						<div class="col-lg-7">
							<div class="hero-img-wrap">
								<img src="images/pngtre.png" class="img-fluid">
							</div>
						</div>
					</div>
				</div>
			</div>
		<!-- End Hero Section -->

		
<!-- Start Filter Categories -->
		<form method="GET" action="shop.php" class="filter-form">
    <?php
    $query = "SELECT * FROM categories WHERE is_active = 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll();
    ?>
    
    <div class="filter-container text-center">
        <?php foreach ($categories as $category): ?>
            <div class="custom-control custom-checkbox custom-control-inline">
                <input type="checkbox" class="custom-control-input" id="category-<?= $category['id']; ?>" name="categories[]" value="<?= $category['id']; ?>" 
                <?php if (isset($_GET['categories']) && in_array($category['id'], $_GET['categories'])) echo 'checked'; ?> 
                onchange="this.form.submit()">
                <label class="custom-control-label" for="category-<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></label>
            </div>
        <?php endforeach; ?>
    </div>
</form>
		<!-- End Filter Categories -->



</div>


</div>


		<div class="untree_co-section product-section before-footer-section">
    <div class="container">
        <div class="row">

            <?php foreach ($products as $product): ?>
            <!-- Start Column -->
            <div class="col-12 col-md-4 col-lg-3 mb-5">
			
           <a class="product-item" href="../../admin/product/product_details.php?id=<?= $product['id'] ?>">

						<img src="/E_commerce_PHP/php_project_group_2/admin/product/uploads/product_images/<?php echo htmlspecialchars($product['product_image']); ?>" class="img-fluid product-thumbnail" alt="<?php echo htmlspecialchars($product['name']); ?>">
						
						<h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
						
						<strong class="product-price"><?php echo '$' . number_format($product['price'], 2); ?></strong>

						<span class="icon-cross">
						<img src="../../public/furni-ed/images/icon_plus.png" class="img-fluid" alt="cross">
							
						</span>
                </a>
            </div>
            <!-- End Column -->
            <?php endforeach; ?>

        </div>
    </div>
</div>
     <!-- Start Footer Section -->
        <?php include '../../includes/footer/index.php'; ?>

    <!-- End Footer Section -->	



		<script src="js/bootstrap.bundle.min.js"></script>
		<script src="js/tiny-slider.js"></script>
		<script src="js/custom.js"></script>
	</body>

</html>
