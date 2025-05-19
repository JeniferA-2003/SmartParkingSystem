<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'Config.php';
 // Assuming this file has correct DB and SMTP constants defined
 date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendEmailNotification($to, $subject, $message) {
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
        return "Email successfully sent to $to.\n";
    } catch (Exception $e) {
        return "Email could not be sent. Mailer Error: " . $mail->ErrorInfo . "\n";
    }
}

function checkAndSendEmails($conn) {
    $responses = [];
    $responses['reminders'] = processReminders($conn, 20, 'Departure Reminder', 'Your parking time will expire in 20 minutes. Please prepare to leave.', 'reminder_sent');
    $responses['last_calls'] = processReminders($conn, 5, 'Last Call Before Departure', 'You have 5 minutes remaining before your parking time expires.', 'last_call_sent');
    $responses['final_calls'] = processFinalCalls($conn);
    return $responses;
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
    $sentEmails = 0;

    while ($row = $result->fetch_assoc()) {
        $conn->begin_transaction();
        $updateSql = "UPDATE ParkedVehicles SET $flagColumn = 1 WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("i", $row['id']);
        $updateStmt->execute();
        if ($updateStmt->affected_rows > 0) {
            if (sendEmailNotification($row['owner_email'], $subject, $message)) {
                $sentEmails++;
                $conn->commit();
            } else {
                $conn->rollback();
            }
        } else {
            $conn->rollback();
        }
        $updateStmt->close();
    }
    $stmt->close(); // Close the SELECT statement
    return $sentEmails;
}

function processFinalCalls($conn) {
    $sql = "SELECT id, owner_email, slot_number, parking_place_id, departure_time
            FROM ParkedVehicles
            WHERE departure_time <= NOW() AND final_call_sent = 0";
    $result = $conn->query($sql);
    $finalCallsProcessed = 0;

    while ($row = $result->fetch_assoc()) {
        $conn->begin_transaction();
        $updateSql = "UPDATE ParkedVehicles SET final_call_sent = 1 WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("i", $row['id']);
        $updateStmt->execute();
        if ($updateStmt->affected_rows > 0) {
            if (sendEmailNotification($row['owner_email'], 'Departure Time Alert', 'Your parking time has now expired. Please vacate the slot immediately.')) {
                $finalCallsProcessed++;
                // Optionally update slot status if necessary
                $updateSlotStatusSql = "UPDATE SlotStatus SET Status = 3 WHERE parking_place_id = ? AND slot_number = ?";
                $updateSlotStatusStmt = $conn->prepare($updateSlotStatusSql);
                $updateSlotStatusStmt->bind_param('ii', $row['parking_place_id'], $row['slot_number']);
                $updateSlotStatusStmt->execute();
                $updateSlotStatusStmt->close();

                $conn->commit();
            } else {
                $conn->rollback();
            }
        } else {
            $conn->rollback();
        }
        $updateStmt->close();
    }
    return $finalCallsProcessed;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $response = checkAndSendEmails($conn);
    echo json_encode($response);
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
