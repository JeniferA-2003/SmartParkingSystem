<?php
// Database connection
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "SmartParking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    date_default_timezone_set('Asia/Kolkata');
}
$gmail = "smartparkingservicespy@gmail.com";
$gpass = "bchwntcukghicmhk";
$host = "localhost/SmartParking";
?>