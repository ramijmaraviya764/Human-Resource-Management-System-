<?php
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - Professional</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body>
  <?php include("include/slidbar.php");?>

    <div class="main-content">
        <div class="top-bar">
            <div class="welcome-text">
                <h1>Welcome back, Sarah! 👋</h1>
                <p>Here's what's happening with your team today</p>
            </div>
            <div class="top-actions">
                <div class="search-box">
                    <input type="text" placeholder="Search employees...">
                    <i class="fas fa-search"></i>
                </div>
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </button>
                <div class="user-profile">
                    <div class="user-avatar">SM</div>
                    <span>Sarah Miller</span>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card" style="--card-color: #667eea; --card-color-dark: #5a67d8;">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">247</div>
                        <div class="stat-label">Total Employees</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 12% from last month
                </span>
            </div>

            <div class="stat-card" style="--card-color: #48bb78; --card-color-dark: #38a169;">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">234</div>
                        <div class="stat-label">Present Today</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 94.7% attendance
                </span>
            </div>

            <div class="stat-card" style="--card-color: #ed8936; --card-color-dark: #dd6b20;">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">13</div>
                        <div class="stat-label">On Leave</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                </div>
                <span class="stat-change negative">
                    <i class="fas fa-arrow-down"></i> 3 more than usual
                </span>
            </div>

            <div class="stat-card" style="--card-color: #9f7aea; --card-color-dark: #805ad5;">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">18</div>
                        <div class="stat-label">New Applicants</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 5 pending review
                </span>
            </div>
        </div>

        <div class="employee-table">
            <div class="table-header">
                <h3>Recent Employee Activity</h3>
                <div class="filter-tabs">
                    <button class="filter-tab active">All</button>
                    <button class="filter-tab">Active</button>
                    <button class="filter-tab">On Leave</button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Check In</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody">
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Sidebar toggle
            $('#toggleBtn').click(function() {
                $('#sidebar').toggleClass('collapsed');
            });

            // Nav link active state
            
            // Filter tabs
            $('.filter-tab').click(function() {
                $('.filter-tab').removeClass('active');
                $(this).addClass('active');
            });

            // Employee data
            const employees = [
                { name: 'John Davis', dept: 'Engineering', position: 'Senior Developer', status: 'active', time: '08:45 AM', color: '#667eea' },
                { name: 'Emma Wilson', dept: 'Marketing', position: 'Marketing Manager', status: 'active', time: '09:00 AM', color: '#48bb78' },
                { name: 'Michael Brown', dept: 'Sales', position: 'Sales Executive', status: 'remote', time: '08:30 AM', color: '#ed8936' },
                { name: 'Sarah Johnson', dept: 'HR', position: 'HR Specialist', status: 'active', time: '08:55 AM', color: '#9f7aea' },
                { name: 'David Lee', dept: 'Finance', position: 'Accountant', status: 'leave', time: '-', color: '#f56565' },
                { name: 'Lisa Anderson', dept: 'Engineering', position: 'UX Designer', status: 'active', time: '09:10 AM', color: '#4299e1' }
            ];

            // Populate employee table
            function populateTable() {
                const tbody = $('#employeeTableBody');
                tbody.empty();
                
                employees.forEach((emp, index) => {
                    const initials = emp.name.split(' ').map(n => n[0]).join('');
                    const statusClass = emp.status === 'active' ? 'status-active' : 
                                       emp.status === 'leave' ? 'status-leave' : 'status-remote';
                    const statusText = emp.status.charAt(0).toUpperCase() + emp.status.slice(1);
                    
                    const row = `
                        <tr style="animation-delay: ${index * 0.1}s;">
                            <td>
                                <span class="employee-avatar" style="background: ${emp.color};">${initials}</span>
                                ${emp.name}
                            </td>
                            <td>${emp.dept}</td>
                            <td>${emp.position}</td>
                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                            <td>${emp.time}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            populateTable();

            // Animate stat values on load
            $('.stat-value').each(function() {
                const $this = $(this);
                const target = parseInt($this.text());
                $this.text('0');
                
                $({ value: 0 }).animate({ value: target }, {
                    duration: 1500,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.value));
                    },
                    complete: function() {
                        $this.text(target);
                    }
                });
            });
        });
    </script>
</body>
</html>