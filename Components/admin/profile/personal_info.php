 <div class="tab-pane fade show active" id="admin-info" role="tabpanel">
     <div class="row">
         <div class="col-md-6">
             <div class="mb-3 info-item">
                 <label class="text-muted form-label">Full Name</label>
                 <p class="mb-0 fw-semibold"><?= htmlspecialchars($first_name . ' ' . $last_name); ?></p>
             </div>
             <div class="mb-3 info-item">
                 <label class="text-muted form-label">Email</label>
                 <p class="mb-0 fw-semibold"><?= htmlspecialchars($admin_data['email']); ?></p>
             </div>
             <div class="mb-3 info-item">
                 <label class="text-muted form-label">Role</label>
                 <p class="mb-0 fw-semibold"><?= htmlspecialchars($admin_data['role']); ?></p>
             </div>
         </div>
         <div class="col-md-6">
             <?php if ($admin_details): ?>
                 <div class="mb-3 info-item">
                     <label class="text-muted form-label">Department</label>
                     <p class="mb-0 fw-semibold"><?= htmlspecialchars($admin_details['department']); ?></p>
                 </div>
                 <div class="mb-3 info-item">
                     <label class="text-muted form-label">Access Level</label>
                     <p class="mb-0 fw-semibold"><?= htmlspecialchars($admin_details['access_level']); ?></p>
                 </div>
             <?php endif; ?>
             <div class="mb-3 info-item">
                 <label class="text-muted form-label">Last Login</label>
                 <p class="mb-0 fw-semibold"><?= $admin_data['last_login'] ?? 'N/A'; ?></p>
             </div>
         </div>
     </div>
 </div>