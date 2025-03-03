<?php
session_start(); // Start the session

require "../../includes/database/config.php";
require "../../includes/functions/index.php";

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $firstName = htmlspecialchars(trim($_POST['inputFirstName']));
    $lastName = htmlspecialchars(trim($_POST['inputLastName']));
    $email = htmlspecialchars(trim($_POST['inputEmail']));
    $password = htmlspecialchars(trim($_POST['inputPassword']));

    // Combine first name and last name for the 'name' field
    $name = $firstName . ' ' . $lastName;

    // Call the function to insert the user
    $isInserted = insertUser($name, $email, $password);

    if ($isInserted) {
        $user = checkUser($email, $password);
        if ($user) {
            // Store user info in session

            setUserSession($user);

            header("Location: ../home/index.php");
            exit();
        } else {
            $message = "Failed to log in after registration.";
        }
    } else {
        $message = "Failed to register user.";
    }
}


?>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>sign Up</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div class="wrapper">
        <form action="index.php" method="post">
            <h2>sign Up</h2>

            <div class="input-field">
                <input type="text" name="inputFirstName" required />
                <label>First Name</label>
            </div>

            <div class="input-field">
                <input type="text" name="inputLastName" required />
                <label>Last Name</label>
            </div>
            <div class="input-field">
                <input type="email" name="inputEmail" required />
                <label>Email</label>
            </div>
            <div class="input-field">
                <input type="password" name="inputPassword" required />
                <label>Password</label>
            </div>
            <div class="input-field">
                <input type="password" name="inputConfirmPassword" required />
                <label>Confirm Password</label>
            </div>
            <?php if (isset($message)): ?>
                <div class="message"><?= $message; ?></div>
            <?php endif; ?>
            <button type="submit">SingUp</button>
            <div class="register">
                <p>Do you have an account? <a href="../login/index.php">Login</a></p>
            </div>
        </form>
    </div>
    <script src="script.js"></script>
</body>

</html>