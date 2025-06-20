<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/UserService.php';

$authService = new AuthService();
$userService = new UserService();

// Require authentication and admin permission
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');
$authService->requirePermission('manage_users', '/agrimarket-erd/v1/dashboard/');

$currentUser = $authService->getCurrentUser();
$csrfToken = $authService->generateCSRFToken();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if (!$authService->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
    
    switch ($_POST['action']) {
        case 'get_users':
            $page = intval($_POST['page'] ?? 1);
            $limit = intval($_POST['limit'] ?? 10);
            $filters = [
                'search' => $_POST['search'] ?? '',
                'role' => $_POST['role'] ?? '',
                'status' => $_POST['status'] ?? ''
            ];
            
            $result = $userService->getPaginatedUsers($page, $limit, $filters);
            echo json_encode($result);
            exit;
            
        case 'create_user':
            $userData = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'customer'
            ];
            
            $result = $userService->createUser($userData);
            echo json_encode($result);
            exit;
            
        case 'update_user':
            $userId = intval($_POST['user_id']);
            $userData = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'role' => $_POST['role'] ?? 'customer'
            ];
            
            $result = $userService->updateUser($userId, $userData);
            echo json_encode($result);
            exit;
            
        case 'toggle_user_status':
            $userId = intval($_POST['user_id']);
            $status = intval($_POST['status']);
            
            $result = $userService->toggleUserStatus($userId, $status, $currentUser['user_id']);
            echo json_encode($result);
            exit;
            
        case 'delete_user':
            $userId = intval($_POST['user_id']);
            
            $result = $userService->deleteUser($userId, $currentUser['user_id']);
            echo json_encode($result);
            exit;
    }
}

// Get data for the page
$userStats = $userService->getUserStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Shared Sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Shared Header -->
            <?php 
            $pageTitle = 'User Management';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h2>User Management</h2>
                        <p>Manage system users, roles, and permissions</p>
                    </div>
                    <button class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i>
                        Add New User
                    </button>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $userStats['total_users']; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $userStats['active_users']; ?></h3>
                            <p>Active Users</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon admin">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $userStats['admin_users']; ?></h3>
                            <p>Admins</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon vendor">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $userStats['vendor_users']; ?></h3>
                            <p>Vendors</p>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="controls-section">
                    <div class="controls-left">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search users...">
                        </div>
                        <select id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="vendor">Vendor</option>
                            <option value="staff">Staff</option>
                            <option value="customer">Customer</option>
                        </select>
                        <select id="statusFilter">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Users List</h3>
                        <div class="table-controls">
                            <select id="limitSelect" onchange="loadUsers()">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="usersTableContent">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Loading users...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New User</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalMessage"></div>
                <form id="userForm">
                    <input type="hidden" id="userId" name="user_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" id="userName" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="userEmail" name="email" required>
                    </div>
                    
                    <div class="form-group" id="passwordGroup">
                        <label>Password *</label>
                        <input type="password" id="userPassword" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Role</label>
                        <select id="userRole" name="role">
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveUser()">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let isEditing = false;

        // Load users on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
        });

        // Load users function
        function loadUsers(page = 1) {
            currentPage = page;
            
            const formData = new FormData();
            formData.append('action', 'get_users');
            formData.append('page', page);
            formData.append('limit', document.getElementById('limitSelect').value);
            formData.append('search', document.getElementById('searchInput').value);
            formData.append('role', document.getElementById('roleFilter').value);
            formData.append('status', document.getElementById('statusFilter').value);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUsers(data);
                } else {
                    document.getElementById('usersTableContent').innerHTML = 
                        '<div class="error">Error loading users: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('usersTableContent').innerHTML = 
                    '<div class="error">Error loading users. Please try again.</div>';
            });
        }

        // Display users in table
        function displayUsers(data) {
            let html = `
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.users.forEach(user => {
                const createdDate = new Date(user.created_at).toLocaleDateString();
                
                html += `
                    <tr>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td><span class="role-badge role-${user.role}">${user.role}</span></td>
                        <td>
                            <span class="status-badge status-${user.is_active ? 'active' : 'inactive'}">
                                ${user.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>${createdDate}</td>
                        <td class="actions">
                            <button class="btn-icon btn-primary" onclick="editUser(${user.user_id}, '${user.name}', '${user.email}', '${user.role}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon btn-warning" onclick="toggleStatus(${user.user_id}, ${user.is_active ? 0 : 1})" title="${user.is_active ? 'Deactivate' : 'Activate'}">
                                <i class="fas fa-${user.is_active ? 'ban' : 'check'}"></i>
                            </button>
                            <button class="btn-icon btn-danger" onclick="deleteUser(${user.user_id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';

            // Add pagination
            if (data.totalPages > 1) {
                html += '<div class="pagination">';
                
                if (currentPage > 1) {
                    html += `<button class="btn btn-secondary" onclick="loadUsers(${currentPage - 1})">Previous</button>`;
                }
                
                for (let i = Math.max(1, currentPage - 2); i <= Math.min(data.totalPages, currentPage + 2); i++) {
                    html += `<button class="btn ${i === currentPage ? 'btn-primary' : 'btn-secondary'}" onclick="loadUsers(${i})">${i}</button>`;
                }
                
                if (currentPage < data.totalPages) {
                    html += `<button class="btn btn-secondary" onclick="loadUsers(${currentPage + 1})">Next</button>`;
                }
                
                html += '</div>';
            }

            document.getElementById('usersTableContent').innerHTML = html;
        }

        // Modal functions
        function openCreateModal() {
            isEditing = false;
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('passwordGroup').style.display = 'block';
            document.getElementById('userPassword').required = true;
            document.getElementById('modalMessage').innerHTML = '';
            document.getElementById('userModal').style.display = 'block';
        }

        function editUser(userId, name, email, role) {
            isEditing = true;
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = userId;
            document.getElementById('userName').value = name;
            document.getElementById('userEmail').value = email;
            document.getElementById('userRole').value = role;
            document.getElementById('passwordGroup').style.display = 'none';
            document.getElementById('userPassword').required = false;
            document.getElementById('modalMessage').innerHTML = '';
            document.getElementById('userModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        function saveUser() {
            const formData = new FormData(document.getElementById('userForm'));
            formData.append('action', isEditing ? 'update_user' : 'create_user');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalMessage').innerHTML = 
                        '<div class="success">' + data.message + '</div>';
                    setTimeout(() => {
                        closeModal();
                        loadUsers(currentPage);
                    }, 1500);
                } else {
                    document.getElementById('modalMessage').innerHTML = 
                        '<div class="error">' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalMessage').innerHTML = 
                    '<div class="error">Error saving user</div>';
            });
        }

        function toggleStatus(userId, newStatus) {
            if (confirm(`Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this user?`)) {
                const formData = new FormData();
                formData.append('action', 'toggle_user_status');
                formData.append('user_id', userId);
                formData.append('status', newStatus);
                formData.append('csrf_token', '<?php echo $csrfToken; ?>');

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadUsers(currentPage);
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete_user');
                formData.append('user_id', userId);
                formData.append('csrf_token', '<?php echo $csrfToken; ?>');

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadUsers(currentPage);
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                loadUsers(1);
            }, 500);
        });

        // Filter changes
        document.getElementById('roleFilter').addEventListener('change', () => loadUsers(1));
        document.getElementById('statusFilter').addEventListener('change', () => loadUsers(1));

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('userModal');
            if (event.target === modal) {
                closeModal();
            }
        });
    </script>
</body>
</html> 