<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once "vendor/autoload.php";

/**
 * Sanitize user input to prevent XSS attacks.
 * @param string $input
 * @return string
 */
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, "UTF-8");
}

function sendVerificationEmail($email, $name, $code)
{
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = function ($str, $level) {
            error_log("PHPMailer debug: $str");
        };



        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kingshostelmgt@gmail.com';
        $mail->Password = 'fnuzctkvhqqdafjk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom('kingshostelmgt@gmail.com', 'Kings Hostel Management');
        $mail->addAddress($email, $name);


        // HTML Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email - Kings Hostel';

        $mail->Body = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Email Verification</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                    color: #333;
                }
                .container {
                    max-width: 600px;
                    margin: 20px auto;
                    background-color: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                }
                .header {
                    background-color:  #986886;
                    padding: 20px;
                    text-align: center;
                    color: #ffffff;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                }
                .content {
                    padding: 30px;
                    text-align: center;
                }
                .content h2 {
                    font-size: 20px;
                    margin-bottom: 20px;
                    color: #986886;
                }
                .content p {
                    font-size: 16px;
                    line-height: 1.5;
                    margin: 0 0 20px;
                }
                .code {
                    display: inline-block;
                    background-color: #f0f0f0;
                    padding: 10px 20px;
                    font-size: 24px;
                    font-weight: bold;
                    letter-spacing: 2px;
                    border-radius: 5px;
                    margin: 20px 0;
                    color: #333;
                }
                .button {
                    display: inline-block;
                    padding: 12px 25px;
                    background-color: #986886;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 5px;
                    font-size: 16px;
                    font-weight: bold;
                }
                
                .footer {
                    background-color: #f9f9f9;
                    padding: 20px;
                    text-align: center;
                    font-size: 14px;
                    color: #777;
                }
                .footer a {
                    color: #986886;
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Welcome to Kings Hostel</h1>
                </div>
                <div class="content">
                    <h2>Email Verification</h2>
                    <p>Hello ' . htmlspecialchars($name) . ',</p>
                    <p>Thank you for signing up with Kings Hostel! To complete your registration, please use the verification code below:</p>
                    <div class="code">' . htmlspecialchars($code) . '</div>
                    <p>This code will expire in 30 minutes. Enter it on the verification page to activate your account.</p>
                    <p>Best regards,<br>Kings Hostel Team</p>
                </div>
                <div class="footer">
                    <p>If you didn’t request this, please ignore this email.</p>
                    <p>&copy; ' . date('Y') . ' Kings Hostel. All rights reserved. | <a href="http://yourdomain.com">Visit Us</a></p>
                </div>
            </div>
        </body>
        </html>';

        // Plain text fallback for non-HTML email clients
        $mail->AltBody = "Hello {$name},\n\nThank you for signing up with Kings Hostel. Please use this code to verify your email:\n\nVerification Code: {$code}\n\nThis code expires in 30 minutes.\n\nBest regards,\nKings Hostel Team";

        $result = $mail->send();
        error_log("Email send attempt result: " . ($result ? "Success" : "Failed"));
        echo "Email sent successfully! {$result}";
        return $result;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        error_log("Failed to send verification email to $email: {$mail->ErrorInfo}");
        return false;
    }
}
