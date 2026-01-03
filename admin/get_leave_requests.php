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

try {
    $query = "SELECT * FROM leave_records ORDER BY 
              CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'approved' THEN 2 
                WHEN status = 'rejected' THEN 3 
              END, 
              created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    
    $leaveRequests = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $leaveRequests[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $leaveRequests
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching leave requests: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>