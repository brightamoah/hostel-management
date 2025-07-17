# Hostel Management System

A comprehensive web-based hostel management system built with PHP and MySQL,
designed to streamline hostel operations for both administrators and students.

## Features

### For Students

-  **Room Management**: Browse available rooms, view room details, and book
   rooms
-  **Maintenance Requests**: Submit and track maintenance requests for room
   issues
-  **Complaint System**: File complaints and track their resolution status
-  **Visitor Management**: Register visitors and manage visitor requests
-  **Billing**: View billing information and payment history
-  **Announcements**: Receive and view important hostel announcements
-  **Profile Management**: Update personal information and account settings

### For Administrators

-  **Dashboard**: Overview of occupancy rates, recent bookings, and system
   statistics
-  **Room Management**: Add, edit, and manage room inventory with occupancy
   tracking
-  **User Management**: Manage student and admin accounts with role-based access
-  **Maintenance**: Review and respond to maintenance requests
-  **Billing Management**: Create invoices, track payments, and send reminders
-  **Visitor Management**: Approve/deny visitor requests and manage visitor logs
-  **Announcements**: Create and manage hostel-wide announcements
-  **Analytics**: Generate reports and view system analytics

## Technology Stack

-  **Backend**: PHP 8.0+
-  **Database**: MySQL 8.0+
-  **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
-  **Additional Libraries**:
   -  PHPMailer for email functionality
   -  DOMPDF for PDF generation
   -  DataTables for enhanced table functionality
   -  SweetAlert2 for notifications
   -  Select2 for enhanced dropdowns

## Prerequisites

-  XAMPP (includes Apache and MySQL)
-  PHP 8.0 or higher
-  MySQL 8.0 or higher
-  Composer (for dependency management)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/brightamoah/hostel-management.git
cd hostel-management
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Set Up XAMPP

1. Install XAMPP from
   [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start Apache and MySQL services from XAMPP Control Panel
3. Place the project files in your XAMPP's `htdocs` directory

### 4. Database Setup

1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Create a new database named `hostel_management`
3. Import the database schema:
   -  Navigate to the `database` folder in the project
   -  Import `hostel_management.sql` file into your database

### 5. Configuration

1. Update database connection settings in `database/db.php`:
   ```php
   private $db_host = 'localhost';
   private $db_user = 'root';
   private $db_name = 'hostel_management';
   private $db_password = '';
   ```

### 6. Access the Application

-  Open your browser and navigate to: `http://localhost/hostel-management`
-  Default admin credentials will be created during database setup

## Project Structure

```
├── api/                    # API endpoints
│   ├── student/           # Student-specific APIs
│   └── admin/             # Admin-specific APIs
├── app/                   # Application logic
│   ├── controllers/       # Controllers
│   ├── models/           # Database models
│   └── admin/            # Admin-specific logic
├── assets/               # Static assets
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   └── img/             # Images
├── Components/           # Reusable components
│   ├── admin/           # Admin components
│   ├── auth/            # Authentication components
│   └── home/            # Home page components
├── config/              # Configuration files
├── database/            # Database files
├── pages/               # Application pages
│   ├── admin/           # Admin pages
│   ├── auth/            # Authentication pages
│   ├── home/            # Home pages
│   └── student/         # Student pages
├── services/            # Service classes
├── utils/               # Utility functions
└── vendor/              # Composer dependencies
```

## Usage

### For Students

1. **Registration**: Create a new account with student credentials
2. **Room Booking**: Browse available rooms and book your preferred room
3. **Maintenance**: Submit maintenance requests for any room issues
4. **Complaints**: File complaints about hostel services or facilities
5. **Visitors**: Register visitors in advance for approval
6. **Billing**: View and track your payment history

### For Administrators

1. **Dashboard**: Monitor overall hostel statistics and recent activities
2. **Room Management**: Add new rooms, update room details, and track occupancy
3. **User Management**: Manage student accounts and assign roles
4. **Maintenance**: Review and respond to maintenance requests
5. **Billing**: Generate invoices and track payments
6. **Announcements**: Create important announcements for residents

## API Endpoints

### Student APIs

-  `GET /student/rooms-data` - Get available rooms
-  `POST /student/room/book/{id}` - Book a room
-  `GET /student/maintenance-data` - Get maintenance requests
-  `POST /maintenance/submit` - Submit maintenance request
-  `GET /student/visitors-data` - Get visitor information
-  `POST /visitor/register` - Register a visitor

### Admin APIs

-  `GET /admin/rooms-data` - Get all rooms
-  `POST /admin/room/add` - Add new room
-  `GET /admin/users-data` - Get all users
-  `POST /admin/user/add` - Add new user
-  `GET /admin/maintenance-data` - Get all maintenance requests
-  `POST /admin/maintenance/update-status` - Update maintenance status

## Security Features

-  **Authentication**: Session-based user authentication
-  **Authorization**: Role-based access control (Student/Admin)
-  **CSRF Protection**: Cross-site request forgery protection
-  **Input Validation**: Server-side input validation and sanitization
-  **Password Security**: Secure password hashing

## Email Configuration

To enable email functionality (for notifications and invoices):

1. Update email settings in `services/EmailService.php`
2. Configure SMTP settings for your email provider
3. Set up email templates in the appropriate service files

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## Troubleshooting

### Common Issues

1. **Database Connection Error**

   -  Verify MySQL service is running in XAMPP
   -  Check database credentials in `database/db.php`
   -  Ensure database exists and is properly imported

2. **Permission Errors**

   -  Check file permissions in the project directory
   -  Ensure Apache has read/write access to the project folder

3. **Email Not Working**

   -  Configure SMTP settings in `services/EmailService.php`
   -  Check email server configuration

4. **CSS/JS Not Loading**
   -  Verify the `assets` folder is accessible
   -  Check file paths in HTML templates

## Support

For support and questions, please contact:

-  Email: kingshostelmgt@gmail.com
-  Phone: +233 54 968 4848
