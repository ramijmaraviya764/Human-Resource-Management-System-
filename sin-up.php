<?php 
include("admin/config/conn.php");

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($phone) < 10) {
        $error = "Phone number must be at least 10 digits";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        try {
            // First CHECK if email already exists
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Email already exists
                $error = "Email is already registered. Please use a different email.";
            } else {
                // Email doesn't exist, so INSERT new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $insert_stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, user_role) VALUES (?, ?, ?, ?, 'Hr')");
                $insert_stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);
                
                if ($insert_stmt->execute()) {
                    $message = "Registration successful! Redirecting to login page...";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                    </script>";
                } else {
                    $error = "Registration failed: " . $insert_stmt->error;
                }
                
                $insert_stmt->close();
            }
            
            $check_stmt->close();
            
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - HR Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/login.css">
    <style>
        .alert {
            display: none;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }
        .alert.show {
            display: block;
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
        <div class="auth-wrapper">
            <div class="auth-left">
                <div class="logo-container">
                    <div class="logo">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h1>HR Management System</h1>
                    <p>Streamline your workforce management with our comprehensive HR solution</p>
                </div>
                <div class="illustration" id="illustration">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>

            <div class="auth-right">
                <div class="page active" id="signupPage">
                    <div class="auth-header">
                        <h2>Create Account</h2>
                        <p>Sign up to get started</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger show">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success show">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" id="signupForm">
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <i class="fas fa-user"></i>
                                <input type="text" class="form-control" name="name" id="signupName" 
                                       placeholder="Enter your full name" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>
                            <div class="invalid-feedback">Please enter your full name</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <i class="fas fa-envelope"></i>
                                <input type="email" class="form-control" name="email" id="signupEmail" 
                                       placeholder="Enter your email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                            <div class="invalid-feedback">Please enter a valid email address</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <i class="fas fa-phone"></i>
                                <input type="tel" class="form-control" name="phone" id="signupPhone" 
                                       placeholder="Enter phone number" 
                                       value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>
                            </div>
                            <div class="invalid-feedback">Please enter a valid phone number (min 10 digits)</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="form-control" name="password" id="signupPassword" 
                                       placeholder="Create a password (min 6 characters)" required>
                            </div>
                            <div class="invalid-feedback">Password must be at least 6 characters</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm Password <span class="required">*</span></label>
                            <div class="input-group-custom">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="form-control" name="confirm_password" id="signupConfirmPassword" 
                                       placeholder="Confirm your password" required>
                            </div>
                            <div class="invalid-feedback">Passwords do not match</div>
                        </div>

                        <button type="submit" class="btn-auth">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </form>

                    <div class="switch-link">
                        Already have an account? <a href="login.php">Login here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Password toggle functionality
            $('.password-toggle').click(function() {
                const target = $(this).data('target');
                const input = $('#' + target);
                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            // Input focus animations
            $('.form-control').focus(function() {
                $(this).parent().find('i:first').css('color', '#667eea');
            }).blur(function() {
                $(this).parent().find('i:first').css('color', '#999');
            });

            // Email validation function
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            // Phone validation function
            function validatePhone(phone) {
                const cleaned = phone.replace(/\D/g, '');
                return cleaned.length >= 10;
            }

            // Form validation before submit
            $('#signupForm').submit(function(e) {
                let isValid = true;
                
                // Reset validation states
                $('.form-control').removeClass('is-invalid is-valid');
                $('.invalid-feedback').hide();

                // Validate name
                const name = $('#signupName').val().trim();
                if (name.length < 2) {
                    $('#signupName').addClass('is-invalid');
                    $('#signupName').siblings('.invalid-feedback').show();
                    isValid = false;
                } else {
                    $('#signupName').addClass('is-valid');
                }

                // Validate email
                const email = $('#signupEmail').val().trim();
                if (!validateEmail(email)) {
                    $('#signupEmail').addClass('is-invalid');
                    $('#signupEmail').siblings('.invalid-feedback').show();
                    isValid = false;
                } else {
                    $('#signupEmail').addClass('is-valid');
                }

                // Validate phone
                const phone = $('#signupPhone').val().trim();
                if (!validatePhone(phone)) {
                    $('#signupPhone').addClass('is-invalid');
                    $('#signupPhone').siblings('.invalid-feedback').show();
                    isValid = false;
                } else {
                    $('#signupPhone').addClass('is-valid');
                }

                // Validate password
                const password = $('#signupPassword').val();
                if (password.length < 6) {
                    $('#signupPassword').addClass('is-invalid');
                    $('#signupPassword').siblings('.invalid-feedback').show();
                    isValid = false;
                } else {
                    $('#signupPassword').addClass('is-valid');
                }

                // Validate confirm password
                const confirmPassword = $('#signupConfirmPassword').val();
                if (password !== confirmPassword) {
                    $('#signupConfirmPassword').addClass('is-invalid');
                    $('#signupConfirmPassword').siblings('.invalid-feedback').show();
                    isValid = false;
                } else {
                    $('#signupConfirmPassword').addClass('is-valid');
                }

                if (!isValid) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 300);
                    return false;
                }
            });

            // Real-time password match validation
            $('#signupConfirmPassword').on('input', function() {
                const password = $('#signupPassword').val();
                const confirmPassword = $(this).val();
                
                if (confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                        $(this).siblings('.invalid-feedback').hide();
                    } else {
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        $(this).siblings('.invalid-feedback').show();
                    }
                }
            });
        });
    </script>
</body>
</html>