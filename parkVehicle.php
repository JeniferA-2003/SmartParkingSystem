
<?php
// Start session and include database configuration
session_start();
include_once 'Config.php'; // Assuming your database connection settings are in this file

// Check if slotquery_id is present in the query string
if (isset($_GET['slotquery_id'])) {
  $slotquery_id = $_GET['slotquery_id'];

  // Prepare and execute query to fetch details along with parking place name
  $sql = "SELECT sq.owner_name, sq.owner_mobile, sq.owner_email, sq.arrival_time, sq.slot_number, sq.parking_place_id, pp.place_name, pp.location 
          FROM SlotQuery sq
          JOIN ParkingPlaces pp ON sq.parking_place_id = pp.id
          WHERE sq.id = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $slotquery_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($row = $result->fetch_assoc()) {
      // Data for form population
      $owner_name = $row['owner_name'];
      $owner_mobile = $row['owner_mobile'];
      $owner_email = $row['owner_email'];
      $arrival_time = $row['arrival_time'];
      $slot_number = $row['slot_number'];
      $place_name = $row['place_name']; // Parking place name
      $parking_place_id = $row['parking_place_id'];
      $location = $row['location'];
  }
  $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve all form data
  $parking_place_id = isset($_POST['parking_place_id']) ? $_POST['parking_place_id'] : null;  // Validate this is the correct way
  $slotquery_id = $_POST['slotquery_id'];
  $owner_name = $_POST['owner_name'];
  $owner_mobile = $_POST['owner_mobile'];
  $owner_email = $_POST['owner_email'];
  $vehicle_number = $_POST['vehicle_number']; // Correct retrieval of the vehicle number
  $arrival_time = $_POST['arrival_time'];
  $place_name = $_POST['place_name'];
  $slot_number = $_POST['slot_number'];
  $duration_hours = $_POST['duration_hours'];
  $duration_minutes = $_POST['duration_minutes'];



  // Calculate departure time
$arrival_datetime = new DateTime($arrival_time);
$interval_spec = sprintf("PT%dH%dM", $duration_hours, $duration_minutes);
$interval = new DateInterval($interval_spec);
$departure_datetime = clone $arrival_datetime;
$departure_datetime->add($interval);
$departure_time = $departure_datetime->format('Y-m-d H:i:s');

// Format parking duration as a TIME string
$parking_duration = sprintf("%02d:%02d:00", $duration_hours, $duration_minutes);

try {
    // Begin transaction
    $conn->begin_transaction();

    // Insert into ParkedVehicles table
    $sqlInsert = "INSERT INTO ParkedVehicles (parking_place_id, slot_number, vehicle_number, owner_name, owner_mobile, owner_email, arrival_time, departure_time, parking_duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("iisssssss", $parking_place_id, $slot_number, $vehicle_number, $owner_name, $owner_mobile, $owner_email, $arrival_time, $departure_time, $parking_duration);
    $stmtInsert->execute();

    // Delete from SlotQuery table
    $sqlDelete = "DELETE FROM SlotQuery WHERE id = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $slotquery_id);
    $stmtDelete->execute();

    // Commit transaction
    $conn->commit();

    echo "<script>alert('Parking details submitted successfully.');</script>";
    
    $stmtInsert->close();
    $stmtDelete->close();
    echo "<script>window.location.href = 'index.php';</script>";
} catch (Exception $e) {
      // Rollback transaction if an error occurs
      $conn->rollback();
      echo "Error: " . $e->getMessage();
      echo "<script>window.location.href = 'index.php';</script>";
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
        <h2><a href="javascript:history.back();" style="text-decoration: none; color: inherit;"><i class="fas fa-arrow-left"></i></a>Park Vehicle</h2>
        <form method="post">
        <input type="hidden" name="slotquery_id" value="<?php echo htmlspecialchars($slotquery_id); ?>">
        <input type="hidden" name="parking_place_id" value="<?php echo htmlspecialchars($parking_place_id); ?>">

        <label for="owner_name">Owner Name:</label>
        <input type="text" id="owner_name" name="owner_name" value="<?php echo htmlspecialchars($owner_name); ?>" required><br><br>

        <label for="owner_mobile">Owner Mobile:</label>
        <input type="text" id="owner_mobile" name="owner_mobile" value="<?php echo htmlspecialchars($owner_mobile); ?>" required><br><br>

        <label for="owner_mobile">Owner Email:</label>
        <input type="text" id="owner_email" name="owner_email" value="<?php echo htmlspecialchars($owner_email); ?>" required><br><br>

        <label for="arrival_time">Arrival Time:</label>
        <input type="datetime-local" id="arrival_time" name="arrival_time" value="<?php echo htmlspecialchars($arrival_time); ?>" required><br><br>

        <label for="place_name">Parking Place:</label>
        <input type="text" id="place_name" name="place_name" value="<?php echo htmlspecialchars($place_name); ?>" readonly><br><br>

        <label for="slot_number">Slot Number:</label>
        <input type="text" id="slot_number" name="slot_number" value="<?php echo htmlspecialchars($slot_number); ?>" readonly><br><br>

        <label for="vehicle_number">Vehicle Number:</label>
        <input type="text" id="vehicle_number" name="vehicle_number" required><br><br>

        <label for="duration_hours">Expected Parking Duration:</label>
        <div class="input-group">
            <input type="number" id="duration_hours" name="duration_hours" class="form-control" placeholder="Hours" min="0" required>
            <input type="number" id="duration_minutes" name="duration_minutes" class="form-control" placeholder="Minutes" min="0" max="59" required>
        </div><br><br>


        <button class="btn btn-info" type="submit">Submit Parking Details</button>
    </form>
    </div>
    <div class="col-md-6 mt-5">
    <?php echo $location ?>
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