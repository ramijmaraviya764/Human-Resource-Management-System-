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
    <style>
        .main-container {
            max-width: auto;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }

        .header h1 {
            color: #2d3748;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header h1 i {
            color: #667eea;
            margin-right: 15px;
        }

        .header p {
            color: #718096;
            font-size: 1.1rem;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 15px;
            color: white;
            text-align: center;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stat-card p i {
            margin-right: 8px;
        }

        .leave-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
        }

        .leave-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .leave-card.processing {
            opacity: 0.6;
            pointer-events: none;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-start {
            align-items: flex-start;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        .employee-info {
            display: flex;
            gap: 20px;
        }

        .employee-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        .employee-details h5 {
            color: #2d3748;
            font-size: 1.4rem;
            margin-bottom: 8px;
        }

        .employee-details p {
            color: #718096;
            margin: 5px 0;
            font-size: 0.95rem;
        }

        .employee-details i {
            margin-right: 8px;
            color: #667eea;
        }

        .badge-status {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .leave-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 25px 0;
            padding: 20px;
            background: #f7fafc;
            border-radius: 10px;
        }

        .leave-info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .leave-info-label {
            color: #718096;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .leave-info-label i {
            margin-right: 5px;
            color: #667eea;
        }

        .leave-info-value {
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .leave-reason {
            padding: 20px;
            background: #edf2f7;
            border-radius: 10px;
            margin: 20px 0;
        }

        .leave-reason strong {
            color: #2d3748;
            font-size: 1.1rem;
        }

        .leave-reason strong i {
            margin-right: 8px;
            color: #667eea;
        }

        .leave-reason p {
            color: #4a5568;
            line-height: 1.6;
            margin-top: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-approve {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
        }

        .btn-reject {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            color: white;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 101, 101, 0.4);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 20px 30px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }

        .notification-error {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        }

        .notification i {
            font-size: 1.5rem;
        }

        .no-requests {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .no-requests i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #cbd5e0;
        }

        .no-requests h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 20px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .leave-details {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
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
        
    <div class="main-container">
        <div class="header">
            <h1><i class="fas fa-calendar-check"></i> Leave Approval Dashboard</h1>
            <p>HR Management Portal</p>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h3 id="pendingCount">0</h3>
                <p><i class="fas fa-clock"></i> Pending Requests</p>
            </div>
            <div class="stat-card">
                <h3 id="approvedCount">0</h3>
                <p><i class="fas fa-check-circle"></i> Approved</p>
            </div>
            <div class="stat-card">
                <h3 id="rejectedCount">0</h3>
                <p><i class="fas fa-times-circle"></i> Rejected</p>
            </div>
        </div>

        <div id="leaveRequests"></div>
    </div>

    <script>
        function getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase();
        }

        function getStatusBadge(status) {
            const badges = {
                pending: '<span class="badge-status badge-pending"><i class="fas fa-clock"></i> Pending</span>',
                approved: '<span class="badge-status badge-approved"><i class="fas fa-check-circle"></i> Approved</span>',
                rejected: '<span class="badge-status badge-rejected"><i class="fas fa-times-circle"></i> Rejected</span>'
            };
            return badges[status];
        }

        function updateStats() {
            $.ajax({
                url: 'get_leave_stats.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#pendingCount').text(response.pending);
                        $('#approvedCount').text(response.approved);
                        $('#rejectedCount').text(response.rejected);
                    }
                }
            });
        }

        function renderLeaveRequests() {
            $.ajax({
                url: 'get_leave_requests.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    const container = $('#leaveRequests');
                    container.empty();

                    if(response.success && response.data.length > 0) {
                        response.data.forEach(request => {
                            const card = `
                                <div class="leave-card" data-id="${request.id}">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="employee-info">
                                            <div class="employee-avatar">${getInitials(request.employee_name)}</div>
                                            <div class="employee-details">
                                                <h5>${request.employee_name}</h5>
                                                <p><i class="fas fa-envelope"></i> ${request.email}</p>
                                            </div>
                                        </div>
                                        ${getStatusBadge(request.status)}
                                    </div>

                                    <div class="leave-details">
                                        <div class="leave-info-item">
                                            <span class="leave-info-label"><i class="fas fa-tag"></i> Leave Type:</span>
                                            <span class="leave-info-value">${request.leave_type}</span>
                                        </div>
                                        <div class="leave-info-item">
                                            <span class="leave-info-label"><i class="fas fa-calendar-alt"></i> Start Date:</span>
                                            <span class="leave-info-value">${request.start_date}</span>
                                        </div>
                                        <div class="leave-info-item">
                                            <span class="leave-info-label"><i class="fas fa-calendar-check"></i> End Date:</span>
                                            <span class="leave-info-value">${request.end_date}</span>
                                        </div>
                                        <div class="leave-info-item">
                                            <span class="leave-info-label"><i class="fas fa-hourglass-half"></i> Total Days:</span>
                                            <span class="leave-info-value">${request.days} days</span>
                                        </div>
                                    </div>

                                    ${request.status === 'pending' ? `
                                        <div class="action-buttons">
                                            <button class="btn btn-approve" onclick="approveLeave(${request.id})">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-reject" onclick="rejectLeave(${request.id})">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    ` : ''}
                                </div>
                            `;
                            container.append(card);
                        });
                    } else {
                        container.html(`
                            <div class="no-requests">
                                <i class="fas fa-inbox"></i>
                                <h3>No Leave Requests</h3>
                                <p>There are no leave requests at the moment.</p>
                            </div>
                        `);
                    }
                    updateStats();
                },
                error: function() {
                    $('#leaveRequests').html(`
                        <div class="no-requests">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h3>Error Loading Data</h3>
                            <p>Could not load leave requests. Please refresh the page.</p>
                        </div>
                    `);
                }
            });
        }

        function showNotification(message, type) {
            const notification = $(`
                <div class="notification notification-${type}">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    ${message}
                </div>
            `);
            
            $('body').append(notification);
            
            setTimeout(() => {
                notification.fadeOut(500, function() {
                    $(this).remove();
                });
            }, 3000);
        }

        function approveLeave(id) {
            const card = $(`.leave-card[data-id="${id}"]`);
            card.addClass('processing');
            
            $.ajax({
                url: 'process_leave.php',
                method: 'POST',
                data: {
                    action: 'approve',
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    card.removeClass('processing');
                    if(response.success) {
                        renderLeaveRequests();
                        showNotification('Leave request approved successfully!', 'success');
                    } else {
                        showNotification(response.message || 'Error processing request.', 'error');
                    }
                },
                error: function() {
                    card.removeClass('processing');
                    showNotification('Error processing request. Please try again.', 'error');
                }
            });
        }

        function rejectLeave(id) {
            const card = $(`.leave-card[data-id="${id}"]`);
            card.addClass('processing');
            
            $.ajax({
                url: 'process_leave.php',
                method: 'POST',
                data: {
                    action: 'reject',
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    card.removeClass('processing');
                    if(response.success) {
                        renderLeaveRequests();
                        showNotification('Leave request rejected.', 'success');
                    } else {
                        showNotification(response.message || 'Error processing request.', 'error');
                    }
                },
                error: function() {
                    card.removeClass('processing');
                    showNotification('Error processing request. Please try again.', 'error');
                }
            });
        }

        $(document).ready(function() {
            renderLeaveRequests();
        });
    </script>
</body>
</html>