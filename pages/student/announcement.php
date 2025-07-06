<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-navbar-sticky layout-menu-fixed layout-menu-collapsed layout-compact" dir="ltr" data-skin="default" data-assets-path="../../assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Kings Hostel - Announcements</title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/favicon_io/favicon-16x16.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/fontawesome.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>

    <style>
        .announcement-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        /* .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        } */

        .announcement-card.unread {
            border-left-color: #7367f0;
            box-shadow: 0 2px 8px rgba(115, 103, 240, 0.1);
        }

        .announcement-card.read {
            opacity: 0.8;
            background-color: #f8f8f8;
            border-left-color: #d0d0d0;
        }

        .priority-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .priority-high {
            background-color: #ea5455;
        }

        .priority-urgent {
            background-color: #ff3e1d;
            animation: pulse 2s infinite;
        }

        .priority-medium {
            background-color: #ff9f43;
        }

        .priority-low {
            background-color: #28c76f;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 62, 29, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 62, 29, 0);
            }
        }

        .announcement-filter-btn.active {
            background-color: #7367f0 !important;
            color: white !important;
        }

        .announcement-date {
            font-size: 0.85rem;
        }

        .mark-read-btn {
            opacity: 0;
            transition: all 0.3s ease;
        }

        .announcement-card:hover .mark-read-btn {
            opacity: 1;
        }

        /* Section separator styling */
        .section-separator {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Enhanced badge for new announcements */
        .badge.bg-primary {
            font-size: 0.7rem;
            padding: 0.25em 0.5em;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include_once __DIR__ . "/../../Components/sidebar.php" ?>

            <div class="menu-mobile-toggler d-xl-none rounded-1">
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                    <i class="bx bx-menu icon-base"></i>
                    <i class="bx bx-chevron-right icon-base"></i>
                </a>
            </div>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php include_once __DIR__ . "/../../Components/header.php" ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <!-- Announcements Header -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div>
                                        <h4 class="mb-1">Announcements</h4>
                                        <p class="mb-0 text-muted" id="announcement-count">Loading announcements...</p>
                                    </div>
                                    <div class="btn-group" role="group" aria-label="Filter announcements">
                                        <button type="button" class="btn btn-outline-primary announcement-filter-btn active" data-filter="unread">Unread</button>
                                        <button type="button" class="btn btn-outline-primary announcement-filter-btn" data-filter="all">All</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Priority Legend -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex gap-3 flex-wrap">
                                    <div class="d-flex align-items-center">
                                        <span class="priority-indicator priority-urgent me-1"></span>
                                        <small>Urgent</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="priority-indicator priority-high me-1"></span>
                                        <small>High</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="priority-indicator priority-medium me-1"></span>
                                        <small>Medium</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="priority-indicator priority-low me-1"></span>
                                        <small>Low</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Announcements Cards -->
                        <div class="row" id="announcements-container">
                            <!-- Announcements will be loaded here dynamically -->
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading announcements...</p>
                                </div>
                            </div>
                        </div>

                        <!-- No Announcements Placeholder -->
                        <div class="card mt-3 d-none" id="no-announcements">
                            <div class="card-body text-center py-5">
                                <img src="../../assets/img/illustrations/no_call.png" alt="No announcements" class="img-fluid mb-3" style="max-width: 200px;">
                                <h4 class="text-primary">No announcements available</h4>
                                <p class="text-muted">Check back later for updates from the administration</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <!-- Footer -->
                <?php include_once "./Components/footer.php" ?>

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script>
    <script src="../../assets/vendor/libs/pickr/pickr.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/moment/moment.js"></script>
    <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/select2/select2.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="../../assets/vendor/libs/cleave-zen/cleave-zen.js"></script>
    <script src="../../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS -->
    <script>
        $(document).ready(function() {
            // Load user's read announcements from localStorage
            let readAnnouncements = JSON.parse(localStorage.getItem('readAnnouncements')) || [];
            let currentFilter = 'unread';

            // Fetch announcements from the API
            $.ajax({
                url: '/student/announcements-data',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Get server-side read announcements
                    const serverReadAnnouncements = data.filter(a => a.is_read === 1)
                        .map(a => a.announcement_id);

                    // Merge with local storage (prioritize server data)
                    readAnnouncements = [...new Set([...serverReadAnnouncements, ...readAnnouncements])];
                    localStorage.setItem('readAnnouncements', JSON.stringify(readAnnouncements));

                    // Update the announcement count
                    const totalCount = data.length;
                    const unreadCount = totalCount - readAnnouncements.length;

                    if (totalCount > 0) {
                        $('#announcement-count').html(`You have <span class="fw-bold">${unreadCount}</span> unread out of <span class="fw-bold">${totalCount}</span> total announcements`);
                        displayAnnouncements(data, currentFilter);
                    } else {
                        $('#announcements-container').empty();
                        $('#no-announcements').removeClass('d-none');
                    }

                    // Update the announcement count in the header if it exists
                    if ($('#announcementCount').length) {
                        $('#announcementCount').text(unreadCount || 0);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching announcements:', error);
                    $('#announcements-container').empty();
                    $('#no-announcements').removeClass('d-none');
                }
            });

            // Filter button click handlers
            $('.announcement-filter-btn').on('click', function() {
                const filter = $(this).data('filter');
                currentFilter = filter;

                $('.announcement-filter-btn').removeClass('active');
                $(this).addClass('active');

                // Re-fetch and display with new filter
                $.ajax({
                    url: '/student/announcements-data',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        displayAnnouncements(data, filter);
                    }
                });
            });

            // Function to display announcements based on filter
            function displayAnnouncements(announcements, filter) {
                const container = $('#announcements-container');
                container.empty();

                // First, update the local storage with server data
                let readAnnouncementsFromServer = [];
                announcements.forEach(function(announcement) {
                    if (announcement.is_read === 1) {
                        readAnnouncementsFromServer.push(announcement.announcement_id);
                    }
                });

                // Merge local and server data (prefer server data)
                readAnnouncements = [...new Set([...readAnnouncementsFromServer, ...readAnnouncements])];
                // Update local storage
                localStorage.setItem('readAnnouncements', JSON.stringify(readAnnouncements));

                // Sort announcements by priority (Urgent > High > Medium > Low) and then by date
                announcements.sort(function(a, b) {

                    const aIsRead = a.is_read === 1 || readAnnouncements.includes(a.announcement_id);
                    const bIsRead = b.is_read === 1 || readAnnouncements.includes(b.announcement_id);

                    // For "All" filter: unread first, then read
                    if (filter === 'all') {
                        if (aIsRead !== bIsRead) {
                            return aIsRead ? 1 : -1; // Unread (false) comes first
                        }
                    }


                    const priorityOrder = {
                        'Urgent': 0,
                        'High': 1,
                        'Medium': 2,
                        'Low': 3
                    };
                    const priorityDiff = priorityOrder[a.priority] - priorityOrder[b.priority];

                    if (priorityDiff !== 0) return priorityDiff;

                    // If same priority, sort by date (newest first)
                    return new Date(b.date_posted) - new Date(a.date_posted);
                });

                let visibleCount = 0;

                announcements.forEach(function(announcement) {
                    const announcementId = announcement.announcement_id;
                    // Check if read based on the is_read field from API
                    const isRead = announcement.is_read === 1 || readAnnouncements.includes(announcementId);

                    // Skip if filter is "unread" and announcement is read
                    if (filter === 'unread' && isRead) {
                        return;
                    }

                    visibleCount++;
                    const formattedDate = moment(announcement.date_posted).format('MMM DD, YYYY [at] h:mm A');
                    const timeAgo = moment(announcement.date_posted).fromNow();
                    const priorityClass = getPriorityClass(announcement.priority);


                    // Add visual separator for read/unread sections when showing "All"
                    let sectionHeader = '';
                    if (filter === 'all' && visibleCount === 1) {
                        if (!isRead) {
                            sectionHeader = `
                    <div class="col-12 mb-3">
                        <div class="d-flex align-items-center">
                            <hr class="flex-grow-1">
                            <span class="px-3 text-muted fw-semibold">
                                <i class="bx bx-bell me-1"></i>Unread Announcements
                            </span>
                            <hr class="flex-grow-1">
                        </div>
                    </div>
                `;
                        } else {
                            sectionHeader = `
                    <div class="col-12 mb-3">
                        <div class="d-flex align-items-center">
                            <hr class="flex-grow-1">
                            <span class="px-3 text-muted fw-semibold">
                                <i class="bx bx-check-circle me-1"></i>Read Announcements
                            </span>
                            <hr class="flex-grow-1">
                        </div>
                    </div>
                `;
                        }
                    }

                    // Check if we need to add "Read Announcements" header
                    if (filter === 'all' && visibleCount > 1) {
                        const prevAnnouncement = announcements[announcements.findIndex(a => a.announcement_id === announcementId) - 1];
                        if (prevAnnouncement) {
                            const prevIsRead = prevAnnouncement.is_read === 1 || readAnnouncements.includes(prevAnnouncement.announcement_id);
                            if (!prevIsRead && isRead) {
                                sectionHeader = `
                        <div class="col-12 mb-3 mt-4">
                            <div class="d-flex align-items-center">
                                <hr class="flex-grow-1">
                                <span class="px-3 text-muted fw-semibold">
                                    <i class="bx bx-check-circle me-1"></i>Read Announcements
                                </span>
                                <hr class="flex-grow-1">
                            </div>
                        </div>
                    `;
                            }
                        }
                    }

                    const announcementCard = `
            ${sectionHeader}
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card announcement-card h-100 ${isRead ? 'read' : 'unread'}" data-id="${announcementId}">
                    <div class="card-header border-bottom pt-3 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="priority-indicator ${priorityClass} me-2"></span>
                                <span class="badge bg-label-${getPriorityBadgeClass(announcement.priority)}">${announcement.priority}</span>
                                ${!isRead ? '<span class="badge bg-primary ms-2">New</span>' : ''}
                            </div>
                            ${!isRead ? `
                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary mark-read-btn" title="Mark as read">
                                <i class="icon-base bx bx-check-circle icon-lg"></i>
                            </button>` : ''}
                        </div>
                        <h5 class="card-title mb-0">${announcement.title}</h5>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <p class="card-text">${announcement.content}</p>
                    </div>
                    <div class="card-footer border-top pt-3 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted announcement-date">
                                <i class="bx bx-calendar me-1"></i>${formattedDate}
                            </small>
                            <small class="text-muted">${timeAgo}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;

                    container.append(announcementCard);
                });

                if (visibleCount === 0) {
                    if (filter === 'unread') {
                        container.append(`
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <img src="../../assets/img/illustrations/no_call.png" alt="All read" class="img-fluid mb-3" style="max-width: 200px;">
                                    <h4 class="text-primary">All caught up!</h4>
                                    <p class="text-muted">You have read all the announcements</p>
                                    <button class="btn btn-primary mt-2 announcement-filter-btn" data-filter="all">View All Announcements</button>
                                </div>
                            </div>
                        </div>
                        `);
                    } else {
                        $('#no-announcements').removeClass('d-none');
                    }
                }

                // Set up event handlers for buttons
                setupEventHandlers();
            }

            // Set up event handlers
            function setupEventHandlers() {
                // Mark as read buttons
                $('.mark-read-btn').on('click', function(e) {
                    e.stopPropagation();
                    const card = $(this).closest('.announcement-card');
                    const announcementId = card.data('id');

                    // First, update the database via AJAX
                    $.ajax({
                        url: '/announcement/mark-read',
                        method: 'POST',
                        data: {
                            announcement_id: announcementId
                        },
                        success: function(response) {
                            if (response.success) {
                                // Add to local storage read announcements
                                if (!readAnnouncements.includes(announcementId)) {
                                    readAnnouncements.push(announcementId);
                                    localStorage.setItem('readAnnouncements', JSON.stringify(readAnnouncements));
                                }

                                // Update UI
                                card.removeClass('unread').addClass('read');
                                $(this).remove();

                                // Update counts
                                $.ajax({
                                    url: '/student/announcements-data',
                                    method: 'GET',
                                    dataType: 'json',
                                    success: function(data) {
                                        const totalCount = data.length;
                                        const unreadCount = totalCount - readAnnouncements.length;
                                        $('#announcement-count').html(`You have <span class="fw-bold">${unreadCount}</span> unread out of <span class="fw-bold">${totalCount}</span> total announcements`);

                                        // Update header count
                                        if ($('#announcementCount').length) {
                                            $('#announcementCount').text(unreadCount || 0);
                                        }

                                        // Show toast notification instead of SweetAlert
                                        const toastEl = $(`
                                <div class="bs-toast toast toast-ex bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="toast-header bg-success text-white">
                                        <i class="bx bx-check-circle me-2"></i>
                                        <div class="me-auto fw-semibold">Success</div>
                                        <small>Just now</small>
                                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    <div class="toast-body">
                                        Announcement marked as read
                                    </div>
                                </div>
                            `);

                                        // Append toast to container
                                        $('#toast-container').append(toastEl);
                                        const toast = new bootstrap.Toast(toastEl, {
                                            delay: 3000,
                                            autohide: true
                                        });
                                        toast.show();
                                    }
                                });
                            }
                        },
                        error: function() {
                            // Show error toast
                            const toastEl = $(`
                    <div class="bs-toast toast toast-ex bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-danger text-white">
                            <i class="bx bx-error-circle me-2"></i>
                            <div class="me-auto fw-semibold">Error</div>
                            <small>Just now</small>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Failed to mark announcement as read
                        </div>
                    </div>
                `);

                            $('#toast-container').append(toastEl);
                            const toast = new bootstrap.Toast(toastEl, {
                                delay: 3000,
                                autohide: true
                            });
                            toast.show();
                        }
                    });
                });

                // Click on filter buttons inside the container
                $('#announcements-container').on('click', '.announcement-filter-btn', function() {
                    const filter = $(this).data('filter');

                    // Update active class on main filter buttons
                    $('.announcement-filter-btn').removeClass('active');
                    $(`.announcement-filter-btn[data-filter="${filter}"]`).addClass('active');

                    // Re-fetch and display
                    currentFilter = filter;
                    $.ajax({
                        url: '/student/announcements-data',
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            displayAnnouncements(data, filter);
                        }
                    });
                });
            }

            // Helper function to get priority CSS class
            function getPriorityClass(priority) {
                switch (priority.toLowerCase()) {
                    case 'urgent':
                        return 'priority-urgent';
                    case 'high':
                        return 'priority-high';
                    case 'medium':
                        return 'priority-medium';
                    default:
                        return 'priority-low';
                }
            }

            // Helper function to get badge class
            function getPriorityBadgeClass(priority) {
                switch (priority.toLowerCase()) {
                    case 'urgent':
                        return 'danger';
                    case 'high':
                        return 'warning';
                    case 'medium':
                        return 'info';
                    default:
                        return 'success';
                }
            }
        });
    </script>

    <!-- Toast container for notifications -->
    <div class="position-fixed bottom-0 end-0 p-3" id="toast-container" style="z-index: 11"></div>
</body>

</html>