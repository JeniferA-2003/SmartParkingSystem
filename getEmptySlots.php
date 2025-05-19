<?php
// Include database connection
include_once 'Config.php';

if (isset($_POST['parking_place_id'])) {
    $parkingPlaceId = $_POST['parking_place_id'];

    // Fetch available slots for the selected parking place
    $sql = "SELECT slot_number FROM SlotStatus WHERE parking_place_id = ? AND status = 0";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $parkingPlaceId);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();

    // Check if slots are available
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['slot_number'] . "'>" . $row['slot_number'] . "</option>";
        }
    } else {
        echo "<option value=''>No slots available for the selected parking place.</option>";
    }

    // Close statement
    $stmt->close();
}

// Close database connection
$conn->close();
?>
