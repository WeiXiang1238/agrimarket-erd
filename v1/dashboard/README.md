# Unified Dashboard

## Overview
This is the unified dashboard for AgriMarket Solutions that serves all user roles (Admin, Vendor, Staff, Customer) with permission-based visibility controls.

## Features
- **Single Dashboard File**: All roles use the same dashboard interface
- **Permission-Based UI**: Menu items and content are shown/hidden based on user permissions
- **Role-Specific Content**: Dashboard title, stats, and actions adapt to user role
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Modern UI**: Clean, professional interface with smooth animations

## File Structure
```
dashboard/
├── dashboard.php    # Main dashboard file with permission controls
├── dashboard.css    # Unified styles for all roles
└── README.md       # This documentation
```

## Permission System
The dashboard uses the role-permission system to control visibility:

### Admin Permissions
- `manage_users` - User management section
- `manage_vendors` - Vendor management section  
- `manage_staff` - Staff management section
- `manage_system` - System settings
- `view_analytics` - Full analytics dashboard

### Vendor Permissions
- `manage_products` - Product management
- `manage_inventory` - Inventory control
- `manage_orders` - Order processing
- `view_reports` - Sales reports

### Staff Permissions
- `customer_support` - Support ticket system
- `manage_orders` - Order assistance

### Customer Permissions
- `place_orders` - Shopping functionality
- `view_orders` - Order history

## Usage
All authenticated users are automatically redirected to:
```
/agrimarket-erd/v1/dashboard/dashboard.php
```

The dashboard will automatically:
1. Detect the user's role and permissions
2. Show appropriate menu items
3. Display relevant statistics
4. Provide role-specific quick actions

## Benefits
- **Maintainability**: Single codebase for all dashboards
- **Consistency**: Unified user experience across roles
- **Security**: Permission-based access control
- **Efficiency**: Reduced code duplication
- **Scalability**: Easy to add new roles and permissions

## Technical Implementation
- PHP session management for authentication
- PDO database connections for permission queries
- Responsive CSS Grid and Flexbox layouts
- JavaScript for interactive features
- CSRF protection for security
- Fallback permissions for database errors 