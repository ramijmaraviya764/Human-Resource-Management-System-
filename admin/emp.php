<?php
session_start();
include("config/conn.php");

$success_message = "";
$error_message = "";

// ✅ Access Control (Only Admin / HR)
if (
    !isset($_SESSION['logged_in']) ||
    !isset($_SESSION['user_id']) ||
    empty($_SESSION['user_id']) ||
    !isset($_SESSION['user_role']) ||
    !in_array(strtolower($_SESSION['user_role']), ['admin', 'hr'])
) {
    header("Location: logout.php");
    exit();
}

// ✅ Function: Generate secure random password
function generateStrongPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
    $password = '';
    $max = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $max)];
    }

    return $password;
}

// ✅ Function: Send Email with Credentials
function sendCredentialsEmail($to_email, $name, $user_id, $password, $company_name = "Zero Proxy") {
    $subject = "Welcome to $company_name - Your Login Credentials";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
            .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
            .content { background-color: white; padding: 30px; margin-top: 20px; }
            .credentials { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
            .button { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Welcome to $company_name!</h2>
            </div>
            <div class='content'>
                <p>Dear <strong>$name</strong>,</p>
                
                <p>Welcome to the team! Your employee account has been successfully created.</p>
                
                <div class='credentials'>
                    <h3>Your Login Credentials:</h3>
                    <p><strong>Employee ID:</strong> $user_id</p>
                    <p><strong>Email:</strong> $to_email</p>
                    <p><strong>Temporary Password:</strong> $password</p>
                </div>
                
                <p><strong>⚠️ Important Security Instructions:</strong></p>
                <ul>
                    <li>Please change your password immediately after your first login</li>
                    <li>Do not share your credentials with anyone</li>
                    <li>Keep your password secure and confidential</li>
                    <li>Use a strong password with a mix of letters, numbers, and special characters</li>
                </ul>
                
                <p>You can login to the system using the link below:</p>
                <a href='https://yourcompany.com/login.php' class='button'>Login Now</a>
                
                <p>If you have any questions or need assistance, please contact the HR department.</p>
                
                <p>Best regards,<br>
                <strong>$company_name HR Team</strong></p>
            </div>
            <div class='footer'>
                <p>This is an automated email. Please do not reply to this message.</p>
                <p>&copy; " . date('Y') . " $company_name. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: HR Department <hr@zeroproxy.com>" . "\r\n";
    $headers .= "Reply-To: hr@zeroproxy.com" . "\r\n";

    return mail($to_email, $subject, $message, $headers);
}

// ✅ Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone']);
    $department  = trim($_POST['department']);
    $position    = trim($_POST['position']);
    $user_role   = trim($_POST['user_role']);
    $salary      = trim($_POST['salary']);
    $address     = trim($_POST['address']);
    $dob         = trim($_POST['date_of_birth']);
    $gender      = trim($_POST['gender']);
    $joining     = trim($_POST['joining_date']);
    $emergency   = trim($_POST['emergency_contact']);
    $blood       = trim($_POST['blood_group']);

    // ✅ Validation
    if (empty($name) || empty($email) || empty($phone)) {
        $error_message = "❌ Please fill all required fields!";
    } else {
        // ✅ Check if email already exists in users table
        $check_email = "SELECT id FROM users WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $check_email);
        mysqli_stmt_bind_param($stmt_check, 's', $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error_message = "❌ Email already exists! Please use a different email.";
        } else {
            // ✅ Generate unique Employee ID and strong password
            $user_id = 'EMP' . date('Y') . rand(1000, 9999);
            $password = generateStrongPassword(12);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            mysqli_begin_transaction($conn);

            try {
                // ✅ First, check what columns exist in users table
                $check_columns = mysqli_query($conn, "DESCRIBE users");
                $columns = [];
                while ($row = mysqli_fetch_assoc($check_columns)) {
                    $columns[] = $row['Field'];
                }

                // ✅ Insert into users table with proper column check
                if (in_array('password', $columns)) {
                    // Users table HAS password column
                    $sql_user = "INSERT INTO users (name, email, phone, password, user_role, status) 
                                 VALUES (?, ?, ?, ?, ?, 1)";
                    $stmt_user = mysqli_prepare($conn, $sql_user);
                    if (!$stmt_user) {
                        throw new Exception("Users statement failed: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($stmt_user, 'sssss', $name, $email, $phone, $hashed_password, $user_role);
                } else {
                    // Users table DOES NOT have password column
                    $sql_user = "INSERT INTO users (name, email, phone, user_role, status) 
                                 VALUES (?, ?, ?, ?, 1)";
                    $stmt_user = mysqli_prepare($conn, $sql_user);
                    if (!$stmt_user) {
                        throw new Exception("Users statement failed: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($stmt_user, 'ssss', $name, $email, $phone, $user_role);
                }

                if (!mysqli_stmt_execute($stmt_user)) {
                    throw new Exception("Error inserting user: " . mysqli_stmt_error($stmt_user));
                }

                $db_user_id = mysqli_insert_id($conn);

                // ✅ Check columns in employees table
                $check_emp_columns = mysqli_query($conn, "DESCRIBE employees");
                $emp_columns = [];
                while ($row = mysqli_fetch_assoc($check_emp_columns)) {
                    $emp_columns[] = $row['Field'];
                }

                // ✅ Build dynamic INSERT query based on existing columns
                $insert_columns = ['name', 'position', 'phone', 'email', 'user_id', 'department'];
                $insert_values = [$name, $position, $phone, $email, $user_id, $department];
                $types = 'ssssss';

                // Add optional columns if they exist
                if (in_array('salary', $emp_columns)) {
                    $insert_columns[] = 'salary';
                    $insert_values[] = $salary;
                    $types .= 'd';
                }
                if (in_array('address', $emp_columns)) {
                    $insert_columns[] = 'address';
                    $insert_values[] = $address;
                    $types .= 's';
                }
                if (in_array('date_of_birth', $emp_columns)) {
                    $insert_columns[] = 'date_of_birth';
                    $insert_values[] = $dob;
                    $types .= 's';
                }
                if (in_array('gender', $emp_columns)) {
                    $insert_columns[] = 'gender';
                    $insert_values[] = $gender;
                    $types .= 's';
                }
                if (in_array('joining_date', $emp_columns)) {
                    $insert_columns[] = 'joining_date';
                    $insert_values[] = $joining;
                    $types .= 's';
                }
                if (in_array('emergency_contact', $emp_columns)) {
                    $insert_columns[] = 'emergency_contact';
                    $insert_values[] = $emergency;
                    $types .= 's';
                }
                if (in_array('blood_group', $emp_columns)) {
                    $insert_columns[] = 'blood_group';
                    $insert_values[] = $blood;
                    $types .= 's';
                }
                if (in_array('password', $emp_columns)) {
                    $insert_columns[] = 'password';
                    $insert_values[] = $hashed_password;
                    $types .= 's';
                }
                if (in_array('status', $emp_columns)) {
                    $insert_columns[] = 'status';
                    $insert_values[] = 'active';
                    $types .= 's';
                }

                // ✅ Build and execute employee insert
                $columns_str = implode(', ', $insert_columns);
                $placeholders = str_repeat('?,', count($insert_columns) - 1) . '?';
                $sql_emp = "INSERT INTO employees ($columns_str) VALUES ($placeholders)";
                
                $stmt_emp = mysqli_prepare($conn, $sql_emp);
                if (!$stmt_emp) {
                    throw new Exception("Employees statement failed: " . mysqli_error($conn));
                }

                // Bind parameters dynamically
                $refs = [];
                $refs[] = $types;
                foreach ($insert_values as $key => $value) {
                    $refs[] = &$insert_values[$key];
                }
                call_user_func_array([$stmt_emp, 'bind_param'], $refs);

                if (!mysqli_stmt_execute($stmt_emp)) {
                    throw new Exception("Error inserting employee: " . mysqli_stmt_error($stmt_emp));
                }

                // ✅ Commit Transaction
                mysqli_commit($conn);

                // ✅ Send Email with Credentials
                $email_sent = sendCredentialsEmail($email, $name, $user_id, $password);

                $success_message = "
                    ✅ <strong>Employee Added Successfully!</strong><br><br>
                    <strong>Employee ID:</strong> $user_id<br>
                    <strong>Name:</strong> $name<br>
                    <strong>Email:</strong> $email<br>
                    <strong>Temporary Password:</strong> <code style='background:#f4f4f4;padding:5px;border-radius:3px;'>$password</code><br><br>
                    " . ($email_sent ? "📧 Credentials have been sent to the employee's email." : "⚠️ Email could not be sent. Please share credentials manually.") . "
                ";

            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error_message = "❌ Error: " . $e->getMessage();
            }
        }
        mysqli_stmt_close($stmt_check);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee - Zero Proxy</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form.css">
    <style>
        .alert {
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            animation: slideIn 0.3s ease;
        }
        .alert-success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        code {
            background: #f4f4f4;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .copy-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
            font-size: 12px;
        }
        .copy-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include("include/slidbar.php"); ?>

    <div class="main-content">
        <div class="top-bar">
            <div class="welcome-text">
                <h1>Add New Employee 👤</h1>
                <p>Fill in the complete details to create a new employee account</p>
            </div>
            <div class="top-actions">
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </button>
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['name'] ?? 'U', 0, 2)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></span>
                </div>
            </div>
        </div>

        <div class="add-employee-container">
            <div class="form-header">
                <h2><i class="fas fa-user-plus"></i> Add New Employee</h2>
                <p>All fields marked with <span style="color: #e74c3c;">*</span> are required</p>
            </div>

            <?php if ($success_message): ?>
            <div class="alert alert-success" id="successAlert">
                <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                <div>
                    <?php echo $success_message; ?>
                    <br><br>
                    <button class="copy-btn" onclick="copyCredentials()">
                        <i class="fas fa-copy"></i> Copy Credentials
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle" style="font-size: 24px;"></i>
                <div><?php echo $error_message; ?></div>
            </div>
            <?php endif; ?>

            <div class="info-box">
                <h4><i class="fas fa-info-circle"></i> Important Information</h4>
                <ul>
                    <li>✅ Employee ID will be auto-generated (Format: EMP2026XXXX)</li>
                    <li>🔐 A strong 12-character password will be created automatically</li>
                    <li>💾 Data will be stored in both <strong>users</strong> and <strong>employees</strong> tables</li>
                    <li>📧 Login credentials will be sent to employee's email automatically</li>
                    <li>⚠️ Employee must change password after first login</li>
                </ul>
            </div>

            <!-- ✅ Employee Form -->
            <form method="POST" id="addEmployeeForm">
                <div class="section-title">
                    <i class="fas fa-user-circle"></i> Personal Information
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" id="name" name="name" placeholder="Enter full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email *</label>
                        <input type="email" id="email" name="email" placeholder="employee@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Phone *</label>
                        <input type="tel" id="phone" name="phone" placeholder="+91 9876543210" required>
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth"><i class="fas fa-calendar"></i> Date of Birth *</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" required>
                    </div>

                    <div class="form-group">
                        <label for="gender"><i class="fas fa-venus-mars"></i> Gender *</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="blood_group"><i class="fas fa-tint"></i> Blood Group *</label>
                        <select id="blood_group" name="blood_group" required>
                            <option value="">Select Blood Group</option>
                            <option value="A+">A+</option><option value="A-">A-</option>
                            <option value="B+">B+</option><option value="B-">B-</option>
                            <option value="O+">O+</option><option value="O-">O-</option>
                            <option value="AB+">AB+</option><option value="AB-">AB-</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="address"><i class="fas fa-map-marker-alt"></i> Address *</label>
                        <textarea id="address" name="address" placeholder="Enter complete address" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="emergency_contact"><i class="fas fa-phone-square"></i> Emergency Contact *</label>
                        <input type="tel" id="emergency_contact" name="emergency_contact" placeholder="+91 9876543210" required>
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-briefcase"></i> Employment Information
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="department"><i class="fas fa-building"></i> Department *</label>
                        <select id="department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="Engineering">Engineering</option>
                            <option value="HR">Human Resources</option>
                            <option value="Sales">Sales</option>
                            <option value="Finance">Finance</option>
                            <option value="Operations">Operations</option>
                            <option value="IT">IT Support</option>
                            <option value="Design">Design</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="position"><i class="fas fa-user-tie"></i> Position *</label>
                        <input type="text" id="position" name="position" placeholder="e.g., Software Engineer" required>
                    </div>

                    <div class="form-group">
                        <label for="user_role"><i class="fas fa-user-shield"></i> Role *</label>
                        <select id="user_role" name="user_role" required>
                            <option value="">Select Role</option>
                            <option value="employee">Employee</option>
                            <option value="hr">HR</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="salary"><i class="fas fa-rupee-sign"></i> Salary *</label>
                        <input type="number" id="salary" name="salary" placeholder="e.g., 50000" min="0" step="1000" required>
                    </div>

                    <div class="form-group">
                        <label for="joining_date"><i class="fas fa-calendar-plus"></i> Joining Date *</label>
                        <input type="date" id="joining_date" name="joining_date" required>
                    </div>
                </div>

                <div class="btn-container">
                    <button type="submit" name="add_employee" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Employee
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset Form
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Set max date for DOB (18 years ago)
            const today = new Date();
            const eighteenYearsAgo = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
            $('#date_of_birth').attr('max', eighteenYearsAgo.toISOString().split('T')[0]);
            
            // Set default joining date to today
            $('#joining_date').val(new Date().toISOString().split('T')[0]);

            // Form validation
            $('#addEmployeeForm').on('submit', function(e) {
                const email = $('#email').val();
                const phone = $('#phone').val();

                if (!email.includes('@')) {
                    alert('Please enter a valid email address');
                    e.preventDefault();
                    return false;
                }

                const phoneDigits = phone.replace(/\D/g, '');
                if (phoneDigits.length < 10) {
                    alert('Please enter a valid phone number (at least 10 digits)');
                    e.preventDefault();
                    return false;
                }
            });

            // Auto-hide alerts after 15 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 15000);
        });

        function copyCredentials() {
            const alert = document.getElementById('successAlert');
            const text = alert.innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('✅ Credentials copied to clipboard!');
            });
        }
    </script>
</body>
</html>