<?php
session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session
header("Location: register.html"); // Or index.php if that’s your landing page
exit;
?>
