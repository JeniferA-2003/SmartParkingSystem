<?php
// Include database connection
include_once 'Config.php';
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
<div class="col-md-6" style="min-height:500px;">
        <h2><a href="index.php" style="text-decoration: none; color: inherit;"><i class="fas fa-arrow-left"> </i> </a>Check Slots</h2>
        <form>
        <label for='parking_place_id'>Select Parking Place:</label>
        <select class="form-control" id='parking_place_id' name='parking_place_id' required> 
        <?php
            // Fetch available parking places
            $sql = "SELECT id, place_name, total_slots, location FROM ParkingPlaces";
            $result = $conn->query($sql);

            // Check if there are available parking places
            if ($result->num_rows > 0) {
                echo "<option value=''>select</option>";
                while ($row = $result->fetch_assoc()) {
                    // Add data attributes to store additional data
                    echo "<option value='" . $row['id'] . "' data-total-slots='" . $row['total_slots'] . "' data-location='" . $row['location'] . "'>" . $row['place_name'] . "</option>";
                }
            } else {
                echo "No parking places available.";
            }
        ?>
            </select><br>
        </form>


            <div id="parking_lot" class="d-flex justify-content-center">
            <div id="available_slots_div" >
                <label for="slot_number"></label>
                <div class="ml-0"id="slot_containers" style="display: flex; flex-wrap: wrap;"></div>
            </div>
        </div>

        
            </div>  
            <div id="selected_place" class="col-md-6 mt-5 overflow-auto"></div>
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
    <script>
     $(document).ready(function() {
    $('#available_slots_div').hide();

    // Function to fetch available slots
    function fetchAvailableSlots() {
        var parkingPlaceId = $('#parking_place_id').val();
        var totalSlots = $('#parking_place_id').find(':selected').data('total-slots');

        $.ajax({
            url: 'getAvailableSlots.php',
            type: 'POST',
            data: {
                parking_place_id: parkingPlaceId,
                total_slots: totalSlots
            },
            success: function(response) {
                $('#available_slots_div').show();
                $('#slot_containers').html(response); // Update HTML with received data
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    // Bind change event to fetch slots whenever the parking place selection changes
    $('#parking_place_id').change(function() {
        fetchAvailableSlots();
    });

    // Fetch slots immediately on initial load
    fetchAvailableSlots();

    // Optionally, set an interval to refresh available slots if needed
    var interval = setInterval(fetchAvailableSlots, 500);

    // Ensure to clear interval when changing selections to prevent memory leaks
    $('#parking_place_id').on('change', function() {
        clearInterval(interval);
        interval = setInterval(fetchAvailableSlots, 500);
    });
});



        const slots = [
  { available: true },
  { available: false },
  // ... add more slots
];

const slotContainers = document.getElementById("slot_containers");

slots.forEach(slot => {
  const slotElement = document.createElement("div");
  slotElement.classList.add("slot");
  slotElement.textContent = slot.available ? "Available" : "Unavailable";
  slotElement.classList.add(slot.available ? "available" : "unavailable");
  slotContainers.appendChild(slotElement);
});
    </script>
<script>
    document.getElementById('parking_place_id').addEventListener('change', function() {
        // Get the selected option element
        var selectedOption = this.options[this.selectedIndex];
        
        // Get the value of the data-location attribute
        var location = selectedOption.getAttribute('data-location');
        
        // Update the content of the selected_place div with the location
        document.getElementById('selected_place').innerHTML = location;
    });
</script>

</body>

</html>