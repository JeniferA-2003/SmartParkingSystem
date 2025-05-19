<?php
session_start();
include 'Config.php'; // Database connection file

// Fetch all parking places from the database
$query = "SELECT * FROM ParkingPlaces";
$result = $conn->query($query);
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
<div class="container mt-5">
        <h2 class="mb-4"> <a href="index.php" style="text-decoration: none; color: inherit;"><i class="fas fa-arrow-left"> </i> </a>Parking Places</h2>
        <table class="table table-bordered table-hover">
            <thead class="text-center">
                <tr>
                    <th>ID</th>
                    <th>Place Name</th>
                    <th>Address</th>
                    <th>Location</th>
                    <th>Total Slots</th>
                    <th>Token ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['place_name']}</td>";
                        echo "<td>{$row['address']}</td>";
                        echo "<td>{$row['location']}</td>";
                        echo "<td class='text-center'>{$row['total_slots']}</td>";
                        echo "<td>{$row['token_id']}</td>";
                        echo "<td>
                              <a href='edit_parking_place.php?id={$row['id']}' class='btn btn-info btn-sm' style='width: 80px;'><i class='fas fa-edit'></i> Edit</a><br><br>
                              <a href='delete_parking_place.php?id={$row['id']}' class='btn btn-warning btn-sm' style='width: 80px;' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fas fa-trash-alt'></i> Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No parking places found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
  <!-- end why section -->
  <script>
document.addEventListener("DOMContentLoaded", function() {
    // Select all iframe elements within table cells
    const iframes = document.querySelectorAll('td iframe');
    
    // Iterate over each iframe and adjust its dimensions
    iframes.forEach(iframe => {
        iframe.style.width = "300px"; // Set the width to 300 pixels
        iframe.style.height = "200px"; // Set the height to 200 pixels
    });
});
</script>

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