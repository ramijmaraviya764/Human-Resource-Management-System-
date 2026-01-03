<?php
header('Content-Type: application/json');
include("admin/config/conn.php");

try {
    $email = $_GET['email'] ?? 'john.doe@company.com'; // Get from session
    
    // Fast query using optimized indexes
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 'approved' THEN days ELSE 0 END) as days_used,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
            24 as total_leave_days,
            (24 - SUM(CASE WHEN status = 'approved' THEN days ELSE 0 END)) as days_available
        FROM leave_records 
        WHERE email = ?
    ");
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_leave_days' => intval($stats['total_leave_days']),
            'days_available' => intval($stats['days_available']),
            'days_used' => intval($stats['days_used']),
            'pending_requests' => intval($stats['pending_requests'])
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>