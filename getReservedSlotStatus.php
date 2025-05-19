<?php
session_start();
include 'Config.php'; // Ensure database connection is correctly included

$mobile_number = $_SESSION['mobile_number'] ?? null;
if ($mobile_number) {
    $stmt = $conn->prepare("SELECT 
                            sq.*, 
                            ss.status,
                            pp.place_name  -- Assuming 'place_name' is the column containing the parking place names
                        FROM 
                            SlotQuery sq
                        INNER JOIN 
                            SlotStatus ss 
                            ON sq.parking_place_id = ss.parking_place_id 
                            AND sq.slot_number = ss.slot_number
                        INNER JOIN 
                            ParkingPlaces pp 
                            ON sq.parking_place_id = pp.id  -- Joining with ParkingPlaces table to get the place name
                        WHERE 
                            sq.owner_mobile = ?
                            AND sq.arrival_time > DATE_SUB(NOW(), INTERVAL 20 MINUTE)
                            AND DATE(sq.arrival_time) = CURDATE()
                            AND (
                                ss.status IN (0, 2)
                                OR 
                                (ss.status = 1 AND ss.timestamp BETWEEN DATE_SUB(sq.arrival_time, INTERVAL 10 MINUTE) AND DATE_ADD(sq.arrival_time, INTERVAL 20 MINUTE))
                            )");
    $stmt->bind_param("s", $mobile_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    } 
    echo json_encode($rows); // Encode the whole array as JSON
    
}else{
    echo null;
} 
?>
