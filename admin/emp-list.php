<?php
include("config/conn.php");

if (
    !isset($_SESSION['logged_in']) ||
    !isset($_SESSION['user_id']) ||
    empty($_SESSION['user_id']) ||
    !isset($_SESSION['user_role']) ||
    !in_array(strtolower($_SESSION['user_role']), ['admin', 'hr'])
) {
    header("Location: ../logout.php");
    exit();
}

// Handle AJAX Requests
if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    
    // Get Employee Data
    if ($_POST['ajax_action'] === 'get_employee') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "SELECT * FROM employees WHERE id = '$id'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $employee = mysqli_fetch_assoc($result);
            unset($employee['password']); // Remove password for security
            echo json_encode($employee);
        } else {
            echo json_encode(['error' => 'Employee not found']);
        }
        exit();
    }
    
    // Update Employee
    if ($_POST['ajax_action'] === 'update_employee') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        $department = mysqli_real_escape_string($conn, $_POST['department']);
        $position = mysqli_real_escape_string($conn, $_POST['position']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $salary = mysqli_real_escape_string($conn, $_POST['salary']);
        $date_of_birth = !empty($_POST['date_of_birth']) ? mysqli_real_escape_string($conn, $_POST['date_of_birth']) : NULL;
        $gender = !empty($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : NULL;
        $joining_date = mysqli_real_escape_string($conn, $_POST['joining_date']);
        $blood_group = !empty($_POST['blood_group']) ? mysqli_real_escape_string($conn, $_POST['blood_group']) : NULL;
        $emergency_contact = !empty($_POST['emergency_contact']) ? mysqli_real_escape_string($conn, $_POST['emergency_contact']) : NULL;
        $address = !empty($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : NULL;
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Check if user_id already exists for another employee
        $check_query = "SELECT id FROM employees WHERE user_id = '$user_id' AND id != '$id'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'User ID already exists']);
            exit();
        }
        
        // Build update query
        $query = "UPDATE employees SET 
                    name = '$name',
                    user_id = '$user_id',
                    department = '$department',
                    position = '$position',
                    email = '$email',
                    phone = '$phone',
                    salary = '$salary',
                    joining_date = '$joining_date',
                    status = '$status'";
        
        if ($date_of_birth !== NULL) {
            $query .= ", date_of_birth = '$date_of_birth'";
        }
        if ($gender !== NULL) {
            $query .= ", gender = '$gender'";
        }
        if ($blood_group !== NULL) {
            $query .= ", blood_group = '$blood_group'";
        }
        if ($emergency_contact !== NULL) {
            $query .= ", emergency_contact = '$emergency_contact'";
        }
        if ($address !== NULL) {
            $query .= ", address = '$address'";
        }
        
        $query .= " WHERE id = '$id'";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Employee updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit();
    }
    
    // Delete Employee
    if ($_POST['ajax_action'] === 'delete_employee') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "DELETE FROM employees WHERE id = '$id'";
        
        if (mysqli_query($conn, $query)) {
            if (mysqli_affected_rows($conn) > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Employee deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Employee not found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit();
    }
}

// Fetch all employees from database
$query = "SELECT * FROM employees ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List - HR Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-view {
            background: #4299e1;
            color: white;
        }
        .btn-view:hover {
            background: #3182ce;
        }
        .btn-edit {
            background: #48bb78;
            color: white;
        }
        .btn-edit:hover {
            background: #38a169;
        }
        .btn-delete {
            background: #f56565;
            color: white;
        }
        .btn-delete:hover {
            background: #e53e3e;
        }
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 3% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 700px;
            max-height: 85vh;
            overflow-y: auto;
            animation: slideDown 0.3s;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }
        .modal-header h2 {
            margin: 0;
            color: #2d3748;
            font-size: 24px;
        }
        .close {
            color: #a0aec0;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
            line-height: 1;
        }
        .close:hover {
            color: #2d3748;
        }
        .employee-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        .detail-item {
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }
        .detail-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }
        .detail-value {
            color: #2d3748;
            font-size: 15px;
            font-weight: 500;
        }
        .employee-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            border: 5px solid #e2e8f0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #4a5568;
            font-size: 14px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .btn-submit {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
        }
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        .employee-table {
            width: 100%;
            border-collapse: collapse;
        }
        .employee-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .employee-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .employee-table tr:hover {
            background: #f7fafc;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-active {
            background: #c6f6d5;
            color: #22543d;
        }
        .status-remote {
            background: #fed7d7;
            color: #742a2a;
        }
        .status-leave {
            background: #feebc8;
            color: #7c2d12;
        }
    </style>
</head>
<body>
    <?php include("include/slidbar.php");?>

    <div class="main-content">
        <div class="top-bar">
            <div class="welcome-text">
                <h1>Employee Management 👥</h1>
                <p>View, edit, and manage employee records</p>
            </div>
            <div class="top-actions">
                <button class="btn-add" onclick="window.location.href='emp.php'">
                    <i class="fas fa-plus"></i> Add New Employee
                </button>
            </div>
        </div>

        <div class="content-wrapper" style="padding: 20px;">
            <div class="stats-grid" style="margin-bottom: 30px;">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Employees</span>
                        <span class="stat-value"><?php echo mysqli_num_rows($result); ?></span>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="employee-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $statusClass = $row['status'] === 'active' ? 'status-active' : 
                                             ($row['status'] === 'inactive' ? 'status-remote' : 'status-leave');
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td><strong>{$row['name']}</strong></td>
                                    <td>{$row['department']}</td>
                                    <td>{$row['position']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['phone']}</td>
                                    <td><span class='status-badge {$statusClass}'>{$row['status']}</span></td>
                                    <td>
                                        <div class='action-buttons'>
                                            <button class='btn-action btn-view' onclick='viewEmployee({$row['id']})' title='View Details'>
                                                <i class='fas fa-eye'></i>
                                            </button>
                                            <button class='btn-action btn-edit' onclick='editEmployee({$row['id']})' title='Edit Employee'>
                                                <i class='fas fa-edit'></i>
                                            </button>
                                            <button class='btn-action btn-delete' onclick='deleteEmployee({$row['id']}, \"{$row['name']}\")' title='Delete Employee'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align: center; padding: 40px;'>
                                    <i class='fas fa-users' style='font-size: 48px; color: #cbd5e0; margin-bottom: 10px;'></i>
                                    <p style='color: #718096; font-size: 16px;'>No employees found</p>
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Employee Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-circle"></i> Employee Details</h2>
                <span class="close" onclick="closeModal('viewModal')">&times;</span>
            </div>
            <div id="viewEmployeeContent"></div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Employee</h2>
                <span class="close" onclick="closeModal('editModal')">&times;</span>
            </div>
            <div id="editEmployeeContent"></div>
        </div>
    </div>

    <script>
        function viewEmployee(id) {
            $.ajax({
                url: 'emp-list.php',
                type: 'POST',
                data: { 
                    ajax_action: 'get_employee',
                    id: id 
                },
                dataType: 'json',
                success: function(emp) {
                    if (emp.error) {
                        alert(emp.error);
                        return;
                    }
                    
                    let html = '';
                    
                    if (emp.user_img) {
                        html += `<img src="${emp.user_img}" class="employee-img" alt="${emp.name}">`;
                    } else {
                        html += `<div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold;">
                                    ${emp.name.split(' ').map(n => n[0]).join('')}
                                 </div>`;
                    }
                    
                    html += `<div class="employee-detail">
                        <div class="detail-item">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value">${emp.name}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">User ID</div>
                            <div class="detail-value">${emp.user_id}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Department</div>
                            <div class="detail-value">${emp.department}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Position</div>
                            <div class="detail-value">${emp.position}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">${emp.email}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value">${emp.phone}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Salary</div>
                            <div class="detail-value">₹${parseFloat(emp.salary).toLocaleString('en-IN')}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Date of Birth</div>
                            <div class="detail-value">${emp.date_of_birth || 'N/A'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Gender</div>
                            <div class="detail-value">${emp.gender || 'N/A'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Joining Date</div>
                            <div class="detail-value">${emp.joining_date}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Blood Group</div>
                            <div class="detail-value">${emp.blood_group || 'N/A'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Emergency Contact</div>
                            <div class="detail-value">${emp.emergency_contact || 'N/A'}</div>
                        </div>
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <div class="detail-label">Address</div>
                            <div class="detail-value">${emp.address || 'N/A'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value" style="text-transform: capitalize;">${emp.status}</div>
                        </div>
                    </div>`;
                    
                    $('#viewEmployeeContent').html(html);
                    $('#viewModal').fadeIn();
                },
                error: function() {
                    alert('Error loading employee data');
                }
            });
        }

        function editEmployee(id) {
            $.ajax({
                url: 'emp-list.php',
                type: 'POST',
                data: { 
                    ajax_action: 'get_employee',
                    id: id 
                },
                dataType: 'json',
                success: function(emp) {
                    if (emp.error) {
                        alert(emp.error);
                        return;
                    }
                    
                    const html = `
                        <form id="editEmployeeForm">
                            <input type="hidden" name="ajax_action" value="update_employee">
                            <input type="hidden" name="id" value="${emp.id}">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" value="${emp.name}" required>
                                </div>
                                <div class="form-group">
                                    <label>User ID *</label>
                                    <input type="text" name="user_id" value="${emp.user_id}" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Department *</label>
                                    <input type="text" name="department" value="${emp.department}" required>
                                </div>
                                <div class="form-group">
                                    <label>Position *</label>
                                    <input type="text" name="position" value="${emp.position}" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="email" value="${emp.email}" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone *</label>
                                    <input type="text" name="phone" value="${emp.phone}" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Salary *</label>
                                    <input type="number" name="salary" value="${emp.salary}" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="date_of_birth" value="${emp.date_of_birth || ''}">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male" ${emp.gender === 'Male' ? 'selected' : ''}>Male</option>
                                        <option value="Female" ${emp.gender === 'Female' ? 'selected' : ''}>Female</option>
                                        <option value="Other" ${emp.gender === 'Other' ? 'selected' : ''}>Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Joining Date *</label>
                                    <input type="date" name="joining_date" value="${emp.joining_date}" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Blood Group</label>
                                    <input type="text" name="blood_group" value="${emp.blood_group || ''}" placeholder="e.g., A+, B-, O+">
                                </div>
                                <div class="form-group">
                                    <label>Emergency Contact</label>
                                    <input type="text" name="emergency_contact" value="${emp.emergency_contact || ''}" placeholder="Emergency phone number">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" rows="3" placeholder="Full address">${emp.address || ''}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="active" ${emp.status === 'active' ? 'selected' : ''}>Active</option>
                                    <option value="inactive" ${emp.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                    <option value="terminated" ${emp.status === 'terminated' ? 'selected' : ''}>Terminated</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save"></i> Update Employee
                            </button>
                        </form>
                    `;
                    
                    $('#editEmployeeContent').html(html);
                    $('#editModal').fadeIn();
                },
                error: function() {
                    alert('Error loading employee data');
                }
            });
        }

        $(document).on('submit', '#editEmployeeForm', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'emp-list.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating employee');
                }
            });
        });

        function deleteEmployee(id, name) {
            if (confirm(`Are you sure you want to delete ${name}?\n\nThis action cannot be undone!`)) {
                $.ajax({
                    url: 'emp-list.php',
                    type: 'POST',
                    data: { 
                        ajax_action: 'delete_employee',
                        id: id 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error deleting employee');
                    }
                });
            }
        }

        function closeModal(modalId) {
            $('#' + modalId).fadeOut();
        }

        $(window).click(function(event) {
            if (event.target.className === 'modal') {
                $(event.target).fadeOut();
            }
        });
    </script>
</body>
</html>