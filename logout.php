<?php
// Start the session to be able to unset and destroy it
session_start();

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the registration page
header("Location: register.html");

exit;
?>