<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Employee Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .profile-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
        }

        .profile-sidebar {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: white;
            font-weight: bold;
            margin: 0 auto 20px;
            position: relative;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .avatar-upload {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .avatar-upload:hover {
            transform: scale(1.1);
            background: #667eea;
            color: white;
        }

        .profile-name {
            text-align: center;
            margin-bottom: 10px;
        }

        .profile-name h2 {
            color: #2d3748;
            margin-bottom: 5px;
        }

        .profile-name p {
            color: #718096;
            font-size: 14px;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #f7fafc;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item .number {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-item .label {
            display: block;
            font-size: 12px;
            color: #718096;
        }

        .profile-actions {
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .action-btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .action-btn.primary {
            background: #667eea;
            color: white;
        }

        .action-btn.secondary {
            background: #f7fafc;
            color: #2d3748;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .profile-main {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #f7fafc;
            padding-bottom: 10px;
        }

        .tab {
            padding: 10px 20px;
            border: none;
            background: transparent;
            color: #718096;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 8px 8px 0 0;
        }

        .tab.active {
            color: #667eea;
            background: #f7fafc;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f7fafc;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            color: #4a5568;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .info-card {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #718096;
            font-size: 14px;
        }

        .info-value {
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }

        .notification-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .notification-text {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #667eea;
        }

        input:checked + .slider:before {
            transform: translateX(24px);
        }

        @media (max-width: 968px) {
            .profile-container {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
            }
        }

        .custom-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #667eea;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.5s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-circle"></i> My Profile</h1>
            <button class="back-btn" onclick="goBack()">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </button>
        </div>

        <div class="profile-container">
            <!-- Sidebar -->
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    JD
                    <div class="avatar-upload" onclick="uploadPhoto()">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="profile-name">
                    <h2>Ramij Maraviya</h2>
                    <p>Software Engineer</p>
                </div>
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="number">3.5</span>
                        <span class="label">Years</span>
                    </div>
                    <div class="stat-item">
                        <span class="number">12</span>
                        <span class="label">Projects</span>
                    </div>
                    <div class="stat-item">
                        <span class="number">95%</span>
                        <span class="label">Rating</span>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="action-btn primary" onclick="downloadResume()">
                        <i class="fas fa-download"></i> Download Resume
                    </button>
                    <button class="action-btn secondary" onclick="changePassword()">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                    <button class="action-btn secondary" onclick="viewActivity()">
                        <i class="fas fa-history"></i> Activity Log
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="profile-main">
                <div class="tabs">
                    <button class="tab active" onclick="switchTab('personal')">
                        <i class="fas fa-user"></i> Personal Info
                    </button>
                    <button class="tab" onclick="switchTab('work')">
                        <i class="fas fa-briefcase"></i> Work Info
                    </button>
                    <button class="tab" onclick="switchTab('settings')">
                        <i class="fas fa-cog"></i> Settings
                    </button>
                </div>

                <!-- Personal Info Tab -->
                <div class="tab-content active" id="personal-tab">
                    <form id="personalForm">
                        <div class="form-section">
                            <h3><i class="fas fa-address-card"></i> Basic Information</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" value="Ramij" placeholder="Enter first name">
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" value="Maraviya" placeholder="Enter last name">
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" value="Ramijmaraviay@gmail.com" placeholder="Enter email">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" value="+1234567890" placeholder="Enter phone">
                                </div>
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" value="2008-05-16">
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select>
                                        <option>Male</option>
                                        <option>Female</option>
                                        <option>Other</option>
                                        <option>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label>Street Address</label>
                                    <input type="text" value="123 Main Street" placeholder="Enter street address">
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" value="San Francisco" placeholder="Enter city">
                                </div>
                                <div class="form-group">
                                    <label>State/Province</label>
                                    <input type="text" value="California" placeholder="Enter state">
                                </div>
                                <div class="form-group">
                                    <label>ZIP/Postal Code</label>
                                    <input type="text" value="94102" placeholder="Enter ZIP code">
                                </div>
                                <div class="form-group">
                                    <label>Country</label>
                                    <select>
                                        <option>United States</option>
                                        <option>Canada</option>
                                        <option>United Kingdom</option>
                                        <option>Australia</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-phone-square"></i> Emergency Contact</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Contact Name</label>
                                    <input type="text" value="Ramij" placeholder="Enter contact name">
                                </div>
                                <div class="form-group">
                                    <label>Relationship</label>
                                    <input type="text" value="Spouse" placeholder="Enter relationship">
                                </div>
                                <div class="form-group">
                                    <label>Contact Phone</label>
                                    <input type="tel" value="+1234567890" placeholder="Enter phone">
                                </div>
                                <div class="form-group">
                                    <label>Contact Email</label>
                                    <input type="email" value="ramijmaraviya@gmail.com" placeholder="Enter email">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>

                <!-- Work Info Tab -->
                <div class="tab-content" id="work-tab">
                    <div class="form-section">
                        <h3><i class="fas fa-id-badge"></i> Employment Details</h3>
                        <div class="info-card">
                            <div class="info-row">
                                <span class="info-label">Employee ID</span>
                                <span class="info-value">EMP-2021-0456</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Job Title</span>
                                <span class="info-value">Senior Software Engineer</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Department</span>
                                <span class="info-value">Engineering</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Team</span>
                                <span class="info-value">Backend Development</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Manager</span>
                                <span class="info-value">Ramij</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Employment Type</span>
                                <span class="info-value">Full-Time</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Join Date</span>
                                <span class="info-value">June 15, 2021</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Work Location</span>
                                <span class="info-value">San Francisco Office</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-graduation-cap"></i> Skills & Certifications</h3>
                        <form id="skillsForm">
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label>Technical Skills</label>
                                    <textarea placeholder="JavaScript, Python, React, Node.js, SQL...">JavaScript, Python, React, Node.js, SQL, MongoDB, AWS, Docker</textarea>
                                </div>
                                <div class="form-group full-width">
                                    <label>Certifications</label>
                                    <textarea placeholder="List your certifications...">AWS Certified Solutions Architect
Google Cloud Professional Developer
Certified Scrum Master (CSM)</textarea>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Update Skills</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-content" id="settings-tab">
                    <div class="form-section">
                        <h3><i class="fas fa-bell"></i> Notification Preferences</h3>
                        <div class="notification-item">
                            <div class="notification-text">
                                <i class="fas fa-envelope"></i>
                                <span>Email Notifications</span>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="notification-item">
                            <div class="notification-text">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Push Notifications</span>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="notification-item">
                            <div class="notification-text">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Leave Reminders</span>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="notification-item">
                            <div class="notification-text">
                                <i class="fas fa-tasks"></i>
                                <span>Task Updates</span>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-lock"></i> Security Settings</h3>
                        <form id="securityForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" placeholder="Enter current password">
                                </div>
                                <div class="form-group">
                                    <label>Two-Factor Authentication</label>
                                    <select>
                                        <option>Enabled</option>
                                        <option>Disabled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Update Security</button>
                            </div>
                        </form>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-palette"></i> Preferences</h3>
                        <form id="preferencesForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Language</label>
                                    <select>
                                        <option>English</option>
                                        <option>Spanish</option>
                                        <option>French</option>
                                        <option>German</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Time Zone</label>
                                    <select>
                                        <option>Pacific Time (PT)</option>
                                        <option>Mountain Time (MT)</option>
                                        <option>Central Time (CT)</option>
                                        <option>Eastern Time (ET)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Date Format</label>
                                    <select>
                                        <option>MM/DD/YYYY</option>
                                        <option>DD/MM/YYYY</option>
                                        <option>YYYY-MM-DD</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Theme</label>
                                    <select>
                                        <option>Light</option>
                                        <option>Dark</option>
                                        <option>Auto</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Preferences</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function switchTab(tabName) {
            $('.tab').removeClass('active');
            $('.tab-content').removeClass('active');
            
            event.target.classList.add('active');
            $(`#${tabName}-tab`).addClass('active');
        }

        function goBack() {
            showNotification('Returning to dashboard...', 'info');
            setTimeout(() => {
                window.history.back();
            }, 500);
        }

        function uploadPhoto() {
            showNotification('Photo upload feature coming soon!', 'info');
        }

        function downloadResume() {
            showNotification('Downloading resume...', 'success');
        }

        function changePassword() {
            showNotification('Opening password change dialog...', 'info');
        }

        function viewActivity() {
            showNotification('Loading activity log...', 'info');
        }

        function resetForm() {
            showNotification('Form reset successfully!', 'info');
        }

        // Form submissions
        $('#personalForm').submit(function(e) {
            e.preventDefault();
            showNotification('Personal information updated successfully!', 'success');
        });

        $('#skillsForm').submit(function(e) {
            e.preventDefault();
            showNotification('Skills updated successfully!', 'success');
        });

        $('#securityForm').submit(function(e) {
            e.preventDefault();
            showNotification('Security settings updated!', 'success');
        });

        $('#preferencesForm').submit(function(e) {
            e.preventDefault();
            showNotification('Preferences saved successfully!', 'success');
        });

        // Notification System
        function showNotification(message, type) {
            const colors = {
                success: '#2ed573',
                info: '#5352ed',
                warning: '#ff9f43',
                error: '#ff4757'
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
                .css('background', colors[type]);

            const icon = $('<i>')
                .addClass('fas')
                .addClass(icons[type]);

            notification.append(icon);
            notification.append($('<span>').text(message));

            $('body').append(notification);

            setTimeout(() => {
                notification.fadeOut(500, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    </script>
</body>
</html>