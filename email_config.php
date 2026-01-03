<?php


// Email settings
define('EMAIL_FROM', 'noreply@hrms.com');
define('EMAIL_FROM_NAME', 'HR Management System');
define('EMAIL_REPLY_TO', 'support@hrms.com');

/**
 * Send email function
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $message Email body (HTML)
 * @return bool Success status
 */
function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">" . "\r\n";
    $headers .= "Reply-To: " . EMAIL_REPLY_TO . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Get email template
 * 
 * @param string $title Email title
 * @param string $content Email content
 * @return string HTML email template
 */
function getEmailTemplate($title, $content) {
    return "
    <html>
    <head>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .container { 
                max-width: 600px; 
                margin: 20px auto; 
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .header { 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                color: white; 
                padding: 30px; 
                text-align: center;
            }
            .header h1 {
                margin: 0;
                font-size: 24px;
            }
            .content { 
                padding: 30px;
                background: white;
            }
            .button { 
                display: inline-block; 
                padding: 12px 30px; 
                background: #667eea; 
                color: white !important; 
                text-decoration: none; 
                border-radius: 5px; 
                margin: 20px 0;
            }
            .footer { 
                text-align: center; 
                padding: 20px; 
                font-size: 12px; 
                color: #999; 
                background: #f9f9f9;
            }
            .alert-box {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin: 15px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>" . htmlspecialchars($title) . "</h1>
            </div>
            <div class='content'>
                " . $content . "
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " HR Management System. All rights reserved.</p>
                <p>This is an automated email. Please do not reply to this message.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Send password reset email
 */
function sendPasswordResetEmail($email, $name, $reset_link) {
    $content = "
        <p>Hello " . htmlspecialchars($name) . ",</p>
        <p>We received a request to reset your password for your HR Management System account.</p>
        <p>Click the button below to reset your password:</p>
        <p style='text-align: center;'>
            <a href='" . $reset_link . "' class='button'>Reset Password</a>
        </p>
        <p>Or copy and paste this link into your browser:</p>
        <p style='word-break: break-all; color: #667eea;'>" . $reset_link . "</p>
        <div class='alert-box'>
            <strong>⚠ Important:</strong> This link will expire in 1 hour for security reasons.
        </div>
        <p>If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
    ";
    
    $subject = "Password Reset Request - HR Management System";
    $message = getEmailTemplate("Password Reset Request", $content);
    
    return sendEmail($email, $subject, $message);
}

/**
 * Send password changed confirmation email
 */
function sendPasswordChangedEmail($email, $name) {
    $content = "
        <p>Hello " . htmlspecialchars($name) . ",</p>
        <p>Your password has been changed successfully.</p>
        <div class='alert-box'>
            <strong>🔒 Security Notice:</strong> If you did not make this change, please contact support immediately.
        </div>
        <p><strong>Date & Time:</strong> " . date('F j, Y, g:i a') . "</p>
        <p>For your security, we recommend:</p>
        <ul>
            <li>Using a strong, unique password</li>
            <li>Not sharing your password with anyone</li>
            <li>Changing your password regularly</li>
        </ul>
    ";
    
    $subject = "Password Changed Successfully - HR Management System";
    $message = getEmailTemplate("Password Changed Successfully", $content);
    
    return sendEmail($email, $subject, $message);
}

// For production use with PHPMailer (recommended)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendEmailSMTP($to, $subject, $message) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Set your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'academicexcellence126@gmail.com';
        $mail->Password   = 'Vzpr iupq mhfu hiby';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(EMAIL_REPLY_TO);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}

?>