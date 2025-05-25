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


		<title>ToolsXpert: Shop </title>
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
								<!-- <h3 style="color:white;">Shop</h3> -->
							</div>
						</div>
                              <!--slider product  -->
        <!-- <div class="product-slider-container mt-0 ">
    <div class="product-slider d-flex justify-content-end mx-auto">
        <?php
        $stmt = $pdo->query("SELECT id, name, image FROM products WHERE is_active = 1 LIMIT 10");
        while ($product = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="slider_products">
                <img  src="../../admin/product/uploads/product_images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?> w-50">
                <p><?= htmlspecialchars($product['name']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div> -->

					</div>
				</div>
			</div><!-- الحل الأول: فلتر جانبي -->
<div class="shop-container">
    <!-- Sidebar للفلاتر -->
    <div class="filter-sidebar">
        <div class="filter-header">
            Filters
        </div>
        
        <form method="GET" action="shop.php" class="filter-content">
            <?php
            $query = "SELECT * FROM categories WHERE is_active = 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $categories = $stmt->fetchAll();
            ?>

            <!-- فلتر التصنيفات -->
            <div class="filter-section">
                <h4>Categories</h4>
                <?php foreach ($categories as $category): ?>
                    <div class="filter-item">
                        <input type="checkbox" 
                               id="cat<?= $category['id'] ?>" 
                               name="categories[]" 
                               value="<?= $category['id']; ?>"
                               <?php if (isset($_GET['categories']) && in_array($category['id'], $_GET['categories'])) echo 'checked'; ?>
                               onchange="this.form.submit()">
                        <label for="cat<?= $category['id'] ?>">
                            <?= htmlspecialchars($category['name']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- فلتر السعر -->
            <div class="filter-section">
                <h4>Price Range</h4>
                <?php
                $prices = [
                    1 => "Under JOD 50",
                    2 => "JOD 50 - 100", 
                    3 => "JOD 100 - 200",
                    4 => "Above JOD 200"
                ];
                foreach ($prices as $val => $label): ?>
                    <div class="filter-item">
                        <input type="radio" 
                               id="price<?= $val ?>" 
                               name="price_range" 
                               value="<?= $val ?>"
                               <?= (isset($_GET['price_range']) && $_GET['price_range'] == $val) ? 'checked' : ''; ?>
                               onchange="this.form.submit()">
                        <label for="price<?= $val ?>">
                            <?= $label ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- زر إعادة تعيين -->
            <button type="button" class="reset-filters" onclick="window.location.href='shop.php'">
                Clear All Filters
            </button>
        </form>
    </div>

    <!-- منطقة المنتجات -->
    <div class="products-area">
        <!-- عداد المنتجات -->
        <div class="products-count">
            Showing <?= count($products) ?> Products
        </div>
        
        <!-- هنا تضع grid المنتجات العادي -->
        <div class="untree_co-section product-section">
            <div class="container-fluid p-0">
                <div class="row">
                    <?php foreach ($products as $product): ?>
                    <div class="col-12 col-md-4 col-lg-3 mb-4">
                        <a class="product-item" href="../../admin/product/product_details.php?id=<?= $product['id'] ?>">
                            <img src="../../admin/product/uploads/product_images/<?php echo htmlspecialchars($product['product_image']); ?>" 
                                 class="img-fluid product-thumbnail" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <strong class="product-price"><?php echo 'JOD ' . number_format($product['price'], 2); ?></strong>
                            <span class="icon-cross">
                                <img src="../../includes/images/icon_plus.png" class="img-fluid" alt="cross">
                            </span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
    	<!-- Start Footer Section -->
            <?php include("../../includes/footer/index.php") ?>
		<!-- End Footer Section -->	


	
        <script src="../../includes/js/bootstrap.bundle.min.js"></script>
		<script src="../../includes/js/tiny-slider.js"></script>
		<script src="../../includes/js/custom.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
            const slider = document.querySelector(".product-slider");
            let scrollAmount = 0;

    function scrollProducts() {
        if (slider==null || slider.scrollWidth ==null) {
            return;
        }
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
