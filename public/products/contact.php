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
		<link href="../../includes/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
		<link href="../../includes/css/tiny-slider.css" rel="stylesheet">
		<link href="../../includes/css/style.css" rel="stylesheet">



		<title> Contact Us</title>
	</head>

	<body>

	
	<!-- Start Header/Navigation -->
	<?php include("../../includes/navbar/index.php") ?>
<!-- End Header/Navigation -->

<!-- Start Contact Form -->
<div >
  <div class="container">
    <div class="block">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-8 pb-4">
          <div class="row mb-5">
            <!-- Contact Information -->
            <div class="col-lg-4">
            </div>
          </div>
          <div class="container light-style flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-4">
            Contact us
        </h4>
		

          <form id="contactForm">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label class="text-black" for="fname">First name</label>
                  <input type="text" class="form-control" id="fname" required>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label class="text-black" for="lname">Last name</label>
                  <input type="text" class="form-control" id="lname" required>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="text-black" for="phone">Phone Number</label>
              <input type="tel" class="form-control" id="phone" required pattern="^[0-9]{10}$">
            </div>

            <div class="form-group">
              <label class="text-black" for="email">Email </label>
              <input type="email" class="form-control" id="email" required>
            </div>

            <div class="form-group">
              <label class="text-black" for="subject">Subject</label>
              <select class="form-control" id="subject" required>
                <option value="general">General Inquiry</option>
                <option value="support">Technical Support</option>
                <option value="feedback">Feedback</option>
              </select>
            </div>

            <div class="form-group mb-5">
              <label class="text-black" for="messages">Message</label>
              <textarea class="form-control" id="messages" cols="30" rows="5" required></textarea>
            </div>

            <br>
			<div id="message" style=" padding: 10px; margin-top: 10px; text-align: center; border-radius: 5px; font-weight: bold;"></div>

            <div class="d-flex">
            <button type="submit" class="btn btn-primary-hover-outline me-2">Send Message</button>
            <button type="reset" class="btn btn-secondary">Reset</button>


            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Contact Form -->


		<!-- Start Footer Section -->
    <?php include("../../includes/footer/index.php") ?>
		<!-- End Footer Section -->	
     
<!-- Include EmailJS Script -->
<script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js">
</script>
<script type="text/javascript">
   (function(){
      emailjs.init({
        publicKey: "0rLjSZVRBy6SWKEww",
      });
   })();
</script>
<script>
  // Initialize 
  emailjs.init("0rLjSZVRBy6SWKEww"); 

  document.getElementById("contactForm").addEventListener("submit", function(event) {
    event.preventDefault(); 

    // Validate email
    const email = document.getElementById("email").value;
    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!emailPattern.test(email)) {
      showMessage("Please enter a valid email address.", "error");
      return;
    }

    // Store form data in cookies
    document.cookie = "fname=" + encodeURIComponent(document.getElementById("fname").value) + "; path=/";
    document.cookie = "lname=" + encodeURIComponent(document.getElementById("lname").value) + "; path=/";
    document.cookie = "phone=" + encodeURIComponent(document.getElementById("phone").value) + "; path=/";
    document.cookie = "email=" + encodeURIComponent(email) + "; path=/";
    document.cookie = "subject=" + encodeURIComponent(document.getElementById("subject").value) + "; path=/";
    document.cookie = "message=" + encodeURIComponent(document.getElementById("messages").value) + "; path=/";

    const formData = {
      fname: document.getElementById("fname").value,
      lname: document.getElementById("lname").value,
      phone: document.getElementById("phone").value,
      email: email,
      subject: document.getElementById("subject").value,
      message: document.getElementById("messages").value
    };

    emailjs.send("service_c75o7uz", "template_0ucmf3f", formData)
      .then(function(response) {
        showMessage("Your message has been sent successfully!", "success");
      }, function(error) {
        showMessage("Error sending message. Please try again.", "error");
      });
});
window.onload = function() {
  // Retrieve cookies and populate the form
  const cookies = document.cookie.split("; ");
  cookies.forEach(cookie => {
    const [name, value] = cookie.split("=");
    switch (name) {
      case "fname":
        document.getElementById("fname").value = decodeURIComponent(value);
        break;
      case "lname":
        document.getElementById("lname").value = decodeURIComponent(value);
        break;
      case "phone":
        document.getElementById("phone").value = decodeURIComponent(value);
        break;
      case "email":
        document.getElementById("email").value = decodeURIComponent(value);
        break;
      case "subject":
        document.getElementById("subject").value = decodeURIComponent(value);
        break;
      case "message":
        document.getElementById("messages").value = decodeURIComponent(value);
        break;
    }
  });
};


  function showMessage(message, type) {
    var messageDiv = document.getElementById("message");
    messageDiv.style.display = "block";
    messageDiv.innerText = message;

    if (type === "success") {
      messageDiv.style.color = "green"; 
    } else {
      messageDiv.style.color = "red";   
    }
  }
</script>


  <!-- End Contact Form -->

		

		<!-- Start Footer Section -->
		
		<!-- End Footer Section -->	


		<script src="../../includes/js/bootstrap.bundle.min.js"></script>
		<script src="../../includes/js/tiny-slider.js"></script>
		<script src="../../includes/js/custom.js"></script>

	</body>

</html>
