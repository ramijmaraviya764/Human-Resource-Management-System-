<?php
session_start();
include("admin/config/conn.php");

// Check login
if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_id']) ||
    empty($_SESSION['user_id']) ||
    !isset($_SESSION['user_role'])
) {
    header("Location: ../login.php");
    exit();
}

// Allow ONLY employee
if (strtolower($_SESSION['user_role']) !== 'employee') {
    header("Location: ../logout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
   <?php include("include/slidbar.php");?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <i class="fas fa-bars mobile-menu-btn" id="mobileMenuBtn"></i>
                <h2 id="pageTitle">Employee Dashboard</h2>
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
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Working Hours</span>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value">168 hrs</div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i>
                        <span>8% from last month</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Attendance Rate</span>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-value">98.5%</div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i>
                        <span>2.5% improvement</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Leave Balance</span>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="stat-value">12 days</div>
                    <div class="stat-change negative">
                        <i class="fas fa-arrow-down"></i>
                        <span>3 days used</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Pending Tasks</span>
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                    <div class="stat-value">8</div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i>
                        <span>2 new tasks</span>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Leave Requests -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Leave Requests</h3>
                        <span class="card-action" id="viewAllLeaves">View All →</span>
                    </div>

                    <div class="leave-item">
                        <div class="leave-header">
                            <span class="leave-type">Annual Leave</span>
                            <span class="leave-status approved">Approved</span>
                        </div>
                        <div class="leave-date">December 20 - 25, 2024 (6 days)</div>
                    </div>

                    <div class="leave-item">
                        <div class="leave-header">
                            <span class="leave-type">Sick Leave</span>
                            <span class="leave-status pending">Pending</span>
                        </div>
                        <div class="leave-date">January 15 - 16, 2025 (2 days)</div>
                    </div>

                    <div class="leave-item">
                        <div class="leave-header">
                            <span class="leave-type">Personal Leave</span>
                            <span class="leave-status pending">Pending</span>
                        </div>
                        <div class="leave-date">February 10, 2025 (1 day)</div>
                    </div>

                    <div class="leave-item">
                        <div class="leave-header">
                            <span class="leave-type">Medical Leave</span>
                            <span class="leave-status approved">Approved</span>
                        </div>
                        <div class="leave-date">November 5 - 7, 2024 (3 days)</div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activity</h3>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="activity-content">
                            <h5>Leave Request Approved</h5>
                            <p>Your annual leave has been approved</p>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon info">
                            <i class="fas fa-info"></i>
                        </div>
                        <div class="activity-content">
                            <h5>Team Meeting</h5>
                            <p>Quarterly review at 10:00 AM tomorrow</p>
                            <div class="activity-time">5 hours ago</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="activity-content">
                            <h5>Attendance Marked</h5>
                            <p>Check-in time: 09:15 AM</p>
                            <div class="activity-time">Today</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon warning">
                            <i class="fas fa-exclamation"></i>
                        </div>
                        <div class="activity-content">
                            <h5>Timesheet Reminder</h5>
                            <p>Submit your timesheet by Friday</p>
                            <div class="activity-time">Yesterday</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle Sidebar
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

            // Close sidebar when clicking overlay
            $('#overlay').click(function() {
                $('#sidebar').removeClass('active');
                $(this).removeClass('active');
            });


            // Stat cards hover effect
            $('.stat-card').hover(
                function() {
                    $(this).find('.stat-icon').css('transform', 'scale(1.1) rotate(5deg)');
                },
                function() {
                    $(this).find('.stat-icon').css('transform', 'scale(1) rotate(0deg)');
                }
            );

            // Leave items click
            $('.leave-item').click(function() {
                const leaveType = $(this).find('.leave-type').text();
                showNotification(`Opening details for ${leaveType}...`, 'info');
            });

            // Quick action buttons
            $('#requestLeaveBtn').click(function() {
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', '');
                    showNotification('Opening leave request form...', 'info');
                }, 150);
            });

            $('#markAttendanceBtn').click(function() {
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', '');
                    showNotification('Attendance marked successfully!', 'success');
                }, 150);
            });

            $('#viewProfileBtn').click(function() {
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', '');
                    showNotification('Opening profile...', 'info');
                }, 150);
            });

            $('#viewPayslipsBtn').click(function() {
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', '');
                    showNotification('Loading payslips...', 'info');
                }, 150);
            });

            // View all leaves
            $('#viewAllLeaves').click(function() {
                showNotification('Loading all leave requests...', 'info');
            });

            // Logout button
            $('#logoutBtn').click(function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    $('body').fadeOut(500, function() {
                        showNotification('Logged out successfully!', 'success');
                        setTimeout(() => {
                            $('body').fadeIn(500);
                        }, 1000);
                    });
                }
            });

            // Notification icon
            $('#notificationIcon').click(function() {
                showNotification('No new notifications', 'info');
            });

            // User profile click
            $('#userProfile').click(function() {
                showNotification('Opening user menu...', 'info');
            });

            // Search functionality
            $('#searchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                if (searchTerm.length > 2) {
                    showNotification('Searching for: ' + searchTerm, 'info');
                }
            });

            // Add smooth scroll animation to page
            $('html, body').css('scroll-behavior', 'smooth');
        });


        // Handle window resize
        $(window).resize(function() {
            if ($(window).width() > 768) {
                $('#sidebar').removeClass('active');
                $('#overlay').removeClass('active');
            }
        });
    </script>
</body>
</html>