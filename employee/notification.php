<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Center - Professional Dashboard</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 0;
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, #2d3436 0%, #1e272e 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
        }

        .sidebar-logo i {
            font-size: 28px;
            color: #667eea;
        }

        .sidebar-logo span {
            font-size: 20px;
            font-weight: 700;
            white-space: nowrap;
        }

        .sidebar.collapsed .sidebar-logo span {
            display: none;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar.collapsed .toggle-btn {
            margin: 0 auto;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .menu-item.active {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.2) 0%, transparent 100%);
            color: white;
            border-left: 4px solid #667eea;
        }

        .menu-item i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        .menu-item span {
            font-size: 15px;
            font-weight: 500;
            white-space: nowrap;
        }

        .sidebar.collapsed .menu-item span {
            display: none;
        }

        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 15px 10px;
        }

        .menu-badge {
            margin-left: auto;
            background: #ff4757;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .sidebar.collapsed .menu-badge {
            display: none;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        .notification-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .title-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .bell-icon {
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .badge-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            border: 3px solid white;
        }

        .title-text h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2d3436;
            margin: 0;
        }

        .title-text p {
            color: #636e72;
            font-size: 14px;
            margin: 0;
        }

        .settings-btn {
            background: #f8f9fa;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .settings-btn:hover {
            background: #e9ecef;
            transform: rotate(90deg);
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            background: #f8f9fa;
            color: #636e72;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .filter-btn:hover {
            transform: translateY(-2px);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mark-read-btn {
            background: #d4edda;
            color: #155724;
        }

        .mark-read-btn:hover {
            background: #c3e6cb;
            transform: translateY(-2px);
        }

        .clear-all-btn {
            background: #f8d7da;
            color: #721c24;
        }

        .clear-all-btn:hover {
            background: #f5c6cb;
            transform: translateY(-2px);
        }

        .notification-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease;
            cursor: pointer;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .notification-card.unread {
            background: linear-gradient(to right, #f8f9ff 0%, #ffffff 100%);
        }

        .notification-card.read {
            opacity: 0.7;
        }

        .notification-card.success {
            border-left-color: #00b894;
        }

        .notification-card.warning {
            border-left-color: #fdcb6e;
        }

        .notification-card.info {
            border-left-color: #0984e3;
        }

        .notification-content {
            display: flex;
            gap: 15px;
        }

        .notification-icon {
            font-size: 32px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .notification-body {
            flex: 1;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 8px;
        }

        .notification-title {
            font-size: 16px;
            font-weight: 600;
            color: #2d3436;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .unread-dot {
            width: 8px;
            height: 8px;
            background: #667eea;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .delete-btn {
            background: transparent;
            border: none;
            color: #b2bec3;
            cursor: pointer;
            padding: 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background: #ffe5e5;
            color: #ff4757;
        }

        .notification-message {
            color: #636e72;
            font-size: 14px;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .notification-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-time {
            color: #b2bec3;
            font-size: 12px;
        }

        .mark-read-link {
            color: #667eea;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .mark-read-link:hover {
            color: #764ba2;
        }

        .empty-state {
            background: white;
            border-radius: 20px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 80px;
            color: #dfe6e9;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #636e72;
            font-size: 20px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block;
            }

            .header-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .title-text h1 {
                font-size: 24px;
            }

            .filter-tabs, .action-buttons {
                width: 100%;
            }

            .filter-btn, .action-btn {
                flex: 1;
                justify-content: center;
            }
        }

        .mobile-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            z-index: 999;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }

        .fade-out {
            animation: fadeOut 0.3s ease;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(50px);
            }
        }
    </style>
</head>
<body>
    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars" style="font-size: 20px; color: #2d3436;"></i>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

   <?php include('include/slidbar.php');?>

    <div class="main-content" id="mainContent">
    <div class="notification-container">
        <div class="header-card">
            <div class="header-title">
                <div class="title-section">
                    <div class="bell-icon">
                        <i class="fas fa-bell"></i>
                        <span class="badge-count" id="unreadCount">0</span>
                    </div>
                    <div class="title-text">
                        <h1>Notifications</h1>
                        <p id="unreadText">0 unread messages</p>
                    </div>
                </div>
                <button class="settings-btn">
                    <i class="fas fa-cog" style="font-size: 20px; color: #636e72;"></i>
                </button>
            </div>

            <div class="filter-tabs">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-list"></i> All (<span id="allCount">0</span>)
                </button>
                <button class="filter-btn" data-filter="unread">
                    <i class="fas fa-envelope"></i> Unread (<span id="unreadFilterCount">0</span>)
                </button>
                <button class="filter-btn" data-filter="read">
                    <i class="fas fa-envelope-open"></i> Read (<span id="readCount">0</span>)
                </button>
            </div>

            <div class="action-buttons">
                <button class="action-btn mark-read-btn" id="markAllReadBtn">
                    <i class="fas fa-check-double"></i> Mark All Read
                </button>
                <button class="action-btn clear-all-btn" id="clearAllBtn">
                    <i class="fas fa-trash-alt"></i> Clear All
                </button>
            </div>
        </div>

        <div id="notificationsList"></div>
    </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <script>
        jQuery(document).ready(function($) {
            var notifications = [
                {
                    id: 1,
                    type: 'success',
                    title: 'Payment Received',
                    message: 'Your payment of $150.00 has been successfully processed.',
                    time: '2 minutes ago',
                    read: false,
                    icon: '💳'
                },
                {
                    id: 2,
                    type: 'info',
                    title: 'New Message',
                    message: 'You have a new message from Sarah Johnson.',
                    time: '15 minutes ago',
                    read: false,
                    icon: '💬'
                },
                {
                    id: 3,
                    type: 'warning',
                    title: 'Security Alert',
                    message: 'New login detected from Chrome on Windows.',
                    time: '1 hour ago',
                    read: false,
                    icon: '🔒'
                },
                {
                    id: 4,
                    type: 'info',
                    title: 'System Update',
                    message: 'A new version is available. Update now to get the latest features.',
                    time: '3 hours ago',
                    read: true,
                    icon: '🔄'
                },
                {
                    id: 5,
                    type: 'success',
                    title: 'Task Completed',
                    message: 'Your backup has been completed successfully.',
                    time: '5 hours ago',
                    read: true,
                    icon: '✅'
                },
                {
                    id: 6,
                    type: 'info',
                    title: 'New Comment',
                    message: 'John Doe commented on your post "Web Development Tips".',
                    time: '6 hours ago',
                    read: true,
                    icon: '💭'
                },
                {
                    id: 7,
                    type: 'success',
                    title: 'Profile Updated',
                    message: 'Your profile information has been updated successfully.',
                    time: '1 day ago',
                    read: true,
                    icon: '👤'
                }
            ];

            var currentFilter = 'all';

            updateCounts();
            renderNotifications();

            $('#toggleBtn').on('click', function() {
                $('#sidebar').toggleClass('collapsed');
                $('#mainContent').toggleClass('expanded');
                
                var icon = $(this).find('i');
                if ($('#sidebar').hasClass('collapsed')) {
                    icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                } else {
                    icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                }
            });

            $('#mobileToggle').on('click', function() {
                $('#sidebar').toggleClass('mobile-open');
                $('#sidebarOverlay').addClass('active');
            });

            $('#sidebarOverlay').on('click', function() {
                $('#sidebar').removeClass('mobile-open');
                $(this).removeClass('active');
            });

            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                renderNotifications();
            });

            $('#markAllReadBtn').on('click', function() {
                notifications.forEach(function(n) {
                    n.read = true;
                });
                updateCounts();
                renderNotifications();
                showToast('All notifications marked as read', 'success');
            });

            $('#clearAllBtn').on('click', function() {
                if (confirm('Are you sure you want to clear all notifications?')) {
                    notifications = [];
                    updateCounts();
                    renderNotifications();
                    showToast('All notifications cleared', 'info');
                }
            });

            $(document).on('click', '.mark-read-link', function(e) {
                e.stopPropagation();
                var id = parseInt($(this).data('id'));
                var notification = notifications.find(function(n) {
                    return n.id === id;
                });
                if (notification) {
                    notification.read = true;
                    updateCounts();
                    renderNotifications();
                    showToast('Marked as read', 'success');
                }
            });

            $(document).on('click', '.delete-btn', function(e) {
                e.stopPropagation();
                var id = parseInt($(this).data('id'));
                var card = $(this).closest('.notification-card');
                
                card.addClass('fade-out');
                setTimeout(function() {
                    notifications = notifications.filter(function(n) {
                        return n.id !== id;
                    });
                    updateCounts();
                    renderNotifications();
                    showToast('Notification deleted', 'info');
                }, 300);
            });

            $(document).on('click', '.notification-card', function() {
                var id = parseInt($(this).data('id'));
                var notification = notifications.find(function(n) {
                    return n.id === id;
                });
                if (notification && !notification.read) {
                    notification.read = true;
                    updateCounts();
                    renderNotifications();
                }
            });

            function updateCounts() {
                var unreadCount = notifications.filter(function(n) {
                    return !n.read;
                }).length;
                var readCount = notifications.filter(function(n) {
                    return n.read;
                }).length;
                
                $('#unreadCount').text(unreadCount);
                $('#unreadText').text(unreadCount + ' unread message' + (unreadCount !== 1 ? 's' : ''));
                $('#allCount').text(notifications.length);
                $('#unreadFilterCount').text(unreadCount);
                $('#readCount').text(readCount);
                $('#sidebarBadge').text(unreadCount);

                if (unreadCount === 0) {
                    $('.badge-count').hide();
                } else {
                    $('.badge-count').show();
                }
            }

            function renderNotifications() {
                var container = $('#notificationsList');
                container.empty();

                var filteredNotifications = notifications;
                
                if (currentFilter === 'unread') {
                    filteredNotifications = notifications.filter(function(n) {
                        return !n.read;
                    });
                } else if (currentFilter === 'read') {
                    filteredNotifications = notifications.filter(function(n) {
                        return n.read;
                    });
                }

                if (filteredNotifications.length === 0) {
                    container.html('<div class="empty-state"><i class="fas fa-bell-slash"></i><h3>No notifications to display</h3></div>');
                    return;
                }

                filteredNotifications.forEach(function(notification) {
                    var readStatus = notification.read ? 'read' : 'unread';
                    var unreadDot = !notification.read ? '<span class="unread-dot"></span>' : '';
                    var markReadLink = !notification.read ? '<a class="mark-read-link" data-id="' + notification.id + '"><i class="fas fa-check"></i> Mark as read</a>' : '';
                    
                    var card = $('<div class="notification-card ' + notification.type + ' ' + readStatus + '" data-id="' + notification.id + '">' +
                        '<div class="notification-content">' +
                            '<div class="notification-icon">' + notification.icon + '</div>' +
                            '<div class="notification-body">' +
                                '<div class="notification-header">' +
                                    '<div class="notification-title">' + notification.title + unreadDot + '</div>' +
                                    '<button class="delete-btn" data-id="' + notification.id + '"><i class="fas fa-times"></i></button>' +
                                '</div>' +
                                '<div class="notification-message">' + notification.message + '</div>' +
                                '<div class="notification-footer">' +
                                    '<span class="notification-time"><i class="far fa-clock"></i> ' + notification.time + '</span>' +
                                    markReadLink +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>');
                    
                    container.append(card);
                });
            }

            function showToast(message, type) {
                var colors = {
                    success: '#00b894',
                    info: '#0984e3',
                    warning: '#fdcb6e'
                };

                var toast = $('<div style="position: fixed; bottom: 30px; right: 30px; background: ' + (colors[type] || colors.info) + '; color: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); z-index: 9999; animation: slideIn 0.3s ease;"><i class="fas fa-check-circle"></i> ' + message + '</div>');

                $('body').append(toast);
                setTimeout(function() {
                    toast.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    </script>
</body>
</html>