<?php
session_start();
include 'Config.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');

$parkedVehicleId = isset($_GET['parkedVehicleId']) ? intval($_GET['parkedVehicleId']) : 0;

if ($parkedVehicleId > 0) {
    $stmt = $conn->prepare("SELECT * FROM ParkedVehicles WHERE id = ?");
    $stmt->bind_param("i", $parkedVehicleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['additional_time'])) {
    $additionalTime = intval($_POST['additional_time']);
    $updateStmt = $conn->prepare("UPDATE ParkedVehicles SET departure_time = DATE_ADD(departure_time, INTERVAL ? MINUTE) WHERE id = ?");
    $updateStmt->bind_param("ii", $additionalTime, $parkedVehicleId);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        echo "<script>alert('Parking duration extended successfully!'); window.location = 'index.php';</script>";
    } else {
        echo "<script>alert('Failed to extend parking duration.'); location.href = 'extendParkingDuration.php?parkedVehicleId=$parkedVehicleId';</script>";
    }
}
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
    <?php if (!empty($vehicle)): ?>
            <form method="post">
                <div>
                    <label for="current_time">Current Departure Time:</label>
                    <input type="text" id="current_time" value="<?= date("Y-m-d H:i:s", strtotime($vehicle['departure_time'])) ?>" disabled>
                </div>
                <div>
                    <label for="additional_time">Extend Time By (minutes):</label>
                    <input type="number" id="additional_time" name="additional_time" min="1" max="120" required>
                </div>
                <div>
                    <button type="submit" class="btn btn-info">Extend Time</button>
                </div>
            </form>
        <?php else: ?>
            <p>No parking data available. Please check if the ID is correct or contact support.</p>
        <?php endif; ?>
    </div>
    <div class="col-md-6 mt-5 overflow-auto" id=LocationDiv>
    </div>
  </div>
</div>




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

