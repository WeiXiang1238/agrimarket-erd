<?php
session_start();
require_once '../../Db_Connect.php';
require_once '../../services/AuthService.php';
require_once '../../services/PermissionService.php';

// Set page title for tracking
$pageTitle = 'Role & Permission Management - AgriMarket Solutions';

// Include page tracking
require_once '../../includes/page_tracking.php';

$authService = new AuthService();
$permissionService = new PermissionService();

// Check authentication and permissions
$authService->requireAuth();
$currentUser = $authService->getCurrentUser();

// Check if user has permission to manage roles
if (!$authService->hasPermission('manage_system') && !$authService->hasRole('admin')) {
    header('Location: ../dashboard/');
    exit;
}

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_permissions') {
        $roleId = intval($_POST['role_id']);
        $permissionIds = $_POST['permissions'] ?? [];
        
        // Validate permission IDs
        $validPermissionIds = [];
        foreach ($permissionIds as $permissionId) {
            if (is_numeric($permissionId)) {
                $validPermissionIds[] = intval($permissionId);
            }
        }
        
        if ($permissionService->updateRolePermissions($roleId, $validPermissionIds)) {
            $message = 'Role permissions updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to update role permissions. Please try again.';
            $messageType = 'error';
        }
    }
}

// Get data for display
$roles = $permissionService->getRolesWithPermissionCounts();
$permissions = $permissionService->getPermissionsByModule();

// Handle AJAX requests for role details
if (isset($_GET['action']) && $_GET['action'] === 'get_role_details' && isset($_GET['role_id'])) {
    $roleId = intval($_GET['role_id']);
    $roleDetails = $permissionService->getRoleDetails($roleId);
    
    header('Content-Type: application/json');
    echo json_encode($roleDetails);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role & Permission Management - AgriMarket Solutions</title>
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
            $pageTitle = 'Role & Permission Management';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2><i class="fas fa-shield-alt"></i> Role & Permission Management</h2>
                            <p>Manage system roles and their permissions</p>
                        </div>
                    </div>
                </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <div class="roles-grid">
                <div class="roles-list-section">
                    <div class="section-header">
                        <h2><i class="fas fa-users-cog"></i> System Roles</h2>
                        <p>Click on a role to edit its permissions</p>
                    </div>
                    
                    <div class="roles-list">
                        <?php foreach ($roles as $role): ?>
                            <div class="role-card" data-role-id="<?= $role['role_id'] ?>">
                                <div class="role-info">
                                    <h3><?= htmlspecialchars($role['role_name']) ?></h3>
                                    <p><?= htmlspecialchars($role['description'] ?? 'No description') ?></p>
                                    <div class="role-stats">
                                        <span class="permission-count">
                                            <i class="fas fa-key"></i>
                                            <?= $role['permission_count'] ?> permissions
                                        </span>
                                    </div>
                                </div>
                                <div class="role-actions">
                                    <button type="button" class="btn btn-primary edit-role-btn" 
                                            data-role-id="<?= $role['role_id'] ?>">
                                        <i class="fas fa-edit"></i> Edit Permissions
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="permissions-edit-section">
                    <div class="section-header">
                        <h2><i class="fas fa-key"></i> Edit Role Permissions</h2>
                        <p>Select a role from the left to edit its permissions</p>
                    </div>
                    
                    <div id="permission-editor" class="permission-editor" style="display: none;">
                        <form id="permission-form" method="POST">
                            <input type="hidden" name="action" value="update_permissions">
                            <input type="hidden" name="role_id" id="edit-role-id">
                            
                            <div class="role-header">
                                <h3 id="edit-role-name"></h3>
                                <p id="edit-role-description"></p>
                            </div>
                            
                            <div class="permissions-grid">
                                <?php foreach ($permissions as $module => $modulePermissions): ?>
                                    <div class="permission-module">
                                        <h4><i class="fas fa-folder"></i> <?= ucfirst(str_replace('_', ' ', $module)) ?></h4>
                                        <div class="permission-list">
                                            <?php foreach ($modulePermissions as $permission): ?>
                                                <label class="permission-item">
                                                    <input type="checkbox" 
                                                           name="permissions[]" 
                                                           value="<?= $permission['permission_id'] ?>"
                                                           data-permission-id="<?= $permission['permission_id'] ?>">
                                                    <span class="checkmark"></span>
                                                    <div class="permission-info">
                                                        <strong><?= htmlspecialchars($permission['permission_name']) ?></strong>
                                                        <?php if ($permission['description']): ?>
                                                            <small><?= htmlspecialchars($permission['description']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Save Permissions
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancel-edit">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div id="no-role-selected" class="no-selection">
                        <i class="fas fa-hand-pointer"></i>
                        <h3>No Role Selected</h3>
                        <p>Please select a role from the list to edit its permissions.</p>
                    </div>
                </div>
            </div>
            </div>
        </main>
    </div>
    
    <script src="script.js"></script>
    <script src="/agrimarket-erd/v1/components/page_tracking.js"></script>
</body>
</html> 