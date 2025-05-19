<?php
session_start();
include 'Config.php'; // Ensure database connection file is included
error_reporting(E_ALL);
ini_set('display_errors', '1');

$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($userId > 0) {
    $stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully.'); window.location.href = 'manage_users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user.'); window.location.href = 'manage_users.php';</script>";
    }
} else {
    echo "<script>alert('Invalid user ID.'); window.location.href = 'manage_users.php';</script>";
}
?>
