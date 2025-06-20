# AgriMarket Components

This directory contains reusable UI components for the AgriMarket Solutions platform.

## Architecture Overview

The application follows a strict **Service Layer Architecture** pattern:

### 📁 Directory Structure
```
v1/
├── components/          # Reusable UI components
│   ├── header.php      # Navigation header
│   ├── sidebar.php     # Navigation sidebar  
│   └── README.md       # This file
├── dashboard/          # Main dashboard
├── user-management/    # User management module
├── auth/              # Authentication pages
└── ...                # Other modules
```

### 🏗️ Service Layer Pattern

**✅ CORRECT Architecture:**
- **UI Components** (`header.php`, `sidebar.php`) → **NO** database queries
- **Page Controllers** (`dashboard/index.php`, `user-management/index.php`) → **NO** direct database access
- **Services** (`/services/`) → **ALL** database operations and business logic

**❌ INCORRECT (Old Pattern):**
- UI components with embedded SQL queries
- Page files with direct PDO connections
- Mixed presentation and data access logic

### 🔧 Available Services

#### PermissionService
Handles all permission-related operations:
```php
$permissionService = new PermissionService();

// Get user permissions
$permissions = $permissionService->getEffectivePermissions($user);

// Check specific permission
$canManage = $permissionService->hasPermission($user, 'manage_users');

// Check multiple permissions
$hasAny = $permissionService->hasAnyPermission($user, ['manage_users', 'manage_vendors']);
```

#### AuthService
Handles authentication and authorization:
```php
$authService = new AuthService();

// Require authentication
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');

// Check permissions
$authService->requirePermission('manage_users');

// Get current user
$currentUser = $authService->getCurrentUser();
```

#### UserService
Handles all user-related operations:
```php
$userService = new UserService();

// Get paginated users
$result = $userService->getPaginatedUsers($page, $limit, $filters);

// Create user
$result = $userService->createUser($userData);

// Get user statistics
$stats = $userService->getUserStatistics();
```

## Component Usage

### Header Component
```php
<?php 
$pageTitle = "Dashboard";
include '../components/header.php'; 
?>
```

### Sidebar Component
```php
<?php 
// Ensure $currentUser is available
$currentUser = $authService->getCurrentUser();
include '../components/sidebar.php'; 
?>
```

**Important:** Both components require `$currentUser` to be available in scope for permission checking.

## Permission System

### How Permissions Work

1. **Database First**: Components try to get permissions from the role-based permission system
2. **Fallback**: If database permissions aren't available, fallback to role-based permissions
3. **UI Control**: Permissions control what navigation items and features are visible

### Permission Categories

- **Admin**: `manage_users`, `manage_vendors`, `manage_system`, `view_analytics`
- **Vendor**: `manage_products`, `manage_orders`, `manage_inventory`, `view_reports`  
- **Staff**: `customer_support`, `manage_orders`
- **Customer**: `place_orders`, `view_orders`

### Adding New Permissions

1. Add permission to database via migration
2. Update `PermissionService::getFallbackPermissions()` method
3. Use permission in UI components:
   ```php
   <?php if (hasSidebarPermission('new_permission')): ?>
   <li><a href="/new-feature/">New Feature</a></li>
   <?php endif; ?>
   ```

## Best Practices

### ✅ DO
- Use services for all database operations
- Keep components focused on presentation
- Use permission checks for UI visibility
- Follow the established naming conventions
- Include proper error handling

### ❌ DON'T
- Add database queries to components
- Mix business logic with presentation
- Hardcode permissions or roles
- Skip permission checks
- Ignore service layer patterns

## Development Workflow

1. **Create Service**: Add business logic to appropriate service
2. **Update Component**: Use service methods in UI components
3. **Add Permissions**: Define and check appropriate permissions
4. **Test**: Verify functionality across different user roles

## Error Handling

Components gracefully handle service errors:
- Permission service failures → fallback to role-based permissions
- Database connection issues → show appropriate fallback UI
- Missing user data → redirect to login or show error state

## Future Enhancements

- Cache permission results for better performance
- Add more granular permission controls
- Implement permission inheritance
- Add audit logging for permission changes

## Components

### 1. Header Component (`header.php`)

A reusable header component that provides consistent navigation and user interface across all dashboard pages.

**Features:**
- Responsive sidebar toggle button
- Dynamic page title
- Notification bell (with badge)
- User dropdown menu with profile options
- Built-in JavaScript for user interactions
- Self-contained CSS styling

**Usage:**
```php
<?php
// Set the page title (optional, defaults to 'Dashboard')
$pageTitle = 'Your Page Title';

// Include the header component
include '../components/header.php';
?>
```

**Required Variables:**
- `$currentUser`: Array containing user data (name, role, etc.)
- `$pageTitle`: String for the page title (optional)

**JavaScript Features:**
- Sidebar toggle functionality (mobile/desktop)
- User dropdown toggle
- Click outside to close dropdown
- Mobile overlay handling

### 2. Sidebar Component (`sidebar.php`)

A dynamic sidebar navigation component with role-based permissions and active state management.

**Features:**
- Role-based navigation items
- Permission-based visibility
- Active state highlighting
- Mobile responsive design
- Database-driven permissions (with fallback)
- Self-contained CSS styling

**Usage:**
```php
<?php
// Include the sidebar component
include '../components/sidebar.php';
?>
```

**Required Variables:**
- `$currentUser`: Array containing user data (user_id, role, etc.)
- Database connection variables: `$host`, `$dbname`, `$user`, `$pass`

**Permission System:**
The sidebar automatically detects user permissions from the database and shows/hides navigation items accordingly. It includes fallback logic based on user roles.

**Navigation Items:**
- Dashboard (all users)
- User Management (admin only)
- Vendor Management (admin only)
- Staff Management (admin only)
- Product Management (admin/vendor)
- Inventory (vendor only)
- Order Management (admin/staff/vendor)
- Shopping (customer only)
- Analytics/Reports (admin/vendor)
- Customer Support (staff only)
- Promotions (admin only)
- System Settings (admin only)

## File Structure

```
v1/components/
├── README.md          # This documentation file
├── header.php         # Shared header component
├── header.css         # Header component styles
├── sidebar.php        # Shared sidebar component
└── sidebar.css        # Sidebar component styles
```

## Integration Example

Here's how to integrate both components in a new page:

```php
<?php
// Your page logic here
require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';

$authService = new AuthService();
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');
$currentUser = $authService->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../dashboard/style.css">
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
            $pageTitle = 'Your Page Title';
            include '../components/header.php'; 
            ?>
            
            <!-- Your Page Content -->
            <div class="dashboard-content">
                <!-- Your content here -->
            </div>
        </main>
    </div>
</body>
</html>
```

## Notes

- Both components include their own JavaScript functionality and CSS styling
- The sidebar component includes the mobile overlay div
- Components are completely self-contained and don't depend on external CSS
- Permission checking is built into the sidebar component
- Components handle responsive design automatically
- All components include proper error handling and fallbacks
- CSS files are automatically included when components are used
- No need to manually include component CSS files in your pages

## Module Naming Convention

All modules in the v1 directory follow a consistent naming convention:
- **PHP Files**: `index.php` (main entry point for each module)
- **CSS Files**: `style.css` (module-specific styling)
- **Directory Structure**: `/v1/{module-name}/` (clean URLs without file extensions)

Examples:
- Dashboard: `/agrimarket-erd/v1/dashboard/`
- User Management: `/agrimarket-erd/v1/user-management/`
- Login: `/agrimarket-erd/v1/auth/login/`
- Register: `/agrimarket-erd/v1/auth/register/` 