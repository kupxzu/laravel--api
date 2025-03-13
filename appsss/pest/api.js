/**
 * Employee Management System API Handler
 * This file handles all API interactions and UI manipulations
 */

// Global configuration object
let config = {
    baseUrl: '',
    adminUserId: 0,
    endpoints: {}
};

// DOM Elements
let loginForm, loginContainer, dashboardSection, loginSection, errorMessage;
let userName, userId, logoutBtn, messageForm, messageId, titleInput, descriptionInput;
let submitBtn, cancelBtn, myMessagesList, allMessagesList;
let tabButtons, tabContents;

/**
 * Initialize the API handler with configuration
 * @param {Object} apiConfig - Configuration object for API endpoints
 */
function initializeAPI(apiConfig) {
    // Store configuration
    config = apiConfig;
    
    // Initialize DOM elements
    initializeElements();
    
    // Check if user is already logged in
    const loggedInUser = JSON.parse(localStorage.getItem('loggedInUser'));
    if (loggedInUser) {
        showDashboard(loggedInUser);
        fetchMyMessages(loggedInUser.id);
        fetchAllMessages();
    }
    
    // Set up event listeners
    setupEventListeners();
}

/**
 * Initialize DOM element references
 */
function initializeElements() {
    // Login elements
    loginForm = document.getElementById('login-form');
    loginSection = document.getElementById('login-section');
    dashboardSection = document.getElementById('dashboard-section');
    errorMessage = document.getElementById('login-error');
    
    // User info elements
    userName = document.getElementById('user-name');
    userId = document.getElementById('user-id');
    logoutBtn = document.getElementById('logout-btn');
    
    // Message form elements
    messageForm = document.getElementById('message-form');
    messageId = document.getElementById('message-id');
    titleInput = document.getElementById('title');
    descriptionInput = document.getElementById('description');
    submitBtn = document.getElementById('submit-btn');
    cancelBtn = document.getElementById('cancel-btn');
    
    // Message lists
    myMessagesList = document.getElementById('my-messages-list');
    allMessagesList = document.getElementById('all-messages-list');
    
    // Tabs
    tabButtons = document.querySelectorAll('.tab');
    tabContents = document.querySelectorAll('.tab-content');
}

/**
 * Set up event listeners for user interactions
 */
function setupEventListeners() {
    // Login form
    loginForm.addEventListener('submit', handleLogin);
    logoutBtn.addEventListener('click', handleLogout);
    
    // Message form
    messageForm.addEventListener('submit', handleMessageSubmit);
    cancelBtn.addEventListener('click', resetMessageForm);
    
    // Tab switching
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
            
            // Refresh content when switching tabs
            const loggedInUser = JSON.parse(localStorage.getItem('loggedInUser'));
            if (loggedInUser) {
                if (tabName === 'my-messages') {
                    fetchMyMessages(loggedInUser.id);
                } else if (tabName === 'all-messages') {
                    fetchAllMessages();
                }
            }
        });
    });
    
    // Expose functions for use in HTML
    window.editMessage = editMessage;
    window.deleteMessage = deleteMessage;
}

/**
 * Switch between tabs
 * @param {string} tabName - Name of tab to switch to
 */
function switchTab(tabName) {
    // Update tab buttons
    tabButtons.forEach(button => {
        if (button.getAttribute('data-tab') === tabName) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    });
    
    // Update tab content
    tabContents.forEach(content => {
        if (content.id === `${tabName}-tab`) {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
    });
}

/**
 * Handle login form submission
 * @param {Event} e - Form submission event
 */
function handleLogin(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    
    // Validate inputs
    if (!username || !password) {
        showLoginError('Please enter both username and password');
        return;
    }
    
    // Attempt to login by fetching employees and finding a match
    const url = `${config.baseUrl}${config.endpoints.employees}`;
    
    secureApiCall(url)
        .then(data => {
            if (data.status === 'success') {
                const employees = data.data;
                const user = employees.find(emp => 
                    emp.first_name.toLowerCase() === username.toLowerCase() && 
                    emp.last_name.toLowerCase() === password.toLowerCase()
                );
                
                if (user) {
                    // Login successful
                    localStorage.setItem('loggedInUser', JSON.stringify(user));
                    showDashboard(user);
                    fetchMyMessages(user.id);
                    fetchAllMessages();
                } else {
                    // Login failed
                    showLoginError('Invalid username or password');
                }
            } else {
                showLoginError('Failed to connect to the server');
            }
        })
        .catch(error => {
            showLoginError('Error connecting to the server');
        });
}

/**
 * Show login error message
 * @param {string} message - Error message to display
 */
function showLoginError(message) {
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
    
    // Hide error after 3 seconds
    setTimeout(() => {
        errorMessage.style.display = 'none';
    }, 3000);
}

/**
 * Show dashboard and set up user interface
 * @param {Object} user - User object
 */
function showDashboard(user) {
    // Hide login, show dashboard
    loginSection.style.display = 'none';
    dashboardSection.style.display = 'block';
    
    // Set user info
    userName.textContent = `${user.first_name} ${user.last_name}`;
    userId.textContent = user.id;
    
    // Default to create message tab
    switchTab('create');
}

/**
 * Make a secure API call with error handling
 * @param {string} url - API endpoint URL
 * @param {Object} options - Fetch options
 * @returns {Promise} - Promise with response data
 */
function secureApiCall(url, options = {}) {
    return fetch(url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .catch(error => {
            // Silently handle error without logging to console
            // Only show errors in UI elements instead
            throw error;
        });
}

/**
 * Handle message form submission (create/update)
 * @param {Event} e - Form submission event
 */
function handleMessageSubmit(e) {
    e.preventDefault();
    
    const loggedInUser = JSON.parse(localStorage.getItem('loggedInUser'));
    if (!loggedInUser) {
        alert('You must be logged in to post messages.');
        return;
    }
    
    const messageData = {
        employee_id: loggedInUser.id,
        title: titleInput.value,
        description: descriptionInput.value
    };
    
    const isEditing = messageId.value !== '';
    
    const url = isEditing 
        ? `${config.baseUrl}${config.endpoints.updateMessage}${messageId.value}` 
        : `${config.baseUrl}${config.endpoints.addMessage}`;
    
    const method = isEditing ? 'PUT' : 'POST';
    
    secureApiCall(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(messageData)
    })
    .then(data => {
        if (data.status === 'success') {
            alert(isEditing ? 'Message updated successfully!' : 'Message posted successfully!');
            resetMessageForm();
            
            // Refresh messages
            fetchMyMessages(loggedInUser.id);
            fetchAllMessages();
            
            // Switch to My Messages tab
            switchTab('my-messages');
        } else {
            alert('Operation failed');
        }
    })
    .catch(error => {
        alert('Error: Operation failed');
    });
}

/**
 * Reset message form to initial state
 */
function resetMessageForm() {
    messageForm.reset();
    messageId.value = '';
    submitBtn.textContent = 'Post Message';
    cancelBtn.style.display = 'none';
}

/**
 * Edit a message
 * @param {number} id - Message ID
 */
function editMessage(id) {
    const url = `${config.baseUrl}${config.endpoints.updateMessage}${id}`;
    
    secureApiCall(url, { method: 'GET' })
        .then(data => {
            if (data.status === 'success') {
                const message = data.data;
                
                // Populate form
                messageId.value = message.id;
                titleInput.value = message.title;
                descriptionInput.value = message.description;
                
                // Update UI
                submitBtn.textContent = 'Update Message';
                cancelBtn.style.display = 'inline-block';
                
                // Switch to create tab
                switchTab('create');
                
                // Scroll to form
                messageForm.scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('Failed to fetch message details');
            }
        })
        .catch(error => {
            alert('Error fetching message details');
        });
}

/**
 * Delete a message
 * @param {number} id - Message ID
 */
function deleteMessage(id) {
    if (confirm('Are you sure you want to delete this message?')) {
        const url = `${config.baseUrl}${config.endpoints.deleteMessage}${id}`;
        
        secureApiCall(url, { method: 'DELETE' })
            .then(data => {
                if (data.status === 'success') {
                    alert('Message deleted successfully!');
                    
                    // Refresh messages
                    const loggedInUser = JSON.parse(localStorage.getItem('loggedInUser'));
                    fetchMyMessages(loggedInUser.id);
                    fetchAllMessages();
                } else {
                    alert('Failed to delete message');
                }
            })
            .catch(error => {
                alert('Error deleting message');
            });
    }
}

/**
 * Fetch messages for the logged-in user
 * @param {number} employeeId - ID of the employee
 */
function fetchMyMessages(employeeId) {
    const url = `${config.baseUrl}${config.endpoints.employeeMessages}${employeeId}/messages`;
    
    myMessagesList.innerHTML = '<div class="loading">Loading your messages...</div>';
    
    secureApiCall(url)
        .then(data => {
            if (data.status === 'success') {
                displayMessages(data.data, myMessagesList, true);
            } else {
                myMessagesList.innerHTML = '<div class="message-item">Failed to load messages. Please make sure API routes are configured correctly.</div>';
            }
        })
        .catch(error => {
            myMessagesList.innerHTML = '<div class="message-item">API endpoint not found. Please check if message routes are set up correctly in your Laravel routes/api.php file.</div>';
        });
}

/**
 * Fetch all messages
 */
function fetchAllMessages() {
    const url = `${config.baseUrl}${config.endpoints.allMessages}`;
    
    allMessagesList.innerHTML = '<div class="loading">Loading all messages...</div>';
    
    secureApiCall(url)
        .then(data => {
            if (data.status === 'success') {
                displayMessages(data.data, allMessagesList, false);
            } else {
                allMessagesList.innerHTML = '<div class="message-item">Failed to load messages. Please make sure API routes are configured correctly.</div>';
            }
        })
        .catch(error => {
            allMessagesList.innerHTML = '<div class="message-item">API endpoint not found. Please check if message routes are set up correctly in your Laravel routes/api.php file.</div>';
        });
}

/**
 * Display messages in the specified container
 * @param {Array} messages - Array of message objects
 * @param {HTMLElement} container - Container element to display messages in
 * @param {boolean} showActions - Whether to show edit/delete actions
 */
function displayMessages(messages, container, showActions) {
    if (!messages || messages.length === 0) {
        container.innerHTML = '<div class="message-item">No messages found.</div>';
        return;
    }
    
    container.innerHTML = '';
    const loggedInUser = JSON.parse(localStorage.getItem('loggedInUser'));
    
    messages.forEach(message => {
        const messageEl = document.createElement('div');
        messageEl.className = 'message-item';
        
        // Format date
        const createdDate = new Date(message.created_at).toLocaleString();
        
        // Get employee name - this will either come with the message or we need to use the ID
        let employeeName = 'Unknown';
        if (message.employee && message.employee.first_name) {
            employeeName = `${message.employee.first_name} ${message.employee.last_name}`;
        }
        
        let messageHTML = `
            <div class="message-title">${message.title}</div>
            <div class="message-description">${message.description}</div>
            <div class="message-meta">
                Posted by: ${employeeName} on ${createdDate}
            </div>
        `;
        
        // Only show edit/delete buttons for user's own messages or admin
        const isOwnMessage = message.employee_id === loggedInUser.id;
        const isAdmin = loggedInUser.id === config.adminUserId;
        
        if ((showActions && isOwnMessage) || isAdmin) {
            messageHTML += `
                <div class="message-actions">
                    <button class="btn-edit" onclick="editMessage(${message.id})">Edit</button>
                    <button class="btn-delete" onclick="deleteMessage(${message.id})">Delete</button>
                </div>
            `;
        }
        
        messageEl.innerHTML = messageHTML;
        container.appendChild(messageEl);
    });
}

/**
 * Handle user logout
 */
function handleLogout() {
    localStorage.removeItem('loggedInUser');
    loginSection.style.display = 'block';
    dashboardSection.style.display = 'none';
    loginForm.reset();
    resetMessageForm();
}