<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'Config.php'; // Database and email configuration settings
date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
$response=[];
function sendEmailNotification($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;  // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pucs2023@gmail.com'; // Update this
        $mail->Password = 'oqmmjpnxokljalct'; // Update this
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('pucs2023@gmail.com', 'Smart Parking System');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();
        //echo "Email successfully sent to $to.\n";
    } catch (Exception $e) {
        //echo "Email could not be sent. Mailer Error: " . $mail->ErrorInfo . "\n";
    }
}

function checkAndSendEmails($conn) {
    $conn->autocommit(FALSE); // Turn off autocommit mode

  
        // Process each type of reminder and notification
        $response[] = processReminders($conn, 20, 'Departure Reminder', 'Your parking time will expire in 20 minutes. Please prepare to leave.', 'reminder_sent');
        $response[] = processReminders($conn, 5, 'Last Call Before Departure', 'You have 5 minutes remaining before your parking time expires.', 'last_call_sent');
        $response[] = processFinalCalls($conn);

}

function processReminders($conn, $minutes, $subject, $message, $flagColumn) {
    $endMinute = $minutes + 1; // Calculate this once
    $sql = "SELECT id, owner_email, slot_number, parking_place_id, departure_time
            FROM ParkedVehicles
            WHERE departure_time BETWEEN DATE_ADD(NOW(), INTERVAL ? MINUTE) AND DATE_ADD(NOW(), INTERVAL ? MINUTE)
            AND $flagColumn = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $minutes, $endMinute);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $conn->begin_transaction();
        $updateSql = "UPDATE ParkedVehicles SET $flagColumn = 1 WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $id = $row['id']; // Explicitly store in a variable to avoid reference issue
        $updateStmt->bind_param("i", $id);
        $updateStmt->execute();
        if ($updateStmt->affected_rows > 0) {
            $conn->commit();
            sendEmailNotification($row['owner_email'], $subject, $message);
        } else {
            $conn->rollback();
        }
        $updateStmt->close();
    }
    $stmt->close(); // Close the SELECT statement
    return json_encode(['success' => false, 'message' => 'processReminders']);
}


function processFinalCalls($conn) {
    $sql = "SELECT id, owner_email, slot_number, parking_place_id, departure_time
            FROM ParkedVehicles
            WHERE departure_time <= NOW() AND final_call_sent = 0";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception('Database query failed: ' . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        $conn->begin_transaction();
        try {
            $updateSql = "UPDATE ParkedVehicles SET final_call_sent = 1 WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $row['id']);
            $updateStmt->execute();

            if ($updateStmt->affected_rows > 0) {
                sendEmailNotification(
                    $row['owner_email'],
                    'Departure Time Alert',
                    'Your parking time has now expired. Please vacate the slot immediately.'
                );

                // Optionally update slot status if necessary
                $updateSlotStatusSql = "UPDATE SlotStatus SET Status = 3 WHERE parking_place_id = ? AND slot_number = ?";
                $updateSlotStatusStmt = $conn->prepare($updateSlotStatusSql);
                $updateSlotStatusStmt->bind_param('ii', $row['parking_place_id'], $row['slot_number']);
                $updateSlotStatusStmt->execute();
                $updateSlotStatusStmt->close();

                $conn->commit();
            } else {
                throw new Exception('No update occurred for final call - rollback initiated');
            }
        } catch (Exception $e) {
            $conn->rollback();
            throw $e; // Re-throw the exception to be handled by the caller
        }

        $updateStmt->close();
    }
    return json_encode(['success' => false, 'message' => 'processFinalCalls']);
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
     checkAndSendEmails($conn);
     echo json_encode($response);
      
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$conn->close();
?>
