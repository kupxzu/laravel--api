<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Message System</title>
    <link rel="stylesheet" href="s.css">
</head>
<body>
    <div class="header">
        <h1>Employee Message System</h1>
    </div>
    
    <div class="container">
        <!-- Login Section -->
        <div id="login-section" style="display: block;">
            <h2>Login</h2>
            <div class="message-form">
                <div id="login-error" style="color: red; margin-bottom: 15px; display: none;"></div>
                <form id="login-form">
                    <div class="form-group">
                        <label for="username">Username (First Name)</label>
                        <input type="text" id="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password (Last Name)</label>
                        <input type="password" id="password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
        
        <!-- Dashboard Section -->
        <div id="dashboard-section" style="display: none;">
            <div class="nav-bar">
                <h2>Dashboard</h2>
                <button id="logout-btn" class="btn-logout">Logout</button>
            </div>
            
            <div class="user-info">
                <h3>Welcome, <span id="user-name"></span>!</h3>
                <p>Employee ID: <span id="user-id"></span></p>
            </div>
            
            <div class="tabs">
                <div class="tab active" data-tab="create">Create Message</div>
                <div class="tab" data-tab="my-messages">My Messages</div>
                <div class="tab" data-tab="all-messages">All Messages</div>
            </div>
            
            <!-- Create Message Tab -->
            <div id="create-tab" class="tab-content">
                <h3>Create New Message</h3>
                <div class="message-form">
                    <form id="message-form">
                        <input type="hidden" id="message-id">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" required></textarea>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" id="submit-btn">Post Message</button>
                            <button type="button" id="cancel-btn" style="background-color: #777; display: none;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- My Messages Tab -->
            <div id="my-messages-tab" class="tab-content" style="display: none;">
                <h3>My Messages</h3>
                <div id="my-messages-list" class="message-list">
                    <div class="loading">Loading your messages...</div>
                </div>
            </div>
            
            <!-- All Messages Tab -->
            <div id="all-messages-tab" class="tab-content" style="display: none;">
                <h3>All Messages</h3>
                <div id="all-messages-list" class="message-list">
                    <div class="loading">Loading all messages...</div>
                </div>
            </div>
        </div>
    </div>

    <script src="api.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize API handler with config
            initializeAPI({
                baseUrl: 'http://127.0.0.1:8000/api',
                adminUserId: 5,
                endpoints: {
                    employees: '/employees',
                    employeeMessages: '/employee/',
                    allMessages: '/messages',
                    addMessage: '/messages/add',
                    updateMessage: '/messages/',
                    deleteMessage: '/messages/'
                }
            });
        });
    </script>
</body>
</html>