<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();
include 'Config.php'; // Database configuration file

// Check if the token is in the query string
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die('Token is required.');
}
$name = '';
$token = $_GET['token'];

// Prepare a statement to retrieve the user based on the token
$stmt = $conn->prepare("SELECT id, username FROM admin WHERE password_reset_token = ? AND token_expiration > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

// Check if token is valid
if ($result->num_rows == 0) {
    die('Invalid or expired token.');
}

$user = $result->fetch_assoc();

// Process the form when it is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'], $_POST['confirm_password'])) {
    if ($_POST['password'] === $_POST['confirm_password']) {
        $newPassword = $_POST['password']; // Get the new password from the form
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the new password
        $name = $user['username'];
        // Prepare a statement to update the user's password
        $updateStmt = $conn->prepare("UPDATE admin SET password_hash = ? WHERE id = ?");
        $updateStmt->bind_param("si", $passwordHash, $user['id']);
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            echo "<script>alert('Your password has been updated successfully.'); window.location = 'login.php';</script>";
        } else {
            echo "<script>alert('Failed to update your password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container h-100 mt-5">
        <div class="row justify-content-center align-items-center h-100 mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                    <h2 class="text-center mb-4">Hai, <?php echo $user['username']; ?> </h2>
                        <h5 class="text-center mb-4">Reset Your Password</h5>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password:</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
