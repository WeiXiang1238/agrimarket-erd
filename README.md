# AgriMarket ERD

A comprehensive agricultural marketplace management system with role-based access control, inventory management, order processing, and analytics capabilities.

## Getting Started

### Prerequisites

- **Web Server**: Apache/Nginx with PHP support
- **PHP**: Version 7.4 or higher
- **Database**: MySQL 5.7 or higher
- **Git**: For repository cloning

### Installation

#### Step 1: Create Database

Create a new MySQL database named `group_assignment`:

#### Step 2: Import Database Schema

Import the SQL file into your newly created database:
1. Open phpMyAdmin
2. Select the `group_assignment` database
3. Go to "Import" tab
4. Choose the file `SQL/group_assignment.sql`
5. Click "Go" to import

#### Step 3: Configure Database Connection

Update the database connection settings in `Db_Connect.php` if needed:

```php
// Update these values according to your database configuration
$host = 'localhost';
$username = 'your_db_username';
$password = 'your_db_password';
$database = 'group_assignment';
```

#### Step 5: Access the Application

Open your web browser and navigate to:

```
http://localhost/agrimarket-erd/v1/auth/login/
```

## Login Credentials

### Admin Account
- **Email**: `admin1@agrimarket.com`
- **Password**: `123456`
- **Access**: Full system administration, user management, analytics

### Vendor Account
- **Email**: `gold@testbusiness.com`
- **Password**: `123456`
- **Access**: Product management, order processing, inventory tracking

### Customer Account
- **Email**: `c4@gmail.com`
- **Password**: `123123`
- **Access**: Product browsing, shopping cart, order placement

### Staff Account
- **Email**: `driver41@gmail.com`
- **Password**: `123456`
- **Access**: Order fulfillment, inventory updates, customer support
