<?php
session_start();
include('admin/config/conn.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

function sendMail($email, $reset_token, $name)
{
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/SMTP.php';
    require 'PHPMailer/PHPMailer.php';
    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'academicexcellence126@gmail.com';
        $mail->Password   = 'vzpr iupq mhfu hiby';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        
        $mail->setFrom('academicexcellence126@gmail.com', 'HR Management System');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - HR Management System';
        
        $reset_link = "http://localhost/YOUR_PROJECT_FOLDER/reset_password.php?email=" . urlencode($email) . "&reset_token=" . $reset_token;
        
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; background: white; }
                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white !important; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; background: #f9f9f9; }
                .alert-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Password Reset Request</h1>
                </div>
                <div class='content'>
                    <p>Hello " . htmlspecialchars($name) . ",</p>
                    <p>We received a request to reset your password for your HR Management System account.</p>
                    <p>Click the button below to reset your password:</p>
                    <p style='text-align: center;'>
                        <a href='" . $reset_link . "' class='button'>Reset Password</a>
                    </p>
                    <p>Or copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; color: #667eea;'>" . $reset_link . "</p>
                    <div class='alert-box'>
                        <strong>⚠ Important:</strong> This link will expire in 24 hours for security reasons.
                    </div>
                    <p>If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " HR Management System. All rights reserved.</p>
                    <p>This is an automated email. Please do not reply to this message.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}

if (isset($_POST['send-link'])) {
    $email = trim($_POST['email'] ?? '');
    
    // Validation
    if (empty($email)) {
        $error = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            date_default_timezone_set('Asia/Kolkata');
            $current_date = date('Y-m-d H:i:s');
            
            // Update reset token
            $update_stmt = $conn->prepare(
                "UPDATE users SET reset_token = ?, token_expiry = DATE_ADD(?, INTERVAL 24 HOUR) WHERE email = ?"
            );
            $update_stmt->bind_param("sss", $reset_token, $current_date, $email);
            
            if ($update_stmt->execute() && sendMail($email, $reset_token, $user['name'])) {
                $success = "Password reset link has been sent to your email address.";
            } else {
                $error = "Failed to send email. Please try again later.";
            }
            
            $update_stmt->close();
        } else {
            // Security: Don't reveal if email exists
            $success = "If an account exists with that email, a password reset link has been sent.";
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - HR Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/login-2.css">
    <style>
        .alert {
            display: none;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }
        .alert.show {
            display: block !important;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .input-group-custom {
            position: relative;
        }
        .input-group-custom i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 10;
        }
        .input-group-custom .form-control {
            padding-left: 45px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="animated-bg">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container">
        <div class="login-wrapper">
            <div class="login-left">
                <div class="logo-container">
                    <div class="logo">
                        <i class="fas fa-key"></i>
                    </div>
                    <h1>Forgot Password?</h1>
                    <p>Don't worry! Enter your email and we'll send you a reset link</p>
                </div>
                <div class="illustration">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
            </div>

            <div class="login-right">
                <div class="login-header">
                    <h2>Reset Password</h2>
                    <p>Enter your registered email address</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success show">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="forgotForm">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label" style="margin-bottom: 8px; font-weight: 500;">Email Address</label>
                        <div class="input-group-custom">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" name="email" id="email" 
                                   placeholder="Enter your registered email" 
                                   style="padding: 12px 12px 12px 45px; border: 1px solid #ddd; border-radius: 8px;"
                                   required>
                        </div>
                    </div>

                    <button type="submit" name="send-link" class="btn-login">
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>

                    <div class="switch-link" style="text-align: center; margin-top: 20px;">
                        Remember your password? <a href="login.php" style="color: #667eea; text-decoration: none; font-weight: 600;">Login here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        const forgotForm = document.getElementById('forgotForm');
        
        forgotForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();

            if (!email) {
                e.preventDefault();
                alert('Please enter your email address');
                return false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
        });

        // Input focus animations
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                const icon = this.parentElement.querySelector('i');
                if (icon) {
                    icon.style.color = '#667eea';
                }
                this.style.borderColor = '#667eea';
            });

            input.addEventListener('blur', function() {
                const icon = this.parentElement.querySelector('i');
                if (icon) {
                    icon.style.color = '#999';
                }
                this.style.borderColor = '#ddd';
            });
        });

        // Auto-hide alerts after 7 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.display = 'none';
            });
        }, 7000);
    </script>
</body>
</html>