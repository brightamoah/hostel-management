<?php
// Ensure session is started (already done in router.php)
if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit();
}

// Get the current request URI and sanitize it
$current_route = rtrim(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '/');
$current_route = strtok($current_route, '?'); // Remove query parameters

// Define menu items based on user role
$role = $_SESSION['user']['role'] ?? 'Student';
$menu_items = [];

$menu_items = ($role === 'Admin') ? [
    ['route' => '/admin/dashboard', 'label' => 'Dashboards', 'icon' => 'bx-home-smile'],
    ['route' => '/admin/rooms', 'label' => 'Rooms', 'icon' => 'bx-buildings'],
    ['route' => '/admin/visitors', 'label' => 'Visitors', 'icon' => 'bx-group'],
    ['route' => '/admin/billings', 'label' => 'Billings', 'icon' => 'bx-group'],
    ['route' => '/admin/maintenance', 'label' => 'Maintenance', 'icon' => 'bx-wrench'],
    ['route' => '/admin/forms', 'label' => 'Forms', 'icon' => 'bx-file'],
    ['route' => '/admin/messages', 'label' => 'Messages', 'icon' => 'message-square'],
] : [
    ['route' => '/student/dashboard', 'label' => 'Dashboards', 'icon' => 'bx-home'],
    ['route' => '/student/rooms', 'label' => 'Rooms', 'icon' => 'bx-buildings'],
    ['route' => '/student/profile', 'label' => 'Profile', 'icon' => 'bx-user'],
    ['route' => '/student/visitors', 'label' => 'Visitors', 'icon' => 'bx-group'],
    ['route' => '/student/complaints', 'label' => 'Complaints', 'icon' => 'bx-error-circle'],
    ['route' => '/student/maintenance', 'label' => 'Maintenance', 'icon' => 'bx-wrench'],
    ['route' => '/student/billing', 'label' => 'Billings', 'icon' => 'bx-credit-card'],
    ['route' => '/student/announcements', 'label' => 'Announcements', 'icon' => 'bx-file'],
];

// Define logout item separately
$logout_item = ['route' => '/logout', 'label' => 'Logout', 'icon' => 'bx-log-out'];
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="<?php echo $role === 'Admin' ? '/admin/dashboard' : '/student/dashboard'; ?>" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="../../assets/img/favicon_io/favicon-32x32.png" alt="logo" class="text-primary" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">Kings</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base bx bx-chevron-left"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1 gap-5">
        <?php foreach ($menu_items as $item): ?>
            <li class="menu-item <?php echo ($current_route === $item['route']) ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($item['route']); ?>" class="menu-link">
                    <i class="menu-icon icon-lg bx <?php echo htmlspecialchars($item['icon']); ?>"></i>
                    <div data-i18n="<?php echo htmlspecialchars($item['label']); ?>">
                        <?php echo htmlspecialchars($item['label']); ?>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Footer section for logout -->
    <div class="menu-footer mt-auto py-3">
        <ul class="menu-inner">
            <li class="menu-item <?php echo ($current_route === $logout_item['route']) ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($logout_item['route']); ?>" class="menu-link">
                    <i class="menu-icon icon-lg bx <?php echo htmlspecialchars($logout_item['icon']); ?>"></i>
                    <div data-i18n="<?php echo htmlspecialchars($logout_item['label']); ?>">
                        <?php echo htmlspecialchars($logout_item['label']); ?>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</aside>