<?php
session_start();
include 'Config.php'; // Ensure the database connection file is included

// Security check: ensure the user is logged in or redirect
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if the 'id' GET parameter is set
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $parkingPlaceId = $_GET['id'];

    // Prepare a delete statement to remove the parking place
    $stmt = $conn->prepare("DELETE FROM ParkingPlaces WHERE id = ?");
    $stmt->bind_param("i", $parkingPlaceId);
    $stmt->execute();

    // Check if the delete was successful
    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Parking place deleted successfully.'); window.location.href = 'manage_parking_places.php';</script>";
    } else {
        echo "<script>alert('Failed to delete parking place or it does not exist.'); window.location.href = 'manage_parking_places.php';</script>";
    }

    $stmt->close();
} else {
    // Redirect if the ID parameter is not valid
    header('Location: manage_parking_places.php');
}

$conn->close();
?>
