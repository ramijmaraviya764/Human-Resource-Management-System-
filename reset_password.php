<?php
session_start();
include('admin/config/conn.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';
$valid_token = false;
$email = $_GET['email'] ?? '';
$token = $_GET['reset_token'] ?? '';

// Function to send confirmation email
function sendConfirmationEmail($email, $name) {
    require 'admin/PHPMailer/Exception.php';
    require 'admin/PHPMailer/SMTP.php';
    require 'admin/PHPMailer/PHPMailer.php';
    
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
        $mail->Subject = 'Password Changed Successfully - HR Management System';
        
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; background: white; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; background: #f9f9f9; }
                .alert-box { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Password Changed Successfully</h1>
                </div>
                <div class='content'>
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
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " HR Management System. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Verify token
if (!empty($email) && !empty($token)) {
    $stmt = $conn->prepare(
        "SELECT id, name, email FROM users 
         WHERE email = ? AND reset_token = ? AND token_expiry > NOW()"
    );
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $valid_token = true;
        $user = $result->fetch_assoc();
    } else {
        $error = "Invalid or expired reset link. Please request a new one.";
    }
    $stmt->close();
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $update_stmt = $conn->prepare(
            "UPDATE users 
             SET password = ?, reset_token = NULL, token_expiry = NULL 
             WHERE email = ? AND reset_token = ?"
        );
        $update_stmt->bind_param("sss", $hashed_password, $email, $token);
        
        if ($update_stmt->execute()) {
            $success = "Password reset successful! You can now login with your new password.";
            $valid_token = false;
            
            // Send confirmation email
            sendConfirmationEmail($user['email'], $user['name']);
        } else {
            $error = "Failed to reset password. Please try again.";
        }
        
        $update_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - HR Management System</title>
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
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-group-custom {
            position: relative;
        }
        .input-group-custom i.fa-lock {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 10;
        }
        .input-group-custom .form-control {
            padding-left: 45px;
            padding-right: 45px;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            z-index: 10;
        }
        .toggle-password:hover {
            color: #667eea;
        }
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        .strength-bar {
            height: 5px;
            background: #e0e0e0;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }
        .strength-bar-fill {
            height: 100%;
            transition: all 0.3s;
            border-radius: 3px;
            width: 0;
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            margin-bottom: 8px;
            font-weight: 500;
            display: block;
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
                        <i class="fas fa-lock"></i>
                    </div>
                    <h1>Reset Password</h1>
                    <p>Create a strong new password for your account</p>
                </div>
                <div class="illustration">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>

            <div class="login-right">
                <div class="login-header">
                    <h2>Create New Password</h2>
                    <p>Enter your new password below</p>
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

                <?php if ($valid_token && !$success): ?>
                <form method="POST" action="" id="resetForm">
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <div class="input-group-custom">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" name="new_password" 
                                   id="newPassword" placeholder="Enter new password"
                                   style="padding: 12px 45px; border: 1px solid #ddd; border-radius: 8px;"
                                   required>
                            <i class="fas fa-eye toggle-password" id="toggleNew"></i>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-bar-fill" id="strengthBar"></div>
                            </div>
                            <small id="strengthText">Password strength: <span id="strengthLevel">-</span></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group-custom">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" name="confirm_password" 
                                   id="confirmPassword" placeholder="Confirm new password"
                                   style="padding: 12px 45px; border: 1px solid #ddd; border-radius: 8px;"
                                   required>
                            <i class="fas fa-eye toggle-password" id="toggleConfirm"></i>
                        </div>
                        <small id="matchText" style="font-size: 12px;"></small>
                    </div>

                    <button type="submit" class="btn-login" id="submitBtn">
                        <i class="fas fa-check"></i> Reset Password
                    </button>

                    <div class="switch-link" style="text-align: center; margin-top: 20px;">
                        <a href="login.php" style="color: #667eea; text-decoration: none; font-weight: 600;">Back to Login</a>
                    </div>
                </form>
                <?php elseif ($success): ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="login.php" class="btn-login" style="display: inline-block; text-decoration: none;">
                            <i class="fas fa-sign-in-alt"></i> Go to Login
                        </a>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <p>Invalid or expired reset link.</p>
                        <a href="forgot_password.php" class="btn-login" style="display: inline-block; text-decoration: none;">
                            <i class="fas fa-redo"></i> Request New Link
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('toggleNew')?.addEventListener('click', function() {
            const password = document.getElementById('newPassword');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggleConfirm')?.addEventListener('click', function() {
            const password = document.getElementById('confirmPassword');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Password strength checker
        const newPassword = document.getElementById('newPassword');
        const strengthBar = document.getElementById('strengthBar');
        const strengthLevel = document.getElementById('strengthLevel');

        newPassword?.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;
            
            let color, text, width;
            
            if (strength <= 1) {
                color = '#ff4444';
                text = 'Weak';
                width = '25%';
            } else if (strength <= 3) {
                color = '#ffaa00';
                text = 'Medium';
                width = '50%';
            } else if (strength <= 4) {
                color = '#00aa00';
                text = 'Strong';
                width = '75%';
            } else {
                color = '#00ff00';
                text = 'Very Strong';
                width = '100%';
            }
            
            strengthBar.style.backgroundColor = color;
            strengthBar.style.width = width;
            strengthLevel.textContent = text;
            strengthLevel.style.color = color;
        });

        // Check password match
        const confirmPassword = document.getElementById('confirmPassword');
        const matchText = document.getElementById('matchText');

        confirmPassword?.addEventListener('input', function() {
            if (this.value === '') {
                matchText.textContent = '';
                return;
            }
            
            if (this.value === newPassword.value) {
                matchText.textContent = '✓ Passwords match';
                matchText.style.color = '#00aa00';
            } else {
                matchText.textContent = '✗ Passwords do not match';
                matchText.style.color = '#ff4444';
            }
        });

        // Form validation
        const resetForm = document.getElementById('resetForm');
        
        resetForm?.addEventListener('submit', function(e) {
            const newPass = newPassword.value;
            const confirmPass = confirmPassword.value;

            if (newPass.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return false;
            }

            if (newPass !== confirmPass) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
        });

        // Input focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                const icon = this.parentElement.querySelector('i.fa-lock');
                if (icon) {
                    icon.style.color = '#667eea';
                }
                this.style.borderColor = '#667eea';
            });

            input.addEventListener('blur', function() {
                const icon = this.parentElement.querySelector('i.fa-lock');
                if (icon) {
                    icon.style.color = '#999';
                }
                this.style.borderColor = '#ddd';
            });
        });

        // Auto-hide alerts
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.display = 'none';
            });
        }, 7000);
    </script>
</body>
</html>