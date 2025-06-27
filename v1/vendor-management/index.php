<?php
// Only display errors for development - disable for production
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/VendorService.php';

$authService = new AuthService();
$vendorService = new VendorService();

// Require authentication and vendor management permission
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');
$authService->requirePermission('manage_vendors', '/agrimarket-erd/v1/dashboard/');

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
        case 'get_vendors':
            $page = intval($_POST['page'] ?? 1);
            $limit = intval($_POST['limit'] ?? 10);
            $filters = [
                'search' => $_POST['search'] ?? '',
                'verification_status' => $_POST['verification_status'] ?? '',
                'subscription_tier' => $_POST['subscription_tier'] ?? '',
                'business_type' => $_POST['business_type'] ?? ''
            ];
            
            $result = $vendorService->getPaginatedVendors($page, $limit, $filters);
            echo json_encode($result);
            break;
            
        case 'create_vendor':
            $vendorData = [
                'contact_person' => $_POST['contact_person'],
                'business_name' => $_POST['business_name'],
                'business_email' => $_POST['business_email'],
                'business_phone' => $_POST['business_phone'],
                'business_address' => $_POST['business_address'],
                'website_url' => $_POST['website_url'] ?? null,
                'description' => $_POST['description'] ?? null,
                'subscription_tier' => $_POST['subscription_tier'] ?? 'basic'
            ];
            
            $result = $vendorService->createVendor($vendorData);
            echo json_encode($result);
            break;
            
        case 'create_vendor_from_user':
            $vendorData = [
                'user_id' => $_POST['user_id'],
                'business_name' => $_POST['business_name'],
                'business_phone' => $_POST['business_phone'],
                'business_address' => $_POST['business_address'],
                'business_email' => $_POST['business_email'] ?? null,
                'website_url' => $_POST['website_url'] ?? null,
                'description' => $_POST['description'] ?? null,
                'subscription_tier' => $_POST['subscription_tier'] ?? 'basic'
            ];
            
            $result = $vendorService->createVendorFromUser($vendorData);
            echo json_encode($result);
            break;
            
        case 'update_vendor':
            $vendorId = intval($_POST['vendor_id']);
            $vendorData = [
                'business_name' => $_POST['business_name'],
                'business_type' => $_POST['business_type'] ?? null,
                'business_registration_number' => $_POST['business_registration_number'] ?? null,
                'contact_person' => $_POST['contact_person'] ?? null,
                'business_address' => $_POST['business_address'] ?? null,
                'business_phone' => $_POST['business_phone'] ?? null,
                'business_email' => $_POST['business_email'] ?? null,
                'website_url' => $_POST['website_url'] ?? null,
                'description' => $_POST['description'] ?? null
            ];
            
            $result = $vendorService->updateVendor($vendorId, $vendorData);
            echo json_encode($result);
            break;
            
        case 'update_verification_status':
            $vendorId = intval($_POST['vendor_id']);
            $status = $_POST['status'];
            
            $result = $vendorService->updateVerificationStatus($vendorId, $status, $currentUser['user_id']);
            echo json_encode($result);
            break;
            
        case 'update_subscription_tier':
            $vendorId = intval($_POST['vendor_id']);
            $tier = $_POST['tier'];
            
            $result = $vendorService->updateSubscriptionTier($vendorId, $tier, $currentUser['user_id']);
            echo json_encode($result);
            break;
            
        case 'delete_vendor':
            $vendorId = intval($_POST['vendor_id']);
            
            $result = $vendorService->deleteVendor($vendorId, $currentUser['user_id']);
            echo json_encode($result);
            break;
            
        case 'get_vendor_details':
            $vendorId = intval($_POST['vendor_id']);
            $vendor = $vendorService->getVendorDetails($vendorId);
            
            if ($vendor) {
                echo json_encode(['success' => true, 'vendor' => $vendor]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Vendor not found']);
            }
            break;
            
        case 'get_available_users':
            $users = $vendorService->getAvailableUsers();
            echo json_encode(['success' => true, 'users' => $users]);
            break;
            
        case 'toggle_vendor_status':
            $vendorId = intval($_POST['vendor_id']);
            $status = intval($_POST['status']);
            
            // Toggle is_active in users table for vendor
            $result = $vendorService->toggleVendorStatus($vendorId, $status, $currentUser['user_id']);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// Get vendor statistics for dashboard
$vendorStats = $vendorService->getVendorStatistics();
$businessTypes = $vendorService->getBusinessTypes();
$subscriptionTiers = $vendorService->getSubscriptionTiers();
$verificationStatuses = $vendorService->getVerificationStatuses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Management - AgriMarket Solutions</title>
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
            $pageTitle = 'Vendor Management';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2>Vendor Management</h2>
                            <p>Manage vendors, subscriptions, and business profiles</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateVendorModal()">
                            <i class="fas fa-plus"></i>
                            Add New Vendor
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon vendor">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $vendorStats['total_vendors']; ?></h3>
                            <p>Total Vendors</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $vendorStats['verified_vendors']; ?></h3>
                            <p>Active Vendors</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon premium">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $vendorStats['premium_tier']; ?></h3>
                            <p>Premium</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon sales">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>$<?php echo number_format($vendorStats['total_platform_sales'], 2); ?></h3>
                            <p>Total Sales</p>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="controls-section">
                    <div class="controls-left">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search vendors...">
                        </div>
                        <select id="verificationFilter">
                            <option value="">All Verification Status</option>
                            <?php foreach ($verificationStatuses as $status): ?>
                            <option value="<?php echo $status['value']; ?>"><?php echo $status['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="tierFilter">
                            <option value="">All Subscription Tiers</option>
                            <?php foreach ($subscriptionTiers as $tier): ?>
                            <option value="<?php echo $tier['value']; ?>"><?php echo $tier['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="businessTypeFilter">
                            <option value="">All Business Types</option>
                            <?php foreach ($businessTypes as $type): ?>
                            <option value="<?php echo $type['value']; ?>"><?php echo $type['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="statusFilter">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Vendors Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Vendors List</h3>
                        <div class="table-controls">
                            <select id="limitSelect" onchange="loadVendors()">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="vendorsTableContent">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Loading vendors...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Vendor Modal -->
    <div id="vendorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-store"></i>Add New Vendor</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalMessage"></div>
                <form id="vendorForm">
                    <input type="hidden" id="vendorId" name="vendor_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-group">
                        <label>Contact Person Name *</label>
                        <input type="text" id="contactPerson" name="contact_person" required 
                               minlength="2" maxlength="100" placeholder="Full name of contact person">
                        <div class="error-message" id="contactPersonError"></div>
                        <small class="help-text">Full name of the primary contact person (2-100 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Business Name *</label>
                        <input type="text" id="businessName" name="business_name" required 
                               minlength="2" maxlength="100" pattern="[a-zA-Z0-9\s\-\._&amp;']+">
                        <div class="error-message" id="businessNameError"></div>
                        <small class="help-text">Business name (2-100 characters, letters, numbers, spaces, and common symbols)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Business Email *</label>
                        <input type="email" id="businessEmail" name="business_email" required
                               maxlength="100" placeholder="business@company.com">
                        <div class="error-message" id="businessEmailError"></div>
                        <small class="help-text">Business email address (will be used for login)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Business Phone *</label>
                        <input type="tel" id="businessPhone" name="business_phone" required 
                               pattern="[+]?[0-9\s\-\(\)]{7,20}" placeholder="e.g., +1234567890">
                        <div class="error-message" id="businessPhoneError"></div>
                        <small class="help-text">Valid phone number (7-20 digits, may include +, spaces, hyphens, parentheses)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Business Address *</label>
                        <textarea id="businessAddress" name="business_address" required rows="3" 
                                  minlength="10" maxlength="500" placeholder="Enter complete business address"></textarea>
                        <div class="error-message" id="businessAddressError"></div>
                        <small class="help-text">Complete business address (10-500 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Website URL</label>
                        <input type="url" id="websiteUrl" name="website_url" 
                               maxlength="255" placeholder="https://www.company.com">
                        <div class="error-message" id="websiteUrlError"></div>
                        <small class="help-text">Optional website URL (must start with http:// or https://)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Business Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  maxlength="1000" placeholder="Describe your business..."></textarea>
                        <div class="error-message" id="descriptionError"></div>
                        <small class="help-text">Optional business description (max 1000 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Subscription Tier</label>
                        <select id="subscriptionTier" name="subscription_tier">
                            <?php foreach ($subscriptionTiers as $tier): ?>
                            <option value="<?php echo $tier['value']; ?>"><?php echo $tier['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message" id="subscriptionTierError"></div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveVendor()">Save Vendor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        let currentPage = 1;
        let isEditing = false;

        // Load vendors on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadVendors();
            
            // Add real-time validation event listeners
            const contactPersonInput = document.getElementById('contactPerson');
            if (contactPersonInput) {
                contactPersonInput.addEventListener('blur', function() {
                    const contactPerson = this.value.trim();
                    if (contactPerson && (contactPerson.length < 2 || contactPerson.length > 100)) {
                        showVendorError('contactPersonError', 'Contact person name must be between 2 and 100 characters');
                    } else if (contactPerson) {
                        clearVendorError('contactPersonError');
                    } else {
                        clearVendorError('contactPersonError');
                    }
                });
            }
            
            const businessNameInput = document.getElementById('businessName');
            if (businessNameInput) {
                businessNameInput.addEventListener('blur', function() {
                    const businessName = this.value.trim();
                    if (businessName && (businessName.length < 2 || businessName.length > 100)) {
                        showVendorError('businessNameError', 'Business name must be between 2 and 100 characters');
                    } else if (businessName && !/^[a-zA-Z0-9\s\-\._&']+$/.test(businessName)) {
                        showVendorError('businessNameError', 'Business name contains invalid characters');
                    } else if (businessName) {
                        clearVendorError('businessNameError');
                    } else {
                        clearVendorError('businessNameError');
                    }
                });
            }
            
            // Business phone validation
            const businessPhoneInput = document.getElementById('businessPhone');
            if (businessPhoneInput) {
                businessPhoneInput.addEventListener('blur', function() {
                    const businessPhone = this.value.trim();
                    if (businessPhone && !/^[+]?[0-9\s\-\(\)]{7,20}$/.test(businessPhone)) {
                        showVendorError('businessPhoneError', 'Please enter a valid phone number');
                    } else if (businessPhone) {
                        clearVendorError('businessPhoneError');
                    } else {
                        clearVendorError('businessPhoneError');
                    }
                });
            }
            
            // Business address validation
            const businessAddressInput = document.getElementById('businessAddress');
            if (businessAddressInput) {
                businessAddressInput.addEventListener('blur', function() {
                    const businessAddress = this.value.trim();
                    if (businessAddress && (businessAddress.length < 10 || businessAddress.length > 500)) {
                        showVendorError('businessAddressError', 'Address must be between 10 and 500 characters');
                    } else if (businessAddress) {
                        clearVendorError('businessAddressError');
                    } else {
                        clearVendorError('businessAddressError');
                    }
                });
            }
            
            // Business email validation
            const businessEmailInput = document.getElementById('businessEmail');
            if (businessEmailInput) {
                businessEmailInput.addEventListener('blur', function() {
                    const businessEmail = this.value.trim();
                    if (businessEmail) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(businessEmail)) {
                            showVendorError('businessEmailError', 'Please enter a valid email address');
                        } else if (businessEmail.length > 100) {
                            showVendorError('businessEmailError', 'Email must be less than 100 characters');
                        } else {
                            clearVendorError('businessEmailError');
                        }
                    } else {
                        clearVendorError('businessEmailError');
                    }
                });
            }
            
            // Website URL validation
            const websiteUrlInput = document.getElementById('websiteUrl');
            if (websiteUrlInput) {
                websiteUrlInput.addEventListener('blur', function() {
                    const websiteUrl = this.value.trim();
                    if (websiteUrl) {
                        try {
                            const url = new URL(websiteUrl);
                            if (!['http:', 'https:'].includes(url.protocol)) {
                                throw new Error('Invalid protocol');
                            }
                            if (websiteUrl.length > 255) {
                                showVendorError('websiteUrlError', 'Website URL must be less than 255 characters');
                            } else {
                                clearVendorError('websiteUrlError');
                            }
                        } catch (e) {
                            showVendorError('websiteUrlError', 'Please enter a valid URL (must start with http:// or https://)');
                        }
                    } else {
                        clearVendorError('websiteUrlError');
                    }
                });
            }
            
            // Description validation
            const descriptionInput = document.getElementById('description');
            if (descriptionInput) {
                descriptionInput.addEventListener('blur', function() {
                    const description = this.value.trim();
                    if (description && description.length > 1000) {
                        showVendorError('descriptionError', 'Description must be less than 1000 characters');
                    } else {
                        clearVendorError('descriptionError');
                    }
                });
            }
            

        });

        // Load vendors function
        function loadVendors(page = 1) {
            currentPage = page;
            
            const formData = new FormData();
            formData.append('action', 'get_vendors');
            formData.append('page', page);
            formData.append('limit', document.getElementById('limitSelect') ? document.getElementById('limitSelect').value : 10);
            formData.append('search', document.getElementById('searchInput') ? document.getElementById('searchInput').value : '');
            formData.append('verification_status', document.getElementById('verificationFilter') ? document.getElementById('verificationFilter').value : '');
            formData.append('subscription_tier', document.getElementById('tierFilter') ? document.getElementById('tierFilter').value : '');
            formData.append('business_type', document.getElementById('businessTypeFilter') ? document.getElementById('businessTypeFilter').value : '');
            formData.append('status', document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : '');
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');

            fetch('', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayVendors(data);
                } else {
                    document.getElementById('vendorsTableContent').innerHTML = 
                        '<div class="error">Error loading vendors: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('vendorsTableContent').innerHTML = 
                    '<div class="error">Error loading vendors. Please try again.</div>';
            });
        }

        // Display vendors in table
        function displayVendors(data) {
            let html = `
                <table class="management-table">
                    <thead>
                        <tr>
                            <th>BUSINESS NAME</th>
                            <th>CONTACT PERSON</th>
                            <th>CONTACT NUMBER</th>
                            <th>ADDRESS</th>
                            <th>SUBSCRIPTION TIER</th>
                            <th>STATUS</th>
                            <th>CREATED</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.vendors.forEach(vendor => {
                const createdDate = new Date(vendor.created_at).toLocaleDateString();
                const isActive = vendor.is_active == 1;
                
                html += `
                    <tr>
                        <td>${vendor.business_name}</td>
                        <td>${vendor.contact_name || 'N/A'}</td>
                        <td>${vendor.contact_number}</td>
                        <td>${vendor.address}</td>
                        <td><span class="tier-badge ${vendor.subscription_tier || 'basic'}">${vendor.subscription_tier || 'Basic'}</span></td>
                        <td>
                            <span class="status-badge status-${isActive ? 'active' : 'inactive'}">
                                ${isActive ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>${createdDate}</td>
                        <td class="actions">
                            <button class="btn-icon btn-primary" onclick="editVendor(${vendor.vendor_id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon btn-warning" onclick="toggleVendorStatus(${vendor.vendor_id}, ${isActive ? 0 : 1})" title="${isActive ? 'Deactivate' : 'Activate'}">
                                <i class="fas fa-${isActive ? 'ban' : 'check'}"></i>
                            </button>
                            <button class="btn-icon btn-danger" onclick="deleteVendor(${vendor.vendor_id})" title="Delete">
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
                    html += `<button class="btn btn-secondary" onclick="loadVendors(${currentPage - 1})">Previous</button>`;
                }
                
                for (let i = Math.max(1, currentPage - 2); i <= Math.min(data.totalPages, currentPage + 2); i++) {
                    html += `<button class="btn ${i === currentPage ? 'btn-primary' : 'btn-secondary'}" onclick="loadVendors(${i})">${i}</button>`;
                }
                
                if (currentPage < data.totalPages) {
                    html += `<button class="btn btn-secondary" onclick="loadVendors(${currentPage + 1})">Next</button>`;
                }
                
                html += '</div>';
            }

            document.getElementById('vendorsTableContent').innerHTML = html;
        }

        // Load available users for vendor creation
        function loadAvailableUsers() {
            const formData = new FormData();
            formData.append('action', 'get_available_users');
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const userSelect = document.getElementById('userId');
                    userSelect.innerHTML = '<option value="">Select a user...</option>';
                    data.users.forEach(user => {
                        userSelect.innerHTML += `<option value="${user.user_id}">${user.name} (${user.email})</option>`;
                    });
                }
            });
        }

        // Modal functions
        function openCreateVendorModal() {
            isEditing = false;
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-store"></i>Add New Vendor';
            document.getElementById('vendorForm').reset();
            document.getElementById('vendorId').value = '';
            document.getElementById('modalMessage').innerHTML = '';
            clearVendorErrors();
            
            // Reset all form groups to neutral state (no error or success styling)
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error', 'success');
            });
            
            document.getElementById('vendorModal').style.display = 'block';
        }

        function editVendor(vendorId) {
            isEditing = true;
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i>Edit Vendor';
            document.getElementById('vendorId').value = vendorId;
            document.getElementById('modalMessage').innerHTML = '<div class="loading">Loading vendor details...</div>';
            clearVendorErrors();
            
            // Reset all form groups to neutral state (no error or success styling)
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error', 'success');
            });
            
            // Load vendor details via AJAX
            const formData = new FormData();
            formData.append('action', 'get_vendor_details');
            formData.append('vendor_id', vendorId);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalMessage').innerHTML = '';
                
                if (data.success && data.vendor) {
                    const vendor = data.vendor;
                    
                    // Populate form fields with vendor data
                    document.getElementById('contactPerson').value = vendor.contact_name || '';
                    document.getElementById('businessName').value = vendor.business_name || '';
                    document.getElementById('businessEmail').value = vendor.user_email || '';
                    document.getElementById('businessPhone').value = vendor.contact_number || '';
                    document.getElementById('businessAddress').value = vendor.address || '';
                    document.getElementById('websiteUrl').value = vendor.website_url || '';
                    document.getElementById('description').value = vendor.description || '';
                    
                    // Set subscription tier
                    const tierSelect = document.getElementById('subscriptionTier');
                    if (tierSelect && vendor.subscription_tier_id) {
                        // Try to set by value (tier name)
                        const tierOptions = tierSelect.options;
                        let tierSet = false;
                        
                        // First try to match by tier name from the subscription_tiers table
                        for (let option of tierOptions) {
                            if (option.value === vendor.subscription_tier_id.toString()) {
                                tierSelect.value = option.value;
                                tierSet = true;
                                break;
                            }
                        }
                        
                        // If not found, try to match by text content
                        if (!tierSet) {
                            for (let option of tierOptions) {
                                if (option.text.toLowerCase().includes('bronze') && vendor.subscription_tier_id == 1) {
                                    tierSelect.value = option.value;
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    document.getElementById('modalMessage').innerHTML = 
                        '<div class="error">Failed to load vendor details: ' + (data.message || 'Unknown error') + '</div>';
                }
            })
            .catch(error => {
                console.error('Error loading vendor details:', error);
                document.getElementById('modalMessage').innerHTML = 
                    '<div class="error">Error loading vendor details. Please try again.</div>';
            });
            
            document.getElementById('vendorModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('vendorModal').style.display = 'none';
        }

        // Form validation functions
        function validateVendorForm() {
            let isValid = true;
            clearVendorErrors();
            
            // Validate Contact Person
            const contactPerson = document.getElementById('contactPerson').value.trim();
            if (!contactPerson) {
                showVendorError('contactPersonError', 'Contact person name is required');
                isValid = false;
            } else if (contactPerson.length < 2) {
                showVendorError('contactPersonError', 'Contact person name must be at least 2 characters');
                isValid = false;
            } else if (contactPerson.length > 100) {
                showVendorError('contactPersonError', 'Contact person name must be less than 100 characters');
                isValid = false;
            }
            
            // Validate Business Name
            const businessName = document.getElementById('businessName').value.trim();
            if (!businessName) {
                showVendorError('businessNameError', 'Business name is required');
                isValid = false;
            } else if (businessName.length < 2) {
                showVendorError('businessNameError', 'Business name must be at least 2 characters');
                isValid = false;
            } else if (businessName.length > 100) {
                showVendorError('businessNameError', 'Business name must be less than 100 characters');
                isValid = false;
            } else if (!/^[a-zA-Z0-9\s\-\._&']+$/.test(businessName)) {
                showVendorError('businessNameError', 'Business name contains invalid characters');
                isValid = false;
            }
            
            // Validate Business Email
            const businessEmail = document.getElementById('businessEmail').value.trim();
            if (!businessEmail) {
                showVendorError('businessEmailError', 'Business email is required');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(businessEmail)) {
                showVendorError('businessEmailError', 'Please enter a valid email address');
                isValid = false;
            } else if (businessEmail.length > 100) {
                showVendorError('businessEmailError', 'Email must be less than 100 characters');
                isValid = false;
            }
            
            // Validate Business Phone
            const businessPhone = document.getElementById('businessPhone').value.trim();
            if (!businessPhone) {
                showVendorError('businessPhoneError', 'Business phone is required');
                isValid = false;
            } else if (!/^[+]?[0-9\s\-\(\)]{7,20}$/.test(businessPhone)) {
                showVendorError('businessPhoneError', 'Please enter a valid phone number');
                isValid = false;
            }
            
            // Validate Business Address
            const businessAddress = document.getElementById('businessAddress').value.trim();
            if (!businessAddress) {
                showVendorError('businessAddressError', 'Business address is required');
                isValid = false;
            } else if (businessAddress.length < 10) {
                showVendorError('businessAddressError', 'Address must be at least 10 characters');
                isValid = false;
            } else if (businessAddress.length > 500) {
                showVendorError('businessAddressError', 'Address must be less than 500 characters');
                isValid = false;
            }
            

            
            // Validate Website URL (optional)
            const websiteUrl = document.getElementById('websiteUrl').value.trim();
            if (websiteUrl) {
                try {
                    const url = new URL(websiteUrl);
                    if (!['http:', 'https:'].includes(url.protocol)) {
                        throw new Error('Invalid protocol');
                    }
                    if (websiteUrl.length > 255) {
                        showVendorError('websiteUrlError', 'Website URL must be less than 255 characters');
                        isValid = false;
                    }
                } catch (e) {
                    showVendorError('websiteUrlError', 'Please enter a valid URL (must start with http:// or https://)');
                    isValid = false;
                }
            }
            
            // Validate Description (optional)
            const description = document.getElementById('description').value.trim();
            if (description && description.length > 1000) {
                showVendorError('descriptionError', 'Description must be less than 1000 characters');
                isValid = false;
            }
            
            return isValid;
        }
        
        function showVendorError(elementId, message) {
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
        
        function clearVendorError(elementId) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.classList.remove('show');
                errorElement.style.display = 'none';
                
                // Remove error state from form group (return to neutral state)
                const formGroup = errorElement.closest('.form-group');
                if (formGroup) {
                    formGroup.classList.remove('error', 'success');
                }
            }
        }
        
        function clearVendorErrors() {
            const errorElements = ['contactPersonError', 'businessNameError', 'businessEmailError', 
                                 'businessPhoneError', 'businessAddressError', 'websiteUrlError', 
                                 'descriptionError', 'subscriptionTierError'];
            errorElements.forEach(id => {
                clearVendorError(id);
            });
            
            // Remove all form group states
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error', 'success');
            });
        }
        
        function saveVendor() {
            // Clear previous messages
            document.getElementById('modalMessage').innerHTML = '';
            
            // Validate form
            if (!validateVendorForm()) {
                document.getElementById('modalMessage').innerHTML = 
                    '<div class="error">Please fix the errors above</div>';
                return;
            }
            
            const formData = new FormData(document.getElementById('vendorForm'));
            formData.append('action', isEditing ? 'update_vendor' : 'create_vendor');

            // Show loading state
            const saveButton = document.querySelector('#vendorForm .btn-primary');
            const originalText = saveButton.textContent;
            saveButton.textContent = 'Saving...';
            saveButton.disabled = true;

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let message = data.message;
                    if (data.temp_password && !isEditing) {
                        message += '<br><br><strong>Temporary Password:</strong> <code style="background: #f5f5f5; padding: 2px 4px; border-radius: 3px;">' + data.temp_password + '</code>';
                        message += '<br><small style="color: #666; font-style: italic;">Please provide this password to the vendor for their first login.</small>';
                    }
                    
                    document.getElementById('modalMessage').innerHTML = 
                        '<div class="success">' + message + '</div>';
                    
                    // Close modal after longer delay if password is shown
                    const delay = (data.temp_password && !isEditing) ? 5000 : 1500;
                    setTimeout(() => {
                        closeModal();
                        loadVendors(currentPage);
                    }, delay);
                } else {
                    document.getElementById('modalMessage').innerHTML = 
                        '<div class="error">' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalMessage').innerHTML = 
                    '<div class="error">Error saving vendor. Please try again.</div>';
            })
            .finally(() => {
                // Reset button state
                saveButton.textContent = originalText;
                saveButton.disabled = false;
            });
        }

        function toggleVendorStatus(vendorId, newStatus) {
            if (confirm(`Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this vendor?`)) {
                const formData = new FormData();
                formData.append('action', 'toggle_vendor_status');
                formData.append('vendor_id', vendorId);
                formData.append('status', newStatus);
                formData.append('csrf_token', '<?php echo $csrfToken; ?>');

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadVendors(currentPage);
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function deleteVendor(vendorId) {
            if (confirm('Are you sure you want to delete this vendor? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete_vendor');
                formData.append('vendor_id', vendorId);
                formData.append('csrf_token', '<?php echo $csrfToken; ?>');

                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadVendors(currentPage);
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        // Search functionality
        if (document.getElementById('searchInput')) {
            document.getElementById('searchInput').addEventListener('keyup', function() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    loadVendors(1);
                }, 500);
            });
        }

        // Filter changes
        ['verificationFilter', 'tierFilter', 'businessTypeFilter', 'statusFilter'].forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                element.addEventListener('change', () => loadVendors(1));
            }
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('vendorModal');
            if (event.target === modal) {
                closeModal();
            }
        });
    </script>
</body>
</html> 