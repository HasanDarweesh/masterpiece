<?php
function insertUser($name, $email, $password)
{
    global $pdo;

    try {
        // Check if the email is already registered
        $checkStmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->rowCount() > 0) {
            // echo "Email is already registered!";
            return false;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement with placeholders
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";

        // Execute the statement with the data array
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $hashedPassword]);

        return true;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
function checkUser($email, $password)
{
    global $pdo;

    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password and return user data if valid
        if ($user && password_verify($password, $user['password'])) {

            return $user;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
function setUserSession($user)
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
}
