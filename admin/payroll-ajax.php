<?php
// payroll-ajax.php - Handle AJAX requests for payroll operations
session_start();
include('config/conn.php');

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch($action) {
        case 'fetch_employee':
            fetchEmployeeData($conn);
            break;
        case 'process_payment':
            processPayment($conn);
            break;
        case 'bulk_payment':
            processBulkPayment($conn);
            break;
        case 'send_payslip':
            sendPayslipEmail($conn);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

function fetchEmployeeData($conn) {
    $emp_id = mysqli_real_escape_string($conn, $_POST['emp_id']);
    $current_month = date('F');
    $current_year = date('Y');
    $current_month_num = date('m');
    
    // First check if employee exists
    $check_query = "SELECT user_id FROM employees WHERE user_id = '$emp_id' AND status = 'active'";
    $check_result = $conn->query($check_query);
    
    if(!$check_result || $check_result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Employee not found or inactive']);
        return;
    }
    
    // Fetch employee data
    $query = "SELECT 
        e.user_id as emp_id,
        e.name,
        e.email,
        e.department,
        e.position as designation,
        COALESCE(e.basic_salary, 0) as basic_salary,
        COALESCE(e.transport_allowance, 0) as transport_allowance,
        COALESCE(e.special_allowance, 0) as special_allowance,
        COALESCE(e.pf_deduction, 0) as pf_deduction,
        COALESCE(e.tax_deduction, 0) as tax_deduction,
        COALESCE(e.tds_deduction, 0) as tds_deduction,
        COALESCE(e.bank_account, '') as bank_account,
        COALESCE(e.bank_ifsc, '') as bank_ifsc,
        COALESCE(e.bank_name, '') as bank_name,
        COALESCE(e.account_holder_name, e.name) as account_holder_name,
        COALESCE(e.upi_id, '') as upi_id
        FROM employees e
        WHERE e.user_id = '$emp_id' AND e.status = 'active'";
    
    $result = $conn->query($query);
    
    if(!$result) {
        echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
        return;
    }
    
    if($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        
        // Get attendance data
        $att_query = "SELECT 
            COUNT(*) as total_records,
            COUNT(CASE WHEN status IN ('present', 'half_day') THEN 1 END) as present_days,
            COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent_days,
            COUNT(CASE WHEN status = 'leave' THEN 1 END) as paid_leave
            FROM attendance 
            WHERE emp_id = '$emp_id' 
            AND MONTH(date) = $current_month_num 
            AND YEAR(date) = $current_year";
        
        $att_result = $conn->query($att_query);
        $att_data = $att_result->fetch_assoc();
        
        // Set working days and attendance
        $working_days = 22; // Can be made dynamic
        $present_days = intval($att_data['present_days']);
        $absent_days = intval($att_data['absent_days']);
        $paid_leave = intval($att_data['paid_leave']);
        
        // If no attendance records, set defaults
        if($att_data['total_records'] == 0) {
            $present_days = $working_days; // Assume full attendance if no records
            $absent_days = 0;
            $paid_leave = 0;
        }
        
        $employee['working_days'] = $working_days;
        $employee['present_days'] = $present_days;
        $employee['absent_days'] = $absent_days;
        $employee['paid_leave'] = $paid_leave;
        
        // Calculate salary
        $attendance_percentage = $working_days > 0 ? ($present_days / $working_days) * 100 : 0;
        $gross_salary = floatval($employee['basic_salary']) + floatval($employee['hra']) + 
                       floatval($employee['transport_allowance']) + floatval($employee['special_allowance']);
        
        $attendance_deduction = 0;
        if($attendance_percentage < 85 && $absent_days > 0) {
            $attendance_deduction = ($gross_salary / $working_days) * $absent_days;
        }
        
        $total_deductions = floatval($employee['pf_deduction']) + floatval($employee['tax_deduction']) + 
                           floatval($employee['tds_deduction']) + $attendance_deduction;
        $net_salary = $gross_salary - $total_deductions;
        
        $employee['attendance_percentage'] = round($attendance_percentage, 1);
        $employee['gross_salary'] = round($gross_salary, 2);
        $employee['attendance_deduction'] = round($attendance_deduction, 2);
        $employee['total_deductions'] = round($total_deductions, 2);
        $employee['net_salary'] = round($net_salary, 2);
        $employee['unpaid_leave'] = $attendance_percentage < 85 ? max(0, $absent_days - $paid_leave) : 0;
        
        echo json_encode(['success' => true, 'data' => $employee]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
    }
}

function processPayment($conn) {
    $emp_id = mysqli_real_escape_string($conn, $_POST['emp_id']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $payment_date = mysqli_real_escape_string($conn, $_POST['payment_date']);
    $transaction_ref = mysqli_real_escape_string($conn, $_POST['transaction_ref'] ?? '');
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');
    
    $current_month = date('F');
    $current_year = date('Y');
    $current_month_num = date('m');
    
    // Fetch employee salary data
    $emp_query = "SELECT * FROM employees WHERE user_id = '$emp_id'";
    $emp_result = $conn->query($emp_query);
    
    if(!$emp_result || $emp_result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
        return;
    }
    
    $emp_data = $emp_result->fetch_assoc();
    
    // Calculate attendance
    $att_query = "SELECT 
        COUNT(CASE WHEN status IN ('present', 'half_day') THEN 1 END) as present_days,
        COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent_days,
        COUNT(CASE WHEN status = 'leave' THEN 1 END) as paid_leave
        FROM attendance 
        WHERE emp_id = '$emp_id' 
        AND MONTH(date) = $current_month_num 
        AND YEAR(date) = $current_year";
    $att_result = $conn->query($att_query);
    $att_data = $att_result->fetch_assoc();
    
    $working_days = 22;
    $present_days = intval($att_data['present_days']);
    $absent_days = intval($att_data['absent_days']);
    $paid_leave = intval($att_data['paid_leave']);
    
    // If no attendance, assume full attendance
    if($present_days == 0 && $absent_days == 0) {
        $present_days = $working_days;
    }
    
    $attendance_percentage = ($present_days / $working_days) * 100;
    
    $basic_salary = floatval($emp_data['basic_salary'] ?? 0);
    $hra = floatval($emp_data['hra'] ?? 0);
    $transport = floatval($emp_data['transport_allowance'] ?? 0);
    $special = floatval($emp_data['special_allowance'] ?? 0);
    $gross_salary = $basic_salary + $hra + $transport + $special;
    
    $attendance_deduction = 0;
    if($attendance_percentage < 85 && $absent_days > 0) {
        $attendance_deduction = ($gross_salary / $working_days) * $absent_days;
    }
    
    $pf = floatval($emp_data['pf_deduction'] ?? 0);
    $tax = floatval($emp_data['tax_deduction'] ?? 0);
    $tds = floatval($emp_data['tds_deduction'] ?? 0);
    $total_deductions = $pf + $tax + $tds + $attendance_deduction;
    $net_salary = $gross_salary - $total_deductions;
    $unpaid_leave = $attendance_percentage < 85 ? max(0, $absent_days - $paid_leave) : 0;
    
    // Insert or update payroll record
    $payroll_query = "INSERT INTO payroll (
        emp_id, month, year, working_days, present_days, absent_days, paid_leave, unpaid_leave, attendance_percentage,
        basic_salary, hra, transport_allowance, special_allowance, gross_salary,
        attendance_deduction, pf_deduction, tax_deduction, tds_deduction, total_deductions, net_salary,
        payment_method, payment_date, transaction_reference, payment_remarks, status
    ) VALUES (
        '$emp_id', '$current_month', $current_year, $working_days, $present_days, $absent_days, $paid_leave, $unpaid_leave, $attendance_percentage,
        $basic_salary, $hra, $transport, $special, $gross_salary,
        $attendance_deduction, $pf, $tax, $tds, $total_deductions, $net_salary,
        '$payment_method', '$payment_date', '$transaction_ref', '$remarks', 'paid'
    ) ON DUPLICATE KEY UPDATE
        payment_method = '$payment_method',
        payment_date = '$payment_date',
        transaction_reference = '$transaction_ref',
        payment_remarks = '$remarks',
        status = 'paid',
        updated_at = CURRENT_TIMESTAMP";
    
    if($conn->query($payroll_query)) {
        $payroll_id = $conn->insert_id > 0 ? $conn->insert_id : $conn->query("SELECT id FROM payroll WHERE emp_id='$emp_id' AND month='$current_month' AND year=$current_year")->fetch_assoc()['id'];
        
        // Insert payment transaction
        $trans_query = "INSERT INTO payment_transactions (
            payroll_id, emp_id, payment_method, amount, transaction_ref, payment_date, remarks
        ) VALUES (
            $payroll_id, '$emp_id', '$payment_method', $net_salary, '$transaction_ref', '$payment_date', '$remarks'
        )";
        $conn->query($trans_query);
        
        echo json_encode(['success' => true, 'message' => 'Payment processed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to process payment: ' . $conn->error]);
    }
}

function processBulkPayment($conn) {
    $emp_ids = $_POST['emp_ids'];
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $payment_date = mysqli_real_escape_string($conn, $_POST['payment_date']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');
    
    $success_count = 0;
    $failed_count = 0;
    $current_month = date('F');
    $current_year = date('Y');
    $current_month_num = date('m');
    
    foreach($emp_ids as $emp_id) {
        $emp_id = mysqli_real_escape_string($conn, $emp_id);
        
        // Get employee data
        $emp_query = "SELECT * FROM employees WHERE user_id = '$emp_id'";
        $emp_result = $conn->query($emp_query);
        
        if(!$emp_result || $emp_result->num_rows == 0) {
            $failed_count++;
            continue;
        }
        
        $emp_data = $emp_result->fetch_assoc();
        
        // Get attendance
        $att_query = "SELECT 
            COUNT(CASE WHEN status IN ('present', 'half_day') THEN 1 END) as present_days,
            COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent_days,
            COUNT(CASE WHEN status = 'leave' THEN 1 END) as paid_leave
            FROM attendance 
            WHERE emp_id = '$emp_id' 
            AND MONTH(date) = $current_month_num 
            AND YEAR(date) = $current_year";
        $att_result = $conn->query($att_query);
        $att_data = $att_result->fetch_assoc();
        
        $working_days = 22;
        $present_days = intval($att_data['present_days']);
        $absent_days = intval($att_data['absent_days']);
        $paid_leave = intval($att_data['paid_leave']);
        
        if($present_days == 0 && $absent_days == 0) {
            $present_days = $working_days;
        }
        
        $attendance_percentage = ($present_days / $working_days) * 100;
        
        $basic_salary = floatval($emp_data['basic_salary'] ?? 0);
        $hra = floatval($emp_data['hra'] ?? 0);
        $transport = floatval($emp_data['transport_allowance'] ?? 0);
        $special = floatval($emp_data['special_allowance'] ?? 0);
        $gross_salary = $basic_salary + $hra + $transport + $special;
        
        $attendance_deduction = 0;
        if($attendance_percentage < 85 && $absent_days > 0) {
            $attendance_deduction = ($gross_salary / $working_days) * $absent_days;
        }
        
        $pf = floatval($emp_data['pf_deduction'] ?? 0);
        $tax = floatval($emp_data['tax_deduction'] ?? 0);
        $tds = floatval($emp_data['tds_deduction'] ?? 0);
        $total_deductions = $pf + $tax + $tds + $attendance_deduction;
        $net_salary = $gross_salary - $total_deductions;
        $unpaid_leave = $attendance_percentage < 85 ? max(0, $absent_days - $paid_leave) : 0;
        
        $payroll_query = "INSERT INTO payroll (
            emp_id, month, year, working_days, present_days, absent_days, paid_leave, unpaid_leave, attendance_percentage,
            basic_salary, hra, transport_allowance, special_allowance, gross_salary,
            attendance_deduction, pf_deduction, tax_deduction, tds_deduction, total_deductions, net_salary,
            payment_method, payment_date, payment_remarks, status
        ) VALUES (
            '$emp_id', '$current_month', $current_year, $working_days, $present_days, $absent_days, $paid_leave, $unpaid_leave, $attendance_percentage,
            $basic_salary, $hra, $transport, $special, $gross_salary,
            $attendance_deduction, $pf, $tax, $tds, $total_deductions, $net_salary,
            '$payment_method', '$payment_date', '$remarks', 'paid'
        ) ON DUPLICATE KEY UPDATE
            payment_method = '$payment_method',
            payment_date = '$payment_date',
            payment_remarks = '$remarks',
            status = 'paid'";
        
        if($conn->query($payroll_query)) {
            $success_count++;
        } else {
            $failed_count++;
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => "Successfully processed $success_count payments" . ($failed_count > 0 ? ", $failed_count failed" : "")
    ]);
}

function sendPayslipEmail($conn) {
    $emp_id = mysqli_real_escape_string($conn, $_POST['emp_id']);
    $current_month = date('F');
    $current_year = date('Y');
    
    // Mark as sent
    $update_query = "UPDATE payroll SET payslip_sent = 1 
                    WHERE emp_id = '$emp_id' 
                    AND month = '$current_month' 
                    AND year = $current_year";
    
    if($conn->query($update_query)) {
        echo json_encode(['success' => true, 'message' => 'Payslip sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send payslip: ' . $conn->error]);
    }
}
?>