# AssetFlow – Full-Stack Asset Management System

AssetFlow is a web-based asset management application developed using **PHP, MySQL, HTML, CSS, and JavaScript**. It helps users organize and track assets, manage categories, monitor warranties, schedule maintenance, and generate reports through a responsive dashboard.

## Project Overview

AssetFlow provides a centralized platform for managing assets and related information. Users can record asset details, assign categories, track asset status, monitor warranty dates, and maintain a history of asset-related activities.

The system supports two roles:

- **Admin:** Manages customers, categories, and assets across the application.
- **Customer:** Manages their own assets and personal profile.

Customer asset ownership is enforced so that customers cannot manage another customer's assets.

## Features

### 1. Authentication and User Management
- User Registration and Login
- Secure Logout Functionality
- Admin and Customer Roles
- Profile Name Update
- Password Change with Current Password Verification
- Login Attempt Tracking and Rate Limiting

### 2. Asset Management
- Add New Assets
- View Asset Details
- Update Existing Assets
- Delete Assets
- Upload Asset Images
- Assign Asset Categories
- Record Asset Type, Purchase Date, Warranty Date, Price, Status, and Description
- Search, Filter, and Paginate Asset Records
- Table and Card Views
- Recently Accessed Assets
- Bulk Actions

### 3. Category Management
- Add Categories
- Update Categories
- Delete Categories
- Organize Assets by Category
- Admin-Only Category Management

### 4. Dashboard and Analytics
- Asset Statistics
- Asset Status Charts
- Category Charts
- Recently Added Assets
- Warranty Alerts
- Asset Health Information
- Estimated Asset Value Indicators

### 5. Maintenance Management
- Schedule Asset Maintenance
- Set Maintenance Due Dates
- Add Maintenance Notes
- Update Maintenance Completion Status
- View Upcoming Maintenance
- Track Overdue Maintenance

### 6. Favorites and Notifications
- Add Assets to Favorites
- Remove Assets from Favorites
- Notification Center
- Warranty Notifications
- Maintenance Alerts

### 7. Reports and Activity Tracking
- Generate Printable Asset Reports
- Export Asset Data as CSV
- Admin Activity Log
- Track User Activities
- Track Important Asset Activities

### 8. User Interface
- Responsive Layout
- Sidebar and Top Navigation
- Dark Mode
- Interactive Dialogs
- CSS-Based Visual Effects

## User Roles

| Functionality | Admin | Customer |
|---|:---:|:---:|
| Register Account | — | Yes |
| Login and Logout | Yes | Yes |
| View Dashboard | Yes | Yes |
| View Assets | All Assets | Own Assets |
| Add Assets | Yes | Yes |
| Edit Assets | All Assets | Own Assets |
| Delete Assets | All Assets | Own Assets |
| Manage Categories | Yes | — |
| Manage Customers | Yes | — |
| View Activity Log | Yes | — |
| Manage Profile | Yes | Yes |
| Manage Favorites | Yes | Yes |
| Asset Maintenance | Based on Access | Based on Access |
| Export Asset Data | Yes | Accessible Records |

## Technology Stack

| Technology | Purpose |
|---|---|
| HTML5 | Web Page Structure |
| CSS3 | Styling and Responsive Design |
| JavaScript | Frontend Interactions |
| PHP | Backend Development |
| MySQL | Database Management |
| MySQLi | Database Connectivity |
| Apache | Local Web Server |
| XAMPP | Local Development Environment |

## Project Structure

```text
AssetFlow/
│
├── index.php
├── login.php
├── signup.php
├── logout.php
├── dashboard.php
├── assets.php
├── asset-view.php
├── categories.php
├── customers.php
├── maintenance.php
├── notifications.php
├── favorite.php
├── profile.php
├── report.php
├── export.php
├── activity.php
│
├── config/
│   └── database.php
│
├── includes/
│   ├── auth.php
│   ├── header.php
│   └── footer.php
│
├── database/
│   └── assetflow.sql
│
├── public/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── app.js
│   └── images/
│       └── products/
│
└── uploads/
```

## Database Structure

The database name is **assetflow_db**.

Database file:

```text
database/assetflow.sql
```

The database includes seven main tables:

| Table | Description |
|---|---|
| users | Stores user accounts, credentials, and roles |
| categories | Stores asset categories |
| assets | Stores asset details and ownership information |
| activity_log | Records user and asset-related activities |
| login_attempts | Tracks login attempts |
| favorites | Stores users' favorite assets |
| maintenance | Stores asset maintenance schedules and completion status |

The database uses primary keys, unique constraints, and foreign keys to maintain data relationships.

## Installation and Setup

### Prerequisites

- Windows Operating System
- XAMPP
- PHP with MySQLi Extension
- MySQL or MariaDB
- Modern Web Browser
- Code Editor (VS Code recommended)

### Step 1: Clone the Repository

```bash
git clone YOUR_GITHUB_REPOSITORY_URL
```

Alternatively, download the repository as a ZIP file and extract it.

### Step 2: Move Project to XAMPP

Copy the AssetFlow folder into:

```text
C:\xampp\htdocs\AssetFlow
```

### Step 3: Start XAMPP

1. Open XAMPP Control Panel.
2. Start Apache.
3. Start MySQL.

### Step 4: Import Database

1. Open your browser.
2. Visit `http://localhost/phpmyadmin`.
3. Select the Import option.
4. Choose `database/assetflow.sql`.
5. Click Import or Go.
6. Confirm that the `assetflow_db` database and its tables have been created.

### Step 5: Configure Database Connection

Open:

```text
config/database.php
```

The default XAMPP connection is:

```php
$conn = new mysqli('localhost', 'root', '', 'assetflow_db');
```

Update the database host, username, password, or database name if your local configuration is different.

### Step 6: Run the Application

Open your browser and visit:

```text
http://localhost/AssetFlow
```

You can register a customer account or log in using the default admin account.

## Default Admin Account

On the first application run, the application creates a default administrator account if an admin account does not already exist.

| Field | Default Value |
|---|---|
| Email | admin@assetflow.com |
| Password | Admin@123 |
| Role | Admin |

**Security Note:** Change the default administrator password immediately after your first login. Never use default credentials on a public or production server.

## Application Pages

| File | Description |
|---|---|
| index.php | Application Entry Page |
| login.php | User Login |
| signup.php | Customer Registration |
| logout.php | Logout |
| dashboard.php | Dashboard and Asset Overview |
| assets.php | Asset Listing and Management |
| asset-view.php | Individual Asset Details |
| categories.php | Category Management |
| customers.php | Admin Customer Management |
| maintenance.php | Maintenance Scheduling and Tracking |
| notifications.php | Warranty and Maintenance Notifications |
| favorite.php | Favorite Asset Management |
| profile.php | Profile and Password Management |
| report.php | Printable Asset Reports |
| export.php | CSV Export |
| activity.php | Admin Activity Log |

## Security and Validation

The application includes several security-related features:

- Password Hashing
- Prepared SQL Statements
- Session-Based Authentication
- Role-Based Access Control
- Customer Asset Ownership Verification
- CSRF Token Validation for Supported POST Requests
- HTML Output Escaping
- Image Upload Validation
- Login Attempt Tracking

This is an academic/development project. Additional security review is recommended before production deployment.

## Future Improvements

- PDF and Excel Report Export
- QR Code Generation for Assets
- Advanced Dashboard Analytics
- Improved Image Upload Security
- Enhanced Input Validation
- Automated Database Backup
- Advanced Audit Logging
- Improved Production Configuration
- Additional Reporting Features

## Author

**MD Shakib Hasan Shafin**

Computer Science & Engineering Student

**Project:** AssetFlow – Full-Stack Asset Management System

## Acknowledgement

Thank you for exploring AssetFlow – Full-Stack Asset Management System.

If you find this project useful, consider giving the repository a star ⭐.

---

**Built with PHP, MySQL, HTML5, CSS3, and JavaScript.**
