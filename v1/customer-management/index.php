<?php
// Only display errors for development - disable for production
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/CustomerService.php';

$authService = new AuthService();
$customerService = new CustomerService();

// Require authentication and customer management permission
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');
$authService->requirePermission('manage_customers', '/agrimarket-erd/v1/dashboard/');

$currentUser = $authService->getCurrentUser();
$csrfToken = $authService->generateCSRFToken();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Clear any output buffer to prevent HTML before JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    
    if (!$authService->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
    
    try {
        switch ($_POST['action']) {
        case 'get_customers':
            $page = intval($_POST['page'] ?? 1);
            $limit = intval($_POST['limit'] ?? 10);
            $filters = [
                'search' => $_POST['search'] ?? '',
                'status' => $_POST['status'] ?? ''
            ];
            
            $result = $customerService->getPaginatedCustomers($page, $limit, $filters);
            echo json_encode($result);
            exit;
            
        case 'create_customer':
            $customerData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'] ?? null,
                'address' => $_POST['address'] ?? null,
                'city' => $_POST['city'] ?? null,
                'state' => $_POST['state'] ?? null,
                'postal_code' => $_POST['postal_code'] ?? null
            ];
            
            $result = $customerService->createCustomer($customerData);
            echo json_encode($result);
            exit;
            
        case 'update_customer':
            $customerId = intval($_POST['customer_id']);
            $customerData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone']
            ];
            
            $result = $customerService->updateCustomer($customerId, $customerData);
            echo json_encode($result);
            exit;
            
        case 'toggle_customer_status':
            $customerId = intval($_POST['customer_id']);
            $status = intval($_POST['status']);
            
            // Toggle is_active in users table for customer
            $result = $customerService->toggleCustomerStatus($customerId, $status, $currentUser['user_id']);
            echo json_encode($result);
            exit;
            
        case 'delete_customer':
            $customerId = intval($_POST['customer_id']);
            
            $result = $customerService->deleteCustomer($customerId, $currentUser['user_id']);
            echo json_encode($result);
            exit;
            
        case 'get_customer_details':
            $customerId = intval($_POST['customer_id']);
            $customer = $customerService->getCustomerDetails($customerId);
            
            if ($customer) {
                echo json_encode(['success' => true, 'customer' => $customer]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Customer not found']);
            }
            exit;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}

// Get customer statistics for dashboard
$customerStats = $customerService->getCustomerStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../components/main.css">
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
            $pageTitle = 'Customer Management';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2>Customer Management</h2>
                            <p>Manage customers and their basic information</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i>
                            Add New Customer
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $customerStats['total_customers']; ?></h3>
                            <p>Total Customers</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $customerStats['active_customers']; ?></h3>
                            <p>Active Customers</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>$<?php echo number_format($customerStats['total_revenue'], 2); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon recent">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $customerStats['recent_customers']; ?></h3>
                            <p>New This Month</p>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="controls-section">
                    <div class="controls-left">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search customers...">
                        </div>
                        <select id="statusFilter">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Customers Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Customers List</h3>
                        <div class="table-controls">
                            <select id="limitSelect" onchange="loadCustomers()">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="customersTableContent">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Loading customers...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Customer Modal -->
    <div id="customerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-user-plus"></i>Add New Customer</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalMessage"></div>
                <form id="customerForm">
                    <input type="hidden" id="customerId" name="customer_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" id="customerName" name="name" required 
                               minlength="2" maxlength="100" placeholder="Enter full name">
                        <div class="error-message" id="customerNameError"></div>
                        <small class="help-text">Full name (2-100 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="customerEmail" name="email" required 
                               maxlength="100" placeholder="customer@example.com">
                        <div class="error-message" id="customerEmailError"></div>
                        <small class="help-text">Valid email address (max 100 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" id="customerPhone" name="phone" 
                               pattern="[+]?[0-9\s\-\(\)]{7,20}" placeholder="e.g., +1234567890">
                        <div class="error-message" id="customerPhoneError"></div>
                        <small class="help-text">Valid phone number (7-20 digits)</small>
                    </div>
                    
                    <div class="form-group" id="addressGroup">
                        <label>Address</label>
                        <input type="text" id="customerAddress" name="address" 
                               maxlength="255" placeholder="Street address">
                        <div class="error-message" id="customerAddressError"></div>
                        <small class="help-text">Street address (optional)</small>
                    </div>
                    
                    <div class="form-row" id="locationGroup">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" id="customerCity" name="city" 
                                   maxlength="100" placeholder="City">
                            <div class="error-message" id="customerCityError"></div>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" id="customerState" name="state" 
                                   maxlength="100" placeholder="State">
                            <div class="error-message" id="customerStateError"></div>
                        </div>
                        <div class="form-group">
                            <label>Postal Code</label>
                            <input type="text" id="customerPostalCode" name="postal_code" 
                                   maxlength="20" placeholder="Postal code">
                            <div class="error-message" id="customerPostalCodeError"></div>
                        </div>
                    </div>
                    
                    <div class="info-box" id="createInfoBox">
                        <i class="fas fa-info-circle"></i>
                        <p>A user account will be automatically created for this customer with a temporary password.</p>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let isEditing = false;

        // Load customers on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCustomers();
            
            // Add real-time validation event listeners
            const emailInput = document.getElementById('customerEmail');
            if (emailInput) {
                emailInput.addEventListener('blur', function() {
                    const email = this.value.trim();
                    if (email) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(email)) {
                            showCustomerError('customerEmailError', 'Please enter a valid email address');
                        } else if (email.length > 100) {
                            showCustomerError('customerEmailError', 'Email must be less than 100 characters');
                        } else {
                            clearCustomerError('customerEmailError');
                        }
                    } else {
                        clearCustomerError('customerEmailError');
                    }
                });
            }
            
            // Add name validation on blur
            const nameInput = document.getElementById('customerName');
            if (nameInput) {
                nameInput.addEventListener('blur', function() {
                    const name = this.value.trim();
                    if (name && (name.length < 2 || name.length > 100)) {
                        showCustomerError('customerNameError', 'Name must be between 2 and 100 characters');
                    } else if (name) {
                        clearCustomerError('customerNameError');
                    } else {
                        clearCustomerError('customerNameError');
                    }
                });
            }
            
            // Add phone validation on blur
            const phoneInput = document.getElementById('customerPhone');
            if (phoneInput) {
                phoneInput.addEventListener('blur', function() {
                    const phone = this.value.trim();
                    if (phone && !/^[+]?[0-9\s\-\(\)]{7,20}$/.test(phone)) {
                        showCustomerError('customerPhoneError', 'Please enter a valid phone number');
                    } else if (phone) {
                        clearCustomerError('customerPhoneError');
                    } else {
                        clearCustomerError('customerPhoneError');
                    }
                });
            }
        });

        function showCustomerError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.add('show');
                errorElement.style.display = 'block';
                
                // Add error state to form group
                const formGroup = errorElement.closest('.form-group');
                if (formGroup) {
                    formGroup.classList.add('error');
                    formGroup.classList.remove('success');
                }
            }
        }

        function clearCustomerError(elementId) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = '';
                errorElement.classList.remove('show');
                errorElement.style.display = 'none';
                
                // Remove error state from form group
                const formGroup = errorElement.closest('.form-group');
                if (formGroup) {
                    formGroup.classList.remove('error');
                }
            }
        }

        function clearCustomerErrors() {
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(element => {
                element.textContent = '';
                element.classList.remove('show');
                element.style.display = 'none';
            });
            
            // Remove error states from form groups
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error', 'success');
            });
        }

        function loadCustomers(page = 1, limit = null) {
            currentPage = page;
            
            const formData = new FormData();
            formData.append('action', 'get_customers');
            formData.append('page', page);
            formData.append('limit', limit || document.getElementById('limitSelect').value);
            formData.append('search', document.getElementById('searchInput').value);
            formData.append('status', document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : '');
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');

            document.getElementById('customersTableContent').innerHTML = 
                '<div class="loading"><i class="fas fa-spinner fa-spin"></i>Loading customers...</div>';

            fetch('', {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    displayCustomers(data);
                } else {
                    document.getElementById('customersTableContent').innerHTML = 
                        '<div class="error">Error loading customers: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('customersTableContent').innerHTML = 
                    '<div class="error">Error loading customers. Please try again.</div>';
            });
        }

        // Display customers in table
        function displayCustomers(data) {
            let html = `
                <table class="customers-table">
                    <thead>
                        <tr>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>PHONE</th>
                            <th>PRIMARY ADDRESS</th>
                            <th>TOTAL ORDERS</th>
                            <th>TOTAL SPENT</th>
                            <th>STATUS</th>
                            <th>MEMBER SINCE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.customers.forEach(customer => {
                const memberSince = new Date(customer.user_created_at).toLocaleDateString();
                const isActive = customer.user_active == 1;
                
                html += `
                    <tr>
                        <td>${customer.full_name || 'N/A'}</td>
                        <td>${customer.email || 'N/A'}</td>
                        <td>${customer.customer_phone || customer.user_phone || 'N/A'}</td>
                        <td>${customer.primary_address || 'No address'}</td>
                        <td>${customer.total_orders}</td>
                        <td>$${parseFloat(customer.total_spent || 0).toFixed(2)}</td>
                        <td>
                            <span class="status-badge status-${isActive ? 'active' : 'inactive'}">
                                ${isActive ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>${memberSince}</td>
                        <td class="actions">
                            <button class="btn-icon btn-primary" onclick="editCustomer(${customer.customer_id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon btn-warning" onclick="toggleCustomerStatus(${customer.customer_id}, ${isActive ? 0 : 1})" title="${isActive ? 'Deactivate' : 'Activate'}">
                                <i class="fas fa-${isActive ? 'ban' : 'check'}"></i>
                            </button>
                            <button class="btn-icon btn-danger" onclick="deleteCustomer(${customer.customer_id})" title="Delete">
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
                    html += `<button class="btn btn-secondary" onclick="loadCustomers(${currentPage - 1})">Previous</button>`;
                }
                
                for (let i = Math.max(1, currentPage - 2); i <= Math.min(data.totalPages, currentPage + 2); i++) {
                    html += `<button class="btn ${i === currentPage ? 'btn-primary' : 'btn-secondary'}" onclick="loadCustomers(${i})">${i}</button>`;
                }
                
                if (currentPage < data.totalPages) {
                    html += `<button class="btn btn-secondary" onclick="loadCustomers(${currentPage + 1})">Next</button>`;
                }
                
                html += '</div>';
            }

            document.getElementById('customersTableContent').innerHTML = html;
        }

        function openCreateModal() {
            isEditing = false;
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus"></i>Add New Customer';
            document.getElementById('customerForm').reset();
            document.getElementById('customerId').value = '';
            document.getElementById('modalMessage').innerHTML = '';
            
            // Show address fields for creation
            document.getElementById('addressGroup').style.display = 'block';
            document.getElementById('locationGroup').style.display = 'block';
            document.getElementById('createInfoBox').style.display = 'block';
            
            clearCustomerErrors();
            
            // Reset all form groups to neutral state (no error or success styling)
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error', 'success');
            });
            
            document.getElementById('customerModal').style.display = 'block';
        }

        function editCustomer(customerId) {
            isEditing = true;
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-edit"></i>Edit Customer';
            
            // Get customer details
            const formData = new FormData();
            formData.append('action', 'get_customer_details');
            formData.append('customer_id', customerId);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const customer = data.customer;
                    document.getElementById('customerId').value = customer.customer_id;
                    document.getElementById('customerName').value = customer.name || '';
                    document.getElementById('customerEmail').value = customer.email || '';
                    document.getElementById('customerPhone').value = customer.customer_phone || '';
                    
                    // Hide address fields for editing (they're managed separately)
                    document.getElementById('addressGroup').style.display = 'none';
                    document.getElementById('locationGroup').style.display = 'none';
                    document.getElementById('createInfoBox').style.display = 'none';
                    
                    document.getElementById('modalMessage').innerHTML = '';
                    clearCustomerErrors();
                    
                    // Reset all form groups to neutral state (no error or success styling)
                    document.querySelectorAll('.form-group').forEach(group => {
                        group.classList.remove('error', 'success');
                    });
                    
                    document.getElementById('customerModal').style.display = 'block';
                } else {
                    alert('Error loading customer details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading customer details.');
            });
        }

        function saveCustomer() {
            // Clear previous messages
            document.getElementById('modalMessage').innerHTML = '';
            clearCustomerErrors();

            const form = document.getElementById('customerForm');
            const formData = new FormData(form);
            
            // Set action based on editing state
            formData.append('action', isEditing ? 'update_customer' : 'create_customer');

            // Show loading
            const saveButton = document.querySelector('.btn-primary');
            const originalText = saveButton.textContent;
            saveButton.textContent = isEditing ? 'Updating...' : 'Creating...';
            saveButton.disabled = true;

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('modalMessage');
                if (data.success) {
                    let message = '<div class="success">' + data.message;
                    if (data.temp_password && !isEditing) {
                        message += '<br><strong>Temporary Password:</strong> ' + data.temp_password;
                        message += '<br><small>Please provide this password to the customer.</small>';
                    }
                    message += '</div>';
                    messageDiv.innerHTML = message;
                    
                    setTimeout(() => {
                        closeModal();
                        loadCustomers(currentPage);
                    }, isEditing ? 1500 : 3000);
                } else {
                    messageDiv.innerHTML = '<div class="error">' + data.message + '</div>';
                }
            })
            .catch(error => {
                const messageDiv = document.getElementById('modalMessage');
                messageDiv.innerHTML = '<div class="error">An error occurred. Please try again.</div>';
                console.error('Error:', error);
            })
            .finally(() => {
                saveButton.textContent = originalText;
                saveButton.disabled = false;
            });
        }

        function toggleCustomerStatus(customerId, newStatus) {
            if (confirm(`Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this customer?`)) {
                const formData = new FormData();
                formData.append('action', 'toggle_customer_status');
                formData.append('customer_id', customerId);
                formData.append('status', newStatus);
                formData.append('csrf_token', '<?php echo $csrfToken; ?>');

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadCustomers(currentPage);
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function deleteCustomer(customerId) {
            if (!confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete_customer');
            formData.append('customer_id', customerId);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    loadCustomers(currentPage);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the customer.');
            });
        }

        function closeModal() {
            document.getElementById('customerModal').style.display = 'none';
        }

        // Search functionality
        if (document.getElementById('searchInput')) {
            document.getElementById('searchInput').addEventListener('keyup', function() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    loadCustomers(1);
                }, 500);
            });
        }

        // Status filter change
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', () => loadCustomers(1));
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
