<?php
// payroll.php - Main payroll management page with PHP/MySQL integration
session_start();
include("config/conn.php");

$current_month = date('F');
$current_year = date('Y');

// Calculate statistics from database
$stats_query = "SELECT 
    COUNT(DISTINCT e.user_id) as total_employees,
    COALESCE(SUM(p.net_salary), 0) as total_payroll,
    COUNT(CASE WHEN p.status = 'pending' THEN 1 END) as pending_count,
    COUNT(CASE WHEN p.status = 'paid' THEN 1 END) as paid_count
    FROM employees e
    LEFT JOIN payroll p ON e.user_id = p.emp_id AND p.month = '$current_month' AND p.year = $current_year
    WHERE e.status = 'active'";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Fetch payroll data with attendance
$payroll_query = "SELECT 
    e.user_id as emp_id,
    e.name,
    e.department,
    e.email,
    e.position as designation,
    e.salary,
    e.special_allowance,
    e.pf_deduction,
    e.tax_deduction,
    e.tds_deduction,
    e.bank_account,
    e.bank_ifsc,
    e.bank_name,
    e.account_holder_name,
    e.upi_id,
    COALESCE(p.status, 'pending') as payment_status,
    COALESCE(p.working_days, 22) as working_days,
    COALESCE(p.present_days, (SELECT COUNT(*) FROM attendance WHERE emp_id = e.user_id AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE()) AND status IN ('present', 'half_day')), 0) as present_days,
    COALESCE(p.absent_days, (SELECT COUNT(*) FROM attendance WHERE emp_id = e.user_id AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE()) AND status = 'absent'), 0) as absent_days,
    COALESCE(p.paid_leave, (SELECT COUNT(*) FROM attendance WHERE emp_id = e.user_id AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE()) AND status = 'leave'), 0) as paid_leave
    FROM employees e
    LEFT JOIN payroll p ON e.user_id = p.emp_id AND p.month = '$current_month' AND p.year = $current_year
    WHERE e.status = 'active'
    ORDER BY e.name";

$payroll_result = $conn->query($payroll_query);

// Avatar colors
$colors = ['#667eea', '#48bb78', '#ed8936', '#9f7aea', '#f56565', '#4299e1'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Management - Dayflow HRMS</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="payroll-styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
            transition: transform 0.3s;
        }

        .logo-container:hover .logo {
            transform: scale(1.1);
        }

        .logo-text {
            color: white;
            font-size: 20px;
            font-weight: 600;
            white-space: nowrap;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            display: none;
        }

        .nav-menu {
            list-style: none;
            padding: 20px 0;
        }

        .nav-item {
            margin: 5px 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            position: relative;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-icon {
            font-size: 20px;
            min-width: 24px;
            margin-right: 15px;
        }

        .sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .nav-text {
            white-space: nowrap;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            display: none;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
            transition: margin-left 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            min-height: 100vh;
        }

        .sidebar.collapsed + .main-content {
            margin-left: 80px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .top-bar {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            animation: slideDown 0.5s ease;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-icon {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .page-title h1 {
            font-size: 28px;
            color: #2d3748;
            margin: 0;
        }

        .page-title p {
            color: #718096;
            font-size: 14px;
            margin: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--card-color);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            background: var(--card-color);
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #718096;
            font-size: 14px;
        }

        .action-bar {
            background: white;
            padding: 20px 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            animation: fadeInUp 0.6s ease;
        }

        .action-bar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .bulk-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-filters {
            display: flex;
            gap: 15px;
            flex: 1;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .btn-custom {
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .payroll-table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            animation: fadeInUp 0.6s ease;
            animation-delay: 0.2s;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f7fafc;
        }

        th {
            padding: 12px 10px;
            text-align: left;
            color: #4a5568;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        td {
            padding: 12px 10px;
            border-top: 1px solid #e2e8f0;
            color: #4a5568;
            white-space: nowrap;
            font-size: 13px;
        }

        tbody tr {
            transition: all 0.3s;
        }

        tbody tr:hover {
            background: #f7fafc;
        }

        .employee-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 13px;
            flex-shrink: 0;
        }

        .employee-details h6 {
            margin: 0;
            color: #2d3748;
            font-size: 13px;
            font-weight: 600;
        }

        .employee-details p {
            margin: 0;
            color: #a0aec0;
            font-size: 11px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-paid {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-pending {
            background: #fef5e7;
            color: #c27803;
        }

        .action-btns {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .btn-view {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-view:hover {
            background: #1e40af;
            color: white;
        }

        .btn-pay {
            background: #c6f6d5;
            color: #22543d;
        }

        .btn-pay:hover {
            background: #22543d;
            color: white;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px 30px;
        }

        .modal-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 30px;
        }

        .form-label {
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .calculation-summary {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            color: #2d3748;
            font-size: 18px;
            padding-top: 15px;
            border-top: 2px solid #667eea;
        }

        .attendance-warning {
            background: #fed7d7;
            border: 2px solid #fc8181;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }

        .attendance-warning i {
            color: #c53030;
        }

        /* Payslip Styles */
        #payslipPreview {
            background: white;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        .payslip-header-section {
            text-align: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info h2 {
            color: #2d3748;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #718096;
            margin: 2px 0;
        }

        .payslip-title {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 10px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 18px;
            font-weight: 600;
        }

        .employee-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .info-item-payslip {
            display: flex;
            flex-direction: column;
        }

        .info-label-payslip {
            color: #718096;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .info-value-payslip {
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }

        .salary-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .salary-table th {
            background: #f7fafc;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
        }

        .salary-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .total-row-payslip {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        .payslip-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #718096;
            font-size: 12px;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #payslipPreview, #payslipPreview * {
                visibility: visible;
            }
            #payslipPreview {
                position: absolute;
                left: 0;
                top: 0;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }

            .sidebar .logo-text,
            .sidebar .nav-text {
                display: none;
            }

            .main-content {
                margin-left: 80px;
                padding: 20px;
            }

            .employee-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<?php include('include/slidbar.php');?>

    <div class="main-content">
        <div class="top-bar">
            <div class="page-header">
                <div class="page-title">
                    <div class="page-icon"><i class="fas fa-wallet"></i></div>
                    <div>
                        <h1>Payroll Management</h1>
                        <p>Process salary with attendance-based calculation</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #667eea, #764ba2);">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?php echo $stats['total_employees']; ?></div>
                        <div class="stat-label">Total Employees</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #48bb78, #38a169);">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">₹<?php echo number_format($stats['total_payroll']); ?></div>
                        <div class="stat-label">Total Payroll (<?php echo $current_month.' '.$current_year; ?>)</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #f6ad55, #ed8936);">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?php echo $stats['pending_count']; ?></div>
                        <div class="stat-label">Pending Payments</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="stat-card" style="--card-color: linear-gradient(135deg, #4299e1, #3182ce);">
                <div class="stat-header">
                    <div>
                        <div class="stat-value"><?php echo $stats['paid_count']; ?></div>
                        <div class="stat-label">Salaries Paid</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="action-bar-content">
                <div class="search-filters">
                    <div class="search-box">
                        <input type="text" id="searchEmployee" placeholder="Search by name or employee ID...">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="bulk-actions">
                    <button class="btn-custom btn-primary-custom" id="bulkPaymentBtn">
                        <i class="fas fa-users"></i> Bulk Payment
                    </button>
                    <button class="btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#processPayrollModal">
                        <i class="fas fa-calculator"></i> Process Single Payroll
                    </button>
                </div>
            </div>
        </div>

        <!-- Payroll Table -->
        <div class="payroll-table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Attendance %</th>
                            <th>Working Days</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Gross Salary</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="payrollTableBody">
                        <?php 
                        $index = 0;
                        while($row = $payroll_result->fetch_assoc()): 
                            $initials = strtoupper(substr($row['name'], 0, 1) . substr(strstr($row['name'], ' '), 1, 1));
                            $color = $colors[$index % count($colors)];
                            
                            $attendance_percentage = ($row['present_days'] / $row['working_days']) * 100;
                            $gross_salary = $row['salary'] + $row['special_allowance'];
                            
                            $attendance_deduction = 0;
                            if($attendance_percentage < 85) {
                                $attendance_deduction = ($gross_salary / $row['working_days']) * $row['absent_days'];
                            }
                            
                            $total_deductions = $row['pf_deduction'] + $row['tax_deduction'] + $row['tds_deduction'] + $attendance_deduction;
                            $net_salary = $gross_salary - $total_deductions;
                            
                            $status_class = $row['payment_status'] == 'paid' ? 'status-paid' : 'status-pending';
                            $status_text = $row['payment_status'] == 'paid' ? 'Paid' : 'Pending';
                            $status_icon = $row['payment_status'] == 'paid' ? 'check-double' : 'clock';
                            $disabled = $row['payment_status'] == 'paid' ? 'disabled' : '';
                            $index++;
                        ?>
                        <tr data-emp-id="<?php echo $row['emp_id']; ?>">
                            <td><input type="checkbox" class="employee-checkbox" value="<?php echo $row['emp_id']; ?>" <?php echo $disabled; ?>></td>
                            <td>
                                <div class="employee-cell">
                                    <div class="employee-avatar" style="background: <?php echo $color; ?>;"><?php echo $initials; ?></div>
                                    <div class="employee-details">
                                        <h6><?php echo htmlspecialchars($row['name']); ?></h6>
                                        <p><?php echo htmlspecialchars($row['emp_id']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td><strong><?php echo number_format($attendance_percentage, 1); ?>%</strong></td>
                            <td><?php echo $row['working_days']; ?></td>
                            <td><?php echo $row['present_days']; ?></td>
                            <td><?php echo $row['absent_days']; ?></td>
                            <td>₹<?php echo number_format($gross_salary); ?></td>
                            <td><strong>₹<?php echo number_format($net_salary); ?></strong></td>
                            <td><span class="status-badge <?php echo $status_class; ?>"><i class="fas fa-<?php echo $status_icon; ?>"></i> <?php echo $status_text; ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <?php if($row['payment_status'] == 'pending'): ?>
                                        <button class="action-btn btn-pay" onclick="openPaymentModal('<?php echo $row['emp_id']; ?>')" title="Process Payment">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="action-btn btn-view" onclick="viewPayslip('<?php echo $row['emp_id']; ?>')" title="View Payslip">
                                            <i class="fas fa-file-invoice"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'payroll-modals.php'; ?>
    
    <script>
        $(document).ready(function() {
    let currentEmployee = null;
    let selectedEmployees = [];

    // Sidebar toggle
    $('#toggleBtn').click(function() {
        $('#sidebar').toggleClass('collapsed');
    });

    // Set today's date
    const today = new Date().toISOString().split('T')[0];
    $('#paymentDate, #bulkPaymentDate').val(today);

    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.employee-checkbox:not(:disabled)').prop('checked', isChecked);
        updateSelectedEmployees();
    });

    // Individual checkbox
    $(document).on('change', '.employee-checkbox', function() {
        updateSelectedEmployees();
    });

    function updateSelectedEmployees() {
        selectedEmployees = [];
        $('.employee-checkbox:checked').each(function() {
            selectedEmployees.push($(this).val());
        });
        
        const totalCheckboxes = $('.employee-checkbox:not(:disabled)').length;
        const checkedCheckboxes = $('.employee-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    }

    // Payment method change
    $('#paymentMethod').on('change', function() {
        const method = $(this).val();
        $('#bankTransferDetails, #upiDetails, #cashDetails, #chequeDetails').hide();
        
        if (!currentEmployee) return;
        
        if (method === 'bank') {
            $('#bankAccountName').val(currentEmployee.account_holder_name);
            $('#bankAccountNumber').val(currentEmployee.bank_account);
            $('#bankIFSC').val(currentEmployee.bank_ifsc);
            $('#bankName').val(currentEmployee.bank_name);
            $('#bankTransferDetails').slideDown();
        } else if (method === 'upi') {
            $('#upiId').val(currentEmployee.upi_id);
            $('#upiDetails').slideDown();
        } else if (method === 'cash') {
            $('#cashReceivedBy').val(currentEmployee.name);
            $('#cashDetails').slideDown();
        } else if (method === 'cheque') {
            $('#chequePayee').val(currentEmployee.name);
            $('#chequeDate').val(today);
            $('#chequeDetails').slideDown();
        }
    });

    // Bulk payment method
    $('#bulkPaymentMethod').on('change', function() {
        const method = $(this).val();
        $('#bulkBankDetails, #bulkUpiDetails, #bulkCashDetails, #bulkChequeDetails').hide();
        
        if (method === 'bank') $('#bulkBankDetails').slideDown();
        else if (method === 'upi') $('#bulkUpiDetails').slideDown();
        else if (method === 'cash') $('#bulkCashDetails').slideDown();
        else if (method === 'cheque') {
            $('#bulkChequeDate').val(today);
            $('#bulkChequeDetails').slideDown();
        }
    });

    // Fetch employee data
    $('#fetchEmployeeBtn').click(function() {
        const empId = $('#empIdInput').val().trim().toUpperCase();
        
        if (!empId) {
            alert('Please enter an Employee ID');
            return;
        }

        $.ajax({
            url: 'payroll-ajax.php',
            method: 'POST',
            data: { action: 'fetch_employee', emp_id: empId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentEmployee = response.data;
                    displayEmployeeData(currentEmployee);
                } else {
                    alert(response.message || 'Employee not found');
                }
            },
            error: function() {
                alert('Error fetching employee data');
            }
        });
    });

    function displayEmployeeData(emp) {
        // Display employee info
        $('#empName').text(emp.name);
        $('#empDept').text(emp.department);
        $('#empEmail').text(emp.email);
        $('#empWorkingDays').text(emp.working_days);
        $('#empPresent').text(emp.present_days);
        $('#empAbsent').text(emp.absent_days);
        $('#empAttendance').html('<strong>' + emp.attendance_percentage + '%</strong>');

        // Show warning if attendance < 85%
        if (emp.attendance_percentage < 85) {
            $('#attendanceWarning').show();
            $('#attendanceDeductionRow').show();
        } else {
            $('#attendanceWarning').hide();
            $('#attendanceDeductionRow').hide();
        }

        // Display calculations
        const allowances = parseFloat(emp.hra) + parseFloat(emp.transport_allowance) + parseFloat(emp.special_allowance);
        
        $('#calcBasic').text('₹' + parseFloat(emp.basic_salary).toLocaleString());
        $('#calcAllowances').text('₹' + allowances.toLocaleString());
        $('#calcGross').text('₹' + parseFloat(emp.gross_salary).toLocaleString());
        $('#calcAttendanceDeduction').text('- ₹' + parseFloat(emp.attendance_deduction).toLocaleString());
        
        const standardDeductions = parseFloat(emp.pf_deduction) + parseFloat(emp.tax_deduction) + parseFloat(emp.tds_deduction);
        $('#calcDeductions').text('- ₹' + standardDeductions.toLocaleString());
        $('#calcNet').text('₹' + parseFloat(emp.net_salary).toLocaleString());

        $('#employeeDataSection').slideDown();
        $('#processSalaryBtn, #saveForLaterBtn').show();
    }

    // Process salary payment
    $('#processSalaryBtn').click(function() {
        const paymentMethod = $('#paymentMethod').val();
        const paymentDate = $('#paymentDate').val();

        if (!paymentMethod || !paymentDate) {
            alert('Please select payment method and date');
            return;
        }

        // Validate based on payment method
        if (paymentMethod === 'bank' && !$('#bankTransactionRef').val()) {
            alert('Please enter transaction reference number');
            return;
        }
        if (paymentMethod === 'cash' && (!$('#cashPaidBy').val() || !$('#cashConfirmation').is(':checked'))) {
            alert('Please complete cash payment details');
            return;
        }
        if (paymentMethod === 'cheque' && !$('#chequeNumber').val()) {
            alert('Please enter cheque number');
            return;
        }

        const formData = {
            action: 'process_payment',
            emp_id: currentEmployee.emp_id,
            payment_method: paymentMethod,
            payment_date: paymentDate,
            transaction_ref: $('#bankTransactionRef').val() || $('#chequeNumber').val() || '',
            remarks: $('#paymentRemarks').val()
        };

        $.ajax({
            url: 'payroll-ajax.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    generatePayslip(currentEmployee, paymentMethod, paymentDate);
                    $('#processPayrollModal').modal('hide');
                    $('#payslipModal').modal('show');
                } else {
                    alert(response.message || 'Payment processing failed');
                }
            },
            error: function() {
                alert('Error processing payment');
            }
        });
    });

    // Bulk payment button
    $('#bulkPaymentBtn').on('click', function() {
        if (selectedEmployees.length === 0) {
            alert('Please select at least one employee');
            return;
        }
        
        // Calculate bulk summary (simplified - in production get from server)
        $('#bulkSelectedCount').text(selectedEmployees.length);
        $('#bulkSummaryCount').text(selectedEmployees.length);
        $('#bulkPaymentDate').val(today);
        $('#bulkPaymentModal').modal('show');
    });

    // Process bulk payment
    $('#processBulkPaymentBtn').on('click', function() {
        const paymentMethod = $('#bulkPaymentMethod').val();
        const paymentDate = $('#bulkPaymentDate').val();
        
        if (!paymentMethod || !paymentDate) {
            alert('Please fill all required fields');
            return;
        }

        $.ajax({
            url: 'payroll-ajax.php',
            method: 'POST',
            data: {
                action: 'bulk_payment',
                emp_ids: selectedEmployees,
                payment_method: paymentMethod,
                payment_date: paymentDate,
                remarks: $('#bulkRemarks').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Bulk payment failed');
                }
            },
            error: function() {
                alert('Error processing bulk payment');
            }
        });
    });

    function generatePayslip(emp, paymentMethod, paymentDate) {
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const currentDate = new Date();
        const month = months[currentDate.getMonth()];
        const year = currentDate.getFullYear();
        
        $('#payslipMonth').text(month.toUpperCase() + ' ' + year);
        $('#payslipEmpName').text(emp.name);
        $('#payslipEmpId').text(emp.emp_id);
        $('#payslipDept').text(emp.department);
        $('#payslipDesignation').text(emp.designation);
        $('#payslipPeriod').text('01 ' + month.substring(0, 3) + ' ' + year + ' - 31 ' + month.substring(0, 3) + ' ' + year);
        $('#payslipPayDate').text(new Date(paymentDate).toLocaleDateString('en-IN'));
        
        // Attendance
        $('#payslipWorkDays').text(emp.working_days);
        $('#payslipPresent').text(emp.present_days);
        $('#payslipAbsent').text(emp.absent_days);
        $('#payslipAttendancePer').html('<strong>' + emp.attendance_percentage + '%</strong>');
        $('#payslipLeave').text(emp.paid_leave);
        $('#payslipUnpaidLeave').text(emp.unpaid_leave);

        // Earnings
        $('#payslipBasic').text('₹' + parseFloat(emp.basic_salary).toLocaleString());
        $('#payslipHRA').text('₹' + parseFloat(emp.hra).toLocaleString());
        $('#payslipTransport').text('₹' + parseFloat(emp.transport_allowance).toLocaleString());
        $('#payslipSpecial').text('₹' + parseFloat(emp.special_allowance).toLocaleString());
        $('#payslipGrossEarn').text('₹' + parseFloat(emp.gross_salary).toLocaleString());

        // Deductions
        if (emp.attendance_percentage < 85) {
            $('#attendanceDeductionRowPayslip').show();
            $('#payslipAttDeduct').text('₹' + parseFloat(emp.attendance_deduction).toLocaleString());
        } else {
            $('#attendanceDeductionRowPayslip').hide();
        }
        
        $('#payslipPF').text('₹' + parseFloat(emp.pf_deduction).toLocaleString());
        $('#payslipTax').text('₹' + parseFloat(emp.tax_deduction).toLocaleString());
        $('#payslipTDS').text('₹' + parseFloat(emp.tds_deduction).toLocaleString());
        $('#payslipTotalDeduct').text('₹' + parseFloat(emp.total_deductions).toLocaleString());

        // Net salary
        $('#payslipNetPay').text('₹' + parseFloat(emp.net_salary).toLocaleString());
        $('#payslipPayMethod').text(paymentMethod.toUpperCase());
        $('#payslipGenDate').text(new Date().toLocaleString('en-IN'));
    }

    // Download PDF
    $('#downloadPDFBtn').click(function() {
        window.print();
    });

    // Send email
    $('#sendEmailBtn').click(function() {
        if (!currentEmployee) {
            alert('No employee data');
            return;
        }

        $.ajax({
            url: 'payroll-ajax.php',
            method: 'POST',
            data: {
                action: 'send_payslip',
                emp_id: currentEmployee.emp_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('✅ Payslip sent successfully to ' + currentEmployee.email);
                    location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Search functionality
    $('#searchEmployee').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#payrollTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
        });
    });

    // Global functions for table buttons
    window.openPaymentModal = function(empId) {
        $('#empIdInput').val(empId);
        $('#fetchEmployeeBtn').click();
        $('#processPayrollModal').modal('show');
    };

    window.viewPayslip = function(empId) {
        $.ajax({
            url: 'payroll-ajax.php',
            method: 'POST',
            data: { action: 'fetch_employee', emp_id: empId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentEmployee = response.data;
                    generatePayslip(currentEmployee, 'Bank Transfer', today);
                    $('#payslipModal').modal('show');
                }
            }
        });
    };
});
    </script>
</body>
</html>