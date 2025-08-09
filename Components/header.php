<?php

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $name = htmlspecialchars($user['name']);
    $email = htmlspecialchars($user['email']);
    $user_id = htmlspecialchars($user['user_id']);
    $role = htmlspecialchars($user['role']);
    $phone_number = htmlspecialchars($user['phone_number']);
    $first_name = htmlspecialchars($user['first_name']);
    $last_name = htmlspecialchars($user['last_name']);
}

function getProfileURL($role)
{
    if ($role === 'Admin') return "/admin/profile";
    else return "/student/profile";
}

$profile_url = getProfileURL($role);





?>

<nav
    class="align-items-center bg-navbar-theme layout-navbar container-xxl navbar-detached navbar navbar-expand-xl"
    id="layout-navbar">
    <div class="align-items-xl-center me-4 me-xl-0 layout-menu-toggle navbar-nav d-xl-none">
        <a class="me-xl-6 px-0 nav-item nav-link" href="javascript:void(0)">
            <i class="icon-base bx bx-menu icon-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        <!-- Search -->
        <div class="align-items-center navbar-nav">
            <div class="mb-0 nav-item navbar-search-wrapper">
                <a class="px-0 nav-item nav-link search-toggler" href="javascript:void(0);">
                    <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete">
                        <div class="aa-Autocomplete" role="combobox" aria-expanded="false" aria-haspopup="listbox" aria-labelledby="autocomplete-0-label"><button type="button" class="aa-DetachedSearchButton" title="Search" id="autocomplete-0-label">
                                <i class="icon-base bx bx-search icon-xl"></i>
                                <div class="aa-DetachedSearchButtonPlaceholder">Search [CTRL + K]</div>
                                <div class="aa-DetachedSearchButtonQuery"></div>
                            </button></div>
                    </span>
                </a>
            </div>
        </div>

        <!-- /Search -->

        <ul class="flex-row align-items-center ms-md-auto navbar-nav">


            <!-- Style Switcher -->
            <li class="me-2 me-xl-0 nav-item dropdown">
                <a
                    class="nav-link dropdown-toggle hide-arrow"
                    id="nav-theme"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown">
                    <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
                    <span class="ms-2 d-none" id="nav-theme-text">Toggle theme</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                    <li>
                        <button
                            type="button"
                            class="align-items-center dropdown-item active"
                            data-bs-theme-value="light"
                            aria-pressed="false">
                            <span><i class="me-3 icon-base bx bx-sun icon-md" data-icon="sun"></i>Light</span>
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="align-items-center dropdown-item"
                            data-bs-theme-value="dark"
                            aria-pressed="true">
                            <span><i class="me-3 icon-base bx bx-moon icon-md" data-icon="moon"></i>Dark</span>
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="align-items-center dropdown-item"
                            data-bs-theme-value="system"
                            aria-pressed="false">
                            <span><i class="me-3 icon-base bx bx-desktop icon-md" data-icon="desktop"></i>System</span>
                        </button>
                    </li>
                </ul>
            </li>
            <!-- / Style Switcher-->

            <!-- Quick links  -->
            <li class="me-2 me-xl-0 nav-item dropdown-shortcuts navbar-dropdown dropdown">
                <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false">
                    <i class="bx-grid-alt icon-base bx icon-md"></i>
                </a>
                <div class="p-0 dropdown-menu dropdown-menu-end">
                    <div class="border-bottom dropdown-menu-header">

                    </div>
                    <div class="dropdown-shortcuts-list scrollable-container">
                        <div class="row-bordered overflow-visible row g-0">
                            <div class="dropdown-shortcuts-item col">
                                <span class="mb-3 rounded-circle dropdown-shortcuts-icon">
                                    <i class="text-heading icon-base bx bx-calendar icon-26px"></i>
                                </span>
                                <a href="/student/visitors" class="stretched-link">Calendar</a>
                                <small>Registered Visitors</small>
                            </div>
                            <div class="dropdown-shortcuts-item col">
                                <span class="mb-3 rounded-circle dropdown-shortcuts-icon">
                                    <i class="text-heading icon-base bx bx-food-menu icon-26px"></i>
                                </span>
                                <a href="/student/billing" class="stretched-link">Billings</a>
                                <small>View Your Bills</small>
                            </div>
                        </div>

                        <div class="row-bordered overflow-visible row g-0">
                            <div class="dropdown-shortcuts-item col">
                                <span class="mb-3 rounded-circle dropdown-shortcuts-icon">
                                    <i class="text-heading icon-base bx bx-pie-chart-alt-2 icon-26px"></i>
                                </span>
                                <a href="/student/dashboard" class="stretched-link">Dashboard<a />
                                    <small>Your Dashboard</small>
                            </div>
                            <div class="dropdown-shortcuts-item col">
                                <span class="mb-3 rounded-circle dropdown-shortcuts-icon">
                                    <i class="text-heading icon-base bx bx-cog icon-26px"></i>
                                </span>
                                <a href="/student/profile" class="stretched-link">Setting</a>
                                <small>Account Settings</small>
                            </div>
                        </div>

                    </div>
                </div>
            </li>
            <!-- Quick links -->

            <!-- Notification -->
            <li class="me-3 me-xl-2 nav-item dropdown-notifications navbar-dropdown dropdown">
                <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false">
                    <span class="position-relative">
                        <i class="icon-base bx bx-bell icon-md"></i>
                        <span class="bg-danger border rounded-pill badge badge-dot badge-notifications"></span>
                    </span>
                </a>
                <ul class="p-0 dropdown-menu dropdown-menu-end">
                    <li class="border-bottom dropdown-menu-header">
                        <div class="d-flex align-items-center py-3 dropdown-header">
                            <h6 class="me-auto mb-0">Notification</h6>
                            <div class="d-flex align-items-center mb-0 h6">
                                <span class="bg-label-primary me-2 badge">8 New</span>
                                <a
                                    href="javascript:void(0)"
                                    class="p-2 dropdown-notifications-all"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Mark all as read"><i class="text-heading icon-base bx bx-envelope-open"></i></a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <img src="../../assets/img/avatars/1.png" alt class="rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">Congratulation Lettie 🎉</h6>
                                        <small class="d-block mb-1 text-body">Won the monthly best seller gold badge</small>
                                        <small class="text-body-secondary">1h ago</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <span class="bg-label-danger rounded-circle avatar-initial">CF</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">Charles Franklin</h6>
                                        <small class="d-block mb-1 text-body">Accepted your connection</small>
                                        <small class="text-body-secondary">12hr ago</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <img src="../../assets/img/avatars/2.png" alt class="rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">New Message ✉️</h6>
                                        <small class="d-block mb-1 text-body">You have new message from Natalie</small>
                                        <small class="text-body-secondary">1h ago</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <span class="bg-label-success rounded-circle avatar-initial"><i class="icon-base bx bx-cart"></i></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">Whoo! You have new order 🛒</h6>
                                        <small class="d-block mb-1 text-body">ACME Inc. made new order $1,154</small>
                                        <small class="text-body-secondary">1 day ago</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <img src="../../assets/img/avatars/9.png" alt class="rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">Application has been approved 🚀</h6>
                                        <small class="d-block mb-1 text-body">Your ABC project application has been approved.</small>
                                        <small class="text-body-secondary">2 days ago</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <span class="bg-label-success rounded-circle avatar-initial"><i class="icon-base bx bx-pie-chart-alt"></i></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">Monthly report is generated</h6>
                                        <small class="d-block mb-1 text-body">July monthly financial report is generated </small>
                                        <small class="text-body-secondary">3 days ago</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <img src="../../assets/img/avatars/5.png" alt class="rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">Send connection request</h6>
                                        <small class="d-block mb-1 text-body">Peter sent you connection request</small>
                                        <small class="text-body-secondary">4 days ago</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <img src="../../assets/img/avatars/6.png" alt class="rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">New message from Jane</h6>
                                        <small class="d-block mb-1 text-body">Your have new message from Jane</small>
                                        <small class="text-body-secondary">5 days ago</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <span class="bg-label-warning rounded-circle avatar-initial"><i class="icon-base bx bx-error"></i></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">CPU is running high</h6>
                                        <small class="d-block mb-1 text-body">CPU Utilization Percent is currently at 88.63%,</small>
                                        <small class="text-body-secondary">5 days ago</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="icon-base bx bx-x"></span></a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="border-top">
                        <div class="d-grid p-4">
                            <a class="d-flex btn btn-primary btn-sm" href="javascript:void(0);">
                                <small class="align-middle">View all notifications</small>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <!--/ Notification -->
            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a
                    class="p-0 nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="../../assets/img/avatars/1.png" alt class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="pages-account-settings-account.html">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="../../assets/img/avatars/1.png" alt class="w-px-40 rounded-circle h-auto" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">
                                        <?= $name; ?>
                                    </h6>
                                    <small class="text-body-secondary">
                                        <?= $role; ?>
                                    </small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="my-1 dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= $profile_url; ?>">
                            <i class="me-3 icon-base bx bx-user icon-md"></i><span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="pages-account-settings-account.html">
                            <i class="me-3 icon-base bx bx-cog icon-md"></i><span>Settings</span>
                        </a>
                    </li>

                    <li>
                        <div class="my-1 dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/logout">
                            <i class="me-3 icon-base bx bx-power-off icon-md"></i><span>Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>

<!-- / Navbar -->