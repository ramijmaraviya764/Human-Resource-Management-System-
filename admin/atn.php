<?php
// ============================================
// FILE: attendance-analysis.php
// HR Attendance Analysis with Punch In/Out & Reports
// ============================================

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

// Get date range from filter (default: today)
$filter_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$filter_start = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$filter_end = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$filter_emp = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Get all employees for filter dropdown
$emp_list_query = "SELECT id, name, user_id FROM employees WHERE status = 'active' ORDER BY name";
$emp_list_result = mysqli_query($conn, $emp_list_query);

// Build query based on filters
$where_conditions = ["a.date BETWEEN '$filter_start' AND '$filter_end'"];

if (!empty($filter_emp)) {
    $where_conditions[] = "a.emp_id = '$filter_emp'";
}

if (!empty($filter_status)) {
    $where_conditions[] = "a.status = '$filter_status'";
}

$where_clause = implode(' AND ', $where_conditions);

// Get attendance records with employee details
$attendance_query = "
    SELECT 
        a.*,
        e.name,
        e.user_id as employee_id,
        e.department,
        e.position,
        e.email
    FROM attendance a
    INNER JOIN employees e ON a.emp_id = e.id
    WHERE $where_clause
    ORDER BY a.date DESC, a.punch_in_time DESC
";
$attendance_result = mysqli_query($conn, $attendance_query);

// Get statistics for the filtered period
$stats_query = "
    SELECT 
        COUNT(*) as total_records,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
        SUM(CASE WHEN status = 'leave' THEN 1 ELSE 0 END) as leave_count,
        SUM(CASE WHEN status = 'half_day' THEN 1 ELSE 0 END) as half_day_count,
        AVG(CASE WHEN total_hours IS NOT NULL THEN total_hours ELSE 0 END) as avg_hours
    FROM attendance a
    WHERE $where_clause
";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Get late arrivals (after 9:30 AM)
$late_arrivals_query = "
    SELECT COUNT(*) as late_count
    FROM attendance a
    WHERE $where_clause
    AND a.punch_in_time > CONCAT(a.date, ' 09:30:00')
    AND a.status IN ('present', 'half_day')
";
$late_result = mysqli_query($conn, $late_arrivals_query);
$late_stats = mysqli_fetch_assoc($late_result);

// Get early departures (before 5:30 PM)
$early_departures_query = "
    SELECT COUNT(*) as early_count
    FROM attendance a
    WHERE $where_clause
    AND a.punch_out_time < CONCAT(a.date, ' 17:30:00')
    AND a.punch_out_time IS NOT NULL
    AND a.status IN ('present', 'half_day')
";
$early_result = mysqli_query($conn, $early_departures_query);
$early_stats = mysqli_fetch_assoc($early_result);

// Calculate percentages
$total = $stats['total_records'];
$present_percentage = $total > 0 ? round(($stats['present_count'] / $total) * 100, 1) : 0;
$absent_percentage = $total > 0 ? round(($stats['absent_count'] / $total) * 100, 1) : 0;
$leave_percentage = $total > 0 ? round(($stats['leave_count'] / $total) * 100, 1) : 0;
$half_day_percentage = $total > 0 ? round(($stats['half_day_count'] / $total) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Analysis - HR Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .analysis-container {
            padding: 20px;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        .page-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }

        .page-header p {
            margin: 0;
            opacity: 0.9;
        }

        .filter-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
            font-size: 14px;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
            color: white;
        }

        .stat-label {
            font-size: 14px;
            color: #718096;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #2d3748;
        }

        .stat-percentage {
            font-size: 14px;
            margin-top: 5px;
        }

        .attendance-table-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table thead {
            background: #f7fafc;
        }

        .attendance-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
        }

        .attendance-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .attendance-table tbody tr:hover {
            background: #f7fafc;
        }

        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .employee-details {
            display: flex;
            flex-direction: column;
        }

        .employee-name {
            font-weight: 600;
            color: #2d3748;
        }

        .employee-dept {
            font-size: 12px;
            color: #718096;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status-present {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-absent {
            background: #fed7d7;
            color: #742a2a;
        }

        .status-leave {
            background: #bee3f8;
            color: #2c5282;
        }

        .status-half_day, .status-half-day {
            background: #feebc8;
            color: #7c2d12;
        }

        .time-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .time-in {
            background: #c6f6d5;
            color: #22543d;
        }

        .time-out {
            background: #fed7d7;
            color: #742a2a;
        }

        .time-late {
            background: #feebc8;
            color: #7c2d12;
        }

        .time-early {
            background: #bee3f8;
            color: #2c5282;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 64px;
            color: #cbd5e0;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #2d3748;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #718096;
        }

        .progress-ring {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
        }

        .progress-ring svg {
            transform: rotate(-90deg);
        }

        .progress-ring circle {
            fill: none;
            stroke-width: 8;
        }

        .progress-ring .background {
            stroke: #e2e8f0;
        }

        .progress-ring .progress {
            stroke-linecap: round;
            transition: stroke-dashoffset 0.5s ease;
        }

        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .progress-value {
            font-size: 20px;
            font-weight: bold;
            color: #2d3748;
        }

        @media print {
            .filter-section,
            .page-header,
            .filter-actions,
            .btn {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .attendance-table {
                font-size: 12px;
            }

            .attendance-table th,
            .attendance-table td {
                padding: 10px 5px;
            }
        }
    </style>
</head>
<body>
    <?php include("include/slidbar.php");?>

    <div class="main-content">
        <div class="analysis-container">
            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fas fa-chart-line"></i> Attendance Analysis & Reports</h1>
                <p>Comprehensive attendance tracking with punch in/out details and analytics</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-label">Total Records</div>
                    <div class="stat-value"><?php echo $stats['total_records']; ?></div>
                    <div class="stat-percentage" style="color: #718096;">
                        <?php echo date('M j', strtotime($filter_start)) . ' - ' . date('M j, Y', strtotime($filter_end)); ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-label">Present</div>
                    <div class="stat-value"><?php echo $stats['present_count']; ?></div>
                    <div class="progress-ring" style="width: 80px; height: 80px; margin-top: 10px;">
                        <svg width="80" height="80">
                            <circle class="background" cx="40" cy="40" r="34"></circle>
                            <circle class="progress" cx="40" cy="40" r="34" 
                                    style="stroke: #48bb78;"
                                    stroke-dasharray="213.63" 
                                    stroke-dashoffset="<?php echo 213.63 - (213.63 * $present_percentage / 100); ?>"></circle>
                        </svg>
                        <div class="progress-text">
                            <div class="progress-value" style="font-size: 16px;"><?php echo $present_percentage; ?>%</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stat-label">Absent</div>
                    <div class="stat-value"><?php echo $stats['absent_count']; ?></div>
                    <div class="stat-percentage" style="color: #f56565;">
                        <?php echo $absent_percentage; ?>%
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);">
                        <i class="fas fa-umbrella-beach"></i>
                    </div>
                    <div class="stat-label">On Leave</div>
                    <div class="stat-value"><?php echo $stats['leave_count']; ?></div>
                    <div class="stat-percentage" style="color: #4299e1;">
                        <?php echo $leave_percentage; ?>%
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-label">Half Day</div>
                    <div class="stat-value"><?php echo $stats['half_day_count']; ?></div>
                    <div class="stat-percentage" style="color: #ed8936;">
                        <?php echo $half_day_percentage; ?>%
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);">
                        <i class="fas fa-business-time"></i>
                    </div>
                    <div class="stat-label">Avg. Work Hours</div>
                    <div class="stat-value"><?php echo number_format($stats['avg_hours']?? 0, 2); ?></div>
                    <!-- Deprecated: number_format(): Passing null to parameter #1 ($num) of type float is deprecated in /Applications/XAMPP/xamppfiles/htdocs/HR/admin/atn.php on line 638 -->
0
                    <div class="stat-percentage" style="color: #9f7aea;">
                        hours/day
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);">
                        <i class="fas fa-hourglass-end"></i>
                    </div>
                    <div class="stat-label">Late Arrivals</div>
                    <div class="stat-value"><?php echo $late_stats['late_count']; ?></div>
                    <div class="stat-percentage" style="color: #ed8936;">
                        After 9:30 AM
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #63b3ed 0%, #4299e1 100%);">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="stat-label">Early Departures</div>
                    <div class="stat-value"><?php echo $early_stats['early_count']; ?></div>
                    <div class="stat-percentage" style="color: #4299e1;">
                        Before 5:30 PM
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="attendance-table-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-table"></i> Detailed Attendance Records
                    </h3>
                    <span style="color: #718096; font-size: 14px;">
                        Showing <?php echo mysqli_num_rows($attendance_result); ?> records
                    </span>
                </div>

                <?php if (mysqli_num_rows($attendance_result) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="attendance-table" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Punch In</th>
                                    <th>Punch Out</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $colors = ['#667eea', '#48bb78', '#ed8936', '#9f7aea', '#4299e1', '#f56565'];
                                $index = 0;
                                
                                while ($record = mysqli_fetch_assoc($attendance_result)) {
                                    $initials = implode('', array_map(function($word) {
                                        return strtoupper($word[0]);
                                    }, explode(' ', $record['name'])));
                                    
                                    $color = $colors[$index % count($colors)];
                                    $statusClass = 'status-' . str_replace('_', '-', $record['status']);
                                    
                                    $punchInTime = $record['punch_in_time'] ? date( strtotime($record['punch_in_time'])) : '--';
                                    $punchOutTime = $record['punch_out_time'] ? date('g:i A', strtotime($record['punch_out_time'])) : '--';
                                    $totalHours = $record['total_hours'] ? number_format($record['total_hours'], 2) . ' hrs' : '--';
                                    
                                    // Check if late (after 9:30 AM)
                                    $isLate = false;
                                    if ($record['punch_in_time']) {
                                        $punchInDateTime = strtotime($record['punch_in_time']);
                                        $lateTime = strtotime($record['date'] . ' 09:30:00');
                                        $isLate = $punchInDateTime > $lateTime;
                                    }
                                    
                                    // Check if early departure (before 5:30 PM)
                                    $isEarly = false;
                                    if ($record['punch_out_time']) {
                                        $punchOutDateTime = strtotime($record['punch_out_time']);
                                        $earlyTime = strtotime($record['date'] . ' 17:30:00');
                                        $isEarly = $punchOutDateTime < $earlyTime;
                                    }
                                    
                                    echo "<tr>
                                        <td>" . date('M j, Y', strtotime($record['date'])) . "<br>
                                            <span style='font-size: 11px; color: #718096;'>" . date('l', strtotime($record['date'])) . "</span>
                                        </td>
                                        <td>
                                            <div class='employee-info'>
                                                <div class='employee-avatar' style='background: {$color};'>
                                                    {$initials}
                                                </div>
                                                <div class='employee-details'>
                                                    <div class='employee-name'>{$record['name']}</div>
                                                    <div class='employee-dept'>ID: {$record['employee_id']}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{$record['department']}<br>
                                            <span style='font-size: 11px; color: #718096;'>{$record['position']}</span>
                                        </td>
                                        <td>";
                                    
                                    if ($record['punch_in_time']) {
                                        echo "<span class='time-badge " . ($isLate ? 'time-late' : 'time-in') . "'>
                                            <i class='fas fa-sign-in-alt'></i> {$punchInTime}
                                        </span>";
                                        if ($isLate) {
                                            echo "<br><span style='font-size: 10px; color: #ed8936;'>⚠️ Late</span>";
                                        }
                                    } else {
                                        echo "<span style='color: #cbd5e0;'>--</span>";
                                    }
                                    
                                    echo "</td>
                                        <td>";
                                    
                                    if ($record['punch_out_time']) {
                                        echo "<span class='time-badge " . ($isEarly ? 'time-early' : 'time-out') . "'>
                                            <i class='fas fa-sign-out-alt'></i> {$punchOutTime}
                                        </span>";
                                        if ($isEarly) {
                                            echo "<br><span style='font-size: 10px; color: #4299e1;'>⚠️ Early</span>";
                                        }
                                    } else {
                                        echo "<span style='color: #cbd5e0;'>--</span>";
                                    }
                                    
                                    echo "</td>
                                        <td><strong>{$totalHours}</strong></td>
                                        <td><span class='status-badge {$statusClass}'>" . ucfirst(str_replace('_', ' ', $record['status'])) . "</span></td>
                                        <td>" . ($record['notes'] ? $record['notes'] : '<span style="color: #cbd5e0;">--</span>') . "</td>
                                    </tr>";
                                    
                                    $index++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3>No Attendance Records Found</h3>
                        <p>No attendance data matches your filter criteria. Try adjusting your filters.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function resetFilters() {
            window.location.href = 'attendance-analysis.php';
        }

        function printReport() {
            window.print();
        }

        function exportToExcel() {
            const table = document.getElementById('attendanceTable');
            if (!table) {
                alert('No data to export');
                return;
            }
            
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const cols = rows[i].querySelectorAll('td, th');
                let row = [];
                
                for (let j = 0; j < cols.length; j++) {
                    let text = cols[j].innerText.replace(/\n/g, ' ').replace(/,/g, ';');
                    row.push(text);
                }
                
                csv.push(row.join(','));
            }
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'attendance_report_' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        // Animate statistics on load
        $(document).ready(function() {
            $('.stat-value').each(function() {
                const $this = $(this);
                const targetText = $this.text();
                const target = parseFloat(targetText);
                
                if (!isNaN(target)) {
                    $this.text('0');
                    
                    $({ value: 0 }).animate({ value: target }, {
                        duration: 1500,
                        easing: 'swing',
                        step: function() {
                            if (targetText.includes('.')) {
                                $this.text(this.value.toFixed(1));
                            } else {
                                $this.text(Math.floor(this.value));
                            }
                        },
                        complete: function() {
                            $this.text(targetText);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>