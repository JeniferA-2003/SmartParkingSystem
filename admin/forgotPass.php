<?php
session_start();
include 'Config.php';  // Your database connection file

error_reporting(E_ALL); // Report all types of errors including E_NOTICE and E_WARNING
ini_set('display_errors', '1');

date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Query to check the user
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        // User found
        $token = bin2hex(random_bytes(50));  // Generate a secure random token
        $expiry = date("Y-m-d H:i:s", time() + 3600);  // Token expiry time (1 hour)

        // Store token in database or send via email
        $updateToken = $conn->prepare("UPDATE admin SET password_reset_token = ?, token_expiration = ? WHERE email = ?");
        $updateToken->bind_param("sss", $token, $expiry, $email);
        $updateToken->execute();

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $gmail;  // Your Gmail address
            $mail->Password = $gpass;  // Your Gmail password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
            $mail->Port = 587;  // TCP port to connect to

            $mail->setFrom($gmail, 'Smart Parking System');
            $mail->addAddress($email);  // Add a recipient

            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = 'Reset Your Password';
            $mail->Body = '<html><body>';
            $mail->Body .= '<p>Please click on the button below to reset your password:</p>';
            $mail->Body .= '<table cellspacing="0" cellpadding="0"> <tr>';
            $mail->Body .= '<td align="center" width="300" height="40" bgcolor="#007BFF" style="border-radius: 5px; color: #ffffff; display: block;">';
            $mail->Body .= '<a href="http://'.$host.'/admin/reset_password.php?token=' . $token . '" style="font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; line-height:40px; width:100%; display:inline-block">Click to Reset Password</a>';
            $mail->Body .= '</td> </tr> </table>';
            $mail->Body .= '</body></html>';
            

            $mail->send();
            echo "<script>alert('Message has been sent');</script>";
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    } else {
        echo "<script>alert('Invalid email'); window.location = 'forgotPass.php';</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }
        .h-custom {
            height: calc(100% - 73px);
        }
        @media (max-width: 450px) {
            .h-custom {
                height: 100%;
            }
        }
    </style>
</head>
<body>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="images/draw2.webp" class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <!-- Form updated with method="POST" and action to a PHP script -->
                    <form  method="POST">
                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <label class="form-label" for="form3Example3">Email address</label>
                            <input type="email" id="form3Example3" name="email" class="form-control form-control-lg"
                                placeholder="Enter a valid email address" required>
                        </div>

                        

                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Checkbox -->
                            
                            <a href="login.php" class="text-body">already know your password? continue</a>
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-info btn-lg"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-light">
            <div class="text-dark mb-3 mb-md-0">
                © 2024 All Rights Reserved By Smart Parking System
            </div>
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.min.js"></script>
</body>
</html>
