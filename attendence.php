<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

// Database configuration
$host = 'localhost';
$dbname = 'HR';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch all active employees from database
function fetchActiveEmployees($pdo) {
    $stmt = $pdo->prepare("SELECT user_id, name, position, email FROM employees WHERE status = 'active'");
    $stmt->execute();
    $employees = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $employees[$row['user_id']] = [
            'name' => $row['name'],
            'position' => $row['position'],
            'email' => $row['email']
        ];
    }
    
    return $employees;
}

// Check if attendance record exists for today
function getTodayAttendance($pdo, $empId, $date) {
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE emp_id = ? AND date = ?");
    $stmt->execute([$empId, $date]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Insert new attendance record (Punch In)
function punchIn($pdo, $empId, $date, $punchInTime, $status, $employees) {
    try {
        $stmt = $pdo->prepare("INSERT INTO attendance (emp_id, date, punch_in_time, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$empId, $date, $punchInTime, $status]);
        return true;
    } catch(PDOException $e) {
        error_log("Punch In Error: " . $e->getMessage());
        return false;
    }
}

// Update attendance record (Punch Out)
function punchOut($pdo, $empId, $date, $punchOutTime) {
    try {
        // Get punch in time
        $record = getTodayAttendance($pdo, $empId, $date);
        if ($record && $record['punch_in_time']) {
            // Calculate total hours
            $punchIn = new DateTime($date . ' ' . $record['punch_in_time']);
            $punchOut = new DateTime($date . ' ' . $punchOutTime);
            $diff = $punchIn->diff($punchOut);
            $totalHours = $diff->h + ($diff->i / 60);
            
            $stmt = $pdo->prepare("UPDATE attendance SET punch_out_time = ?, total_hours = ?, updated_at = NOW() WHERE emp_id = ? AND date = ?");
            $stmt->execute([$punchOutTime, $totalHours, $empId, $date]);
            return true;
        }
        return false;
    } catch(PDOException $e) {
        error_log("Punch Out Error: " . $e->getMessage());
        return false;
    }
}

// Mark employee as absent
function markAbsent($pdo, $empId, $date, $autoMarked = false) {
    try {
        $notes = $autoMarked ? 'Auto-marked absent (No punch-in by 10:00 AM)' : 'Marked absent';
        $stmt = $pdo->prepare("INSERT INTO attendance (emp_id, date, status, notes, created_at) VALUES (?, ?, 'absent', ?, NOW())");
        $stmt->execute([$empId, $date, $notes]);
        return true;
    } catch(PDOException $e) {
        error_log("Mark Absent Error: " . $e->getMessage());
        return false;
    }
}

// Get today's attendance records
function getTodayAttendanceRecords($pdo, $employees, $date) {
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE date = ? ORDER BY created_at DESC");
    $stmt->execute([$date]);
    $records = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $empId = $row['emp_id'];
        $empData = isset($employees[$empId]) ? $employees[$empId] : ['name' => 'Unknown', 'position' => 'Unknown'];
        
        // Determine attendance type and colors based on status and time
        $attendanceInfo = getAttendanceTypeFromRecord($row);
        
        $records[] = [
            'empId' => $empId,
            'name' => $empData['name'],
            'position' => $empData['position'],
            'date' => $row['date'],
            'punchIn' => $row['punch_in_time'],
            'punchOut' => $row['punch_out_time'],
            'totalHours' => $row['total_hours'],
            'status' => $row['status'],
            'attendanceType' => $attendanceInfo['type'],
            'statusColor' => $attendanceInfo['color'],
            'statusBg' => $attendanceInfo['bg'],
            'notes' => $row['notes'],
            'autoMarked' => strpos($row['notes'], 'Auto-marked') !== false
        ];
    }
    
    return $records;
}

// Get attendance type from database record
function getAttendanceTypeFromRecord($record) {
    if ($record['status'] === 'absent') {
        return ['type' => 'Absent', 'color' => 'text-red-600', 'bg' => 'bg-red-100'];
    }
    
    if ($record['status'] === 'half_day') {
        return ['type' => 'Half Day', 'color' => 'text-orange-600', 'bg' => 'bg-orange-100'];
    }
    
    if ($record['punch_in_time']) {
        $punchInTime = new DateTime($record['date'] . ' ' . $record['punch_in_time']);
        $hours = (int)$punchInTime->format('H');
        $minutes = (int)$punchInTime->format('i');
        $totalMinutes = $hours * 60 + $minutes;
        
        if ($totalMinutes < 480) {
            return ['type' => 'Full Day', 'color' => 'text-green-600', 'bg' => 'bg-green-100'];
        } elseif ($totalMinutes >= 480 && $totalMinutes < 600) {
            return ['type' => 'Full Day (Late)', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-100'];
        } elseif ($totalMinutes >= 600 && $totalMinutes < 840) {
            return ['type' => 'Half Day', 'color' => 'text-orange-600', 'bg' => 'bg-orange-100'];
        }
    }
    
    return ['type' => 'Present', 'color' => 'text-green-600', 'bg' => 'bg-green-100'];
}

// Function to mark absent employees after 10:00 AM
function markAbsentEmployees($pdo, $employees, $totalMinutes) {
    if ($totalMinutes >= 600) {
        $today = date('Y-m-d');
        
        // Get employees who already have attendance records today
        $stmt = $pdo->prepare("SELECT DISTINCT emp_id FROM attendance WHERE date = ?");
        $stmt->execute([$today]);
        $presentEmployees = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $presentEmployees[] = $row['emp_id'];
        }
        
        // Mark remaining employees as absent
        $markedCount = 0;
        foreach ($employees as $empId => $empData) {
            if (!in_array($empId, $presentEmployees)) {
                if (markAbsent($pdo, $empId, $today, true)) {
                    sendAbsentEmail($empData['email'], $empData['name'], $today);
                    $markedCount++;
                }
            }
        }
        
        return $markedCount;
    }
    
    return 0;
}

// Function to send absent email
function sendAbsentEmail($email, $name, $date) {
    $formattedDate = date('l, F j, Y', strtotime($date));
    $subject = "Attendance Alert - Marked Absent";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .alert { background: #fee; border-left: 4px solid #e53e3e; padding: 15px; margin: 20px 0; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Attendance Management System</h1>
            </div>
            <div class='content'>
                <h2>Hello $name,</h2>
                <div class='alert'>
                    <strong>⚠️ Attendance Alert</strong>
                    <p>You have been marked as <strong>ABSENT</strong> for today.</p>
                </div>
                <p><strong>Date:</strong> $formattedDate</p>
                <p><strong>Reason:</strong> No punch-in recorded by 10:00 AM</p>
                <p>If you believe this is an error, please contact HR immediately.</p>
                <p>Thank you,<br><strong>HR Department</strong></p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: attendance@company.com" . "\r\n";
    
    mail($email, $subject, $message, $headers);
    error_log("Absent email sent to: $email ($name) for date: $date");
}

function getAttendanceStatus($punchInTime) {
    $hours = (int)$punchInTime->format('H');
    $minutes = (int)$punchInTime->format('i');
    $totalMinutes = $hours * 60 + $minutes;
    
    if ($totalMinutes < 480) {
        return ['status' => 'present', 'type' => 'Full Day', 'color' => 'text-green-600', 'bg' => 'bg-green-100'];
    } elseif ($totalMinutes >= 480 && $totalMinutes < 600) {
        return ['status' => 'present', 'type' => 'Full Day (Late)', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-100'];
    } elseif ($totalMinutes >= 600 && $totalMinutes < 840) {
        return ['status' => 'absent', 'type' => 'Absent', 'color' => 'text-red-600', 'bg' => 'bg-red-100'];
    } elseif ($totalMinutes >= 840 && $totalMinutes < 1200) {
        return ['status' => 'half_day', 'type' => 'Half Day', 'color' => 'text-orange-600', 'bg' => 'bg-orange-100'];
    } else {
        return ['status' => 'absent', 'type' => 'Invalid Time', 'color' => 'text-gray-600', 'bg' => 'bg-gray-100'];
    }
}

function calculateWorkHours($totalHours) {
    if ($totalHours === null) return '—';
    
    $hours = floor($totalHours);
    $minutes = round(($totalHours - $hours) * 60);
    
    return $hours . 'h ' . $minutes . 'm';
}

function formatTime($time) {
    if ($time === null) return '—';
    return date('h:i:s A', strtotime($time));
}

// Get current time info
$currentDateTime = new DateTime();
$currentHour = (int)$currentDateTime->format('H');
$currentMinute = (int)$currentDateTime->format('i');
$totalMinutes = $currentHour * 60 + $currentMinute;
$today = date('Y-m-d');

// Fetch employees
$employees = fetchActiveEmployees($pdo);

// Auto-mark absent employees (runs once per day after 10 AM)
$markedCount = markAbsentEmployees($pdo, $employees, $totalMinutes);

$message = '';
$messageType = '';

if ($markedCount > 0 && empty($_POST)) {
    $message = "$markedCount employee(s) automatically marked absent after 10:00 AM";
    $messageType = 'info';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empId'])) {
    $empId = strtoupper(trim($_POST['empId']));
    
    if (empty($empId)) {
        $message = 'Please enter Employee ID';
        $messageType = 'error';
    } elseif (!isset($employees[$empId])) {
        $message = 'Invalid Employee ID';
        $messageType = 'error';
    } else {
        $existingRecord = getTodayAttendance($pdo, $empId, $today);
        
        if (!$existingRecord) {
            // Punch In
            $punchInTime = new DateTime();
            $attendanceInfo = getAttendanceStatus($punchInTime);
            
            if ($attendanceInfo['status'] === 'absent') {
                $message = 'Punch In not allowed. Time window exceeded (after 10:00 AM)';
                $messageType = 'error';
            } else {
                if (punchIn($pdo, $empId, $today, $punchInTime->format('H:i:s'), $attendanceInfo['status'], $employees)) {
                    $message = "Welcome {$employees[$empId]['name']}! Punch In recorded ({$attendanceInfo['type']})";
                    $messageType = 'success';
                } else {
                    $message = 'Failed to record Punch In. Please try again.';
                    $messageType = 'error';
                }
            }
        } elseif ($existingRecord['punch_out_time'] === null) {
            // Check if auto-marked absent
            if ($existingRecord['status'] === 'absent' && strpos($existingRecord['notes'], 'Auto-marked') !== false) {
                $message = 'Cannot punch out. You were marked absent for not punching in by 10:00 AM';
                $messageType = 'error';
            } else {
                // Punch Out
                if (punchOut($pdo, $empId, $today, date('H:i:s'))) {
                    $message = "Goodbye {$employees[$empId]['name']}! Punch Out recorded";
                    $messageType = 'success';
                } else {
                    $message = 'Failed to record Punch Out. Please try again.';
                    $messageType = 'error';
                }
            }
        } else {
            $message = 'Attendance already completed for today';
            $messageType = 'info';
        }
    }
}

// Get today's attendance records from database
$attendanceRecords = getTodayAttendanceRecords($pdo, $employees, $today);

// Get current PHP time for display
$phpCurrentTime = date('h:i:s A');
$phpCurrentDate = date('l, F j, Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
    <script>
        let serverTime = new Date('<?php echo date('Y-m-d H:i:s'); ?>');
        
        function updateClock() {
            serverTime.setSeconds(serverTime.getSeconds() + 1);
            
            const timeStr = serverTime.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: true 
            });
            const dateStr = serverTime.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            document.getElementById('current-time').textContent = timeStr;
            document.getElementById('current-date').textContent = dateStr;
            
            const hours = serverTime.getHours();
            const minutes = serverTime.getMinutes();
            const seconds = serverTime.getSeconds();
            
            if (hours === 10 && minutes === 0 && seconds === 0) {
                location.reload();
            }
        }
        
        setInterval(updateClock, 1000);
        window.onload = updateClock;
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 transform hover:scale-[1.01] transition-transform duration-300">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-4 rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Attendance Management</h1>
                        <p class="text-gray-600 mt-1">Employee Time Tracking System</p>
                    </div>
                </div>
                <div class="text-right">
                    <div id="current-time" class="text-3xl font-bold text-gray-800"><?php echo $phpCurrentTime; ?></div>
                    <div id="current-date" class="text-sm text-gray-600 mt-1"><?php echo $phpCurrentDate; ?></div>
                </div>
            </div>
        </div>

        <!-- Punch In/Out Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 transform hover:scale-[1.01] transition-transform duration-300">
            <div class="max-w-md mx-auto">
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-800">Employee Check-In</h2>
                </div>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Employee ID</label>
                        <input
                            type="text"
                            name="empId"
                            placeholder="Enter your Employee ID (e.g., EMP001)"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all duration-300 text-lg"
                            required
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Punch In / Out
                    </button>
                </form>

                <?php if ($message): ?>
                <div class="mt-4 p-4 rounded-xl flex items-center gap-3 animate-fadeIn <?php 
                    echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 
                        ($messageType === 'error' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'); 
                ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?php if ($messageType === 'success'): ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <?php elseif ($messageType === 'error'): ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <?php else: ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <?php endif; ?>
                    </svg>
                    <span class="font-medium"><?php echo htmlspecialchars($message); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Attendance Rules -->
            <div class="mt-8 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-100">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Attendance Rules
                </h3>
                <div class="grid md:grid-cols-2 gap-3 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-gray-700"><strong>Before 8:00 AM:</strong> Full Day</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-gray-700"><strong>8:00 - 10:00 AM:</strong> Full Day (Late)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                        <span class="text-gray-700"><strong>2:00 - 8:00 PM:</strong> Half Day</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-gray-700"><strong>After 10:00 AM:</strong> Auto-marked Absent</span>
                    </div>
                </div>
                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-xs text-yellow-800"><strong>⚠️ Important:</strong> Employees who don't punch in by 10:00 AM will be automatically marked as absent and receive an email notification.</p>
                </div>
            </div>
        </div>

        <!-- Attendance Records -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-center gap-3 mb-6">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-800">Today's Attendance (<?php echo count($attendanceRecords); ?> records)</h2>
            </div>

            <?php if (empty($attendanceRecords)): ?>
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 text-lg">No attendance records yet</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-indigo-50 to-purple-50">
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Employee ID</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Position</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Punch In</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Punch Out</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Hours</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceRecords as $record): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-semibold text-gray-800"><?php echo htmlspecialchars($record['empId']); ?></td>
                            <td class="px-6 py-4 text-gray-600 text-sm"><?php echo htmlspecialchars($record['position']); ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo formatTime($record['punchIn']); ?></td>
                            <td class="px-6 py-4 text-gray-700">
                                <?php echo $record['punchOut'] ? formatTime($record['punchOut']) : ($record['status'] === 'absent' ? '—' : '<span class="text-blue-600 font-medium">In Progress</span>'); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-700 font-medium">
                                <?php echo $record['totalHours'] ? calculateWorkHours($record['totalHours']) : ($record['status'] === 'absent' ? '—' : 'In Progress'); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $record['statusBg'] . ' ' . $record['statusColor']; ?>">
                                    <?php echo htmlspecialchars($record['attendanceType']); ?>
                                    <?php if ($record['autoMarked']): ?>
                                        <span class="text-xs ml-1">(Auto)</span>
                                    <?php endif; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>