<?php
session_start();
session_destroy(); // Destroy the session
header("Location: ../../public/home/index.php"); // Redirect to login page
exit();
