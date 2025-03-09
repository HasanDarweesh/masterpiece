<?php
require_once "../../includes/database/config.php";

// Query to fetch the most recent 3 items
try {
    $stmt = $pdo->prepare('SELECT id, name, image, price FROM products ORDER BY created_at DESC LIMIT 3');
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Query failed: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Untree.co">
  
  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap4" />

		<!-- Bootstrap CSS -->
		<link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
		<link href="../../includes/css/tiny-slider.css" rel="stylesheet">
		<link href="../../includes/css/style.css" rel="stylesheet">
		<link rel="shortcut icon" href="../../includes/images/store.png">
		<title>Craftify</title>
	</head>

	<body>

		<!-- Start Header/Navigation -->
		<?php include("../../includes/navbar/index.php"); ?>
		<!-- End Header/Navigation -->

		<!-- Start Hero Section -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active">
            <div class="hero">
                <div class="container px-5">
                    <div class="row justify-content-between">
                        <div class="col-lg-5">
                            <div class="intro-excerpt">
                                <h1>Personalized <span class="d-block">Products</span></h1>
                                <p class="mb-4">Custom products that reflect your unique style, for personal use or gifts. Let us bring your ideas to life.</p>
                                <p><a href="../products/shop.php" class="btn btn-secondary me-2">Shop Now</a></p>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="hero-img-wrap">
                                <img src="../../includes/images/pngtre.png" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Slide 2 -->
        <div class="carousel-item">
            <div class="hero">
                <div class="container px-5">
                    <div class="row justify-content-between align-items-start">
                        <div class="col-lg-5">
                            <div class="intro-excerpt">
                                <h1>Custom <span class="d-block">Engravings</span></h1>
                                <p class="mb-4">Add a personal touch to your items with custom engravings. Perfect for gifts and special occasions.</p>
								<p><a href="../products/shop.php" class="btn btn-secondary me-2">Shop Now</a></p>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="hero-img-wrap ml-5">
                                <img src="../../includes/images/slider2.png" class="img-fluid w-75">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Slide 3 -->
        <div class="carousel-item">
            <div class="hero">
                <div class="container px-5">
                    <div class="row justify-content-between">
                        <div class="col-lg-5">
                            <div class="intro-excerpt">
                                <h1>Exclusive <span class="d-block">Designs</span></h1>
                                <p class="mb-4">Discover our exclusive range of custom designs for phone cases, shirts, hoodies, and more.</p>
								<p><a href="../products/shop.php" class="btn btn-secondary me-2">Shop Now</a></p>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="hero-img-wrap">
                                <img src="../../includes/images/slider3.png" class="img-fluid w-75">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev " type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<!-- End Hero Section -->


		<!-- Start Product Section -->
		<div class="product-section">
    <div class="container">
        <div class="row">
            <!-- Start Column 1 -->
            <div class="col-md-12 col-lg-3 mb-5 mb-lg-0">
                <h2 class="mb-4 section-title">New Arrivals</h2>
                <p class="mb-4">Discover our latest products</p>
                <p><a href="../products/shop.php" class="btn">Explore</a></p>
            </div> 
            <!-- End Column 1 -->

            <!-- Dynamically generate product columns -->
            <?php
            if (!empty($products)) {
                foreach ($products as $product) {
                    echo '<div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
                            <a class="product-item" href="../../admin/product/product_details.php?id='.$product["id"].'">
                                <img src="../../admin/product/uploads/product_images/'.$product["image"].'" class="img-fluid product-thumbnail">
                                <h3 class="product-title">'.$product["name"].'</h3>
                                <strong class="product-price">JOD '.$product["price"].'</strong>
                                <span class="icon-cross">
                                    <img src="../../includes/images/cross.svg" class="img-fluid">
                                </span>
                            </a>
                          </div>';
                }
            } else {
                echo "<p>No new arrivals.</p>";
            }
            ?>
        </div>
    </div>
</div>



		<!-- End Product Section -->

		<!-- Start Why Choose Us Section -->
		<div class="why-choose-section">
			<div class="container">
				<div class="row justify-content-between">
					<div class="col-lg-6">
						<h2 class="section-title">Why Choose Us</h2>
						<p>Custom products that reflect your unique style, for personal use or gifts. Let us bring your ideas to life.</p>

						<div class="row my-5">
							<div class="col-6 col-md-6">
								<div class="feature">
									<div class="icon">
										<img src="../../includes/images/truck.svg" alt="Image" class="imf-fluid">
									</div>
									<h3>Fast &amp; Free Shipping</h3>
									<p>Receive your custom products quickly with no shipping cost.</p>
								</div>
							</div>

							<div class="col-6 col-md-6">
								<div class="feature">
									<div class="icon">
										<img src="../../includes/images/bag.svg" alt="Image" class="imf-fluid">
									</div>
									<h3>Easy to Shop</h3>
									<p>Navigate our user-friendly website with ease.</p>
								</div>
							</div>

							<div class="col-6 col-md-6">
								<div class="feature">
									<div class="icon">
										<img src="../../includes/images/support.svg" alt="Image" class="imf-fluid">
									</div>
									<h3>Premium Quality</h3>
									<p>Top-notch products made with the finest materials.</p>
								</div>
							</div>

							<div class="col-6 col-md-6">
								<div class="feature">
									<div class="icon">
										<img src="../../includes/images/return.svg" alt="Image" class="imf-fluid">
									</div>
									<h3>Hassle Free Returns</h3>
									<p>Easily return items without any stress.</p>
								</div>
							</div>

						</div>
					</div>

					<div class="col-lg-5">
						<div class="img-wrap">
							<img src="../../includes/images/front.jpg" alt="Image" class="img-fluid">
						</div>
					</div>

				</div>
			</div>
		</div>
		<!-- End Why Choose Us Section -->

		<!-- Start We Help Section -->
		<div class="we-help-section" id="services">
			<div class="container">
				<div class="row justify-content-between">
					<div class="col-lg-7 mb-5 mb-lg-0">
						<div class="imgs-grid">
							<div class="grid grid-1"><img src="../../includes/images/woman.jpg" alt="Untree.co"></div>
							<div class="grid grid-2"><img src="../../includes/images/xx.jpg" alt="Untree.co"></div>
							<div class="grid grid-3"><img src="../../includes/images/pretty.jpg" alt="Untree.co"></div>
						</div>
					</div>
					<div class="col-lg-5 ps-lg-5">
						<h2 class="section-title mb-4">We Help You Create Personalized Products</h2>
						<p>At our store, we offer custom products that reflect your unique style. Whether it's personalized phone cases, custom-printed shirts, hoodies, or engraved gifts, we bring your ideas to life. Let us help you create something special.</p>

						<ul class="list-unstyled custom-list my-4">
							<li>Unique Customization.</li>
							<li>Wide Range of Products.</li>
							<li>Top-notch Quality.</li>
							<li>Perfect for Gifts.</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- End We Help Section -->

	
		<!-- Start Testimonial Slider -->
		<!-- <div class="testimonial-section">
			<div class="container">
				<div class="row">
					<div class="col-lg-7 mx-auto text-center">
						<h2 class="section-title">Testimonials</h2>
					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-lg-12">
						<div class="testimonial-slider-wrap text-center">

							<div id="testimonial-nav">
								<span class="prev" data-controls="prev"><span class="fa fa-chevron-left"></span></span>
								<span class="next" data-controls="next"><span class="fa fa-chevron-right"></span></span>
							</div>

							<div class="testimonial-slider">
								
								<div class="item">
									<div class="row justify-content-center">
										<div class="col-lg-8 mx-auto">

											<div class="testimonial-block text-center">
												<blockquote class="mb-5">
													<p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Custom products that reflect your unique style, for personal use or gifts. Let us bring your ideas to life. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
												</blockquote>

												<div class="author-info">
													<div class="author-pic">
														<img src="../../includes/images/person-1.png" alt="Maria Jones" class="img-fluid">
													</div>
													<h3 class="font-weight-bold">Maria Jones</h3>
													<span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
												</div>
											</div>

										</div>
									</div>
								</div> 
								 END item -->

								<!-- <div class="item">
									<div class="row justify-content-center">
										<div class="col-lg-8 mx-auto">

											<div class="testimonial-block text-center">
												<blockquote class="mb-5">
													<p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Custom products that reflect your unique style, for personal use or gifts. Let us bring your ideas to life. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
												</blockquote>

												<div class="author-info">
													<div class="author-pic">
														<img src="../../includes/images/person-1.png" alt="Maria Jones" class="img-fluid">
													</div>
													<h3 class="font-weight-bold">Maria Jones</h3>
													<span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
												</div>
											</div>

										</div>
									</div>
								</div>  -->
								<!-- END item -->

								<!-- <div class="item">
									<div class="row justify-content-center">
										<div class="col-lg-8 mx-auto">

											<div class="testimonial-block text-center">
												<blockquote class="mb-5">
													<p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Custom products that reflect your unique style, for personal use or gifts. Let us bring your ideas to life. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
												</blockquote>

												<div class="author-info">
													<div class="author-pic">
														<img src="../../includes/images/person-1.png" alt="Maria Jones" class="img-fluid">
													</div>
													<h3 class="font-weight-bold">Maria Jones</h3>
													<span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
												</div>
											</div>

										</div>
									</div>
								</div>  -->
								<!-- END item -->
<!-- 
							</div>

						</div>
					</div>
				</div>
			</div>
		</div> --> 
		<!-- End Testimonial Slider -->

		<!-- Start Footer Section -->
		<?php include("../../includes/footer/index.php"); ?>
		<!-- End Footer Section -->	



		<script src="../../includes/js/bootstrap.bundle.min.js"></script>
		<script src="../../includes/js/tiny-slider.js"></script>
		<script src="../../includes/js/custom.js"></script>
	</body>

</html>
