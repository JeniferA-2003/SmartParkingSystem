
<?php
include 'include/auth.php';  // Include the authentication functions
checkLogin(); 

include 'Config.php'; 
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

  <title>ADMIN | SMART PARKING</title>


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
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

</head>

<body>

  <div class="hero_area">
    <div class="bg-box">
      <img src="images/slider-bg.jpg" alt="">
    </div>
    <!-- header section strats -->
    <?php include("include/header.php");?>
    <!-- end header section -->
    <!-- slider section -->
    <section class="slider_section ">
      <div class="container-fluid">
          <div class="row">
              <div class="detail-box col-md-9 mx-auto text-center mt-0">
                  <h1>
                      Welcome, <?php echo $_SESSION['username']; ?>
                  </h1>
              </div>
          </div>
          <br> <br> <br>
          <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="row">
                    <!-- Card 1 -->
                    <div class="col-md-4">
                      <div class="card bg-light">
                          <div class="card-img-top text-center p-4">
                          <i class="fa fa-map-marker fa-5x" aria-hidden="true"></i>
                          </div>
                          <?php
                            // Query to get the count of parking places
                            $query = "SELECT COUNT(*) AS count FROM ParkingPlaces";
                            $result = $conn->query($query);
                            if ($result) {
                                $row = $result->fetch_assoc();
                                $parkingPlaceCount = $row['count'];
                            } else {
                                $parkingPlaceCount = "Unavailable";
                            }
                            ?>
                          <div class="card-body text-center">
                              <h5 class="card-title">Manage Parking Places</h5>
                              <p class="card-text">Total Parking Places: <?php echo $parkingPlaceCount; ?></p>
                              <a href="manage_parking_places.php" class="btn btn-info">Manage Parking</a>
                          </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="card bg-light">
                          <div class="card-img-top text-center p-4">
                          <i class="fa fa-search fa-5x" aria-hidden="true"></i>
                          </div>
                          
                          <div class="card-body text-center">
                              <h5 class="card-title">Check slot status</h5>
                              <p class="card-text">One Parking Place has many slots</p>
                              <a href="checkSlots.php" class="btn btn-info">Check</a>
                          </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="card bg-light">
                          <div class="card-img-top text-center p-4">
                          <i class="fa fa-users fa-5x" aria-hidden="true"></i>
                          </div>
                          <?php
                            // Query to get the count of parking places
                            $query = "SELECT COUNT(*) AS count1 FROM admin";
                            $result = $conn->query($query);
                            if ($result) {
                                $row = $result->fetch_assoc();
                                $userCount = $row['count1'];
                            } else {
                                $puserCount = "Unavailable";
                            }
                            ?>
                          <div class="card-body text-center">
                              <h5 class="card-title">Manage Users</h5>
                              <p class="card-text">Total users:  <?php echo $userCount; ?></p>
                              <a href="manage_users.php" class="btn btn-info">Manage</a>
                          </div>
                      </div>
                    </div>

                    
                    
                </div>
            </div>
        </div>
      </div>
    </section>
    <!-- end slider section -->

   
  </div>

  <?php include("include/footer.php");?>
  <!-- jQery -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <!-- popper js -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js">
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