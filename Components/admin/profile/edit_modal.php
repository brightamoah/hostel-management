<div class="modal fade" id="editAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple modal-edit-user">
        <div class="p-3 modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="mb-4 text-center">
                    <h3>Edit Admin Information</h3>
                    <p>Updating admin details will receive a privacy audit.</p>
                </div>
                <form id="editAdminForm" class="row g-3" method="POST" action="/admin/profile/update">
                    <?php set_csrf(); ?>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="modalEditAdminFirstName">First Name</label>
                        <input
                            type="text"
                            id="modalEditAdminFirstName"
                            name="first_name"
                            class="form-control"
                            value="<?= htmlspecialchars($admin_details['first_name'] ?? ($admin_data['name'] ? explode(' ', $admin_data['name'])[0] : '')); ?>"
                            required />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="modalEditAdminLastName">Last Name</label>
                        <input
                            type="text"
                            id="modalEditAdminLastName"
                            name="last_name"
                            class="form-control"
                            value="<?= htmlspecialchars($admin_details['last_name'] ?? ($admin_data['name'] ? trim(str_replace(explode(' ', $admin_data['name'])[0], '', $admin_data['name'])) : '')); ?>"
                            required />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="modalEditAdminEmail">Email</label>
                        <input
                            type="email"
                            id="modalEditAdminEmail"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($admin_data['email']); ?>"
                            readonly />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="modalEditAdminDepartment">Department</label>
                        <select id="modalEditAdminDepartment" name="department" class="form-select" required>
                            <option value="">Select Department</option>
                            <?php
                            // List of departments
                            $departments = [
                                'Administration',
                                'IT',
                                'Maintenance',
                                'Finance',
                                'Student Affairs',
                                'Security'
                            ];
                            $selected_department = $admin_details['department'] ?? '';
                            // If current department is not in the list, add it as selected
                            if ($selected_department && !in_array($selected_department, $departments)) {
                                echo '<option value="' . htmlspecialchars($selected_department) . '" selected>' . htmlspecialchars($selected_department) . ' (Other)</option>';
                            }
                            foreach ($departments as $dept) {
                                $selected = ($selected_department === $dept) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($dept) . '" ' . $selected . '>' . htmlspecialchars($dept) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <?php if (($admin_details['access_level'] ?? 'Regular Admin') === 'Super Admin'): ?>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditAdminAccessLevel">Access Level</label>
                            <select id="modalEditAdminAccessLevel" name="access_level" class="form-select" required>
                                <option value="Regular Admin" <?= ($admin_details['access_level'] ?? 'Regular Admin') == 'Regular Admin' ? 'selected' : ''; ?>>Regular Admin</option>
                                <option value="Super Admin" <?= ($admin_details['access_level'] ?? '') == 'Super Admin' ? 'selected' : ''; ?>>Super Admin</option>
                                <option value="Support Staff" <?= ($admin_details['access_level'] ?? '') == 'Support Staff' ? 'selected' : ''; ?>>Support Staff</option>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="modalEditAdminAccessLevel">Access Level</label>
                            <input
                                type="text"
                                id="modalEditAdminAccessLevel"
                                class="form-control"
                                value="<?= htmlspecialchars($admin_details['access_level'] ?? 'Regular Admin'); ?>"
                                readonly />
                            <small class="text-muted">Only Super Admins can modify access levels</small>
                        </div>
                    <?php endif; ?>
                    <div class="text-center col-12">
                        <button type="submit" class="me-1 me-sm-3 btn btn-success">Update Profile</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>