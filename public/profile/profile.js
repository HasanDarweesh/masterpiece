document
  .getElementById("profile-form")
  .addEventListener("submit", function (e) {
    if (!validateProfileForm()) {
      e.preventDefault();
    }
  });

document
  .getElementById("password-form")
  .addEventListener("submit", function (e) {
    if (!validatePasswordForm()) {
      e.preventDefault();
    }
  });

function validateProfileForm() {
  let isValid = true;

  if (!validateName()) isValid = false;
  if (!validateEmail()) isValid = false;
  if (!validatePhone()) isValid = false;
  if (!validateLocation()) isValid = false;
  if (!validatePasswordsMatch()) isValid = false;

  return isValid;
}

function validatePasswordForm() {
  let isValid = true;

  // Validate all fields in password form
  if (!validateNewPassword()) isValid = false;
  if (!validatePasswordsMatch()) isValid = false;

  return isValid;
}

function validateName() {
  const nameField = document.getElementById("nameField");
  const nameError = document.getElementById("nameError");
  const nameId = nameField.parentElement;

  const nameRegex = /^[A-Za-z\s]+$/;
  if (!nameRegex.test(nameField.value.trim())) {
    nameError.textContent = "Name should contain only letters and spaces.";
    return false;
  }
  nameError.textContent = "";
  return true;
}

function validateEmail() {
  const emailField = document.getElementById("emailField");
  const emailError = document.getElementById("emailError");
  const emailId = emailField.parentElement;

  const emailRegex = /^[^\s@]+@[^\s@]+.[^\s@]+$/;
  if (!emailRegex.test(emailField.value.trim())) {
    emailError.textContent = "Enter a valid email address.";
    return false;
  }
  emailError.textContent = "";
  return true;
}

function validatePhone() {
  const phoneField = document.getElementById("phoneField");
  const phoneError = document.getElementById("phoneError");
  const phoneId = phoneField.parentElement;

  const phoneRegex = /^[0-9]{8,15}$/;
  if (!phoneRegex.test(phoneField.value.trim())) {
    phoneError.textContent = "Phone should contain only numbers (8-15 digits).";
    return false;
  }
  phoneError.textContent = "";
  return true;
}

function validateLocation() {
  const locationField = document.getElementById("locationField");
  const locationError = document.getElementById("locationError");
  const locationId = locationField.parentElement;

  if (locationField.value.trim() === "") {
    locationError.textContent = "Location cannot be empty.";
    return false;
  }
  locationError.textContent = "";
  return true;
}

function validateNewPassword() {
  const newPasswordField = document.getElementById("newPassword");
  const newPasswordError = document.getElementById("newPasswordError");
  const newPasswordId = newPasswordField.parentElement;

  const passwordRegex = /^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_]).{8,}$/;
  if (newPasswordField.value && !passwordRegex.test(newPasswordField.value)) {
    newPasswordError.textContent =
      "Password must be at least 8 characters, contain an uppercase letter, a lowercase letter, a number, and a special character.";
    return false;
  }
  newPasswordError.textContent = "";
  return true;
}

function validatePasswordsMatch() {
  const password = document.getElementById("newPassword");
  const confirmPassword = document.getElementById("repeatNewPassword");
  const passwordError = document.getElementById("repeatNewPasswordError");
  const fPasswordId = password.parentElement;

  if (password.value !== confirmPassword.value) {
    passwordError.textContent = "Passwords do not match!";
    return false;
  }
  passwordError.textContent = "";
  return true;
}
function previewFile() {
  console.log("File selected");
}
