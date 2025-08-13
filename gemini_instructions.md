# Hostel Management System - Development Instructions

## System Overview

This is a comprehensive web-based hostel management system built with PHP 8.0+,
MySQL 8.0+, and Bootstrap 5. The system serves two primary user roles:
**Students** and **Administrators**, with distinct functionalities for each
role.

## Architecture & Technology Stack

### Backend Technologies

-  **PHP 8.0+** - Server-side scripting
-  **MySQL 8.0+** - Database management
-  **Composer** - Dependency management

### Frontend Technologies

-  **HTML5, CSS3, JavaScript**
-  **Bootstrap 5** - UI framework
-  **DataTables** - Enhanced table functionality
-  **SweetAlert2** - Notifications and alerts
-  **Select2** - Enhanced dropdown components

### Additional Libraries

-  **PHPMailer** - Email functionality
-  **DOMPDF** - PDF generation
-  **Custom Router** - URL routing system

## Database Schema

### Core Tables

1. **users** - Base user accounts (user_id, name, email, password, role,
   is_email_verified, last_login)
2. **students** - Student-specific data (student_id, user_id, first_name,
   last_name, gender, date_of_birth, phone_number, address,
   emergency_contact_name, emergency_contact_number, health_condition,
   enrollment_date, resident_status)
3. **admins** - Admin-specific data (admin_id, user_id, access_level)
4. **rooms** - Room inventory (room_id, room_number, building, floor, room_type,
   capacity, current_occupancy, features, amount, status)
5. **allocations** - Room assignments (allocation_id, student_id, room_id,
   start_date, end_date, status)
6. **billing** - Financial records (billing_id, student_id, allocation_id,
   amount, description, date_issued, date_due, status, billing_type,
   academic_period, payment_terms, paid_amount)
7. **payments** - Payment transactions (payment_id, student_id, amount,
   transaction_reference, payment_method, purpose, status, payment_date,
   payment_notes)
8. **maintenance_requests** - Maintenance tracking (request_id, student_id,
   room_id, issue_type, description, priority, status, request_date,
   resolved_date)
9. **maintenance_responses** - Admin responses to maintenance (response_id,
   request_id, user_id, response_text, response_date)
10.   **complaints** - Student complaints (complaint_id, student_id, room_id,
      complaint_type, description, priority, status, submitted_at, resolved_at)
11.   **complaint_responses** - Admin complaint responses (response_id,
      complaint_id, admin_id, response_text, response_date)
12.   **visitors** - Visitor management (visitor_id, student_id, visitor_name,
      relation, phone_number, visit_date, purpose, status, registered_at)
13.   **visitor_logs** - Check-in/out tracking (log_id, visitor_id,
      check_in_time, check_out_time)
14.   **announcements** - System announcements (announcement_id, title, content,
      priority, target_audience, created_by, created_at, is_active)

## Project Structure

```
├── api/                    # API endpoints for AJAX calls
│   ├── student/           # Student-specific API endpoints
│   ├── admin/             # Admin-specific API endpoints
│   └── *.php              # General API endpoints
├── app/                   # Core application logic
│   ├── controllers/       # Request handlers and business logic
│   ├── models/           # Database interaction classes
│   └── admin/            # Admin-specific logic and data processing
├── assets/               # Static resources
│   ├── css/             # Stylesheets and themes
│   ├── js/              # JavaScript functionality
│   ├── img/             # Images and icons
│   └── vendor/          # Third-party frontend libraries
├── Components/           # Reusable UI components
│   ├── admin/           # Admin interface components
│   ├── auth/            # Authentication forms
│   └── home/            # Public-facing components
├── config/              # Configuration files
├── database/            # Database setup and connection
├── pages/               # Main application views
│   ├── admin/           # Administrator dashboard and pages
│   ├── auth/            # Login, signup, password reset
│   ├── home/            # Landing pages
│   └── student/         # Student dashboard and pages
├── services/            # Service classes (Email, PDF generation)
├── utils/               # Utility functions and helpers
└── vendor/              # Composer dependencies
```

## User Roles & Permissions

### Student Role Capabilities

-  **Room Management**: Browse and book available rooms
-  **Maintenance Requests**: Submit and track maintenance issues
-  **Complaint System**: File and monitor complaint resolution
-  **Visitor Management**: Register visitors for approval
-  **Billing**: View invoices and payment history
-  **Announcements**: View hostel announcements
-  **Profile**: Update personal information

### Administrator Role Capabilities

-  **Dashboard**: System overview and statistics
-  **Room Management**: CRUD operations on room inventory
-  **User Management**: Manage student and admin accounts
-  **Maintenance**: Review, respond to, and update maintenance requests
-  **Billing Management**: Create invoices, track payments
-  **Visitor Management**: Approve/deny visitor requests, manage logs
-  **Announcements**: Create and manage system announcements
-  **Analytics**: Generate reports and view system metrics

## Authentication & Security

### Session Management

-  Session-based authentication with role verification
-  Automatic session timeout and cleanup
-  Secure session data storage

### Security Features

-  **CSRF Protection**: Token-based protection against cross-site request
   forgery
-  **Input Validation**: Server-side validation and sanitization
-  **Password Security**: Bcrypt hashing for password storage
-  **SQL Injection Prevention**: Prepared statements throughout
-  **Role-Based Access Control**: Middleware-based permission checking

### Middleware System

-  `auth` - Requires authenticated user
-  `guest` - Requires unauthenticated user
-  Role-specific route protection

## API Design Patterns

### RESTful Endpoints

-  **Student APIs**: `/student/*` - Student-specific operations
-  **Admin APIs**: `/admin/*` - Administrative operations
-  **General APIs**: Root-level endpoints for shared functionality

### Response Format

```php
// Success Response
['success' => true, 'data' => $data, 'message' => 'Operation successful']

// Error Response
['success' => false, 'error' => 'Error message', 'details' => $details]

// DataTable Response
['data' => $records, 'recordsTotal' => $total, 'recordsFiltered' => $filtered]
```

## Database Connection Pattern

### Connection Management

```php
// Using Database class
$db = new Database();
$conn = $db->connect();

// Using getDb() helper function
$conn = getDb();
```

### Model Architecture

```php
class ModelName {
    private $conn;

    public function __construct() {

        $this->conn = $this->conn = getDb();
    }
}
```

## Common Development Patterns

### Controller Structure

```php
class ControllerName {
    private $model;

    public function __construct() {
        $this->model = new ModelName();
    }

    public function methodName() {
        // Authentication check
        if (!isset($_SESSION['user'])) {
            // Handle unauthorized access
        }

        // Business logic
        // Return response
    }
}
```

### Error Handling

```php
try {
    // Database operations
    $result = $stmt->execute();
    if (!$result) {
        throw new Exception("Operation failed");
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    return ['success' => false, 'error' => $e->getMessage()];
}
```

## Frontend Development Guidelines

### DataTable Integration

-  Use server-side processing for large datasets
-  Implement search, sort, and pagination
-  Include action buttons for record management

### Form Validation

-  Client-side validation with JavaScript
-  Server-side validation in controllers
-  Real-time feedback for user inputs

### AJAX Communication

```javascript
$.ajax({
   url: "/api/endpoint",
   method: "POST",
   data: formData,
   success: function (response) {
      if (response.success) {
         // Handle success
      } else {
         // Handle error
      }
   },
});
```

## Development Best Practices

### Code Organization

1. **Separation of Concerns**: Models handle data, Controllers handle logic,
   Views handle presentation
2. **DRY Principle**: Reuse common functionality through utility functions
3. **Consistent Naming**: Use camelCase for variables, PascalCase for classes
4. **Error Logging**: Log errors for debugging and monitoring

### Database Best Practices

1. **Prepared Statements**: Always use prepared statements for SQL queries
2. **Transaction Management**: Use transactions for multi-table operations
3. **Index Optimization**: Ensure proper indexing for performance
4. **Data Validation**: Validate data types and constraints

### Security Considerations

1. **Input Sanitization**: Sanitize all user inputs
2. **Output Encoding**: Encode output to prevent XSS
3. **File Upload Security**: Validate file types and sizes
4. **Access Control**: Verify user permissions for all operations

## Testing Guidelines

### Manual Testing Checklist

1. **Authentication**: Login/logout functionality
2. **Authorization**: Role-based access control
3. **CRUD Operations**: Create, read, update, delete for all entities
4. **Form Validation**: Client and server-side validation
5. **Error Handling**: Graceful error handling and user feedback

### Performance Considerations

1. **Database Queries**: Optimize query performance
2. **Caching**: Implement appropriate caching strategies
3. **Asset Optimization**: Minify CSS/JS files
4. **Image Optimization**: Compress images for web delivery

## Deployment Guidelines

### Environment Setup

1. **XAMPP Configuration**: Apache and MySQL setup
2. **PHP Configuration**: Enable required extensions
3. **Database Import**: Import schema and seed data
4. **File Permissions**: Set appropriate file/folder permissions

### Production Considerations

1. **Error Reporting**: Disable debug mode in production
2. **HTTPS Configuration**: Enable SSL/TLS encryption
3. **Database Security**: Secure database credentials
4. **Backup Strategy**: Implement regular backup procedures

## Maintenance & Updates

### Regular Tasks

1. **Database Maintenance**: Regular cleanup and optimization
2. **Security Updates**: Keep dependencies updated
3. **Log Monitoring**: Monitor error logs and system performance
4. **User Support**: Respond to user issues and feature requests

### Feature Development

1. **Requirements Analysis**: Understand business requirements
2. **Database Design**: Plan schema changes if needed
3. **Implementation**: Follow established patterns and conventions
4. **Testing**: Thorough testing before deployment

This system instruction serves as a comprehensive guide for developers working
on the hostel management system, ensuring consistency, security, and
maintainability throughout the development process.
