document.querySelector("form").addEventListener("submit", function (event) {
  let isValid = true;

  // Get input values
  const firstName = document.querySelector("input[name='inputFirstName']");
  const lastName = document.querySelector("input[name='inputLastName']");
  const email = document.querySelector("input[name='inputEmail']");
  const password = document.querySelector("input[name='inputPassword']");
  const confirmPassword = document.querySelector(
    "input[name='inputConfirmPassword']"
  );

  // Reset styles and titles
  [firstName, lastName, email, password, confirmPassword].forEach((input) => {
    input.closest(".input-field").style.borderBottom = "2px solid #ccc";
    input.removeAttribute("title");
  });

  // Validate First Name (Only letters, hyphens, and apostrophes, 2 to 50 characters)
  const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ'-]{2,50}$/;
  if (!nameRegex.test(firstName.value)) {
    firstName.closest(".input-field").style.borderBottom = "2px solid red";
    firstName.setAttribute(
      "title",
      "Name must be 2 to 50 characters and can only contain letters, hyphens, and apostrophes."
    );
    isValid = false;
  }

  // Validate Last Name (Only letters, hyphens, and apostrophes, 2 to 50 characters)
  if (!nameRegex.test(lastName.value)) {
    lastName.closest(".input-field").style.borderBottom = "2px solid red";
    lastName.setAttribute(
      "title",
      "Name must be 2 to 50 characters and can only contain letters, hyphens, and apostrophes."
    );
    isValid = false;
  }

  // Validate Email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    email.closest(".input-field").style.borderBottom = "2px solid red";
    email.setAttribute("title", "Please enter a valid email address.");
    isValid = false;
  }

  // Validate Password (Minimum 8 characters, at least one uppercase and one number)
  const passwordRegex = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;
  if (!passwordRegex.test(password.value)) {
    password.closest(".input-field").style.borderBottom = "2px solid red";
    password.setAttribute(
      "title",
      "Password must be at least 8 characters, with at least one uppercase letter and one number."
    );
    isValid = false;
  }

  // Check if passwords match
  if (password.value !== confirmPassword.value) {
    confirmPassword.closest(".input-field").style.borderBottom =
      "2px solid red";
    confirmPassword.setAttribute("title", "Passwords do not match.");
    isValid = false;
  }

  // Prevent form submission if invalid
  if (!isValid) {
    event.preventDefault();
  }
});
