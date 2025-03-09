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

if (isset($_GET['price_range'])) {
    switch ($_GET['price_range']) {
        case '1': 
            $query .= " AND products.price < 50";
            break;
        case '2': 
            $query .= " AND products.price BETWEEN 50 AND 100";
            break;
        case '3': 
            $query .= " AND products.price BETWEEN 100 AND 200";
            break;
        case '4': 
            $query .= " AND products.price > 200";
            break;
    }
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
		<link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
		<link href="../../includes/css/tiny-slider.css" rel="stylesheet">
        <link href="../../includes/css/Filter.css" rel="stylesheet">
		<link href="../../includes/css/style.css" rel="stylesheet">


		<title>Craftify: Shop </title>
	</head>

	<body>

		<!-- Start Header/Navigation -->
        <?php include("../../includes/navbar/index.php")?>
		<!-- End Header/Navigation -->

		<!-- Start Hero Section -->
			<div class="hero p-0">
				<div class="container">
					<div class="row justify-content-between">
						<div class="col-lg-5">
							<div class="intro-excerpt">
								<h3>Shop</h3>
							</div>
						</div>
                              <!--slider product  -->
        <div class="product-slider-container mt-4">
    <div class="product-slider d-flex justify-content-end mx-auto">
        <?php
        $stmt = $pdo->query("SELECT id, name, image FROM products WHERE is_active = 1 LIMIT 10");
        while ($product = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="slider_products">
                <img  src="../../admin/product/uploads/product_images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                <p><?= htmlspecialchars($product['name']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

					</div>
				</div>
			</div>
		<!-- End Hero Section -->

<!-- Start Filter Price -->
<form method="GET" action="shop.php" class="filter-form">
    <?php
    $query = "SELECT * FROM categories WHERE is_active = 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll();
    ?>
    
    <div class="filter-container text-center">
        <!-- Start Filter Category-->
        <?php foreach ($categories as $category): ?>
            <div class="custom-control custom-checkbox custom-control-inline">
                <input type="checkbox" class="custom-control-input" id="category-<?= $category['id']; ?>" name="categories[]" value="<?= $category['id']; ?>" 
                <?php if (isset($_GET['categories']) && in_array($category['id'], $_GET['categories'])) echo 'checked'; ?> 
                onchange="this.form.submit()">
                <label class="custom-control-label" for="category-<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></label>
            </div>
        <?php endforeach; ?>
        <!-- End Filter Category-->


       
<!-- Radio Buttons -->
        <div class="price-filter">
            <p><strong>Filter by Price:</strong></p>
            
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="price-1" name="price_range" value="1" 
                <?php if (isset($_GET['price_range']) && $_GET['price_range'] == '1') echo 'checked'; ?>
                onchange="this.form.submit()">
                <label class="custom-control-label" for="price-1">Under JOD 50</label>
            </div>
            
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="price-2" name="price_range" value="2" 
                <?php if (isset($_GET['price_range']) && $_GET['price_range'] == '2') echo 'checked'; ?>
                onchange="this.form.submit()">
                <label class="custom-control-label" for="price-2">JOD 50 - JOD 100</label>
            </div>
            
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="price-3" name="price_range" value="3" 
                <?php if (isset($_GET['price_range']) && $_GET['price_range'] == '3') echo 'checked'; ?>
                onchange="this.form.submit()">
                <label class="custom-control-label" for="price-3">JOD 100 - JOD 200</label>
            </div>
            
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="price-4" name="price_range" value="4" 
                <?php if (isset($_GET['price_range']) && $_GET['price_range'] == '4') echo 'checked'; ?>
                onchange="this.form.submit()">
                <label class="custom-control-label" for="price-4">Above JOD 200</label>
            </div>
        </div>
    </div>
</form>
<!-- End Filter Price -->


</div>


</div>


		<div class="untree_co-section product-section before-footer-section">
    <div class="container">
        <div class="row">

            <?php foreach ($products as $product): ?>
            <!-- Start Column -->
            <div class="col-12 col-md-4 col-lg-3 mb-5">
			
           <a class="product-item" href="../../admin/product/product_details.php?id=<?= $product['id'] ?>">

                        <img src="../../admin/product/uploads/product_images/<?php echo htmlspecialchars($product['product_image']); ?>" class="img-fluid product-thumbnail" alt="<?php echo htmlspecialchars($product['name']); ?>">

						<h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
						
						<strong class="product-price"><?php echo 'JOD ' . number_format($product['price'], 2); ?></strong>

						<span class="icon-cross">
                        <img src="../../includes/images/icon_plus.png" class="img-fluid" alt="cross">

						</span>
                </a>
            </div>
            <!-- End Column -->
            <?php endforeach; ?>

        </div>
    </div>
</div>
    		<!-- Start Footer Section -->
		<footer class="footer-section">
			<div class="container relative">

				<div class="row g-5 mb-5">
					<div class="col-lg-4">
						<div class="mb-4 footer-logo-wrap"><a href="#" class="footer-logo">Craftify<span>.</span></a></div>
						<p class="mb-4">Custom products that reflect your unique style, for personal use or gifts. Let us bring your ideas to life.</p>

						<ul class="list-unstyled custom-social">
							<li><a href="#"><span class="fa fa-brands fa-facebook-f"></span></a></li>
							<li><a href="#"><span class="fa fa-brands fa-twitter"></span></a></li>
							<li><a href="#"><span class="fa fa-brands fa-instagram"></span></a></li>
							<li><a href="#"><span class="fa fa-brands fa-linkedin"></span></a></li>
						</ul>
					</div>

					<div class="col-lg-8">
						<div class="row links-wrap">
							<div class="col-6 col-sm-6 col-md-3">
								<ul class="list-unstyled">
									<li><a href="#">About us</a></li>
									<li><a href="../../public/products/shop.php">Shop</a></li>
									<li><a href="../../public/products/contact.php">Contact us</a></li>
								</ul>
							</div>

						

						
						</div>
					</div>

				</div>

				<div class="border-top copyright">
					<div class="row pt-4">
						<div class="col-lg-6">
							<p class="mb-2 text-center text-lg-start">
								Copyright &copy;<script>document.write(new Date().getFullYear());</script>. All Rights Reserved. &mdash; 
								Designed with love by Craftify 
								to offer unique and customized products for you.
							</p>
							
						</div>

						<div class="col-lg-6 text-center text-lg-end">
							<ul class="list-unstyled d-inline-flex ms-auto">
								<li class="me-4"><a href="#">Terms &amp; Conditions</a></li>
								<li><a href="#">Privacy Policy</a></li>
							</ul>
						</div>

					</div>
				</div>

			</div>
		</footer>
		<!-- End Footer Section -->	



	
        <script src="../../includes/js/bootstrap.bundle.min.js"></script>
		<script src="../../includes/js/tiny-slider.js"></script>
		<script src="../../includes/js/custom.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
            const slider = document.querySelector(".product-slider");
            let scrollAmount = 0;

    function scrollProducts() {
        if (scrollAmount >= slider.scrollWidth / 2) {
            scrollAmount = 0;
            slider.style.transform = `translateX(0)`;
        } else {
            scrollAmount += 1;
            slider.style.transform = `translateX(-${scrollAmount}px)`;
        }
    }

    setInterval(scrollProducts, 10);
});

        </script>
	</body>

</html>
