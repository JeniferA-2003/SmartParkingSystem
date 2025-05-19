<?php
session_start();
include 'Config.php';  // Ensure the database connection file is included

$parkingPlaceId = isset($_GET['parking_place_id']) ? $_GET['parking_place_id'] : '';
$slotNumber = isset($_GET['slot_number']) ? $_GET['slot_number'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$statusText = ""; // Initialize the status text variable

// Determine the status description based on the value
switch($status) {
    case 1:
        $statusText = "Occupied";
        break;
    case 2:
        $statusText = "Reserved";
        break;
    case 3:
        $statusText = "<u><b>Blocked</b></u>";
        break;
    default:
        $statusText = "Unknown"; // Default case if the status is neither 1 nor 2
}
// Prepare statements to fetch data from both tables
$parkedVehiclesQuery = "SELECT * FROM ParkedVehicles WHERE parking_place_id = ? AND slot_number = ?";
$slotQueryQuery = "SELECT * FROM SlotQuery WHERE parking_place_id = ? AND slot_number = ?";

// Fetch parked vehicle details
$stmtParked = $conn->prepare($parkedVehiclesQuery);
$stmtParked->bind_param("ii", $parkingPlaceId, $slotNumber);
$stmtParked->execute();
$parkedVehicleDetails = $stmtParked->get_result();
$parkedVehicleDetails = $parkedVehicleDetails->num_rows > 0 ? $parkedVehicleDetails->fetch_assoc() : null;

// Fetch slot query details
$stmtSlotQuery = $conn->prepare($slotQueryQuery);
$stmtSlotQuery->bind_param("ii", $parkingPlaceId, $slotNumber);
$stmtSlotQuery->execute();
$resultSlotQuery = $stmtSlotQuery->get_result();
$slotQueryDetails = $resultSlotQuery->num_rows > 0 ? $resultSlotQuery->fetch_assoc() : null;

?>

<!DOCTYPE html>
<html lang="en">
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

  <!-- why section -->
    <div class="container mt-5">
        
        <h2> <a href="javascript:history.back();" style="text-decoration: none; color: inherit;"><i class="fas fa-arrow-left"> </i> </a>Slot Details</h2>
        <?php if ($parkedVehicleDetails): ?>
            <div class="card">
                <div class="card-header">
                   Parked Details for Slot Number: <?= htmlspecialchars($slotNumber) ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($parkedVehicleDetails['owner_name'] ?? 'N/A') ?></h5>
                    <p class="card-text">
                        <strong>Vehicle Number:</strong> <?= htmlspecialchars($parkedVehicleDetails['vehicle_number'] ?? 'N/A') ?><br>
                        <strong>Owner Email:</strong> <?= htmlspecialchars($parkedVehicleDetails['owner_email'] ?? 'N/A') ?><br>
                        <strong>Status:</strong> <?= $statusText ?><br>
                        <strong>Arrival Time:</strong> <?= isset($parkedVehicleDetails['arrival_time']) ? date("Y-m-d H:i:s", strtotime($parkedVehicleDetails['arrival_time'])) : 'N/A' ?><br>
                        <strong>Departure Time:</strong> <?= isset($parkedVehicleDetails['departure_time']) ? date("Y-m-d H:i:s", strtotime($parkedVehicleDetails['departure_time'])) : 'N/A' ?>
                    </p>
                </div>
            </div>
            <?php elseif ($slotQueryDetails): ?>
            <div class="card">
                <div class="card-header">
                    Reservation Details for Slot Number: <?= htmlspecialchars($slotNumber) ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($slotQueryDetails['owner_name'] ?? 'N/A') ?></h5>
                    <p class="card-text">
                        <strong>Vehicle Number:</strong> <?= htmlspecialchars($slotQueryDetails['vehicle_number'] ?? 'N/A') ?><br>
                        <strong>Owner Email:</strong> <?= htmlspecialchars($slotQueryDetails['owner_email'] ?? 'N/A') ?><br>
                        <strong>Status:</strong> <?= htmlspecialchars($statusText) ?><br>
                        <strong>Arrival Time:</strong> <?= isset($slotQueryDetails['arrival_time']) ? date("Y-m-d H:i:s", strtotime($slotQueryDetails['arrival_time'])) : 'N/A' ?><br>
                        <strong>Departure Time:</strong> <?= isset($slotQueryDetails['departure_time']) ? date("Y-m-d H:i:s", strtotime($slotQueryDetails['departure_time'])) : 'N/A' ?>
                    </p>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                No vehicle details found for the specified slot.
            </div>
        <?php endif; ?>
    </div>

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
