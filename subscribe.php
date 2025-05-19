<?php
// Start the session and include database configuration
session_start();
include 'Config.php'; // Make sure this path is correct

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subscribe'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    // Validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address.'); window.history.go(-1);</script>";
    } else {
        // Prepare SQL to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO Subscribers (email) VALUES (?)");
        $stmt->bind_param("s", $email);

        // Execute the query
        if ($stmt->execute()) {
            echo "<script>alert('Subscription successful! Thank you for subscribing.'); window.location.href='index.php';</script>";
        } else {
            // Check for duplicate entry
                echo "<script>alert('You have already subscribed with this email address.'); window.history.go(-1);</script>";
        }

        // Close the statement
        $stmt->close();
    }

    // Close the connection
    $conn->close();
}
?>
