<?php
include("config/conn.php");

header('Content-Type: application/json');

if (
    !isset($_SESSION['logged_in']) ||
    !isset($_SESSION['user_id']) ||
    !in_array(strtolower($_SESSION['user_role']), ['admin', 'hr'])
) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if (!in_array($action, ['approve', 'reject']) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

try {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    // Get leave details
    $query = "SELECT * FROM leave_records WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $leave = mysqli_fetch_assoc($result);
    
    if (!$leave) {
        throw new Exception('Leave request not found');
    }
    
    if ($leave['status'] !== 'pending') {
        throw new Exception('This leave request has already been processed');
    }
    
    // Update status
    $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
    $updateQuery = "UPDATE leave_records SET status = ?, processed_date = NOW() WHERE id = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "si", $newStatus, $id);
    
    if (!mysqli_stmt_execute($updateStmt)) {
        throw new Exception('Failed to update leave status');
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Send email notification (optional - you can implement this)
    // sendEmailNotification($leave['email'], $leave['employee_name'], $newStatus, $leave);
    
    echo json_encode([
        'success' => true,
        'message' => 'Leave request ' . $newStatus . ' successfully',
        'status' => $newStatus
    ]);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);

// Optional: Email notification function
function sendEmailNotification($email, $name, $status, $leaveDetails) {
    // Implement your email sending logic here
    // You can use PHPMailer or mail() function
    
    $subject = "Leave Request " . ucfirst($status);
    $message = "Dear $name,\n\n";
    $message .= "Your leave request has been $status.\n\n";
    $message .= "Leave Type: " . $leaveDetails['leave_type'] . "\n";
    $message .= "Start Date: " . $leaveDetails['start_date'] . "\n";
    $message .= "End Date: " . $leaveDetails['end_date'] . "\n";
    $message .= "Duration: " . $leaveDetails['days'] . " days\n\n";
    $message .= "Best regards,\nHR Department";
    
    // mail($email, $subject, $message);
}
?>