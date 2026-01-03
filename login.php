<?php
session_start();
include("admin/config/conn.php");

$error = '';
$success = '';

/* ===============================
   ALREADY LOGGED-IN CHECK
================================ */
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    switch (strtolower($_SESSION['user_role'])) {
        case 'hr':
            header("Location: admin/index.php");
            break;

        case 'employee':
            header("Location: employee/index.php");
            break;
        default:
            header("Location: login.php");
    }
    exit();
}

/* ===============================
   LOGIN PROCESS
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email       = trim($_POST['email'] ?? '');
    $password    = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    // Validation
    if (empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, user_role, status 
             FROM users 
             WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check account status
            if ($user['status'] === 'inactive') {
                $error = "Your account has been deactivated. Contact admin.";
            }
            // Verify password
            elseif (password_verify($password, $user['password'])) {

                // SESSION SET
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role']  = $user['user_role'];
                $_SESSION['logged_in']  = true;

                // Remember Me
                if ($remember_me) {
                    setcookie(
                        "user_email",
                        $email,
                        time() + (30 * 24 * 60 * 60),
                        "/"
                    );
                }

                // Update last login
                $update = $conn->prepare(
                    "UPDATE users SET last_login = NOW() WHERE id = ?"
                );
                $update->bind_param("i", $user['id']);
                $update->execute();
                $update->close();

                // ROLE BASED REDIRECT
                switch (strtolower($user['user_role'])) {
                    case 'hr':
                        header("Location: admin/index.php");
                        break;

                    case 'employee':
                        header("Location: employee/index.php");
                        break;
                    default:
                        header("Location: login.php");
                }
                exit();

            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }

        $stmt->close();
    }
}

$remembered_email = $_COOKIE['user_email'] ?? '';
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HR Management System</title>
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
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h1>HR Management System</h1>
                    <p>Streamline your workforce management with our comprehensive HR solution</p>
                </div>
                <div class="illustration">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>

            <div class="login-right">
                <div class="login-header">
                    <h2>Welcome Back!</h2>
                    <p>Please login to access your account</p>
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

                <form method="POST" action="" id="loginForm">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-group-custom">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" name="email" id="email" 
                                   placeholder="Enter your email" 
                                   value="<?php echo htmlspecialchars($remembered_email); ?>" 
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-group-custom">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" name="password" id="password" 
                                   placeholder="Enter your password" required>
                        </div>
                    </div>

                    <div class="form-options">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember_me" 
                                   id="rememberMe" <?php echo $remembered_email ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="rememberMe">
                                Remember me
                            </label>
                        </div>
                        <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>

                    <div class="switch-link">
                        Don't have an account? <a href="sin-up.php">Sign up here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        const loginForm = document.getElementById('loginForm');
        
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
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
                const icon = this.parentElement.querySelector('i.fa-envelope, i.fa-lock');
                if (icon) {
                    icon.style.color = '#667eea';
                }
            });

            input.addEventListener('blur', function() {
                const icon = this.parentElement.querySelector('i.fa-envelope, i.fa-lock');
                if (icon) {
                    icon.style.color = '#999';
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>