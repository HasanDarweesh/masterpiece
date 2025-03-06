<?php
require_once "../../includes/database/config.php";
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Untree.co">
  <link rel="shortcut icon" href="favicon.png">

  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap4" />

		<!-- Bootstrap CSS -->
		<link href="../../public/furni-ed/css/bootstrap.min.css" rel="stylesheet">

		
		<link href="../../public/furni-ed/css/style.css" rel="stylesheet">
		<link href="../../public/furni-ed/css/tiny-slider.css" rel="stylesheet">
		<link href="../../public/furni-ed/css/customlmg.css" rel="stylesheet">



		
		<title>View Details </title>
	</head>

	<body>

		<!-- Start Header/Navigation -->
		<nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" aria-label="Furni navigation bar">

			<div class="container">
				<a class="navbar-brand" href="index.html"><div><img src="img\EXCLUSIVE_VIP.png" width="290px" height="290px"></div><span>.</span></a>

				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni" aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarsFurni">
					<ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
						<li class="nav-item">
							<a class="nav-link" href="index.html">Home</a>
						</li>
						<li><a class="nav-link" href="shop.php">Shop</a></li>
						<li><a class="nav-link" href="about.html">About us</a></li>
						<li><a class="nav-link" href="services.html">Services</a></li>
						<li class="active"><a class="nav-link" href="blog.html">Blog</a></li>
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
								<h1>Blog</h1>
								<p class="mb-4">Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.</p>
								<p><a href="" class="btn btn-secondary me-2">Shop Now</a><a href="#" class="btn btn-white-outline">Explore</a></p>
							</div>
						</div>
						<div class="col-lg-7">
							<div class="hero-img-wrap">
								<img src="images/couch.png" class="img-fluid">
							</div>
						</div>
					</div>
				</div>
			</div>
		<!-- End Hero Section -->

		

	<!-- Start Blog Section -->
	<?php
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product): ?>
            <!-- Start Product Details Section -->
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-lg border-0">
                            <div class="row g-0">
                                <div class="col-md-6">
								<div class="product-image-container" style="position: relative;">

                                    <img src="/E_commerce_PHP/php_project_group_2/admin/product/uploads/product_images/
										<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                        class="img-fluid rounded-start w-100">
                                         <!-- added img customize  -->
										<img id="customOverlayImage" src="" style="position: absolute; top: 50px; left: 50px; width: 100px; height: auto; display: none;">
								</div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="card-body text-center">
                                        <h2 class="card-title text-primary"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                        <p class="card-text text-muted"><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        <h3 class="text-danger"><?php echo '$' . number_format($product['price'], 2); ?></h3>
										<!-- Product Customization Options -->
										<div class="mt-4">
											 <!-- Text Customization -->
											 <label for="customText">Customize Text:</label>
                                            <input type="text" id="customText" class="form-control mb-2" placeholder="Enter custom text" oninput="updateText()">
                                            <label for="textColor">Choose Text Color:</label>
                                            <input type="color" id="textColor" class="form-control mb-2" oninput="updateText()">
											
											</select>
                                            

													<div id="customTextOverlay" style="position: absolute; top: 50px; left: 50px; font-size: 24px; font-weight: bold; color: black; display: none;">
													Your Text
												</div>
												<img id="customOverlayImage" src="" style="position: absolute; top: 50px; left: 50px; width: 100px; height: auto; display: none;">
											</div>
                                            <!-- Add to Cart Button -->
                                            <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn btn-success btn-lg me-2 mt-4">Add to Cart</a>
                                            <a href="../../public/furni-ed/shop.php" class="btn btn-outline-secondary btn-lg mt-4">Back to Store</a>
                                        </div>
                                           
                                           
                                        </div>
										<script>
										

function updateText() {
    const customText = document.getElementById('customText').value;
    const textColor = document.getElementById('textColor').value;

    // Get the customTextOverlay element
    const textOverlay = document.getElementById('customTextOverlay');

    // If customTextOverlay doesn't exist, return
    if (!textOverlay) {
        console.error('customTextOverlay element not found');
        return;
    }

    // Set the text content and color
    textOverlay.textContent = customText;
    textOverlay.style.color = textColor;

    // Optionally, apply additional styling for positioning
    textOverlay.style.position = 'absolute';
    textOverlay.style.top = '50%';
    textOverlay.style.left = '50%';
    textOverlay.style.transform = 'translate(-50%, -50%)';
    textOverlay.style.fontSize = '24px';
    textOverlay.style.fontWeight = 'bold';
    textOverlay.style.textShadow = '2px 2px 4px rgba(0, 0, 0, 0.5)';

    // Display the text if it's not empty, hide it if it's empty
    if (customText.trim() !== "") {
        textOverlay.style.display = "block";
    } else {
        textOverlay.style.display = "none";
    }
}
// Function to update the custom text
function updateText() {
        const customText = document.getElementById('customText').value;
        const textColor = document.getElementById('textColor').value;

        // Get the container where the text will be displayed
        const productImageContainer = document.querySelector('.product-image-container'); // Replace this with your actual image container

        // Create or update the text element
        let productText = document.getElementById('productText');
        
        // If the text element doesn't exist, create one
        if (!productText) {
            productText = document.createElement('div');
            productText.id = 'productText';
            productImageContainer.appendChild(productText);
        }

        // Set the text content and color
        productText.textContent = customText;
        productText.style.color = textColor;

        // Optionally, apply additional styling
        productText.style.position = 'absolute';
        productText.style.top = '50%';
        productText.style.left = '50%';
        productText.style.transform = 'translate(-50%, -50%)';
        productText.style.fontSize = '24px';
        productText.style.fontWeight = 'bold';
        productText.style.textShadow = '2px 2px 4px rgba(0, 0, 0, 0.5)';
    }

	





</script>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Product Details Section -->
			
        <?php else: ?>
            <div class="text-center text-danger mt-5">
                <h4>Product does not exist.</h4>
            </div>
        <?php endif;
    } catch (PDOException $e) {
        echo '<div class="text-center text-danger mt-5"><h4>Data retrieval error: ' . $e->getMessage() . '</h4></div>';
    }
} else {
    echo '<div class="text-center text-danger mt-5"><h4>Invalid link.</h4></div>';
}

?>

		

		<!-- Start Testimonial Slider -->
		<div class="testimonial-section before-footer-section">
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
													<p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
												</blockquote>

												<div class="author-info">
													<div class="author-pic">
														<img src="images/person-1.png" alt="Maria Jones" class="img-fluid">
													</div>
													<h3 class="font-weight-bold">Maria Jones</h3>
													<span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
												</div>
											</div>

										</div>
									</div>
								</div> 
								<!-- END item -->

								<div class="item">
									<div class="row justify-content-center">
										<div class="col-lg-8 mx-auto">

											<div class="testimonial-block text-center">
												<blockquote class="mb-5">
													<p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
												</blockquote>

												<div class="author-info">
													<div class="author-pic">
														<img src="images/person-1.png" alt="Maria Jones" class="img-fluid">
													</div>
													<h3 class="font-weight-bold">Maria Jones</h3>
													<span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
												</div>
											</div>

										</div>
									</div>
								</div> 
								<!-- END item -->

								<div class="item">
									<div class="row justify-content-center">
										<div class="col-lg-8 mx-auto">

											<div class="testimonial-block text-center">
												<blockquote class="mb-5">
													<p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
												</blockquote>

												<div class="author-info">
													<div class="author-pic">
														<img src="images/person-1.png" alt="Maria Jones" class="img-fluid">
													</div>
													<h3 class="font-weight-bold">Maria Jones</h3>
													<span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
												</div>
											</div>

										</div>
									</div>
								</div> 
								<!-- END item -->

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Testimonial Slider -->

		

		 <!-- Start Footer Section -->
		 <?php include '../../includes/footer/index.php'; ?>

        <!-- End Footer Section -->	

		<script src="js/bootstrap.bundle.min.js"></script>
		<script src="js/tiny-slider.js"></script>
		<script src="js/custom.js"></script>
	
			
	</body>

</html>
