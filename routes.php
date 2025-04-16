<?php

require_once __DIR__ . '/router.php';

// Static GET Routes
get('/', '/pages/index.php');

// Authentication Routes
get('/signup', '/pages/auth/signup.php', ['guest']);
get('/login', '/pages/auth/login.php', ['guest']);
get('/forgot-password', '/pages/auth/forgotPassword.php', ['guest']);
get('/verify-email', './pages/auth/verify_email.php', ['guest']);
get('/email-verified', '/pages/auth/email_verified.php', ['guest']);
get('/logout', '/app/controllers/logout.php', ['auth']);
get('/reset-password', '/pages/auth/reset_password.php', ['guest']);
get('/reset-password/$tkn', '/app/controllers/ResetPassword.php', ['guest']);

// General Routes
get('/layout', '/pages/layout.php');
get('/visitors-data', '/app/models/visitors_data.php', ['auth']);
get('/room-data', '/app/controllers/rooms/GetAvailableRooms.php', ['auth']);
get('/visitor/view/$id', '/app/controllers/visitors/GetVisitor.php', ['auth']);
get('/visitor/edit/$id', '/app/controllers/visitors/EditVisitors.php', ['auth']);
get('/announcements', '/app/controllers/announcement.php', ['auth']);
// Admin Routes
get('/admin/dashboard', '/pages/admin/admin_dashboard.php', ['auth', 'admin']);
get('/admin/room-data', '/app/controllers/rooms/GetAllRooms.php', ['auth', 'admin']);

// Student Routes
get('/student/dashboard', '/pages/student/dashboard.php', ['auth']);
get('/student/profile', '/pages/student/profile.php', ['auth']);
get('/student/complaints', '/pages/student/complaint_form.php', ['auth']);
get('/student/maintenance', '/pages/student/maintenance.php', ['auth']);
get('/student/billing', '/pages/student/billings.php', ['auth']);
get('/student/announcements', '/pages/student/announcement.php', ['auth']);
get('/student/rooms', '/pages/student/rooms.php', ['auth']);
get('/student/room/$id', '/pages/student/room_details.php', ['auth']);
get('/student/visitors', '/pages/student/visitors.php', ['auth']);
get('/student/book-room', '/pages/student/book_room.php', ['auth']);

get('/student/data', '/app/controllers/student.php', ['auth']);
get('/student/billing-data', '/app/controllers/GetBillings.php', ['auth']);


// POST Routes
post('/signup', '/app/controllers/Signup.php');
post('/login', '/app/controllers/Login.php');
post('/verify-email', '/pages/auth/verify_email.php');
post('/forgot-password', '/app/controllers/ForgotPassword.php');
post('/reset-password/$tkn', '/app/controllers/ResetPassword.php');
post('/student/room/book/$id', '/app/controllers/rooms/BookRoom.php', ['auth']);
post('/visitor/delete/$id', '/app/controllers/visitors/DeleteVisitor.php', ['auth']);
post('/visitor/cancel/$id', './app/controllers/visitors/CancelVisitor.php', ['auth']);
post('/visitor/register', '/app/controllers/visitors/RegisterVisitor.php', ['auth']);

// Room Management Routes (Admin)
post('/admin/room/add', '/app/controllers/rooms/AddRoom.php', ['auth', 'admin']);
post('/admin/room/update', '/app/controllers/rooms/UpdateRoom.php', ['auth', 'admin']);
post('/admin/room/delete', '/app/controllers/rooms/DeleteRoom.php', ['auth', 'admin']);

// 404 Route
any('/404', 'pages/404.php');
