document.querySelector("form").addEventListener("submit", function (event) {
  let isValid = true;

  // Get input values
  const email = document.querySelector("input[name='inputEmail']");
  const password = document.querySelector("input[name='inputPassword']");

  // Reset styles and titles
  [email, password].forEach((input) => {
    input.closest(".input-field").style.borderBottom = "2px solid #ccc";
    input.removeAttribute("title");
  });

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

  // Prevent form submission if invalid
  if (!isValid) {
    event.preventDefault();
  }
});
