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
   
    $conn->autocommit(FALSE); // Turn off autocommit mode

         $notifySql = "SELECT id, owner_email, slot_number, parking_place_id, arrival_time
                    FROM SlotQuery
                    WHERE arrival_time BETWEEN DATE_SUB(NOW(), INTERVAL 16 MINUTE) AND DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                    AND email_sent = 0";
            if ($notifyResult = $conn->query($notifySql)) {
                $sentReminderEmails = 0;
                if ($notifyResult->num_rows > 0) {
                    while ($row = $notifyResult->fetch_assoc()) {
                        $conn->begin_transaction(); // Start transaction

                        $updateSql = "UPDATE SlotQuery SET email_sent = 1 WHERE id = ?";
                        $updateStmt = $conn->prepare($updateSql);
                        $updateStmt->bind_param("i", $row['id']);
                        $updateStmt->execute();
                        if ($updateStmt->affected_rows > 0) {
                            $conn->commit(); // Commit the transaction
                             sendEmailNotification(
                                $row['owner_email'],
                                'Reminder: Upcoming Slot Cancellation',
                                'Your parking slot reservation will be cancelled in 5 minutes.'
                            );
                            $sentReminderEmails ++;
                            
                        } else {
                            $conn->rollback(); // Rollback on failure
                            
                        }
                        $updateStmt->close();
                    }
                }
                $responses['reminders'] = $sentReminderEmails;
            }

        // Deletion query: Delete entries that are past their deletion time
        $deleteSql = "SELECT id, owner_email, slot_number, parking_place_id, arrival_time 
                            FROM SlotQuery 
                            WHERE arrival_time < (NOW() - INTERVAL 20 MINUTE)";
                $deleteResult = $conn->query($deleteSql);

                if ($deleteResult) {
                    $conn->begin_transaction(); // Start transaction before any changes
                    $allOperationsSuccessful = true; // Assume all operations will be successful
                    $sentCanceledEmails = 0;
                    while ($row = $deleteResult->fetch_assoc()) {
                        // Update SlotStatus to make the slot available again
                        $updateStatusSql = "UPDATE SlotStatus SET status = 0 WHERE slot_number = ? AND parking_place_id = ?";
                        $updateStmt = $conn->prepare($updateStatusSql);
                        $updateStmt->bind_param('ii', $row['slot_number'], $row['parking_place_id']);
                        $updateStmt->execute();
                        $updateAffectedRows = $updateStmt->affected_rows; // Get affected rows before closing statement
                        $updateStmt->close();

                        // Delete the SlotQuery
                        $deleteQuerySql = "DELETE FROM SlotQuery WHERE id = ?";
                        $deleteStmt = $conn->prepare($deleteQuerySql);
                        $deleteStmt->bind_param('i', $row['id']);
                        $deleteStmt->execute();
                        $deleteAffectedRows = $deleteStmt->affected_rows; // Get affected rows before closing statement
                        $deleteStmt->close();
                        
                        if ($updateAffectedRows > 0 && $deleteAffectedRows > 0) {
                            // If all operations were successful, send email
                            $conn->commit();
                            sendEmailNotification(
                                $row['owner_email'],
                                'Notice: Slot Cancellation',
                                'Your parking slot reservation has been cancelled.'
                            );
                            $sentCanceledEmails ++;
                        } else {
                            $allOperationsSuccessful = false; // Mark as failure if any operation fails
                            $conn->rollback(); // Rollback on failure
                            break; // Exit loop on failure
                        }
                    }
                    $responses['canceled'] = $sentCanceledEmails;
                } 
    return $responses;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $response = checkAndSendEmails($conn);
    echo json_encode($response);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
$conn->close();
?>
