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
get('/paystack/callback', '/app/controllers/PaystackCallback.php');



//general APIs


//student APIs
get('/student/announcements-data', 'api/student/GetAnnouncements.php', ['auth']);
get('/student/rooms-data', 'api/student/GetAvailableRooms.php', ['auth']);
get('/student/room/$id', 'api/student/GetRoomById.php', ['auth']);
get('/student/billing-data', 'api/student/GetBillings.php', ['auth']);
get('/student/billing/$bill_id', 'api/student/billing/GetBillingById.php', ['auth']);

get('/student/maintenance-data', 'api/student/GetAllMaintenance.php', ['auth']);
get('/student/maintenance/$r_id', 'api/student/GetSpecificMaintenance.php', ['auth']);
get('/student/maintenance/$r_id/response', 'api/student/GetMaintenanceResponse.php', ['auth']);
get('/student/complaint-data', 'api/student/GetComplaints.php', ['auth']);
get('/student/complaint/$c_id', 'api/student/GetComplaintById.php', ['auth']);
get('/student/complaint/$c_id/response', 'api/student/GetComplaintResponse.php', ['auth']);
get('/student/visitors-data', 'api/student/GetVisitorsData.php', ['auth']);
get('/student/visitor/$id', 'api/student/GetVisitorById.php', ['auth']);




//admin APIs
get('/admin/rooms-data', 'api/admin/rooms/GetAllRooms.php', ['auth', 'admin']);
get('/admin/room/$id', 'api/admin/rooms/GetRoomById.php', ['auth', 'admin']);
get('/admin/recent-payments', 'api/admin/GetRecentPayments.php', ['auth', 'admin']);
get('/admin/payment/$id', 'api/admin/GetPaymentById.php', ['auth', 'admin']);
get('/admin/users-data', 'api/admin/GetAllUsers.php', ['auth', 'admin']);
get('/admin/student/$id', 'api/admin/GetStudentById.php', ['auth', 'admin']);
get('/admin/visitors-data', 'api/admin/GetAllVisitors.php', ['auth', 'admin']);
get('/admin/visitor/$id', 'api/admin/GetVisitorById.php', ['auth', 'admin']);
get('/visitor/logs/$id', 'api/admin/GetVisitorLogs.php', ['auth', 'admin']);
get('/admin/maintenance-data', 'api/admin/GetAllMaintenanceRequest.php', ['auth', 'admin']);
get('/admin/maintenance/$r_id', 'api/admin/GetMaintenanceById.php', ['auth', 'admin']);
get('/admin/announcements-data', 'api/admin/announcement/GetAnnouncements.php', ['auth', 'admin']);
get('/admin/announcements/get/$a_id', 'api/admin/announcement/GetAnnouncementById.php', ['auth', 'admin']);
get('/admin/billing-data', 'api/admin/billings/GetBillingData.php', ['auth', 'admin']);
get('/admin/billing/$bill_id', 'api/admin/billings/GetBillingById.php', ['auth', 'admin']);
get('/admin/building-data', 'api/admin/billings/GetBuilding.php', ['auth', 'admin']);
get('/admin/students-data', 'api/admin/billings/GetStudents.php', ['auth', 'admin']);
get('/admin/generate-invoice-pdf', 'api/admin/billings/GeneratePDF.php', ['auth', 'admin']);
get('/admin/email-invoice', 'api/admin/billings/EmailHandler.php', ['auth', 'admin']);
get('/admin/complaints-data', 'api/admin/complaints/GetAllComplaint.php', ['auth', 'admin']);
get('/admin/complaint/$c_id', 'api/admin/complaints/GetComplaintById.php', ['auth', 'admin']);
get('/admin/complaint/$c_id/responses', 'api/admin/complaints/GetResponsesForComplaint.php', ['auth', 'admin']);
get('/admin/analytics-data', 'api/admin/GetAnalyticsData.php', ['auth', 'admin']);




// Admin Routes
get('/admin/dashboard', '/pages/admin/admin_dashboard.php', ['auth', 'admin']);
get('/admin/profile', './pages/admin/profile.php', ['auth', 'admin']);
get('/admin/analytics', 'pages/admin/analytics.php', ['auth', 'admin']);
get('/admin/announcements', 'pages/admin/announcements.php', ['auth', 'admin']);
get('/admin/announcements/create', 'pages/admin/create_announcements.php', ['auth', 'admin']);
get('/admin/announcements/edit/$a_id', 'pages/admin/edit_announcement.php', ['auth', 'admin']);
get('/admin/announcements/fetch-targets/$type', 'api/admin/announcement/fetchTargets.php', ['auth', 'admin']);

get('/admin/billings', 'pages/admin/billings.php', ['auth', 'admin']);
get('/admin/complaints', 'pages/admin/complaints.php', ['auth', 'admin']);
get('/admin/maintenance', 'pages/admin/maintenance.php', ['auth', 'admin']);
get('/admin/rooms', 'pages/admin/rooms.php', ['auth', 'admin']);
get('/admin/users', 'pages/admin/users.php', ['auth', 'admin']);
get('/admin/visitors', 'pages/admin/visitors.php', ['auth', 'admin']);



// Student Routes
get('/student/dashboard', '/pages/student/dashboard.php', ['auth']);
get('/student/profile', '/pages/student/profile.php', ['auth']);
get('/student/complaints', '/pages/student/complaint_form.php', ['auth']);
get('/student/maintenance', '/pages/student/maintenance.php', ['auth']);
get('/student/billing', '/pages/student/billings.php', ['auth']);
get('/student/announcements', '/pages/student/announcement.php', ['auth']);
get('/student/rooms', '/pages/student/rooms.php', ['auth']);
get('/student/visitors', '/pages/student/visitors.php', ['auth']);
get('/student/book-room', '/pages/student/book_room.php', ['auth']);
get('/student/payment-success', '/pages/student/payment-successful.php', ['auth']);
get('/student/payment-failed', '/pages/student/payment-failed.php', ['auth']);

// get('/student/data', '/app/controllers/student.php', ['auth']);

post('/send-contact-form', '/app/controllers/ContactController.php');


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
post('/visitor/edit/$id', '/app/controllers/visitors/EditVisitors.php', ['auth']);
post('/student/profile/update', '/app/controllers/ProfileController.php', ['auth']);
post('/admin/profile/update', '/app/controllers/AdminProfileController.php', ['auth', 'admin']);
post('/complaint/submit', 'api/SubmitComplaint.php', ['auth']);
post('/maintenance/submit', '/api/SubmitMaintenance.php', ['auth']);
post('/announcement/mark-read', 'api/student/MarkAnnouncementAsRead.php', ['auth']);
post('/admin/user/add', 'api/admin/users/AddUser.php', ['auth', 'admin']);
post('/admin/user/update', 'api/admin/UpdateUser.php', ['auth', 'admin']);
post('/admin/user/delete', 'api/admin/users/DeleteUser.php', ['auth', 'admin']);
post('/admin/user/change-role', 'api/admin/ChangeUserRole.php', ['auth', 'admin']);
post('/admin/visitor/$id/approve', 'api/admin/ApproveVisitor.php', ['auth', 'admin']);
post('/admin/visitor/$id/deny', 'api/admin/DenyVisitor.php', ['auth', 'admin']);
post('/admin/visitor/$id/check_in', 'api/admin/CheckInVisitor.php', ['auth', 'admin']);
post('/admin/visitor/$id/check_out', 'api/admin/CheckOutVisitor.php', ['auth', 'admin']);
post('/admin/maintenance/add-response', 'api/admin/AddNewResponse.php', ['auth', 'admin']);
post('/admin/maintenance/update-status', 'api/admin/UpdateMaintenanceStatus.php', ['auth', 'admin']);
post('/admin/announcements/action', 'app/admin/announcements_logic.php', ['admin']);
post("/admin/announcements/create", "pages/admin/create_announcements.php", ['auth', 'admin']);
post('/admin/announcements/edit/$a_id', 'pages/admin/edit_announcement.php', ['auth', 'admin']);
post('/admin/create-invoice', 'api/admin/billings/CreateBilling.php', ['auth', 'admin']);
post('/admin/update-invoice/$billingId', 'api/admin/billings/UpdateBilling.php', ['auth', 'admin']);
post('/admin/delete-invoice/$billingId', 'api/admin/billings/DeleteInvoice.php', ['auth', 'admin']);
post('/admin/billing/send-reminder', 'api/admin/billings/SendBillingReminder.php', ['auth', 'admin']);
post('/admin/billing/record-payment', 'api/admin/billings/RecordPayment.php', ['auth', 'admin']);
post('/admin/complaint/$c_id/status', 'api/admin/complaints/UpdateComplaintStatus.php', ['auth', 'admin']);
post('/admin/complaint/$c_id/response', 'api/admin/complaints/AddComplaintResponse.php', ['auth', 'admin']);
post('/student/billing/$bill_id/pay', 'api/student/billing/InitiatePayment.php', ['auth']);
// post('/api/admin/fetch-targets', 'api/admin/announcement/fetchTargets.php', ['auth', 'admin']);

// Room Management Routes (Admin)
post('/admin/room/add', '/app/controllers/rooms/AddRoom.php', ['auth', 'admin']);
post('/admin/room/update', '/app/controllers/rooms/UpdateRoom.php', ['auth', 'admin']);
post('/admin/room/delete', '/app/controllers/rooms/DeleteRoom.php', ['auth', 'admin']);

// 404 Route
any('/404', 'pages/404.php');
