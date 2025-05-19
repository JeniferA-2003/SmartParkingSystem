<?php
session_start();
include 'Config.php'; // Ensure database connection is correctly included
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Get the parking place ID from the query string
$parkingPlaceId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($parkingPlaceId > 0) {
    // Fetch existing data
    $stmt = $conn->prepare("SELECT * FROM ParkingPlaces WHERE id = ?");
    $stmt->bind_param("i", $parkingPlaceId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $place = $result->fetch_assoc();
    } else {
        die("<script>alert('No such parking place found.'); window.location = 'manage_parking_places.php';</script>");
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_name'], $_POST['address'], $_POST['total_slots'], $_POST['location'])) {
    $place_name = $_POST['place_name'];
    $address = $_POST['address'];
    $total_slots = $_POST['total_slots'];
    $location = $_POST['location'];

    // Update the parking place
    $updateSql = "UPDATE ParkingPlaces SET place_name = ?, address = ?, total_slots = ?, location = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssisi", $place_name, $address, $total_slots, $location, $parkingPlaceId);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        echo "<script>alert('Parking place updated successfully!'); window.location.href = 'manage_parking_places.php';</script>";
    } else {
        echo "<script>alert('No changes were made or update failed.');</script>";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

  <title>Paspark</title>


  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

  <!-- nice selecy -->
  <link rel="stylesheet" href="css/nice-select.min.css">

  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />

  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

</head>

<body class="sub_page">
<div class="hero_area">
    <div class="bg-box">
      <img src="images/slider-bg.jpg" alt="">
    </div>
    <!-- header section strats -->
    <?php include("include/header.php");?>
    <!-- end header section -->
    </div>

    <div class="container">
    <div class="row">
    <div class="col-md-6">
        <h2><a href="javascript:history.back();" style="text-decoration: none; color: inherit;"><i class="fas fa-arrow-left"> </i> </a> Edit Parking Place</h2>
        <form action="edit_parking_place.php?id=<?= $parkingPlaceId ?>" method="post">
            <div class="mb-3">
                <label for="place_name">Place Name:</label>
                <input type="text" id="place_name" name="place_name" required value="<?= htmlspecialchars($place['place_name']) ?>">
            </div>
            <div class="mb-3">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required value="<?= htmlspecialchars($place['address']) ?>">
            </div>
            <div class="mb-3">
                <label for="total_slots">Total Slots:</label>
                <input type="number" id="total_slots" name="total_slots" required value="<?= $place['total_slots'] ?>">
            </div>
            <div class="mb-3">
                <label for="address">Token:</label>
                <input type="text" id="token" name="token" required value="<?= htmlspecialchars($place['token_id']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="location">Location:</label>
                <textarea id="location" name="location" required style="width: 100%; height: 9em;"><?= htmlspecialchars($place['location']) ?></textarea>
            </div>
            <button class="btn btn-info">Update Parking Place Details</button>
        </form>
    </div>

    <div class="col-md-6 mt-5 overflow-auto" id=LocationDiv>
    </div>

    </div>
    </div>
    <script>

document.addEventListener('DOMContentLoaded', function() {
    var locationTextarea = document.getElementById('location');

    // Check initial value on page load
    checkLocation(locationTextarea.value);

    // Add an input event listener to the textarea to check content dynamically
    locationTextarea.addEventListener('input', function() {
        checkLocation(this.value);
    });
});

function checkLocation(locationText) {
    var safePattern = /^<iframe src="https:\/\/www\.google\.com\/maps\/embed\?[^"]+" width="\d+" height="\d+" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"><\/iframe>$/;

    // Check if the input matches the safe pattern
    if (safePattern.test(locationText)) {
        // Update the div's content safely
        document.getElementById('LocationDiv').innerHTML = locationText;
    } else {
        // Clear the div or handle as necessary
        document.getElementById('LocationDiv').innerHTML = "<img src='images/wrongLocation.png' alt='Wrong Location' width='90%'>";
    }
}


        document.getElementById('location').addEventListener('input', function() {
            var locationTextarea = document.getElementById('location');

// Check initial value on page load
checkLocation(locationTextarea.value);

// Add an input event listener to the textarea to check content dynamically
locationTextarea.addEventListener('input', function() {
    checkLocation(this.value);
});
});

    </script>

    <script src="js/bootstrap.js"></script>
      <!-- info section -->
  <?php include("include/footer.php");?>
  <!-- footer section -->
</body>




</html>
