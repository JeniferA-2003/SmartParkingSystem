<?php
// Include database connection
include_once 'Config.php';

// Set content type to JSON
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;  // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = "smartparkingservicespy@gmail.com"; // Update this
        $mail->Password = "bchwntcukghicmhk"; // Update this
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom("smartparkingservicespy@gmail.com", 'Smart Parking System');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();
        //echo "Email successfully sent.\n";
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: " . $mail->ErrorInfo . "\n";
    }
}
// Create response array
$response = array();

// Check if data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode JSON data from the ESP32
    $data = json_decode(file_get_contents("php://input"));

    // Extract data from JSON
    $tokenID = $data->tokenID;
    $slotNumber = $data->slotNumber;
    $status = $data->status;

    // Prepare and execute query to get parking place ID from token ID
    $sql = "SELECT id FROM ParkingPlaces WHERE token_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tokenID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if parking place with given token ID exists
    // Check if parking place with given token ID exists
    // if ($result->num_rows > 0) {
    //     $row = $result->fetch_assoc();
    //     $parkingPlaceID = $row['id'];
    
    //     // Prepare and execute query to insert or update slot status into SlotStatus table
    //     $sql = "INSERT INTO SlotStatus (parking_place_id, slot_number, status) VALUES (?, ?, ?)
    //             ON DUPLICATE KEY UPDATE status = VALUES(status)";
    //     $stmt = $conn->prepare($sql);
    //     $stmt->bind_param("iii", $parkingPlaceID, $slotNumber, $status);
    
    //     if ($stmt->execute()) {
    //         // Slot status inserted or updated successfully
    //         $response['success'] = true;
    //         $response['message'] = "Slot status " . (($stmt->affected_rows > 0) ? "inserted" : "updated") . " successfully!";
    //     } else {
    //         // Error inserting or updating slot status
    //         $response['success'] = false;
    //         $response['message'] = "Error: " . $conn->error;
    //     }
    // }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $parkingPlaceID = $row['id'];

        // Check if a row with the given combination already exists in SlotStatus table
        $sql = "SELECT * FROM SlotStatus WHERE parking_place_id = ? AND slot_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $parkingPlaceID, $slotNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Determine the desired new status before updating
            if ($status == 0) {
                // Check if there are pending reservations in SlotQuery for the same slot
                $queryCheckSql = "SELECT id, owner_email FROM SlotQuery WHERE parking_place_id = ? AND slot_number = ? AND arrival_time > NOW()";
                $queryCheckStmt = $conn->prepare($queryCheckSql);
                $queryCheckStmt->bind_param("ii", $parkingPlaceID, $slotNumber);
                $queryCheckStmt->execute();
                $queryResult = $queryCheckStmt->get_result();
                if ($queryResult->num_rows > 0) {
                    $reservation = $queryResult->fetch_assoc();

                    // Reservation pending, update status to indicate registration required (2)
                    $status = 2;
                    $email =$reservation['owner_email'];
                    // echo $email;
                    sendEmail($email, "Your Reserved Slot is Now Ready", "Your parking slot is now available and ready for you. Please arrive as per your reservation schedule.");
                
                }

                    // No reservations pending, proceed to delete parked vehicle if status is being set to 0
                    $deleteSql = "DELETE FROM ParkedVehicles WHERE parking_place_id = ? AND slot_number = ?";
                    $deleteStmt = $conn->prepare($deleteSql);
                    $deleteStmt->bind_param("ii", $parkingPlaceID, $slotNumber);
                    $deleteStmt->execute();
            }
        
            // Update the SlotStatus with the new or confirmed status
            $sql = "UPDATE SlotStatus SET status = ?, timestamp = CURRENT_TIMESTAMP WHERE parking_place_id = ? AND slot_number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $status, $parkingPlaceID, $slotNumber);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Slot status updated successfully!";
                if (isset($deleteStmt) && $deleteStmt->affected_rows > 0) {
                    $response['message'] .= " Parked vehicle data deleted successfully.";
                }
            } else {
                $response['success'] = false;
                $response['message'] = "Error updating slot status: " . $conn->error;
            }
        } else {
            // Row doesn't exist, insert a new row
            $sql = "INSERT INTO SlotStatus (parking_place_id, slot_number, status) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $parkingPlaceID, $slotNumber, $status);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Slot status inserted successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = "Error inserting slot status: " . $conn->error;
            }
        }
    }else {
        // Parking place with given token ID not found
        $response['success'] = false;
        $response['message'] = "Error: Parking place not found!";
    }
} else {
    // Invalid request method
    $response['success'] = false;
    $response['message'] = "Error: Invalid request method!";
}

// Encode response array to JSON and output
echo json_encode($response);
?>
