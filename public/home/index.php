<?php
session_start(); // Start the session
echo "Session variables: <br>";
echo "user Id: " .  $_SESSION['user_id'] . "<br>";
echo "name: " .  $_SESSION['name'] . "<br>";
echo "email: " .  $_SESSION['email'] . "<br>";
echo "user type: " .  $_SESSION['role'] . "<br>";
