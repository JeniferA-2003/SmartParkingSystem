<?php
// Include database connection
include_once 'Config.php';


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
if(isset($_POST['parking_place_id'], $_POST['slot_number'], $_POST['vehicle_number'], $_POST['owner_name'], $_POST['owner_mobile'], $_POST['arrival_time'])) {
    $parking_place_id = $_POST['parking_place_id'];
    $slot_number = $_POST['slot_number'];
    $vehicle_number = $_POST['vehicle_number'];
    $owner_name = $_POST['owner_name'];
    $owner_mobile = $_POST['owner_mobile'];
    $arrival_time = $_POST['arrival_time'];
    // Get parking duration from form input
    $parking_duration_hours = intval($_POST['parking_duration_hours']); // Hours input field
    $parking_duration_minutes = intval($_POST['parking_duration_minutes']); // Minutes input field

    // Calculate total duration in minutes
    $total_minutes = ($parking_duration_hours * 60) + $parking_duration_minutes;

    // Convert total minutes to a time format (HH:MM:SS)
    $parking_duration_time = gmdate("H:i:s", $total_minutes * 60); 

    // Insert query into database
    $sql = "INSERT INTO SlotQuery (parking_place_id, slot_number, vehicle_number, owner_name, owner_mobile, arrival_time, parking_duration) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $parking_place_id, $slot_number, $vehicle_number, $owner_name, $owner_mobile, $arrival_time, $parking_duration_time);


    if ($stmt->execute()) {
        echo "Query submitted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close statement and database connection
    $stmt->close();
    
}
else {
    // Print an error message indicating required parameters are missing
    echo "<script>alert('Error: Required parameters are missing.');</script>";
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
  <div class="col-md-8">
        <h2><a href="javascript:history.back();" style="text-decoration: none; color: inherit;"><i class="fas fa-arrow-left"></i></a>Slot Reservation</h2>
        <form action="reserveSlot.php" method="post">
            
            <?php
            if(isset($_GET['parking_place_id']) && isset($_GET['slot_number'])){
                echo '<input type="text" id="parking_place_id" name="parking_place_id" value="'.$_GET['parking_place_id'].'" required hidden>';
                echo '<input type="text" id="slot_number" name="slot_number" value="'.$_GET['slot_number'].'" required hidden>';
            }
            ?>

            <label for="vehicle_number">Vehicle Number:</label>
            <input type="text" id="vehicle_number" name="vehicle_number" required><br>

            <label for="owner_name">Owner Name:</label>
            <input type="text" id="owner_name" name="owner_name" required><br>

            <label for="owner_mobile">Owner Mobile:</label>
            <input type="text" id="owner_mobile" name="owner_mobile" required><br>
            <label for="arrival_time">Arrival Time:</label>
            <input type="datetime-local" id="arrival_time" name="arrival_time" required><br>

            <label for="parking_duration_hours">Parking Duration (Hours):</label>
            <input type="number" id="parking_duration_hours" name="parking_duration_hours" min="0" required><br>

            <label for="parking_duration_minutes">Parking Duration (Minutes):</label>
            <input type="number" id="parking_duration_minutes" name="parking_duration_minutes" min="0" max="59" required><br>

            <button class="btn btn-info" type="submit">Submit</button>
        </form>
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


</body>

</html>