<?php if (isset($user) && !empty($_SESSION['user'])): ?>
    <li>
        <a href="<?= getRoute(); ?>" class="btn btn-success">
            <span class="tf-icons icon-lg bx bx-user-circle scaleX-n1-rtl me-md-1"></span>
            <span class="d-none d-md-block">
                <?php echo isset($user['role']) && $user['role'] === 'Student' ? 'Student Dashboard' : 'Admin Dashboard'; ?>
            </span>
        </a>
    </li>

<?php else: ?>
    <li>
        <a href="login" class="btn btn-primary">
            <span class="tf-icons icon-base bx bx-log-in-circle scaleX-n1-rtl me-md-1"></span>
            <span class="d-none d-md-block">Login/Register</span>
        </a>
    </li>
<?php endif; ?>