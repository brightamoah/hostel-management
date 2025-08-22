<?php
require_once __DIR__ . "/../../services/EmailService.php";
require_once __DIR__ . "/../../utils/functions.php";
class ContactController
{
    private $emailService;


    public function __construct()
    {
        $this->emailService = new EmailService();
    }

    public function sendContactForm()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $name = sanitizeInput($_POST['fullName']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phoneNumber']);
        $subject = sanitizeInput($_POST['subject']);
        $message = sanitizeInput($_POST['message']);

        // Validate input
        $missingFields = [];
        if (empty($name)) {
            $missingFields[] = 'name';
        }
        if (empty($email)) {
            $missingFields[] = 'email';
        }
        if (empty($subject)) {
            $missingFields[] = 'subject';
        }
        if (empty($message)) {
            $missingFields[] = 'message';
        }

        if (!empty($missingFields)) {
            http_response_code(400);
            echo json_encode([
                'error' => 'The following fields are required: ' . implode(', ', $missingFields)
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            return;
        }

        if ($phone && !preg_match('/^(0\d{9}|\+233\d{9})$/', $phone)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid phone number format']);
            return;
        }

        $adminEmail = 'kingshostelmgt@gmail.com';
        $emailSubject = "New Contact Form Submission: $subject";
        $emailBody = $this->buildContactEmailHTML($name, $email, $phone, $subject, $message);

        $result = $this->emailService->sendEmail(
            to: $adminEmail,
            subject: $emailSubject,
            body: $emailBody,

        );

        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Your message has been sent successfully. We will get back to you soon.'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to send message. Please try again later.',
                'details' => $result['message']
            ]);
        }
    }


    private function buildContactEmailHTML($name, $email, $phone, $subject, $message)
    {
        $phoneDisplay = $phone ? $phone : 'Not provided';

        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Contact Form Submission</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 20px;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #ffffff;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                }
                .header {
                    background: linear-gradient(135deg, #986886 0%, #b8859c 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .content {
                    padding: 30px;
                }
                .section {
                    margin-bottom: 25px;
                    padding: 20px;
                    background: #f8f9fc;
                    border-radius: 8px;
                    border-left: 4px solid #986886;
                }
                .section h3 {
                    margin: 0 0 15px 0;
                    color: #2c3e50;
                    font-size: 18px;
                }
                .contact-details {
                    display: grid;
                    gap: 12px;
                }
                .detail-row {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .detail-label {
                    font-weight: 600;
                    color: #2c3e50;
                    min-width: 80px;
                }
                .detail-value {
                    color: #34495e;
                }
                .message-content {
                    background: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    border: 1px solid #e8ecf4;
                    margin-top: 15px;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                }
                .footer {
                    background: #2c3e50;
                    color: #95a5a6;
                    text-align: center;
                    padding: 20px;
                    font-size: 14px;
                }
                .priority-badge {
                    display: inline-block;
                    background: #e74c3c;
                    color: white;
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                @media (max-width: 600px) {
                    .email-container {
                        margin: 10px;
                    }
                    .header, .content {
                        padding: 20px;
                    }
                    .detail-row {
                        flex-direction: column;
                        align-items: flex-start;
                        gap: 5px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>🏠 Kings Hostel Management</h1>
                    <p style='margin: 10px 0 0 0; opacity: 0.9;'>New Contact Form Submission</p>
                </div>
                
                <div class='content'>
                    <div class='section'>
                        <h3>📋 Contact Information</h3>
                        <div class='contact-details'>
                            <div class='detail-row'>
                                <span class='detail-label'>Name:</span>
                                <span class='detail-value'><strong>" . htmlspecialchars($name) . "</strong></span>
                            </div>
                            <div class='detail-row'>
                                <span class='detail-label'>Email:</span>
                                <span class='detail-value'><a href='mailto:" . htmlspecialchars($email) . "' style='color: #986886; text-decoration: none;'>" . htmlspecialchars($email) . "</a></span>
                            </div>
                            <div class='detail-row'>
                                <span class='detail-label'>Phone:</span>
                                <span class='detail-value'>" . htmlspecialchars($phoneDisplay) . "</span>
                            </div>
                            <div class='detail-row'>
                                <span class='detail-label'>Subject:</span>
                                <span class='detail-value'><strong>" . htmlspecialchars($subject) . "</strong></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class='section'>
                        <h3>💬 Message</h3>
                        <div class='message-content'>" . htmlspecialchars($message) . "</div>
                    </div>
                    
                    <div class='section' style='background: #fff3cd; border-left-color: #ffc107;'>
                        <h3 style='color: #856404;'>⚡ Action Required</h3>
                        <p style='margin: 0; color: #856404;'>
                            Please respond to this inquiry within 24 hours. You can reply directly to <strong>" . htmlspecialchars($email) . "</strong> or contact them at <strong>" . htmlspecialchars($phoneDisplay) . "</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
}


$contactController = new ContactController();
$contactController->sendContactForm();
