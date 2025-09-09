<?php
require_once __DIR__ . "/../utils/active_route.php";

if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit();
}


$current_route = rtrim(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), '/');
$current_route = strtok($current_route, '?'); // Remove query parameters

// Define menu items based on user role
$role = $_SESSION['user']['role'] ?? 'Student';
$menu_items = [];

$menu_items = ($role === 'Admin') ? [
    ['route' => '/admin/dashboard', 'label' => 'Dashboards', 'icon' => 'bx-home'],
    ['route' => '/admin/rooms', 'label' => 'Rooms', 'icon' => 'bx-buildings'],
    ['route' => '/admin/visitors', 'label' => 'Visitors', 'icon' => 'bx-group'],
    ['route' => '/admin/users', 'label' => 'Users', 'icon' => 'bx-user'],
    ['route' => '/admin/maintenance', 'label' => 'Maintenance', 'icon' => 'bx-wrench'],
    ['route' => '/admin/billings', 'label' => 'Billings', 'icon' => 'bx-credit-card'],
    ['route' => '/admin/complaints', 'label' => 'Complaints', 'icon' => 'bx-message-error'],
    ['route' => '/admin/announcements', 'label' => 'Announcements', 'icon' => 'bxs-megaphone'],
    ['route' => '/admin/analytics', 'label' => 'Analytics', 'icon' => 'bx-line-chart'],
] : [
    ['route' => '/student/dashboard', 'label' => 'Dashboards', 'icon' => 'bx-home'],
    ['route' => '/student/rooms', 'label' => 'Rooms', 'icon' => 'bx-buildings'],
    ['route' => '/student/profile', 'label' => 'Profile', 'icon' => 'bx-user'],
    ['route' => '/student/visitors', 'label' => 'Visitors', 'icon' => 'bx-group'],
    ['route' => '/student/complaints', 'label' => 'Complaints', 'icon' => 'bx-message-error'],
    ['route' => '/student/maintenance', 'label' => 'Maintenance', 'icon' => 'bx-wrench'],
    ['route' => '/student/billing', 'label' => 'Billings', 'icon' => 'bx-credit-card'],
    ['route' => '/student/announcements', 'label' => 'Announcements', 'icon' => 'bxs-megaphone'],
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
            <span class="ms-2 app-brand-text demo menu-text fw-bold">Kings</span>
        </a>
        <a href="javascript:void(0);" class="ms-auto text-large layout-menu-toggle menu-link">
            <i class="bx-chevron-left icon-base bx"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="gap-5 py-1 menu-inner">
        <?php foreach ($menu_items as $item): ?>
            <li class="menu-item <?php echo isRouteActive($item['route'], $current_route) ? 'active' : ''; ?>">
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
    <div class="mt-auto py-3 menu-footer">
        <ul class="menu-inner">
            <li class="menu-item <?php echo isRouteActive($logout_item['route'], $current_route) ? 'active' : ''; ?>">
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