<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #2d3748;
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

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .logo {
            padding: 20px 30px;
            color: white;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .sidebar.collapsed .logo {
            padding: 20px 10px;
            font-size: 18px;
            text-align: center;
        }

        .logo span {
            font-size: 14px;
            opacity: 0.8;
            display: block;
            margin-top: 5px;
            font-weight: normal;
        }

        .sidebar.collapsed .logo span {
            display: none;
        }

        .nav-menu {
            list-style: none;
            padding: 0 15px;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 18px;
            min-width: 20px;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar.collapsed .nav-text {
            display: none;
        }

        .menu-toggle {
            position: absolute;
            right: -15px;
            top: 30px;
            width: 30px;
            height: 30px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            color: #667eea;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 20px 30px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: 80px;
        }

        /* Top Bar */
        .topbar {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .topbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
            font-size: 20px;
            padding: 10px;
            background: #f7fafc;
            border-radius: 8px;
            color: #667eea;
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #f56565;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
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
            margin-bottom: 2px;
        }

        .user-info p {
            font-size: 12px;
            color: #718096;
        }

        /* Dashboard Content */
        .dashboard-content {
            display: grid;
            gap: 20px;
        }

        /* Punch In/Out Section */
        .punch-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            border-radius: 15px;
            color: white;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        .punch-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .punch-header h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .current-date {
            font-size: 16px;
            opacity: 0.9;
        }

        .punch-time-display {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 30px;
        }

        .time-box {
            background: rgba(255,255,255,0.2);
            padding: 20px 30px;
            border-radius: 10px;
            text-align: center;
        }

        .time-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .time-value {
            font-size: 32px;
            font-weight: bold;
        }

        .punch-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .punch-btn {
            padding: 15px 40px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .punch-in-btn {
            background: #48bb78;
            color: white;
        }

        .punch-in-btn:hover {
            background: #38a169;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
        }

        .punch-out-btn {
            background: #f56565;
            color: white;
        }

        .punch-out-btn:hover {
            background: #e53e3e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 101, 101, 0.4);
        }

        .punch-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .work-duration {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            opacity: 0.9;
        }

        .work-duration span {
            font-size: 24px;
            font-weight: bold;
            display: block;
            margin-top: 5px;
        }

        /* Stats Grid */
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
            position: relative;
            overflow: hidden;
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
        }

        .stat-card.attendance .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-card.present .stat-icon {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .stat-card.absent .stat-icon {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            color: white;
        }

        .stat-card.late .stat-icon {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
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
            font-size: 18px;
            color: #667eea;
            margin-left: 5px;
        }

        /* Attendance Calendar */
        .attendance-calendar {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header h3 {
            color: #2d3748;
        }

        .calendar-nav {
            display: flex;
            gap: 10px;
        }

        .calendar-nav button {
            padding: 8px 15px;
            border: none;
            background: #f7fafc;
            color: #667eea;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .calendar-nav button:hover {
            background: #667eea;
            color: white;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .calendar-day.header {
            font-weight: bold;
            color: #667eea;
            cursor: default;
        }

        .calendar-day.present {
            background: #c6f6d5;
            color: #22543d;
        }

        .calendar-day.absent {
            background: #fed7d7;
            color: #742a2a;
        }

        .calendar-day.leave {
            background: #feebc8;
            color: #744210;
        }

        .calendar-day.today {
            border: 2px solid #667eea;
            font-weight: bold;
        }

        .calendar-day:hover:not(.header) {
            transform: scale(1.05);
        }

        /* Recent Activity */
        .recent-activity {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .recent-activity h3 {
            margin-bottom: 20px;
            color: #2d3748;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }

        .activity-icon.in {
            background: #c6f6d5;
            color: #22543d;
        }

        .activity-icon.out {
            background: #fed7d7;
            color: #742a2a;
        }

        .activity-details {
            flex: 1;
        }

        .activity-time {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 3px;
        }

        .activity-date {
            font-size: 12px;
            color: #718096;
        }

        .activity-duration {
            font-size: 12px;
            color: #667eea;
            font-weight: 600;
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

            .mobile-menu-btn {
                display: block;
            }

            .punch-time-display {
                flex-direction: column;
                gap: 15px;
            }

            .punch-buttons {
                flex-direction: column;
            }

            .calendar-grid {
                gap: 5px;
            }

            .calendar-day {
                font-size: 12px;
                padding: 5px;
            }
        }

        /* Progress Ring */
        .progress-ring {
            position: relative;
            width: 120px;
            height: 120px;
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
            stroke: #667eea;
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
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
        }

        .progress-label {
            font-size: 12px;
            color: #718096;
        }
    </style>
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="menu-toggle" id="menuToggle">
            <i class="fas fa-chevron-left"></i>
        </div>
        <div class="logo">
            Dayflow
            <span>Employee Portal</span>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="#" class="nav-link active" data-page="dashboard">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-page="attendance">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-text">Attendance</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-page="leave">
                    <i class="fas fa-umbrella-beach"></i>
                    <span class="nav-text">Leave Requests</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-page="profile">
                    <i class="fas fa-user"></i>
                    <span class="nav-text">My Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-page="payroll">
                    <i class="fas fa-money-bill"></i>
                    <span class="nav-text">Payroll</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" id="logoutBtn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <i class="fas fa-bars mobile-menu-btn" id="mobileMenuBtn"></i>
                <h2 id="pageTitle">Attendance Dashboard</h2>
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

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card attendance">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-label">Attendance Rate</div>
                    <div class="stat-value">
                        <span id="attendancePercentage">92</span><span class="stat-percentage">%</span>
                    </div>
                    <div class="progress-ring">
                        <svg width="120" height="120">
                            <circle class="background" cx="60" cy="60" r="52"></circle>
                            <circle class="progress" cx="60" cy="60" r="52" 
                                    stroke-dasharray="326.56" 
                                    stroke-dashoffset="26.12" 
                                    id="attendanceProgress"></circle>
                        </svg>
                        <div class="progress-text">
                            <div class="progress-value" id="attendanceProgressValue">92%</div>
                            <div class="progress-label">This Month</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card present">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-label">Present Days</div>
                    <div class="stat-value" id="presentDays">23</div>
                    <div style="margin-top: 10px; font-size: 14px; color: #718096;">
                        Out of <span id="totalDays">25</span> working days
                    </div>
                </div>

                <div class="stat-card absent">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-label">Absent Days</div>
                    <div class="stat-value" id="absentDays">2</div>
                    <div style="margin-top: 10px; font-size: 14px; color: #718096;">
                        This month
                    </div>
                </div>

                <div class="stat-card late">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-label">Late Arrivals</div>
                    <div class="stat-value" id="lateDays">1</div>
                    <div style="margin-top: 10px; font-size: 14px; color: #718096;">
                        This month
                    </div>
                </div>
            </div>

            <!-- Attendance Calendar -->
            <div class="attendance-calendar">
                <div class="calendar-header">
                    <h3>📅 Monthly Attendance</h3>
                    <div class="calendar-nav">
                        <button id="prevMonth"><i class="fas fa-chevron-left"></i> Prev</button>
                        <button id="currentMonth">January 2026</button>
                        <button id="nextMonth">Next <i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="calendar-grid" id="calendarGrid"></div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <h3>📊 Recent Attendance History</h3>
                <div id="activityList"></div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Attendance Data Storage
        let attendanceData = JSON.parse(localStorage.getItem('attendanceData')) || {};
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();

        $(document).ready(function() {
            initializeApp();
            updateCurrentTime();
            setInterval(updateCurrentTime, 1000);
            setInterval(updateWorkDuration, 1000);
            loadTodayAttendance();
            renderCalendar();
            loadRecentActivity();
            updateStats();
        });

        function initializeApp() {
            // Update current date
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            $('#currentDate').text(today.toLocaleDateString('en-US', options));

            // Sidebar toggle
            $('#menuToggle').click(function() {
                $('#sidebar').toggleClass('collapsed');
                const icon = $(this).find('i');
                if ($('#sidebar').hasClass('collapsed')) {
                    icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                } else {
                    icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                }
            });

            // Mobile menu
            $('#mobileMenuBtn').click(function() {
                $('#sidebar').addClass('active');
                $('#overlay').addClass('active');
            });

            $('#overlay').click(function() {
                $('#sidebar').removeClass('active');
                $(this).removeClass('active');
            });

            // Navigation
            $('.nav-link').click(function(e) {
                e.preventDefault();
                if ($(this).attr('id') !== 'logoutBtn') {
                    $('.nav-link').removeClass('active');
                    $(this).addClass('active');
                    const pageTitle = $(this).find('.nav-text').text();
                    $('#pageTitle').text(pageTitle);
                    showNotification(`Navigating to ${pageTitle}...`, 'info');
                }
            });

            // Punch In Button
            $('#punchInBtn').click(function() {
                punchIn();
            });

            // Punch Out Button
            $('#punchOutBtn').click(function() {
                punchOut();
            });

            // Calendar Navigation
            $('#prevMonth').click(function() {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                renderCalendar();
            });

            $('#nextMonth').click(function() {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                renderCalendar();
            });

            // Logout
            $('#logoutBtn').click(function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    showNotification('Logged out successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 1000);
                }
            });
        }

        function updateCurrentTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            $('#currentTime').text(`${hours}:${minutes}:${seconds}`);
        }

        function punchIn() {
            const today = getDateKey();
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            if (!attendanceData[today]) {
                attendanceData[today] = {
                    punchIn: now.toISOString(),
                    punchOut: null,
                    status: 'present'
                };
                
                $('#punchInTime').text(timeString);
                $('#punchInBtn').prop('disabled', true);
                $('#punchOutBtn').prop('disabled', false);
                
                saveAttendance();
                showNotification('Punched in successfully!', 'success');
                loadRecentActivity();
                updateStats();
            } else {
                showNotification('Already punched in today!', 'warning');
            }
        }

        function punchOut() {
            const today = getDateKey();
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            if (attendanceData[today] && !attendanceData[today].punchOut) {
                attendanceData[today].punchOut = now.toISOString();
                
                $('#punchOutTime').text(timeString);
                $('#punchOutBtn').prop('disabled', true);
                
                saveAttendance();
                showNotification('Punched out successfully!', 'success');
                loadRecentActivity();
            } else {
                showNotification('Already punched out!', 'warning');
            }
        }

        function loadTodayAttendance() {
            const today = getDateKey();
            const todayData = attendanceData[today];

            if (todayData) {
                const punchIn = new Date(todayData.punchIn);
                const punchInTime = punchIn.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                $('#punchInTime').text(punchInTime);
                $('#punchInBtn').prop('disabled', true);
                $('#punchOutBtn').prop('disabled', false);

                if (todayData.punchOut) {
                    const punchOut = new Date(todayData.punchOut);
                    const punchOutTime = punchOut.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    $('#punchOutTime').text(punchOutTime);
                    $('#punchOutBtn').prop('disabled', true);
                }
            }
        }

        function updateWorkDuration() {
            const today = getDateKey();
            const todayData = attendanceData[today];

            if (todayData && todayData.punchIn) {
                const punchIn = new Date(todayData.punchIn);
                const punchOut = todayData.punchOut ? new Date(todayData.punchOut) : new Date();
                
                const diff = punchOut - punchIn;
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                
                $('#durationTime').text(`${hours}h ${minutes}m`);
            }
        }

        function renderCalendar() {
            const monthNames = ["January", "February", "March", "April", "May", "June",
                              "July", "August", "September", "October", "November", "December"];
            
            $('#currentMonth').text(`${monthNames[currentMonth]} ${currentYear}`);
            
            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            
            let calendarHTML = '';
            
            // Day headers
            const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayHeaders.forEach(day => {
                calendarHTML += `<div class="calendar-day header">${day}</div>`;
            });
            
            // Empty cells before first day
            for (let i = 0; i < firstDay; i++) {
                calendarHTML += '<div class="calendar-day"></div>';
            }
            
            // Calendar days
            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const dateKey = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dayData = attendanceData[dateKey];
                
                let classes = 'calendar-day';
                if (dayData) {
                    classes += ` ${dayData.status}`;
                }
                
                if (today.getDate() === day && today.getMonth() === currentMonth && today.getFullYear() === currentYear) {
                    classes += ' today';
                }
                
                const statusIcon = dayData ? (dayData.status === 'present' ? '✓' : '✗') : '';
                calendarHTML += `<div class="${classes}">${day}<br>${statusIcon}</div>`;
            }
            
            $('#calendarGrid').html(calendarHTML);
        }

        function loadRecentActivity() {
            const sortedDates = Object.keys(attendanceData).sort().reverse().slice(0, 7);
            let activityHTML = '';

            sortedDates.forEach(dateKey => {
                const data = attendanceData[dateKey];
                const date = new Date(dateKey);
                const dateStr = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                
                if (data.punchIn) {
                    const punchIn = new Date(data.punchIn);
                    const punchInTime = punchIn.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    
                    let duration = '--';
                    if (data.punchOut) {
                        const punchOut = new Date(data.punchOut);
                        const punchOutTime = punchOut.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                        const diff = punchOut - punchIn;
                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        duration = `${hours}h ${minutes}m`;
                        
                        activityHTML += `
                            <div class="activity-item">
                                <div class="activity-icon out">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <div class="activity-details">
                                    <div class="activity-time">Punch Out: ${punchOutTime}</div>
                                    <div class="activity-date">${dateStr}</div>
                                </div>
                                <div class="activity-duration">${duration}</div>
                            </div>
                        `;
                    }
                    
                    activityHTML += `
                        <div class="activity-item">
                            <div class="activity-icon in">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="activity-details">
                                <div class="activity-time">Punch In: ${punchInTime}</div>
                                <div class="activity-date">${dateStr}</div>
                            </div>
                            <div class="activity-duration">${duration}</div>
                        </div>
                    `;
                }
            });

            $('#activityList').html(activityHTML || '<p style="text-align: center; color: #718096;">No attendance records yet</p>');
        }

        function updateStats() {
            const now = new Date();
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            
            let presentDays = 0;
            let totalWorkingDays = 0;
            let lateDays = 0;
            
            for (let d = new Date(firstDay); d <= lastDay; d.setDate(d.getDate() + 1)) {
                const dayOfWeek = d.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Exclude weekends
                    totalWorkingDays++;
                    
                    const dateKey = getDateKey(d);
                    if (attendanceData[dateKey] && attendanceData[dateKey].status === 'present') {
                        presentDays++;
                        
                        const punchIn = new Date(attendanceData[dateKey].punchIn);
                        if (punchIn.getHours() > 9 || (punchIn.getHours() === 9 && punchIn.getMinutes() > 30)) {
                            lateDays++;
                        }
                    }
                }
            }
            
            const absentDays = totalWorkingDays - presentDays;
            const attendancePercentage = totalWorkingDays > 0 ? Math.round((presentDays / totalWorkingDays) * 100) : 0;
            
            $('#presentDays').text(presentDays);
            $('#totalDays').text(totalWorkingDays);
            $('#absentDays').text(absentDays);
            $('#lateDays').text(lateDays);
            $('#attendancePercentage').text(attendancePercentage);
            $('#attendanceProgressValue').text(attendancePercentage + '%');
            
            // Update progress ring
            const circumference = 2 * Math.PI * 52;
            const offset = circumference - (attendancePercentage / 100) * circumference;
            $('#attendanceProgress').css('stroke-dashoffset', offset);
        }

        function getDateKey(date = new Date()) {
            const d = new Date(date);
            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        }

        function saveAttendance() {
            localStorage.setItem('attendanceData', JSON.stringify(attendanceData));
            renderCalendar();
        }

        function showNotification(message, type) {
            const colors = {
                success: '#48bb78',
                info: '#667eea',
                warning: '#ed8936',
                error: '#f56565'
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
                    boxShadow: '0 5px 20px rgba(0,0,0,0.3)',
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

            if (!$('#notificationAnimation').length) {
                $('<style id="notificationAnimation">')
                    .text(`
                        @keyframes slideInRight {
                            from { transform: translateX(400px); opacity: 0; }
                            to { transform: translateX(0); opacity: 1; }
                        }
                    `)
                    .appendTo('head');
            }

            setTimeout(() => {
                notification.fadeOut(500, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    </script>
</body>
</html>