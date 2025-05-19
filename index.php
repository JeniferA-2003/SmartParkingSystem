<?php
session_start();
// Include database connection
include_once 'Config.php';
error_reporting(E_ALL); // Report all types of errors including E_NOTICE and E_WARNING
ini_set('display_errors', '1'); // Ensure that errors are displayed to the output

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Capture form data
  $parking_place_id = $_POST['parking_place_id'];
  $name = $_POST['name'];
  $mobile_number = $_POST['mobile_number'];
  $email = $_POST['email'];
  $arrival_time = $_POST['arrival_time'];

  $_SESSION['name']=$name;
  $_SESSION['mobile_number'] = $mobile_number;
  $_SESSION['email'] = $email;
  // Get the available slot
  $result = getAvailableSlot($conn, $arrival_time, $parking_place_id);
  if ($result != null) {
      // Prepare and execute insert statement
      $stmt = $conn->prepare("INSERT INTO `SlotQuery` (`parking_place_id`, `slot_number`, `owner_name`, `owner_mobile`, `owner_email`, `arrival_time`) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("iissss", $parking_place_id, $result['slot_number'], $name, $mobile_number, $email, $arrival_time);

      if ($stmt->execute()) {
          
          echo "<script>alert('Slot reserved successfully!');</script>";
      } else {
          echo "<script>alert('Error executing reservation: " . $stmt->error . "');</script>";
      }
      $stmt->close();
  } else {
      echo "<script>alert('No slots available!');</script>";
  }
  echo "<script>window.location.href = 'index.php';</script>";
}

function getAvailableSlot($conn, $newArrivalTime, $parking_place_id) {
  // Check for slots that are about to be freed
  $sqlEx = "SELECT pv.*, ss.status
            FROM Parkedvehicles pv
            JOIN SlotStatus ss ON pv.parking_place_id = ss.parking_place_id AND pv.slot_number = ss.slot_number
            WHERE ss.status IN (0, 1) AND pv.departure_time < DATE_SUB(?, INTERVAL 15 MINUTE)
            ORDER BY ABS(TIMESTAMPDIFF(SECOND, pv.departure_time, ?)) ASC
            LIMIT 1";
  
  $stmtEx = $conn->prepare($sqlEx);
  $stmtEx->bind_param("ss", $newArrivalTime, $newArrivalTime);
  $stmtEx->execute();
  $resultEx = $stmtEx->get_result();
  
  if ($resultEx->num_rows > 0) {
      return $resultEx->fetch_assoc();
  } else {
      // Check for any free slots
      $sqlFree = "SELECT * FROM SlotStatus WHERE parking_place_id = ? AND status = 0 LIMIT 1";
      $stmtFree = $conn->prepare($sqlFree);
      $stmtFree->bind_param("i", $parking_place_id);
      $stmtFree->execute();
      $resultFree = $stmtFree->get_result();
      
      if ($resultFree->num_rows > 0) {
          $resultNew = $resultFree->fetch_assoc();
          $slotNumber = $resultNew['slot_number'];

          // Update the status to reserved if a free slot is found
          $sqlUpdate = "UPDATE SlotStatus SET status = 2 WHERE slot_number = ? AND parking_place_id = ?";
          $stmtUpdate = $conn->prepare($sqlUpdate);
          $stmtUpdate->bind_param("ii", $slotNumber, $parking_place_id);
          
          if ($stmtUpdate->execute()) {
              // Return the new slot data if update is successful
              $stmtUpdate->close();
              return $resultNew;
          } else {
              $stmtUpdate->close();
              return null;
          }
      } else {
          // No free slots found
          return null;
      }
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

  <title>SMART PARKING</title>


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
      <div class="container">
        <div class="detail-box col-md-9 mx-auto px-0">
          <h1>
            Finding Parking Lots Made Easy
          </h1>
          <p>
          Unlocking convenience one parking space at a time, smart parking systems pave the way for smoother journeys in bustling cities
          </p>
        </div>
        <div class="find_form_container">
          <form method="POST">
            <div class="row">
              
              <?php
              $SessionName = '';
              $SessionMobile_number = '';
              $SessionEmail = '';
              
              // Check if the session variables are set
              if (isset($_SESSION['name'])) {
                $SessionName = $_SESSION['name'];
              }
              if (isset($_SESSION['mobile_number'])) {
                $SessionMobile_number = $_SESSION['mobile_number'];
              }
              if (isset($_SESSION['email'])) {
                $SessionEmail = $_SESSION['email'];
              }

              ?>

              <div class="col-md-5 ">
                <div class="form-group ">
                  <label for="">Name</label>
                  <div class="input-group">
                    <input type="text" name="name" id="name" class="form-control" placeholder="Your Name" value="<?php echo htmlspecialchars($SessionName); ?>"/>
                  </div>
                </div>
              
                <div class="form-group">
                  <label for="">Mobile Number</label>
                  <div class="input-group ">
                    <input type="text" name="mobile_number" id="mobile_number" class="form-control" placeholder="Your Mobile Number" value="<?php echo htmlspecialchars($SessionMobile_number); ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label for="">Email</label>
                  <div class="input-group ">
                    <input type="text" name="email" id="email" class="form-control" placeholder="Your Mobile Email" value="<?php echo htmlspecialchars($SessionEmail); ?>" />
                  </div>
                </div>

              
                <div class="form-group" >
                  <label for="">Select Parking</label>
                  <div class="input-group">
                  <select id='parking_place_id' name='parking_place_id' class="form-control col-12" required>
                    <?php
                        // Include database connection
                        include_once 'Config.php';

                        // Fetch available parking places
                        $sql = "SELECT id, place_name FROM ParkingPlaces";
                        $result = $conn->query($sql);

                        // Check if there are available parking places
                        if ($result->num_rows > 0) {
                            echo "<option value=''>select</option>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . $row['place_name'] . "</option>";
                            }
                          
                        } else {
                            echo "No parking places available.";
                        }

                        // Close database connection
                        
                        ?>
                      </select>
            
                  </div>
                </div>
              
                  <div class="form-group">
                      <label for="arrival_time">Arrival Time</label>
                      <div class="input-group">
                          <input type="datetime-local" name="arrival_time" id="arrival_time" class="form-control" placeholder="Select your arrival time" />
                      </div>
                  </div>

                  <div class="form-group">
                 
                        <div class="btn-box">
                          
                    <button type="submit" class="">
                      <span>
                        Search Now
                      </span>
                    </button>
                  </div>
                  </div>
              </div>

              <div id="SlotStatusTitle" class="col-md-3 px-0 mt-0">
    <h3 class="ml-5 text-white" ></h3> <!-- Title for the column -->
    <div class="ml-5" id="slotStatusDisplay"></div>
              
              <?php
              $mobile_numbe1 = $_SESSION['mobile_number'] ?? null;
              if ($mobile_numbe1) {
                  ?>
              <script>
                  document.addEventListener('DOMContentLoaded', function() {
                      // Initially hide the title
                      const titleElement = document.getElementById('SlotStatusTitle'); // Ensure this ID matches your HTML
                      titleElement.style.display = 'none';

                      // Set interval to check every 5 seconds
                      setInterval(fetchSlotStatus, 500); // Adjusted to 5000 for 5 seconds

                      function fetchSlotStatus() {
                          fetch('getReservedSlotStatus.php') // This PHP script returns the slot status
                          .then(response => {
                              if (response.ok) {
                                  return response.json();
                              } else {
                                  throw new Error('Network response was not ok.');
                              }
                          })
                          .then(data => {
                              if (data.error) {
                                  document.getElementById('slotStatusDisplay').innerHTML = data.error;
                              } else {
                                  // Check if there are any slots returned
                                  if (data.length > 0) {
                                      titleElement.style.display = 'block'; // Show title if there are slots
                                  }

                                  let htmlContent = '';
                                  data.forEach(item => {
                                      htmlContent += `
                                          <div class="card" style="width: 18rem; margin-bottom: 20px;">
                                              <div class="card-body">
                                                  <h5 class="card-title">Slot: ${item.slot_number}</h5>
                                                  <h6>${item.place_name}</h6>
                                                  <p class="card-text">Your arrival time is: ${item.arrival_time}.</p>
                                                  <a href="parkVehicle.php?slotquery_id=${item.id}" class="btn btn-info">Park Vehicle</a>
                                              </div>
                                          </div>`;
                                  });
                                  document.getElementById('slotStatusDisplay').innerHTML = htmlContent;
                              }
                          })
                          .catch(error => {
                              console.error('Error:', error);
                             
                          });
                      }
                  });
              </script>

              <?php
              }
              ?>
              </div>
              <div id="timeExtensionTitle" class="col-md-3 ml-5" >
             
              <div class="ml-5" id="timeExtension"></div>
              
              <?php
              $mobile_numbe1 = $_SESSION['mobile_number'] ?? null;
              if ($mobile_numbe1) {
                  ?>
              <script>
                

                document.addEventListener('DOMContentLoaded', function() {

                 

                    setInterval(fetchSlotStatus, 500); // Check every 5 seconds

                    function fetchSlotStatus() {
                        fetch('getTimeExtension.php')  // This PHP script will return the slot status
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                document.getElementById('timeExtensionTitle').innerHTML = data.error;
                            } else {
                             


                                let htmlContent = '';
                                data.forEach(item => {
                                  htmlContent += `
                                        <div class="card" style="width: 18rem; margin-bottom: 20px; background-color:orange;">
                                            <div class="card-body">
                                                <h5 class="card-title">Slot ${item.slot_number}</h5>
                                                <h6>${item.place_name}</h6>
                                                <p class="card-text">Your departure time is: ${item.departure_time}.</p>
                                                <a href="extendParkingDuration.php?parkedVehicleId=${item.id}" class="btn btn-light">Extend!</a>
                                            </div>
                                        </div>`;

                                });
                                document.getElementById('timeExtensionTitle').innerHTML = htmlContent;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('timeExtension').innerHTML = 'Failed to load slot statuses. Please check the console for more information.';
                        });
                    }
                });
              </script>
              <?php
              }
              ?>
              </div>
              <!-- <div class="col-md-4 px-0">
                <div class="form-group">
                  <label for="duration">Duration (in minutes)</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="duration" placeholder="Enter duration">
                  </div>
                </div>
              </div> -->
            </div>
            
          </form>
        </div>
      </div>
    </section>
    <!-- end slider section -->

   
  </div>

  <!-- about section -->

  <section id="about"class="about_section layout_padding mt-3">
    <div class="container  ">
      <div class="heading_container ">
        <h2>
          About Us
        </h2>
        <p>
        simplifying urban mobility with intelligent solutions for seamless parking experiences
        </p>
      </div>
      <div class="row">
        <div class="col-lg-6 ">
          <div class="img-box">
            <img src="images/about-img.jpg" alt="">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="detail-box">
            <h3>
              We Are Here For Help
            </h3>
            <p>
            Our dedicated team at Smart Parking Solutions is committed to providing top-notch support and assistance to ensure your satisfaction and success with our smart parking system. Whether you have questions about installation, encounter challenges with operation, or need guidance on maximizing efficiency, we're always just a message or call away. Your parking needs are our priority, and we're here to help you every step of the way. Trust us to be your reliable partner in overcoming parking obstacles and achieving your goals of efficient and hassle-free parking
            </p>
            <p>
            With us, you're never alone – because We Are Here For Help
            </p>
            <a href="#about">
              Read More
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end about section -->

  <!-- why section -->

  <section id="why" class="why_section layout_padding-bottom mt-3">
    <div class="container">
      <div class="col-md-10 px-0">
        <div class="heading_container">
          <h2>
            Why Choose Us
          </h2>
          <p>
          "Choose us for smart parking solutions tailored to your needs and backed by expertise and reliability"
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box">
            <div class="img-box">
              <img src="images/w1.png" alt="">
            </div>
            <div class="detail-box">
              <h4>
                No Booking Fees
              </h4>
              <p>
              We prioritize transparency and simplicity in our services. With us, you can trust that there are no hidden fees or booking charges. Our commitment to clear and straightforward policies ensures you receive the best value for your needs.<br><br>
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box">
            <div class="img-box">
              <img src="images/w2.png" alt="" width="80" height="80">
            </div>
            <div class="detail-box">
              <h4>
              Wide Selection
              </h4>
              <p>
              Choose from a wide selection of parking options tailored to your needs. Whether you're looking for short-term or long-term parking, we have you covered with a variety of options to suit every preference. Experience hassle-free parking with us today!
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box ">
            <div class="img-box">
              <img src="images/w3.png" alt="">
            </div>
            <div class="detail-box">
              <h4>
                Simple Booking Process
              </h4>
              <p>
              Our user-friendly platform makes booking a parking spot a breeze. With just a few clicks, you can reserve your space hassle-free. Say goodbye to complicated booking processes and hello to convenience.<br><br><br>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end why section -->



  <!-- info section -->
<?php include("include/footer.php");?>
  <!-- footer section -->

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