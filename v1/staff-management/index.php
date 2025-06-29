<?php
// Only display errors for development - disable for production
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/StaffService.php';

// Set page title for tracking
$pageTitle = 'Staff Management - AgriMarket Solutions';

// Include page tracking
require_once __DIR__ . '/../../includes/page_tracking.php';

/**
 * Sends a JSON response and terminates the script.
 * @param mixed $data The data to encode and send.
 */
function send_json_response($data) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// Initialize services first
try {
    $authService = new AuthService();
    $staffService = new StaffService();
} catch (Exception $e) {
    // For a real-world app, you'd log this error.
    // For now, we stop execution if services fail.
    send_json_response(['success' => false, 'message' => 'Service initialization failed: ' . $e->getMessage()]);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        // AJAX-specific auth checks can go here if needed
        $currentUser = $authService->getCurrentUser();
        if (!$currentUser) {
            send_json_response(['success' => false, 'message' => 'Authentication required.']);
        }
        if (!$authService->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            send_json_response(['success' => false, 'message' => 'Invalid CSRF token.']);
        }

        switch ($_POST['action']) {
            case 'get_staff':
                $page = intval($_POST['page'] ?? 1);
                $limit = intval($_POST['limit'] ?? 10);
                $filters = [
                    'search' => $_POST['search'] ?? '',
                    'department' => $_POST['department'] ?? '',
                    'status' => $_POST['status'] ?? '',
                    'position' => $_POST['position'] ?? ''
                ];
                $result = $staffService->getPaginatedStaff($page, $limit, $filters);
                send_json_response($result);
                break;

            case 'create_staff':
                $staffData = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'department' => $_POST['department'],
                    'position' => $_POST['position'],
                    'hire_date' => $_POST['hire_date'],
                    'salary' => $_POST['salary'] ?? null,
                    'phone' => $_POST['phone'] ?? null,
                    'emergency_contact' => $_POST['emergency_contact'] ?? null,
                    'address' => $_POST['address'] ?? null,
                    'status' => $_POST['status'] ?? 'active'
                ];
                $result = $staffService->createStaff($staffData);
                send_json_response($result);
                break;

            case 'update_staff':
                $staffData = [
                    'staff_id' => $_POST['staff_id'],
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'department' => $_POST['department'],
                    'position' => $_POST['position'],
                    'hire_date' => $_POST['hire_date'],
                    'salary' => $_POST['salary'] ?? null,
                    'phone' => $_POST['phone'] ?? null,
                    'emergency_contact' => $_POST['emergency_contact'] ?? null,
                    'address' => $_POST['address'] ?? null,
                    'status' => $_POST['status'] ?? 'active',
                    'performance_rating' => $_POST['performance_rating'] ?? null
                ];
                $result = $staffService->updateStaff($staffData);
                send_json_response($result);
                break;

            case 'delete_staff':
                $result = $staffService->deleteStaff($_POST['staff_id']);
                send_json_response($result);
                break;

            case 'get_staff_details':
                $staff = $staffService->getStaffById($_POST['staff_id']);
                send_json_response(['success' => true, 'staff' => $staff]);
                break;

            case 'generate_employee_id':
                $employeeId = $staffService->generateEmployeeId();
                send_json_response(['success' => true, 'employee_id' => $employeeId]);
                break;

            case 'assign_task':
                $taskData = [
                    'staff_id' => $_POST['staff_id'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'priority' => $_POST['priority'] ?? 'medium',
                    'due_date' => $_POST['due_date'] ?? null
                ];
                $result = $staffService->assignTask($taskData);
                send_json_response($result);
                break;

            case 'get_staff_tasks':
                $tasks = $staffService->getStaffTasks($_POST['staff_id']);
                send_json_response(['success' => true, 'tasks' => $tasks]);
                break;

            case 'get_staff_performance':
                $performance = $staffService->getStaffPerformance($_POST['staff_id']);
                send_json_response(['success' => true, 'performance' => $performance]);
                break;

            case 'update_staff_status':
                $result = $staffService->toggleStaffStatus($_POST['staff_id'], $_POST['status'], $currentUser['user_id']);
                send_json_response($result);
                break;

            case 'get_updated_statistics':
                $stats = $staffService->getStaffStatistics();
                send_json_response(['success' => true, 'statistics' => $stats]);
                break;

            case 'update_task':
                $taskData = [
                    'task_id' => $_POST['task_id'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'priority' => $_POST['priority'] ?? 'medium',
                    'due_date' => $_POST['due_date'] ?? null
                ];
                $result = $staffService->updateTask($taskData);
                send_json_response($result);
                break;

            default:
                send_json_response(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } catch (Exception $e) {
        // Catch any uncaught exceptions from services and send as a JSON error
        send_json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
}

// --- Regular Page Load Logic ---
// If we get here, it's not an AJAX request.

// Require authentication and staff management permission for regular page load
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');
$authService->requirePermission('manage_staff', '/agrimarket-erd/v1/dashboard/');

$currentUser = $authService->getCurrentUser();
$csrfToken = $authService->generateCSRFToken();

// Get staff statistics for dashboard
$staffStats = $staffService->getStaffStatistics();
$departments = $staffService->getDepartments();
$positions = $staffService->getPositions();
$statuses = ['active', 'inactive', 'terminated'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management - AgriMarket Solutions</title>
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
            $pageTitle = 'Staff Management';
            include '../components/header.php';
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2>Staff Management</h2>
                            <p>Manage employees, departments, and organizational structure</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateStaffModal()">
                            <i class="fas fa-plus"></i>
                            Add New Staff
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon staff">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $staffStats['total_staff']; ?></h3>
                            <p>Total Staff</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo array_sum(array_column(array_filter($staffStats['status_stats'], function($s) { return $s['status'] === 'active'; }), 'count')); ?></h3>
                            <p>Active Staff</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon salary">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>$<?php echo number_format($staffStats['avg_salary'], 2); ?></h3>
                            <p>Avg Salary</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon hires">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $staffStats['recent_hires']; ?></h3>
                            <p>Recent Hires</p>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="controls-section">
                    <div class="controls-left">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Search staff by name, ID, department...">
                            <i class="fas fa-search"></i>
                        </div>
                        
                        <select id="departmentFilter">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select id="statusFilter">
                            <option value="">All Status</option>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?php echo htmlspecialchars($status); ?>"><?php echo ucfirst(htmlspecialchars($status)); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select id="positionFilter">
                            <option value="">All Positions</option>
                            <?php foreach ($positions as $pos): ?>
                                <option value="<?php echo htmlspecialchars($pos); ?>"><?php echo htmlspecialchars($pos); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="table-controls">
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fas fa-times"></i>
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Staff Table -->
                <div class="table-container">
                    <table class="data-table" id="staffTable">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Hire Date</th>
                                <th>Salary</th>
                                <th>TASKS</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="staffTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                    
                    <!-- Loading indicator -->
                    <div id="loadingIndicator" class="loading-indicator" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        Loading staff data...
                    </div>
                    
                    <!-- No data message -->
                    <div id="noDataMessage" class="no-data-message" style="display: none;">
                        <i class="fas fa-users"></i>
                        <p>No staff members found</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="pagination-container" id="paginationContainer">
                    <!-- Pagination will be generated here -->
                </div>
            </div>
        </main>
    </div>

    <!-- Create/Edit Staff Modal -->
    <div id="createStaffModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-user-plus"></i>Add New Staff</h3>
                <button class="close-btn" type="button" onclick="closeCreateStaffModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalMessage"></div>
                <form id="createStaffForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" id="createEmployeeId" name="employee_id" readonly>
                        <small class="help-text">Auto-generated employee ID</small>
                    </div>
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" id="createName" name="name" required minlength="2" maxlength="100" placeholder="Full name of staff member">
                        <div class="error-message" id="nameError"></div>
                        <small class="help-text">Full name of the staff member (2-100 characters)</small>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="createEmail" name="email" required maxlength="100" placeholder="staff@company.com">
                        <div class="error-message" id="emailError"></div>
                        <small class="help-text">Staff email address (will be used for login)</small>
                    </div>
                    <div class="form-group">
                        <label>Department *</label>
                        <select id="createDepartment" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message" id="departmentError"></div>
                    </div>
                    <div class="form-group">
                        <label>Position *</label>
                        <select id="createPosition" name="position" required>
                            <option value="">Select Position</option>
                            <?php foreach ($positions as $pos): ?>
                                <option value="<?php echo htmlspecialchars($pos); ?>"><?php echo htmlspecialchars($pos); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message" id="positionError"></div>
                    </div>
                    <div class="form-group">
                        <label>Hire Date *</label>
                        <input type="date" id="createHireDate" name="hire_date" required>
                        <div class="error-message" id="hireDateError"></div>
                    </div>
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="number" id="createSalary" name="salary" step="0.01" min="0" placeholder="e.g., 5000">
                        <div class="error-message" id="salaryError"></div>
                        <small class="help-text">Optional. Enter monthly salary in your currency.</small>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" id="createPhone" name="phone" pattern="[+]?[0-9\s\-\(\)]{7,20}" placeholder="e.g., +1234567890">
                        <div class="error-message" id="phoneError"></div>
                        <small class="help-text">Valid phone number (7-20 digits, may include +, spaces, hyphens, parentheses)</small>
                    </div>
                    <div class="form-group">
                        <label>Emergency Contact</label>
                        <input type="text" id="createEmergencyContact" name="emergency_contact" maxlength="100" placeholder="Emergency contact person or number">
                        <div class="error-message" id="emergencyContactError"></div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea id="createAddress" name="address" rows="3" maxlength="500" placeholder="Enter staff address"></textarea>
                        <div class="error-message" id="addressError"></div>
                        <small class="help-text">Optional. Complete address (max 500 characters)</small>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="createStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                        </select>
                        <div class="error-message" id="statusError"></div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeCreateStaffModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div id="editStaffModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-edit"></i>Edit Staff Member</h3>
                <button class="close-btn" type="button" onclick="closeEditStaffModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="editModalMessage"></div>
                <form id="editStaffForm">
                    <input type="hidden" id="editStaffId" name="staff_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" id="editEmployeeId" name="employee_id" readonly>
                        <small class="help-text">Employee ID cannot be changed.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" id="editName" name="name" required minlength="2" maxlength="100" placeholder="Full name of staff member">
                        <div class="error-message" id="editNameError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="editEmail" name="email" required maxlength="100" placeholder="staff@company.com">
                        <div class="error-message" id="editEmailError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Department *</label>
                        <select id="editDepartment" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message" id="editDepartmentError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Position *</label>
                        <select id="editPosition" name="position" required>
                            <option value="">Select Position</option>
                            <?php foreach ($positions as $pos): ?>
                                <option value="<?php echo htmlspecialchars($pos); ?>"><?php echo htmlspecialchars($pos); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message" id="editPositionError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Hire Date *</label>
                        <input type="date" id="editHireDate" name="hire_date" required>
                        <div class="error-message" id="editHireDateError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="number" id="editSalary" name="salary" step="0.01" min="0" placeholder="e.g., 5000">
                        <div class="error-message" id="editSalaryError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" id="editPhone" name="phone" pattern="[+]?[0-9\s\\-\\(\\)]_7,20_" placeholder="e.g., +1234567890">
                        <div class="error-message" id="editPhoneError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Emergency Contact</label>
                        <input type="text" id="editEmergencyContact" name="emergency_contact" maxlength="100" placeholder="Emergency contact person or number">
                        <div class="error-message" id="editEmergencyContactError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Address</label>
                        <textarea id="editAddress" name="address" rows="3" maxlength="500" placeholder="Enter staff address"></textarea>
                        <div class="error-message" id="editAddressError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select id="editStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                        </select>
                        <div class="error-message" id="editStatusError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Performance Rating</label>
                        <input type="number" id="editPerformanceRating" name="performance_rating" step="0.01" min="0" max="5" placeholder="e.g., 4.5">
                        <div class="error-message" id="editPerformanceRatingError"></div>
                        <small class="help-text">Optional. Performance rating from 0 to 5.</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditStaffModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Staff Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Staff Details Modal -->
    <div id="viewStaffModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user"></i>Staff Details</h3>
                <button class="close-btn" type="button" onclick="closeViewStaffModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="staff-details">
                    <div class="detail-section">
                        <h4>Basic Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Employee ID:</label>
                                <span id="viewEmployeeId"></span>
                            </div>
                            <div class="detail-item">
                                <label>Name:</label>
                                <span id="viewName"></span>
                            </div>
                            <div class="detail-item">
                                <label>Email:</label>
                                <span id="viewEmail"></span>
                            </div>
                            <div class="detail-item">
                                <label>Department:</label>
                                <span id="viewDepartment"></span>
                            </div>
                            <div class="detail-item">
                                <label>Position:</label>
                                <span id="viewPosition"></span>
                            </div>
                            <div class="detail-item">
                                <label>Hire Date:</label>
                                <span id="viewHireDate"></span>
                            </div>
                            <div class="detail-item">
                                <label>Salary:</label>
                                <span id="viewSalary"></span>
                            </div>
                            <div class="detail-item">
                                <label>Status:</label>
                                <span id="viewStatus"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4>Contact Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Phone:</label>
                                <span id="viewPhone"></span>
                            </div>
                            <div class="detail-item">
                                <label>Emergency Contact:</label>
                                <span id="viewEmergencyContact"></span>
                            </div>
                            <div class="detail-item">
                                <label>Address:</label>
                                <span id="viewAddress"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4>Performance & Activities</h4>
                        <div class="performance-summary">
                            <div class="performance-item">
                                <label>Performance Rating:</label>
                                <span id="viewPerformanceRating"></span>
                            </div>
                            <div class="performance-item">
                                <label>Tasks Completed:</label>
                                <span id="viewTasksCompleted"></span>
                            </div>
                            <div class="performance-item">
                                <label>Last Activity:</label>
                                <span id="viewLastActivity"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4>Assigned Tasks</h4>
                        <div id="assignedTasksList" class="tasks-list">
                            <div class="loading-tasks">
                                <i class="fas fa-spinner fa-spin"></i>
                                Loading tasks...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeViewStaffModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="openTaskAssignmentModal()">Assign Task</button>
            </div>
        </div>
    </div>

    <!-- Task Assignment Modal -->
    <div id="taskAssignmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-tasks"></i>Assign Task</h3>
                <button class="close-btn" type="button" onclick="closeTaskAssignmentModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="taskAssignmentForm">
                    <input type="hidden" id="taskStaffId" name="staff_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-group">
                        <label>Task Title *</label>
                        <input type="text" id="taskTitle" name="title" required maxlength="100" placeholder="Enter task title">
                        <div class="error-message" id="taskTitleError"></div>
                        <small class="help-text">Brief title describing the task (max 100 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Task Description</label>
                        <textarea id="taskDescription" name="description" rows="4" maxlength="500" placeholder="Describe the task details, requirements, and expectations..."></textarea>
                        <div class="error-message" id="taskDescriptionError"></div>
                        <small class="help-text">Detailed description of the task (max 500 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Priority</label>
                        <select id="taskPriority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                        <div class="error-message" id="taskPriorityError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" id="taskDueDate" name="due_date" min="<?php echo date('Y-m-d'); ?>">
                        <div class="error-message" id="taskDueDateError"></div>
                        <small class="help-text">Optional due date for the task</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeTaskAssignmentModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i>Confirm Delete</h3>
                <button class="close-btn" type="button" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this staff member? This action cannot be undone.</p>
                <p><strong>Staff Member:</strong> <span id="deleteStaffName"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <!-- Password Display Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-key"></i>Staff Account Created Successfully</h3>
                <button class="close-btn" type="button" onclick="closePasswordModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <p>The staff member has been created successfully!</p>
                </div>
                
                <div class="login-info">
                    <h4>Login Information</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Email:</label>
                            <span id="passwordEmail"></span>
                        </div>
                        <div class="info-item">
                            <label>Generated Password:</label>
                            <div class="password-container">
                                <input type="text" id="generatedPassword" readonly>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="copyPassword()" title="Copy Password">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="important-notice">
                    <h4><i class="fas fa-exclamation-triangle"></i> Important</h4>
                    <ul>
                        <li>Please provide this password to the staff member securely</li>
                        <li>The password will not be shown again</li>
                        <li>Staff member should change their password on first login</li>
                        <li>If password is lost, contact the administrator</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="closePasswordModal()">Got it!</button>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div id="editTaskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i>Edit Task</h3>
                <button class="close-btn" type="button" onclick="closeEditTaskModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm">
                    <input type="hidden" id="editTaskId" name="task_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <div class="form-group">
                        <label>Task Title *</label>
                        <input type="text" id="editTaskTitle" name="title" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Task Description</label>
                        <textarea id="editTaskDescription" name="description" rows="4" maxlength="500"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <select id="editTaskPriority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" id="editTaskDueDate" name="due_date">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditTaskModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentPage = 1;
        let currentFilters = {};
        let staffToDelete = null;
        let currentViewStaffId = null;

        // Format date helper function
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadStaffData();
            
            // Set up event listeners
            document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
            document.getElementById('departmentFilter').addEventListener('change', handleFilterChange);
            document.getElementById('statusFilter').addEventListener('change', handleFilterChange);
            document.getElementById('positionFilter').addEventListener('change', handleFilterChange);
            
            // Form submissions
            document.getElementById('createStaffForm').addEventListener('submit', handleCreateStaff);
            document.getElementById('editStaffForm').addEventListener('submit', handleEditStaff);
            document.getElementById('taskAssignmentForm').addEventListener('submit', handleTaskAssignment);
        });

        // Load staff data
        function loadStaffData(page = 1) {
            currentPage = page;
            const loadingIndicator = document.getElementById('loadingIndicator');
            const tableBody = document.getElementById('staffTableBody');
            const noDataMessage = document.getElementById('noDataMessage');
            
            loadingIndicator.style.display = 'block';
            tableBody.innerHTML = '';
            noDataMessage.style.display = 'none';
            
            const formData = new FormData();
            formData.append('action', 'get_staff');
            formData.append('page', page);
            formData.append('limit', 10);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            // Add filters
            Object.keys(currentFilters).forEach(key => {
                if (currentFilters[key]) {
                    formData.append(key, currentFilters[key]);
                }
            });
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                loadingIndicator.style.display = 'none';
                
                if (!data) {
                    console.error('No data received from server');
                    noDataMessage.style.display = 'block';
                    return;
                }

                if (!data.success) {
                    console.error('Server returned error:', data.message);
                    noDataMessage.style.display = 'block';
                    noDataMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i><p>${data.message || 'Failed to load staff data'}</p>`;
                    return;
                }

                if (!data.staff || !Array.isArray(data.staff)) {
                    console.error('Invalid staff data received:', data);
                    noDataMessage.style.display = 'block';
                    return;
                }

                if (data.staff.length === 0) {
                    noDataMessage.style.display = 'block';
                    return;
                }

                displayStaffData(data.staff);
                if (data.pagination) {
                    displayPagination(data.pagination);
                }
            })
            .catch(error => {
                loadingIndicator.style.display = 'none';
                noDataMessage.style.display = 'block';
                noDataMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i><p>Error loading staff data: ${error.message}</p>`;
                console.error('Error:', error);
            });
        }

        // Display staff data in table
        function displayStaffData(staff) {
            const tableBody = document.getElementById('staffTableBody');
            
            staff.forEach(member => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${member.employee_id}</td>
                    <td>${member.name}</td>
                    <td>${member.email}</td>
                    <td>${member.department}</td>
                    <td>${member.position}</td>
                    <td>${formatDate(member.hire_date)}</td>
                    <td>${member.salary ? '$' + parseFloat(member.salary).toLocaleString() : 'N/A'}</td>
                    <td>${member.completed_task_count || 0}/${member.task_count || 0}</td>
                    <td>
                        <span class="status-badge status-${member.status}">
                            ${member.status.charAt(0).toUpperCase() + member.status.slice(1)}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-info" onclick="viewStaff(${member.staff_id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="editStaff(${member.staff_id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="toggleStaffStatus(${member.staff_id}, '${member.status === 'active' ? 'inactive' : 'active'}')" title="${member.status === 'active' ? 'Deactivate' : 'Activate'}">
                                <i class="fas fa-${member.status === 'active' ? 'pause' : 'play'}"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteStaff(${member.staff_id}, '${member.name}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Display pagination
        function displayPagination(pagination) {
            const container = document.getElementById('paginationContainer');
            const { current_page, total_pages, total_records } = pagination;
            
            if (total_pages <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let paginationHTML = `
                <div class="pagination-info">
                    Showing ${((current_page - 1) * 10) + 1} to ${Math.min(current_page * 10, total_records)} of ${total_records} staff members
                </div>
                <div class="pagination-controls">
            `;
            
            // Previous button
            if (current_page > 1) {
                paginationHTML += `<button class="btn btn-sm" onclick="loadStaffData(${current_page - 1})">Previous</button>`;
            }
            
            // Page numbers
            for (let i = Math.max(1, current_page - 2); i <= Math.min(total_pages, current_page + 2); i++) {
                paginationHTML += `
                    <button class="btn btn-sm ${i === current_page ? 'btn-primary' : ''}" onclick="loadStaffData(${i})">
                        ${i}
                    </button>
                `;
            }
            
            // Next button
            if (current_page < total_pages) {
                paginationHTML += `<button class="btn btn-sm" onclick="loadStaffData(${current_page + 1})">Next</button>`;
            }
            
            paginationHTML += '</div>';
            container.innerHTML = paginationHTML;
        }

        // Handle search
        function handleSearch() {
            currentFilters.search = document.getElementById('searchInput').value;
            currentPage = 1;
            loadStaffData();
        }

        // Handle filter changes
        function handleFilterChange() {
            currentFilters.department = document.getElementById('departmentFilter').value;
            currentFilters.status = document.getElementById('statusFilter').value;
            currentFilters.position = document.getElementById('positionFilter').value;
            currentPage = 1;
            loadStaffData();
        }

        // Clear filters
        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('departmentFilter').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('positionFilter').value = '';
            
            currentFilters = {};
            currentPage = 1;
            loadStaffData();
        }

        // Modal functions
        function openCreateStaffModal() {
            document.getElementById('createStaffModal').style.display = 'block';
            document.getElementById('createStaffForm').reset();
            
            // Generate and display employee ID
            const formData = new FormData();
            formData.append('action', 'generate_employee_id');
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('createEmployeeId').value = data.employee_id;
                }
            })
            .catch(error => {
                console.error('Error generating employee ID:', error);
            });
        }

        function closeCreateStaffModal() {
            document.getElementById('createStaffModal').style.display = 'none';
        }

        function openEditStaffModal() {
            document.getElementById('editStaffModal').style.display = 'block';
        }

        function closeEditStaffModal() {
            document.getElementById('editStaffModal').style.display = 'none';
        }

        function openViewStaffModal() {
            document.getElementById('viewStaffModal').style.display = 'block';
        }

        function closeViewStaffModal() {
            document.getElementById('viewStaffModal').style.display = 'none';
            currentViewStaffId = null;
        }

        function openTaskAssignmentModal() {
            document.getElementById('taskAssignmentModal').style.display = 'block';
            document.getElementById('taskAssignmentForm').reset();
            document.getElementById('taskStaffId').value = currentViewStaffId;
        }

        function closeTaskAssignmentModal() {
            document.getElementById('taskAssignmentModal').style.display = 'none';
        }

        function openDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            staffToDelete = null;
        }

        // Password modal functions
        function showPasswordModal(password, email) {
            document.getElementById('passwordEmail').textContent = email;
            document.getElementById('generatedPassword').value = password;
            document.getElementById('passwordModal').style.display = 'block';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        function copyPassword() {
            const passwordInput = document.getElementById('generatedPassword');
            passwordInput.select();
            passwordInput.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                document.execCommand('copy');
                
                // Show visual feedback
                const copyBtn = document.querySelector('.password-container .btn');
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check"></i>';
                copyBtn.classList.add('copy-success');
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.classList.remove('copy-success');
                }, 1000);
                
            } catch (err) {
                console.error('Failed to copy password:', err);
                alert('Failed to copy password. Please select and copy manually.');
            }
        }

        // Staff actions
        function viewStaff(staffId) {
            currentViewStaffId = staffId;
            
            const formData = new FormData();
            formData.append('action', 'get_staff_details');
            formData.append('staff_id', staffId);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const staff = data.staff;
                    
                    // Populate view form
                    document.getElementById('viewEmployeeId').textContent = staff.employee_id;
                    document.getElementById('viewName').textContent = staff.name;
                    document.getElementById('viewEmail').textContent = staff.email;
                    document.getElementById('viewDepartment').textContent = staff.department;
                    document.getElementById('viewPosition').textContent = staff.position;
                    document.getElementById('viewHireDate').textContent = formatDate(staff.hire_date);
                    document.getElementById('viewSalary').textContent = staff.salary ? '$' + parseFloat(staff.salary).toLocaleString() : 'N/A';
                    document.getElementById('viewStatus').textContent = staff.status.charAt(0).toUpperCase() + staff.status.slice(1);
                    document.getElementById('viewPhone').textContent = staff.phone || 'N/A';
                    document.getElementById('viewEmergencyContact').textContent = staff.emergency_contact || 'N/A';
                    document.getElementById('viewAddress').textContent = staff.address || 'N/A';
                    document.getElementById('viewPerformanceRating').textContent = staff.performance_rating ? staff.performance_rating + '/5' : 'N/A';
                    
                    // Load performance data
                    loadStaffPerformance(staffId);
                    
                    // Load assigned tasks
                    loadStaffTasks(staffId);
                    
                    openViewStaffModal();
                } else {
                    alert('Error loading staff details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading staff details');
            });
        }

        function loadStaffPerformance(staffId) {
            const formData = new FormData();
            formData.append('action', 'get_staff_performance');
            formData.append('staff_id', staffId);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const performance = data.performance;
                    document.getElementById('viewTasksCompleted').textContent = performance.tasks_completed || 0;
                    document.getElementById('viewLastActivity').textContent = performance.last_activity || 'N/A';
                }
            })
            .catch(error => {
                console.error('Error loading performance:', error);
            });
        }

        function loadStaffTasks(staffId) {
            const formData = new FormData();
            formData.append('action', 'get_staff_tasks');
            formData.append('staff_id', staffId);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const tasksList = document.getElementById('assignedTasksList');
                
                if (data.success && data.tasks && data.tasks.length > 0) {
                    let tasksHTML = '';
                    data.tasks.forEach(task => {
                        const priorityClass = task.priority === 'high' ? 'priority-high' : 
                                            task.priority === 'medium' ? 'priority-medium' : 'priority-low';
                        const statusClass = task.status === 'completed' ? 'status-completed' : 
                                          task.status === 'in_progress' ? 'status-progress' : 'status-pending';
                        const completedDate = task.completed_date ? new Date(task.completed_date).toLocaleString() : null;
                        tasksHTML += `
                            <div class="task-item ${priorityClass} ${statusClass}">
                                <div class="task-header">
                                    <h5>${task.title}</h5>
                                    <div class="task-meta">
                                        <span class="priority-badge ${priorityClass}">${task.priority}</span>
                                        <span class="status-badge ${statusClass}">${task.status}</span>
                                    </div>
                                </div>
                                <div class="task-description">${task.description || 'No description provided'}</div>
                                <div class="task-footer">
                                    <span class="task-date" style="margin-right: 1.5rem;">Assigned: ${formatDate(task.assigned_date)}</span>
                                    ${task.due_date ? `<span class="task-due" style="margin-right: 1.5rem;">Due: ${formatDate(task.due_date)}</span>` : ''}
                                    ${completedDate ? `<span class="task-completed" style="color:#059669; margin-right: 1.5rem;">Completed: ${completedDate}</span>` : ''}
                                    <button class="btn btn-secondary btn-sm" style="margin-left:1rem;" onclick="openEditTaskModal(${task.task_id}, '${encodeURIComponent(task.title)}', '${encodeURIComponent(task.description || '')}', '${task.priority}', '${task.due_date || ''}')">Edit</button>
                                </div>
                            </div>
                        `;
                    });
                    tasksList.innerHTML = tasksHTML;
                } else {
                    tasksList.innerHTML = '<div class="no-tasks">No tasks assigned to this staff member.</div>';
                }
            })
            .catch(error => {
                console.error('Error loading tasks:', error);
                document.getElementById('assignedTasksList').innerHTML = '<div class="error-loading">Error loading tasks.</div>';
            });
        }

        function editStaff(staffId) {
            currentViewStaffId = staffId;
            
            const formData = new FormData();
            formData.append('action', 'get_staff_details');
            formData.append('staff_id', staffId);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const staff = data.staff;
                    
                    // Populate edit form
                    document.getElementById('editStaffId').value = staff.staff_id;
                    document.getElementById('editEmployeeId').value = staff.employee_id;
                    document.getElementById('editName').value = staff.name;
                    document.getElementById('editEmail').value = staff.email;
                    document.getElementById('editDepartment').value = staff.department;
                    document.getElementById('editPosition').value = staff.position;
                    document.getElementById('editHireDate').value = staff.hire_date;
                    document.getElementById('editSalary').value = staff.salary || '';
                    document.getElementById('editPhone').value = staff.phone || '';
                    document.getElementById('editEmergencyContact').value = staff.emergency_contact || '';
                    document.getElementById('editAddress').value = staff.address || '';
                    document.getElementById('editStatus').value = staff.status;
                    document.getElementById('editPerformanceRating').value = staff.performance_rating || '';
                    
                    openEditStaffModal();
                } else {
                    alert('Error loading staff details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading staff details');
            });
        }

        function toggleStaffStatus(staffId, newStatus) {
            const formData = new FormData();
            formData.append('action', 'update_staff_status');
            formData.append('staff_id', staffId);
            formData.append('status', newStatus);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadStaffData();
                    
                    // Update statistics after status change
                    updateStatistics();
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }

        function deleteStaff(staffId, staffName) {
            staffToDelete = { id: staffId, name: staffName };
            document.getElementById('deleteStaffName').textContent = staffName;
            openDeleteModal();
        }

        function confirmDelete() {
            if (!staffToDelete) return;
            
            const formData = new FormData();
            formData.append('action', 'delete_staff');
            formData.append('staff_id', staffToDelete.id);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDeleteModal();
                    loadStaffData();
                    updateStatistics();
                } else {
                    alert('An error occurred: ' + (data.message || 'Unknown server error'));
                }
            })
            .catch(error => {
                console.error('The delete succeeded on the server, but the response was not valid JSON.', error);
                closeDeleteModal();
                loadStaffData();
                updateStatistics();
            });
        }

        // Form handlers
        function handleCreateStaff(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('action', 'create_staff');
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeCreateStaffModal();
                    loadStaffData();
                    
                    // Update statistics after successful staff creation
                    updateStatistics();
                    
                    // Show password modal if password is provided
                    if (data.password) {
                        showPasswordModal(data.password, formData.get('email'));
                    } else {
                        alert(data.message || 'Staff member created successfully!');
                    }
                } else {
                    alert('Error creating staff: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating staff');
            });
        }

        function handleEditStaff(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('action', 'update_staff');
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditStaffModal();
                    loadStaffData();
                    alert(data.message || 'Staff member updated successfully!');
                } else {
                    alert('Error updating staff: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating staff');
            });
        }

        function handleTaskAssignment(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('action', 'assign_task');
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeTaskAssignmentModal();
                    if (currentViewStaffId) {
                        loadStaffTasks(currentViewStaffId);
                    }
                    loadStaffData();
                    alert('Task assigned successfully!');
                } else {
                    alert('Error assigning task: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error assigning task');
            });
        }

        // Update statistics function
        function updateStatistics() {
            const formData = new FormData();
            formData.append('action', 'get_updated_statistics');
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const stats = data.statistics;
                    
                    // Update Total Staff count
                    const totalStaffElement = document.querySelector('.stat-card:nth-child(1) .stat-info h3');
                    if (totalStaffElement) {
                        totalStaffElement.textContent = stats.total_staff;
                    }
                    
                    // Update Active Staff count
                    const activeStaffElement = document.querySelector('.stat-card:nth-child(2) .stat-info h3');
                    if (activeStaffElement) {
                        const activeCount = stats.status_stats.find(s => s.status === 'active')?.count || 0;
                        activeStaffElement.textContent = activeCount;
                    }
                    
                    // Update Average Salary
                    const avgSalaryElement = document.querySelector('.stat-card:nth-child(3) .stat-info h3');
                    if (avgSalaryElement) {
                        avgSalaryElement.textContent = '$' + parseFloat(stats.avg_salary).toLocaleString();
                    }
                    
                    // Update Recent Hires count
                    const recentHiresElement = document.querySelector('.stat-card:nth-child(4) .stat-info h3');
                    if (recentHiresElement) {
                        recentHiresElement.textContent = stats.recent_hires;
                    }
                }
            })
            .catch(error => {
                console.error('Error updating statistics:', error);
            });
        }

        // Utility functions
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Edit Task Modal functions
        function openEditTaskModal(taskId, title, description, priority, dueDate) {
            document.getElementById('editTaskId').value = taskId;
            document.getElementById('editTaskTitle').value = decodeURIComponent(title);
            document.getElementById('editTaskDescription').value = decodeURIComponent(description);
            document.getElementById('editTaskPriority').value = priority;
            document.getElementById('editTaskDueDate').value = dueDate;
            document.getElementById('editTaskModal').style.display = 'block';
        }

        function closeEditTaskModal() {
            document.getElementById('editTaskModal').style.display = 'none';
        }

        document.getElementById('editTaskForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'update_task');
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditTaskModal();
                    if (currentViewStaffId) loadStaffTasks(currentViewStaffId);
                    loadStaffData();
                    alert('Task updated successfully!');
                } else {
                    alert('Error updating task: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating task:', error);
                alert('Error updating task');
            });
        });
    </script>
    <script src="/agrimarket-erd/v1/components/page_tracking.js"></script>
</body>
</html> 