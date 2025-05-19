<?php

require("Config.php");
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $place_name = $_POST['place_name'];
    $address = $_POST['address'];
    $total_slots = $_POST['total_slots'];
    $location = $_POST['location'];

    // Generate token ID
    $hexChars = bin2hex(random_bytes(8)); // 16-character hexadecimal string
    $upperCaseChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $specialChars = "!@#$%^&*_+<>?";
    $allChars = $hexChars . $upperCaseChars . $specialChars;

    // Shuffle and select the first 16 characters
    $token_id = substr(str_shuffle($allChars), 0, 16);


    
    // Insert data into ParkingPlaces table
    $sql = "INSERT INTO ParkingPlaces (place_name, address, total_slots, token_id, location) VALUES ('$place_name', '$address', '$total_slots', '$token_id', '$location')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Parking place added successfully!');</script>";
        echo "<script>window.location.href = 'addPlace.php';</script>";
        exit; // Ensure that no further PHP code is executed
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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

  <!-- nice selecy -->
  <link rel="stylesheet" href="css/nice-select.min.css">

  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

  <!-- why section -->

  <div class="container">
    <div class="row">
    <div class="col-md-6">
        <h2><a href="index.php" style="text-decoration: none; color: inherit;"><i class="fas fa-arrow-left"> </i> </a>Add Parking Place</h2>
        <form id="parkingForm" action="addPlace.php" method="post">
            <label for="place_name">Place Name:</label><br>
            <input type="text" id="place_name" name="place_name" required><br><br>

            <label for="address">Address:</label><br>
            <input type="text" id="address" name="address" required><br><br>

            <label for="total_slots">Total Slots:</label><br>
            <input type="number" id="total_slots" name="total_slots" required><br><br>

            <label for="location">Location:</label><br>
            <textarea class="col-12" id="location" name="location" required></textarea><br><br>

            <button class="btn btn-info" type="submit">Add Parking Place</button>
        </form>
    </div>
    <div class="col-md-6 mt-5 overflow-auto" id=LocationDiv>
    </div>
  </div>
</div>


<script>
        document.getElementById('location').addEventListener('input', function() {
    // Get the value from the textarea
    var locationText = this.value;
    var safePattern = /^<iframe src="https:\/\/www\.google\.com\/maps\/embed\?[^"]+" width="\d+" height="\d+" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"><\/iframe>$/;

    // Check if the input matches the safe pattern
    if (safePattern.test(locationText)) {
        // Update the div's content safely
        document.getElementById('LocationDiv').innerHTML = locationText;
    } else {
        // Clear the div or handle as necessary
        document.getElementById('LocationDiv').innerHTML = "<img src='images/wrongLocation.png' alt='Wrong Location' width='90%'>";
    }
});

    </script>

  <!-- end why section -->

  <!-- info section -->
  <?php include("include/footer.php");?>
  <!-- footer section -->

  <!-- jQery -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <!-- popper js -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
  </script>
  <!-- nice select -->
  <script src="js/jquery.nice-select.min.js"></script>
  <!-- bootstrap js -->
  <script src="js/bootstrap.js"></script>
  <!-- owl slider -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js">
  </script>
  <!-- custom js -->
  <script src="js/custom.js"></script>


</body>

</html>