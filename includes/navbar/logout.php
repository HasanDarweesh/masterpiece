<?php
session_start();
session_destroy(); // Destroy the session
header("Location: ../../public/furni-ed/index.html"); // Redirect to login page
exit();
