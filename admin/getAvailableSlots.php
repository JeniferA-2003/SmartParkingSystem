<?php
// Include database connection
include_once 'Config.php';

if (isset($_POST['parking_place_id']) && isset($_POST['total_slots'])) {
    $parkingPlaceId = $_POST['parking_place_id'];
    $totalSlots = $_POST['total_slots'];

    // Fetch available slots for the selected parking place
    $sql = "SELECT slot_number, status FROM SlotStatus WHERE parking_place_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $parkingPlaceId);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();

    // Initialize an array to store the available slots
    $availableSlots = [];

    // Store available slots in the array
    while ($row = $result->fetch_assoc()) {
        $availableSlots[$row['slot_number']] = $row['status'];
    }

    // Close statement
    $stmt->close();

    // Generate HTML containers for each slot
    for ($i = 1; $i <= $totalSlots; $i++) {
        // Check if the slot is available
        $status = isset($availableSlots[$i]) ? $availableSlots[$i] : -1; // Default status for unavailable slots
        // Set the container color based on availability
        $containerColor = ($status == 0) ? '#14C534' :   // Green for available
                        (($status == 1) ? '#FF5758' : // Red for occupied
                        (($status == 2) ? 'orange' :  // Orange for reserved
                        (($status == 3) ? 'black' : 'lightgray'))); // Dark gray for status 3, light gray for others
        // Output HTML for the container
        echo "<div class='slot-container' id='slot-$i' style='background-color: $containerColor;text-align:center;'>Slot<br>$i</div>";
        
    }
}

// Close database connection
$conn->close();
?>
<script>
    $(document).ready(function(){
        $('.slot-container').click(function(){
            var slotId = $(this).attr('id');
            var slotNumber = slotId.split('-')[1];
            var status = '<?php echo json_encode($availableSlots); ?>';
            status = JSON.parse(status)[slotNumber];
            
            if (status == 0) {
                alert("Slot " + slotNumber + " is empty.");
            } else if (status == 1) {
                var parkingPlaceId = <?php echo $parkingPlaceId; ?>;
                window.location.href = "slotDetails.php?parking_place_id=" + parkingPlaceId + "&slot_number=" + slotNumber +"&status=" + status;
                
            } else if (status == 2) {

                var parkingPlaceId = <?php echo $parkingPlaceId; ?>;
                window.location.href = "slotDetails.php?parking_place_id=" + parkingPlaceId + "&slot_number=" + slotNumber +"&status=" + status;
            }  else if (status == 3) {

            var parkingPlaceId = <?php echo $parkingPlaceId; ?>;
            window.location.href = "slotDetails.php?parking_place_id=" + parkingPlaceId + "&slot_number=" + slotNumber +"&status=" + status;
            }else {
                alert("Slot " + slotNumber + " data not avaliable.");
            }
        });
    });
</script>
