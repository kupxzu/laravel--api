<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        button {
            padding: 8px 12px;
            cursor: pointer;
            margin-right: 5px;
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 4px;
        }
        button.delete {
            background-color: #f44336;
        }
        button.edit {
            background-color: #2196F3;
        }
        .form-container {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        input {
            padding: 8px;
            margin: 5px;
            width: 200px;
        }
    </style>
</head>
<body>
    <h1>Employee Management System</h1>
    
    <!-- Form for Adding/Editing Employee -->
    <div class="form-container">
        <h2 id="form-title">Add New Employee</h2>
        <form id="employee-form">
            <input type="hidden" id="employee-id">
            <div>
                <label for="first-name">First Name:</label>
                <input type="text" id="first-name" required>
            </div>
            <div>
                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" required>
            </div>
            <div>
                <button type="submit" id="submit-btn">Add Employee</button>
                <button type="button" id="cancel-btn" style="display:none;">Cancel</button>
            </div>
        </form>
    </div>
    
    <!-- Employee Table -->
    <h2>Employees List</h2>
    <div id="loading">Loading employees...</div>
    <table id="employees-table" style="display:none;">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="employees-list"></tbody>
    </table>

    <script>
        // API Endpoints
        const API_BASE_URL = 'http://127.0.0.1:8000/api';
        const EMPLOYEES_ENDPOINT = `${API_BASE_URL}/employees`;
        const EMPLOYEE_ADD_ENDPOINT = `${API_BASE_URL}/employee/add`;
        
        // DOM Elements
        const employeeForm = document.getElementById('employee-form');
        const formTitle = document.getElementById('form-title');
        const employeeIdInput = document.getElementById('employee-id');
        const firstNameInput = document.getElementById('first-name');
        const lastNameInput = document.getElementById('last-name');
        const submitBtn = document.getElementById('submit-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const employeesTable = document.getElementById('employees-table');
        const employeesList = document.getElementById('employees-list');
        const loadingDiv = document.getElementById('loading');
        
        // Initialize
        document.addEventListener('DOMContentLoaded', fetchEmployees);
        
        // Event Listeners
        employeeForm.addEventListener('submit', handleFormSubmit);
        cancelBtn.addEventListener('click', resetForm);
        
        // Fetch all employees
        function fetchEmployees() {
            loadingDiv.style.display = 'block';
            employeesTable.style.display = 'none';
            
            fetch(EMPLOYEES_ENDPOINT)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        displayEmployees(data.data);
                    } else {
                        console.error('API returned error:', data);
                        alert('Failed to fetch employees');
                    }
                })
                .catch(error => {
                    console.error('Error fetching employees:', error);
                    alert('Error fetching employees: ' + error.message);
                })
                .finally(() => {
                    loadingDiv.style.display = 'none';
                    employeesTable.style.display = 'table';
                });
        }
        
        // Display employees in table
        function displayEmployees(employees) {
            employeesList.innerHTML = '';
            
            employees.forEach(employee => {
                const row = document.createElement('tr');
                
                // Format dates for readability
                const createdDate = new Date(employee.created_at).toLocaleString();
                const updatedDate = new Date(employee.updated_at).toLocaleString();
                
                row.innerHTML = `
                    <td>${employee.id}</td>
                    <td>${employee.first_name}</td>
                    <td>${employee.last_name}</td>
                    <td>${createdDate}</td>
                    <td>${updatedDate}</td>
                    <td>
                        <button class="edit" onclick="editEmployee(${employee.id})">Edit</button>
                        <button class="delete" onclick="deleteEmployee(${employee.id})">Delete</button>
                    </td>
                `;
                
                employeesList.appendChild(row);
            });
        }
        
        // Handle form submission (Add/Edit)
        function handleFormSubmit(e) {
            e.preventDefault();
            
            const employeeData = {
                first_name: firstNameInput.value,
                last_name: lastNameInput.value
            };
            
            const employeeId = employeeIdInput.value;
            const isEditing = !!employeeId;
            
            const url = isEditing 
                ? `${API_BASE_URL}/employee/${employeeId}` 
                : EMPLOYEE_ADD_ENDPOINT;
                
            const method = isEditing ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(employeeData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(isEditing ? 'Employee updated successfully!' : 'Employee added successfully!');
                    resetForm();
                    fetchEmployees();
                } else {
                    console.error('API returned error:', data);
                    alert('Operation failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            });
        }
        
        // Edit employee
        function editEmployee(id) {
            // Find employee by ID
            fetch(`${API_BASE_URL}/employee/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const employee = data.data;
                        // Populate form
                        employeeIdInput.value = employee.id;
                        firstNameInput.value = employee.first_name;
                        lastNameInput.value = employee.last_name;
                        
                        // Update UI
                        formTitle.textContent = 'Edit Employee';
                        submitBtn.textContent = 'Update Employee';
                        cancelBtn.style.display = 'inline-block';
                    } else {
                        alert('Failed to fetch employee details');
                    }
                })
                .catch(error => {
                    console.error('Error fetching employee:', error);
                    alert('Error: ' + error.message);
                });
        }
        
        // Delete employee
        function deleteEmployee(id) {
            if (confirm('Are you sure you want to delete this employee?')) {
                fetch(`${API_BASE_URL}/employee/${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Employee deleted successfully!');
                        fetchEmployees();
                    } else {
                        alert('Failed to delete employee');
                    }
                })
                .catch(error => {
                    console.error('Error deleting employee:', error);
                    alert('Error: ' + error.message);
                });
            }
        }
        
        // Reset form to add mode
        function resetForm() {
            employeeForm.reset();
            employeeIdInput.value = '';
            formTitle.textContent = 'Add New Employee';
            submitBtn.textContent = 'Add Employee';
            cancelBtn.style.display = 'none';
        }
    </script>
</body>
</html>