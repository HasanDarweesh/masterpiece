<?php
session_start(); // Start the session

require "../../includes/database/config.php";
require "../../includes/functions/index.php";

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST['inputEmail']));
    $password = htmlspecialchars(trim($_POST['inputPassword']));

    // Call the checkUser function
    $user = checkUser($email, $password);

    if ($user) {
        // Store user info in session
        setUserSession($user);

        if ($user['role'] === 'superadmin' || $user['role'] === 'admin') {
            header("Location: ../../admin/home/index.php");
        } else {
            header("Location: ../../public/home/index.php");
        }
        exit();
        exit();
    } else {
        $errorMessage = "Invalid email or password.";
    }
}
?>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div class="wrapper">
        <form action="index.php" method="post">
            <h2>Login</h2>
            <div class="input-field">
                <input type="email" name="inputEmail" required />
                <label>Enter your email</label>
            </div>
            <div class="input-field">
                <input type="password" name="inputPassword" required autocomplete="off" />
                <label>Enter your password</label>
            </div>
            <?php if (isset($errorMessage)): ?>
                <div class="message error"><?= $errorMessage; ?></div>
            <?php endif; ?>
            <div class="forget">
                <label for="remember">
                    <input type="checkbox" id="remember" name="remember" />
                    <p>Remember me</p>
                </label>
                <a href="#">Forgot password?</a>
            </div>
            <button type="submit">Log In</button>
            <div class="register">
                <p>Don't have an account? <a href="../registration/index.php">Register</a></p>
            </div>
        </form>
    </div>
    <script src="script.js"></script>
</body>

</html>