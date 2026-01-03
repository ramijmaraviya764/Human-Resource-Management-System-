<?php
session_start();
header('Content-Type: application/json');
include("config/conn.php");

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
    
    // Get total days in the month
    $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    // Query to get all employees
    $employeeQuery = "SELECT 
        id,
        name,
        user_id as empId,
        email,
        department as dept,
        position as designation,
        salary,
        status
    FROM employees 
    WHERE status = 'active'
    ORDER BY name ASC";
    
    $employeeResult = mysqli_query($conn, $employeeQuery);
    
    if (!$employeeResult) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }
    
    $employees = [];
    $totalPayroll = 0;
    $pendingCount = 0;
    $paidCount = 0;
    
    // Array of colors for avatars
    $colors = ['#667eea', '#764ba2', '#48bb78', '#38a169', '#f6ad55', '#ed8936', '#4299e1', '#3182ce'];
    
    while ($emp = mysqli_fetch_assoc($employeeResult)) {
        $empId = $emp['empId'];
        
        // Get attendance data for the month
        $attendanceQuery = "SELECT 
            COUNT(*) as total_records,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
            SUM(CASE WHEN status = 'leave' THEN 1 ELSE 0 END) as leave_days,
            SUM(CASE WHEN status = 'half_day' THEN 0.5 ELSE 0 END) as half_days
        FROM attendance 
        WHERE emp_id = ? 
        AND MONTH(date) = ? 
        AND YEAR(date) = ?";
        
        $stmt = mysqli_prepare($conn, $attendanceQuery);
        mysqli_stmt_bind_param($stmt, "sii", $empId, $month, $year);
        mysqli_stmt_execute($stmt);
        $attendanceResult = mysqli_stmt_get_result($stmt);
        $attendance = mysqli_fetch_assoc($attendanceResult);
        
        $presentDays = floatval($attendance['present_days']) + floatval($attendance['half_days']);
        $absentDays = intval($attendance['absent_days']);
        $leaveDays = intval($attendance['leave_days']);
        
        // Calculate working days (assuming 26 working days per month, adjust as needed)
        $workingDays = 26;
        
        // Calculate attendance percentage
        $attendancePercentage = $workingDays > 0 ? round(($presentDays / $workingDays) * 100, 2) : 0;
        
        // Salary calculations
        $grossSalary = floatval($emp['salary']);
        $basicSalary = round($grossSalary * 0.50); // 50% of gross
        $hra = round($grossSalary * 0.20); // 20% HRA
        $transport = round($grossSalary * 0.10); // 10% Transport
        $special = round($grossSalary * 0.20); // 20% Special allowance
        
        // Deductions
        $pf = round($basicSalary * 0.12); // 12% PF on basic
        $tax = round($grossSalary * 0.05); // 5% tax
        $tds = round($grossSalary * 0.02); // 2% TDS
        
        // Attendance-based deduction (if attendance < 85%)
        $attendanceDeduction = 0;
        if ($attendancePercentage < 85) {
            $attendanceDeduction = round(($grossSalary / $workingDays) * $absentDays);
        }
        
        $totalDeductions = $pf + $tax + $tds + $attendanceDeduction;
        $netSalary = $grossSalary - $totalDeductions;
        
        // Check if payroll is already processed for this month
        $payrollQuery = "SELECT id, payment_status 
                        FROM payroll 
                        WHERE emp_id = ? 
                        AND month = ? 
                        AND year = ?";
        
        $stmt = mysqli_prepare($conn, $payrollQuery);
        mysqli_stmt_bind_param($stmt, "sii", $empId, $month, $year);
        mysqli_stmt_execute($stmt);
        $payrollResult = mysqli_stmt_get_result($stmt);
        $payroll = mysqli_fetch_assoc($payrollResult);
        
        $status = 'pending';
        if ($payroll && isset($payroll['payment_status'])) {
            $status = $payroll['payment_status'] === 'paid' ? 'paid' : 'pending';
        }
        
        if ($status === 'paid') {
            $paidCount++;
        } else {
            $pendingCount++;
        }
        
        $totalPayroll += $netSalary;
        
        // Random color for avatar
        $color = $colors[array_rand($colors)];
        
        $employees[] = [
            'empId' => $empId,
            'name' => $emp['name'],
            'email' => $emp['email'],
            'dept' => $emp['dept'],
            'designation' => $emp['designation'],
            'workingDays' => $workingDays,
            'present' => round($presentDays, 1),
            'absent' => $absentDays,
            'paidLeave' => $leaveDays,
            'attendancePercentage' => $attendancePercentage,
            'grossSalary' => $grossSalary,
            'basicSalary' => $basicSalary,
            'hra' => $hra,
            'transport' => $transport,
            'special' => $special,
            'pf' => $pf,
            'tax' => $tax,
            'tds' => $tds,
            'attendanceDeduction' => $attendanceDeduction,
            'totalDeductions' => $totalDeductions,
            'netSalary' => $netSalary,
            'status' => $status,
            'color' => $color,
            // Bank details (you may need to add these fields to your employees table)
            'accountHolderName' => $emp['name'],
            'bankAccount' => 'XXXX' . rand(1000, 9999),
            'bankIFSC' => 'SBIN0001234',
            'bankName' => 'State Bank of India',
            'upiId' => strtolower(str_replace(' ', '', $emp['name'])) . '@paytm'
        ];
    }
    
    $summary = [
        'totalEmployees' => count($employees),
        'totalPayroll' => $totalPayroll,
        'pendingCount' => $pendingCount,
        'paidCount' => $paidCount
    ];
    
    echo json_encode([
        'success' => true,
        'employees' => $employees,
        'summary' => $summary
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>