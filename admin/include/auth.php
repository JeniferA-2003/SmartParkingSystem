<?php
session_start();  // Start the session at the beginning to use session variables

function checkLogin() {
    // Check if user ID exists in session
    if (!isset($_SESSION['user_id'])) {
        // If the session does not contain user ID, redirect to login page
        header("Location: login.php");
        exit();  // Stop further execution of the script
    }
    // Optionally, you can also check for user roles or permissions here
}

// You might want to add more functions related to authentication and user management here
?>