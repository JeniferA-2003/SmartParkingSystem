<?php
session_start();
include 'Config.php'; // Ensure database connection is correctly included

if (!isset($_SESSION['mobile_number'])) {
    echo json_encode(['error' => 'No mobile number found in session.']);
    exit;
}

$mobile_number = $_SESSION['mobile_number'];

// Query to fetch parking details near the departure time
$stmt = $conn->prepare("SELECT 
                        pv.*,
                        ss.status,
                        pp.place_name  -- Assuming 'place_name' is the column containing the parking place names
                        FROM 
                        ParkedVehicles pv
                        INNER JOIN 
                        SlotStatus ss ON pv.parking_place_id = ss.parking_place_id AND pv.slot_number = ss.slot_number
                        INNER JOIN 
                        ParkingPlaces pp ON pv.parking_place_id = pp.id  -- Joining with ParkingPlaces table
                        WHERE 
                        pv.owner_mobile = ?
                        AND (
                            pv.departure_time BETWEEN DATE_SUB(NOW(), INTERVAL 10 MINUTE) AND NOW()
                            OR
                            pv.departure_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 25 MINUTE)
                        )
                        AND DATE(pv.departure_time) = CURDATE();
                        ");

if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare the statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $mobile_number);
$stmt->execute();
$result = $stmt->get_result();
$rows = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows); // Encode the whole array as JSON
} 

$stmt->close();
$conn->close();
?>
