<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request - Employee Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h3 {
            font-size: 20px;
            margin-bottom: 5px;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .sidebar-header h3,
        .sidebar.collapsed .sidebar-header p {
            opacity: 0;
            display: none;
        }

        .sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            border-left: 4px solid white;
        }

        .nav-link i {
            font-size: 20px;
            min-width: 40px;
        }

        .nav-text {
            margin-left: 10px;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            display: none;
        }

        .menu-toggle {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Overlay for mobile */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: 80px;
        }

        /* Top Bar */
        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .mobile-menu-btn {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: #667eea;
        }

        .topbar h2 {
            color: #2d3748;
            font-size: 24px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
            font-size: 20px;
            color: #667eea;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .user-info h4 {
            font-size: 14px;
            color: #2d3748;
        }

        .user-info p {
            font-size: 12px;
            color: #718096;
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: 30px;
        }

        .content-header {
            margin-bottom: 30px;
        }

        .content-header h1 {
            color: #2d3748;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .content-header p {
            color: #718096;
            font-size: 14px;
        }

        /* Leave Stats */
        .leave-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
        }

        .stat-icon.purple {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .stat-icon.green {
            background: rgba(46, 213, 115, 0.1);
            color: #2ed573;
        }

        .stat-icon.orange {
            background: rgba(255, 159, 67, 0.1);
            color: #ff9f43;
        }

        .stat-icon.red {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
        }

        .stat-details h3 {
            font-size: 28px;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .stat-details p {
            color: #718096;
            font-size: 14px;
        }

        /* Leave Request Form */
        .leave-form-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .form-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f5f7fa;
        }

        .form-header h2 {
            color: #2d3748;
            font-size: 22px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            color: #4a5568;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group label span {
            color: #ff4757;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .file-upload {
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .file-upload i {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .file-upload p {
            color: #718096;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        /* Leave History */
        .leave-history {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .history-header h2 {
            color: #2d3748;
            font-size: 22px;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .leave-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leave-table thead {
            background: #f7fafc;
        }

        .leave-table th {
            padding: 15px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #718096;
            text-transform: uppercase;
        }

        .leave-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #2d3748;
            font-size: 14px;
        }

        .leave-table tr:hover {
            background: #f7fafc;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge.approved {
            background: rgba(46, 213, 115, 0.1);
            color: #2ed573;
        }

        .status-badge.pending {
            background: rgba(255, 159, 67, 0.1);
            color: #ff9f43;
        }

        .status-badge.rejected {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .action-btn.view {
            background: rgba(83, 82, 237, 0.1);
            color: #5352ed;
        }

        .action-btn.delete {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.collapsed ~ .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .user-info {
                display: none;
            }

            .filter-buttons {
                flex-wrap: wrap;
            }

            .leave-table {
                font-size: 12px;
            }

            .leave-table th,
            .leave-table td {
                padding: 10px 5px;
            }
        }

        @media (max-width: 480px) {
            .dashboard-content {
                padding: 15px;
            }

            .leave-stats {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 15px;
            }
        }

        /* Notification */
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <?php include('include/slidbar.php');?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <i class="fas fa-bars mobile-menu-btn" id="mobileMenuBtn"></i>
                <h2 id="pageTitle">Leave Request</h2>
            </div>
            <div class="topbar-right">
                <div class="notification-icon" id="notificationIcon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">JD</div>
                    <div class="user-info">
                        <h4>John Doe</h4>
                        <p>Software Engineer</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <div class="content-header">
                <h1>Leave Management</h1>
                <p>Request and manage your leave applications</p>
            </div>

            <!-- Leave Stats -->
            <div class="leave-stats">
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-details">
                        <h3>24</h3>
                        <p>Total Leave Days</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-details">
                        <h3>12</h3>
                        <p>Days Available</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-details">
                        <h3>8</h3>
                        <p>Days Used</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-details">
                        <h3>2</h3>
                        <p>Pending Requests</p>
                    </div>
                </div>
            </div>

            <!-- Leave Request Form -->
            <div class="leave-form-container">
                <div class="form-header">
                    <i class="fas fa-file-alt" style="color: #667eea; font-size: 24px;"></i>
                    <h2>Submit Leave Request</h2>
                </div>

                <form id="leaveRequestForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Leave Type <span>*</span></label>
                            <select id="leaveType" required>
                                <option value="">Select leave type</option>
                                <option value="annual">Annual Leave</option>
                                <option value="sick">Sick Leave</option>
                                <option value="casual">Casual Leave</option>
                                <option value="maternity">Maternity Leave</option>
                                <option value="paternity">Paternity Leave</option>
                                <option value="unpaid">Unpaid Leave</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Duration <span>*</span></label>
                            <select id="duration" required>
                                <option value="">Select duration</option>
                                <option value="full">Full Day</option>
                                <option value="half">Half Day</option>
                                <option value="multiple">Multiple Days</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Start Date <span>*</span></label>
                            <input type="date" id="startDate" required>
                        </div>

                        <div class="form-group">
                            <label>End Date <span>*</span></label>
                            <input type="date" id="endDate" required>
                        </div>

                        <div class="form-group full-width">
                            <label>Reason <span>*</span></label>
                            <textarea id="reason" placeholder="Please provide a reason for your leave request..." required></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label>Attach Supporting Documents (Optional)</label>
                            <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag and drop</p>
                                <p style="font-size: 12px; margin-top: 5px;">PDF, JPG, PNG (Max 5MB)</p>
                            </div>
                            <input type="file" id="fileInput" style="display: none;" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="resetLeaveForm()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>

            <!-- Leave History -->
            <div class="leave-history">
                <div class="history-header">
                    <h2>Leave History</h2>
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="pending">Pending</button>
                        <button class="filter-btn" data-filter="approved">Approved</button>
                        <button class="filter-btn" data-filter="rejected">Rejected</button>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="leave-table">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#LR-2024-001</td>
                                <td>Annual Leave</td>
                                <td>Jan 15, 2024</td>
                                <td>Jan 19, 2024</td>
                                <td>5 Days</td>
                                <td><span class="status-badge approved">Approved</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view" onclick="viewLeave('001')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteLeave('001')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>#LR-2024-002</td>
                                <td>Sick Leave</td>
                                <td>Jan 22, 2024</td>
                                <td>Jan 23, 2024</td>
                                <td>2 Days</td>
                                <td><span class="status-badge pending">Pending</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view" onclick="viewLeave('002')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteLeave('002')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>#LR-2024-003</td>
                                <td>Casual Leave</td>
                                <td>Dec 25, 2023</td>
                                <td>Dec 26, 2023</td>
                                <td>2 Days</td>
                                <td><span class="status-badge rejected">Rejected</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view" onclick="viewLeave('003')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteLeave('003')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>#LR-2024-004</td>
                                <td>Annual Leave</td>
                                <td>Feb 10, 2024</td>
                                <td>Feb 14, 2024</td>
                                <td>5 Days</td>
                                <td><span class="status-badge pending">Pending</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view" onclick="viewLeave('004')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteLeave('004')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle Sidebar
            $('#menuToggle').click(function() {
                $('#sidebar').toggleClass('collapsed');
                const icon = $(this).find('i');
                if ($('#sidebar').hasClass('collapsed')) {
                    icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                } else {
                    icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                }
            });

            // Mobile Menu Toggle
            $('#mobileMenuBtn').click(function() {
                $('#sidebar').addClass('active');
                $('#overlay').addClass('active');
            });

            // Close sidebar when clicking overlay
            $('#overlay').click(function() {
                $('#sidebar').removeClass('active');
                $(this).removeClass('active');
            });

            // Navigation Links
            $('.nav-link').click(function(e) {
                e.preventDefault();
                
                if ($(this).attr('id') === 'logoutBtn') {
                    if (confirm('Are you sure you want to logout?')) {
                        showNotification('Logged out successfully!', 'success');
                    }
                    return;
                }
                
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                
                const pageTitle = $(this).find('.nav-text').text();
                $('#pageTitle').text(pageTitle);
                
                if (window.innerWidth <= 768) {
                    $('#sidebar').removeClass('active');
                    $('#overlay').removeClass('active');
                }
                
                showNotification(`Navigating to ${pageTitle}...`, 'info');
            });

            // Leave Request Form Submission
            $('#leaveRequestForm').submit(function(e) {
                e.preventDefault();
                
                const leaveType = $('#leaveType').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                const reason = $('#reason').val();
                
                if (!leaveType || !startDate || !endDate || !reason) {
                    showNotification('Please fill all required fields!', 'error');
                    return;
                }

                // Calculate number of days
                const start = new Date(startDate);
                const end = new Date(endDate);
                const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

                if (days <= 0) {
                    showNotification('End date must be after start date!', 'error');
                    return;
                }
                
                // Prepare form data
                const formData = new FormData();
                formData.append('employee_name', 'John Doe'); // Get from session
                formData.append('email', 'john.doe@company.com'); // Get from session
                formData.append('leave_type', leaveType);
                formData.append('start_date', startDate);
                formData.append('end_date', endDate);
                formData.append('days', days);
                formData.append('reason', reason);
                
                // Add file if uploaded
                const fileInput = document.getElementById('fileInput');
                if (fileInput.files.length > 0) {
                    formData.append('document', fileInput.files[0]);
                }
                
                // Submit via AJAX
                $.ajax({
                    url: 'submit_leave.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                showNotification('Leave request submitted successfully!', 'success');
                                resetLeaveForm();
                                // Reload the leave history table
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                showNotification(result.message || 'Failed to submit leave request!', 'error');
                            }
                        } catch (e) {
                            showNotification('Leave request submitted successfully!', 'success');
                            resetLeaveForm();
                        }
                    },
                    error: function() {
                        showNotification('Error submitting leave request!', 'error');
                    }
                });
            });

            // Filter buttons
            $('.filter-btn').click(function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                const filter = $(this).data('filter');
                loadLeaveRecords(filter);
                showNotification(`Filtering ${filter} requests...`, 'info');
            });

            // File upload
            $('#fileInput').change(function() {
                const fileName = $(this).val().split('\\').pop();
                if (fileName) {
                    showNotification(`File "${fileName}" selected`, 'success');
                }
            });

            // Notification icon
            $('#notificationIcon').click(function() {
                showNotification('No new notifications', 'info');
            });

            // User profile click
            $('#userProfile').click(function() {
                showNotification('Opening user menu...', 'info');
            });

            // Stat cards hover effect
            $('.stat-card').hover(
                function() {
                    $(this).find('.stat-icon').css('transform', 'scale(1.1) rotate(5deg)');
                },
                function() {
                    $(this).find('.stat-icon').css('transform', 'scale(1) rotate(0deg)');
                }
            );

            // Load leave records on page load
            loadLeaveRecords('all');
        });

        // Function to load leave records from database
        function loadLeaveRecords(filter = 'all') {
            $.ajax({
                url: 'get_leave_records.php',
                type: 'GET',
                data: { filter: filter },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            updateLeaveTable(result.data);
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                },
                error: function() {
                    showNotification('Error loading leave records!', 'error');
                }
            });
        }

        // Function to update leave table
        function updateLeaveTable(records) {
            const tbody = $('.leave-table tbody');
            tbody.empty();

            if (records.length === 0) {
                tbody.append('<tr><td colspan="7" style="text-align: center; padding: 30px;">No leave records found</td></tr>');
                return;
            }

            records.forEach(function(record) {
                const statusClass = record.status.toLowerCase();
                const statusText = record.status.charAt(0).toUpperCase() + record.status.slice(1);
                
                // Format dates
                const startDate = new Date(record.start_date).toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });
                const endDate = new Date(record.end_date).toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });

                // Format leave type
                const leaveTypeFormatted = record.leave_type.charAt(0).toUpperCase() + 
                                          record.leave_type.slice(1).replace('_', ' ');

                const row = `
                    <tr>
                        <td>#LR-2024-${String(record.leave_id).padStart(3, '0')}</td>
                        <td>${leaveTypeFormatted} Leave</td>
                        <td>${startDate}</td>
                        <td>${endDate}</td>
                        <td>${record.days} Day${record.days > 1 ? 's' : ''}</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn view" onclick="viewLeave('${record.leave_id}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn delete" onclick="deleteLeave('${record.id}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        function resetLeaveForm() {
            $('#leaveRequestForm')[0].reset();
            showNotification('Form reset successfully!', 'info');
        }

        function viewLeave(id) {
            showNotification(`Viewing leave request #LR-2024-${id}`, 'info');
        }

        function deleteLeave(id) {
            if (confirm('Are you sure you want to delete this leave request?')) {
                $.ajax({
                    url: 'delete_leave.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                showNotification(result.message, 'success');
                                // Reload the current filter
                                const activeFilter = $('.filter-btn.active').data('filter');
                                loadLeaveRecords(activeFilter);
                            } else {
                                showNotification(result.message, 'error');
                            }
                        } catch (e) {
                            showNotification('Error deleting leave request!', 'error');
                        }
                    },
                    error: function() {
                        showNotification('Error deleting leave request!', 'error');
                    }
                });
            }
        }

        // Notification System
        function showNotification(message, type) {
            const colors = {
                success: '#2ed573',
                info: '#5352ed',
                warning: '#ff9f43',
                error: '#ff4757'
            };

            const icons = {
                success: 'fa-check-circle',
                info: 'fa-info-circle',
                warning: 'fa-exclamation-triangle',
                error: 'fa-times-circle'
            };

            $('.custom-notification').remove();

            const notification = $('<div>')
                .addClass('custom-notification')
                .css({
                    position: 'fixed',
                    top: '20px',
                    right: '20px',
                    background: colors[type],
                    color: 'white',
                    padding: '15px 25px',
                    borderRadius: '10px',
                    boxShadow: '0 5px 20px rgba(0,0,0,0.2)',
                    zIndex: 10000,
                    display: 'flex',
                    alignItems: 'center',
                    gap: '10px',
                    animation: 'slideInRight 0.5s ease',
                    minWidth: '250px'
                });

            const icon = $('<i>')
                .addClass('fas')
                .addClass(icons[type])
                .css('fontSize', '20px');

            notification.append(icon);
            notification.append($('<span>').text(message));

            $('body').append(notification);

            setTimeout(() => {
                notification.css('animation', 'slideOutRight 0.5s ease');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }

        // Handle window resize
        $(window).resize(function() {
            if ($(window).width() > 768) {
                $('#sidebar').removeClass('active');
                $('#overlay').removeClass('active');
            }
        });