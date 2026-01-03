<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll - Employee Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
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
        }

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

        .main-content {
            margin-left: 260px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: 80px;
        }

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

        .salary-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .salary-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .salary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .salary-card.paid {
            border-color: #2ed573;
        }

        .salary-card.pending {
            border-color: #ff9f43;
        }

        .salary-card.upcoming {
            border-color: #5352ed;
        }

        .salary-card.total {
            border-color: #667eea;
        }

        .salary-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .salary-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .salary-icon.paid {
            background: rgba(46, 213, 115, 0.1);
            color: #2ed573;
        }

        .salary-icon.pending {
            background: rgba(255, 159, 67, 0.1);
            color: #ff9f43;
        }

        .salary-icon.upcoming {
            background: rgba(83, 82, 237, 0.1);
            color: #5352ed;
        }

        .salary-icon.total {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .salary-amount {
            text-align: right;
        }

        .salary-amount h3 {
            font-size: 28px;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .salary-amount p {
            font-size: 13px;
            color: #718096;
        }

        .salary-card-footer {
            padding-top: 15px;
            border-top: 1px solid #f7fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.success {
            background: rgba(46, 213, 115, 0.1);
            color: #2ed573;
        }

        .status-badge.warning {
            background: rgba(255, 159, 67, 0.1);
            color: #ff9f43;
        }

        .status-badge.info {
            background: rgba(83, 82, 237, 0.1);
            color: #5352ed;
        }

        .payroll-history {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .history-header h2 {
            color: #2d3748;
            font-size: 22px;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .year-filter {
            padding: 8px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .download-btn {
            padding: 8px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            background: #5568d3;
        }

        .payroll-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payroll-table thead {
            background: #f7fafc;
        }

        .payroll-table th {
            padding: 15px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #718096;
            text-transform: uppercase;
        }

        .payroll-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #2d3748;
            font-size: 14px;
        }

        .payroll-table tr:hover {
            background: #f7fafc;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .action-btn.view {
            background: rgba(83, 82, 237, 0.1);
            color: #5352ed;
        }

        .action-btn.download {
            background: rgba(46, 213, 115, 0.1);
            color: #2ed573;
        }

        .action-btn:hover {
            transform: scale(1.05);
        }

        .salary-breakdown {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .breakdown-header {
            margin-bottom: 25px;
        }

        .breakdown-header h2 {
            color: #2d3748;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .breakdown-item {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            border-left: 3px solid #667eea;
        }

        .breakdown-item h4 {
            color: #718096;
            font-size: 13px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .breakdown-amount {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .breakdown-amount .amount {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
        }

        .breakdown-amount .change {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .change.positive {
            background: rgba(46, 213, 115, 0.1);
            color: #2ed573;
        }

        .change.negative {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
        }

        .total-section {
            margin-top: 20px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            color: white;
        }

        .total-section h3 {
            font-size: 16px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .total-section .net-salary {
            font-size: 36px;
            font-weight: bold;
        }

        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 10000;
            opacity: 0;
            transform: translateX(400px);
            transition: all 0.3s ease;
        }

        .notification-toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        .notification-toast.success {
            border-left: 4px solid #2ed573;
        }

        .notification-toast.error {
            border-left: 4px solid #ff4757;
        }

        .notification-toast.info {
            border-left: 4px solid #667eea;
        }

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

            .mobile-menu-btn {
                display: block;
            }

            .user-info {
                display: none;
            }

            .breakdown-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>

    <?php include('include/slidbar.php'); ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <i class="fas fa-bars mobile-menu-btn" id="mobileMenuBtn"></i>
                <h2 id="pageTitle">Payroll Management</h2>
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

        <div class="dashboard-content">
            <div class="content-header">
                <h1>Salary & Payroll</h1>
                <p>Track your salary payments and payslips</p>
            </div>

            <div class="salary-overview">
                <div class="salary-card paid">
                    <div class="salary-card-header">
                        <div class="salary-icon paid">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="salary-amount">
                            <h3>$5,500</h3>
                            <p>This Month</p>
                        </div>
                    </div>
                    <div class="salary-card-footer">
                        <span class="status-badge success">
                            <i class="fas fa-check"></i> Paid
                        </span>
                        <small style="color: #718096;">Jan 1, 2026</small>
                    </div>
                </div>

                <div class="salary-card pending">
                    <div class="salary-card-header">
                        <div class="salary-icon pending">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="salary-amount">
                            <h3>$5,500</h3>
                            <p>Next Month</p>
                        </div>
                    </div>
                    <div class="salary-card-footer">
                        <span class="status-badge warning">
                            <i class="fas fa-clock"></i> Upcoming
                        </span>
                        <small style="color: #718096;">Feb 1, 2026</small>
                    </div>
                </div>

                <div class="salary-card upcoming">
                    <div class="salary-card-header">
                        <div class="salary-icon upcoming">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="salary-amount">
                            <h3>12</h3>
                            <p>Payments</p>
                        </div>
                    </div>
                    <div class="salary-card-footer">
                        <span class="status-badge info">This Year</span>
                        <small style="color: #718096;">2026</small>
                    </div>
                </div>

                <div class="salary-card total">
                    <div class="salary-card-header">
                        <div class="salary-icon total">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="salary-amount">
                            <h3>$66,000</h3>
                            <p>Annual Salary</p>
                        </div>
                    </div>
                    <div class="salary-card-footer">
                        <span class="status-badge info">CTC</span>
                        <small style="color: #718096;">Gross</small>
                    </div>
                </div>
            </div>

            <div class="payroll-history">
                <div class="history-header">
                    <h2>Salary History</h2>
                    <div class="filter-group">
                        <select class="year-filter" id="yearFilter">
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                        </select>
                        <button class="download-btn" onclick="downloadAllPayslips()">
                            <i class="fas fa-download"></i> Download All
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="payroll-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Pay Date</th>
                                <th>Gross Salary</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>January 2026</strong></td>
                                <td>Jan 1, 2026</td>
                                <td>$6,000</td>
                                <td>$500</td>
                                <td><strong>$5,500</strong></td>
                                <td><span class="status-badge success">Paid</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view" onclick="viewPayslip('JAN2026')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="action-btn download" onclick="downloadPayslip('JAN2026')">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>December 2025</strong></td>
                                <td>Dec 1, 2025</td>
                                <td>$6,000</td>
                                <td>$500</td>
                                <td><strong>$5,500</strong></td>
                                <td><span class="status-badge success">Paid</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view" onclick="viewPayslip('DEC2025')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="action-btn download" onclick="downloadPayslip('DEC2025')">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>November 2025</strong></td>
                                <td>Nov 1, 2025</td>
                                <td>$6,000</td>
                                <td>$500</td>
                                <td><strong>$5,500</strong></td>
                                <td><span class="status-badge success">Paid</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view" onclick="viewPayslip('NOV2025')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="action-btn download" onclick="downloadPayslip('NOV2025')">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>February 2026</strong></td>
                                <td>Feb 1, 2026</td>
                                <td>$6,000</td>
                                <td>$500</td>
                                <td><strong>$5,500</strong></td>
                                <td><span class="status-badge warning">Upcoming</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view" disabled style="opacity: 0.5; cursor: not-allowed;">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="action-btn download" disabled style="opacity: 0.5; cursor: not-allowed;">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="salary-breakdown">
                <div class="breakdown-header">
                    <h2>Current Month Breakdown</h2>
                    <p style="color: #718096; font-size: 14px;">January 2026</p>
                </div>

                <div class="breakdown-grid">
                    <div class="breakdown-item">
                        <h4>Basic Salary</h4>
                        <div class="breakdown-amount">
                            <span class="amount">$4,000</span>
                            <span class="change positive">
                                <i class="fas fa-arrow-up"></i> 0%
                            </span>
                        </div>
                    </div>

                    <div class="breakdown-item">
                        <h4>House Rent Allowance</h4>
                        <div class="breakdown-amount">
                            <span class="amount">$1,200</span>
                            <span class="change positive">
                                <i class="fas fa-arrow-up"></i> 0%
                            </span>
                        </div>
                    </div>

                    <div class="breakdown-item">
                        <h4>Special Allowance</h4>
                        <div class="breakdown-amount">
                            <span class="amount">$500</span>
                            <span class="change positive">
                                <i class="fas fa-arrow-up"></i> 0%
                            </span>
                        </div>
                    </div>

                    <div class="breakdown-item">
                        <h4>Transport Allowance</h4>
                        <div class="breakdown-amount">
                            <span class="amount">$300</span>
                            <span class="change positive">
                                <i class="fas fa-arrow-up"></i> 0%
                            </span>
                        </div>
                    </div>

                    <div class="breakdown-item" style="border-left-color: #ff4757;">
                        <h4>Tax Deductions</h4>
                        <div class="breakdown-amount">
                            <span class="amount">-$300</span>
                            <span class="change negative">
                                <i class="fas fa-arrow-down"></i> Deducted
                            </span>
                        </div>
                    </div>

                    <div class="breakdown-item" style="border-left-color: #ff4757;">
                        <h4>Insurance</h4>
                        <div class="breakdown-amount">
                            <span class="amount">-$200</span>
                            <span class="change negative">
                                <i class="fas fa-arrow-down"></i> Deducted
                            </span>
                        </div>
                    </div>
                </div>

                <div class="total-section">
                    <h3>Net Salary (Take Home)</h3>
                    <div class="net-salary">$5,500</div>
                    <p style="font-size: 14px; margin-top: 10px; opacity: 0.9;">Credited to your account on Jan 1, 2026</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Notification Toast Function
        function showNotification(message, type = 'info') {
            const toast = $('<div class="notification-toast ' + type + '"></div>');
            
            let icon = '<i class="fas fa-info-circle"></i>';
            if (type === 'success') icon = '<i class="fas fa-check-circle"></i>';
            if (type === 'error') icon = '<i class="fas fa-exclamation-circle"></i>';
            
            toast.html(icon + '<span>' + message + '</span>');
            $('body').append(toast);
            
            setTimeout(() => toast.addClass('show'), 100);
            
            setTimeout(() => {
                toast.removeClass('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // View Payslip Function
        function viewPayslip(payslipId) {
            showNotification('Opening payslip for ' + payslipId + '...', 'info');
            setTimeout(() => {
                showNotification('Payslip loaded successfully!', 'success');
            }, 1000);
        }

        // Download Payslip Function
        function downloadPayslip(payslipId) {
            showNotification('Downloading payslip for ' + payslipId + '...', 'info');
            setTimeout(() => {
                showNotification('Payslip downloaded successfully!', 'success');
            }, 1500);
        }

        // Download All Payslips Function
        function downloadAllPayslips() {
            const year = $('#yearFilter').val();
            showNotification('Preparing all payslips for ' + year + '...', 'info');
            setTimeout(() => {
                showNotification('All payslips downloaded successfully!', 'success');
            }, 2000);
        }

        $(document).ready(function() {
            // Sidebar Toggle
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

            // Close Sidebar on Overlay Click
            $('#overlay').click(function() {
                $('#sidebar').removeClass('active');
                $(this).removeClass('active');
            });
            // Year Filter Change Handler
            $('#yearFilter').change(function() {
                const year = $(this).val();
                showNotification('Loading payroll data for ' + year + '...', 'info');
                setTimeout(() => {
                    showNotification('Payroll data updated!', 'success');
                }, 1000);
            });

            // Notification Icon Click
            $('#notificationIcon').click(function() {
                showNotification('You have 3 new notifications', 'info');
            });

            // User Profile Click
            $('#userProfile').click(function() {
                showNotification('Opening profile settings...', 'info');
            });

            // Animate salary cards on load
            $('.salary-card').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                });
                $(this).animate({
                    'opacity': '1'
                }, {
                    duration: 500,
                    delay: index * 100,
                    complete: function() {
                        $(this).css('transform', 'translateY(0)');
                    }
                });
            });

            // Table Row Hover Effect Enhancement
            $('.payroll-table tbody tr').hover(
                function() {
                    $(this).css('transition', 'all 0.3s ease');
                },
                function() {
                    $(this).css('transition', 'all 0.3s ease');
                }
            );

            // Animate salary breakdown items
            $('.breakdown-item').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateX(-20px)'
                });
                setTimeout(() => {
                    $(this).animate({
                        'opacity': '1'
                    }, {
                        duration: 400,
                        complete: function() {
                            $(this).css('transform', 'translateX(0)');
                        }
                    });
                }, index * 80);
            });

            // Welcome notification on page load
            setTimeout(() => {
                showNotification('Welcome back, John Doe! 👋', 'success');
            }, 500);

            // Search functionality (if search box exists)
            if ($('.search-box input').length) {
                $('.search-box input').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    if (searchTerm.length > 2) {
                        showNotification('Searching for: ' + searchTerm, 'info');
                    }
                });
            }

            // Responsive behavior
            $(window).resize(function() {
                if ($(window).width() > 768) {
                    $('#sidebar').removeClass('active');
                    $('#overlay').removeClass('active');
                }
            });

            // Add smooth scrolling
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 80
                    }, 600);
                }
            });

            // Keyboard shortcuts
            $(document).keydown(function(e) {
                // Ctrl/Cmd + K to toggle sidebar
                if ((e.ctrlKey || e.metaKey) && e.keyCode === 75) {
                    e.preventDefault();
                    $('#menuToggle').click();
                }
            });
        });
    </script>
</body>
</html>