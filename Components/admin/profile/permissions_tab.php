 <div class="tab-pane fade" id="admin-permissions" role="tabpanel">
     <div class="row">
         <div class="col-12">
             <h5 class="mb-3">Access Permissions</h5>
             <div class="row">
                 <div class="col-md-6">
                     <div class="list-group">
                         <div class="list-group-item d-flex align-items-center justify-content-between">
                             <span><i class="me-2 bx bx-user-check"></i>User Management</span>
                             <span class="bg-success rounded-pill badge">Granted</span>
                         </div>
                         <div class="list-group-item d-flex align-items-center justify-content-between">
                             <span><i class="me-2 bx bx-home"></i>Room Management</span>
                             <span class="bg-success rounded-pill badge">Granted</span>
                         </div>
                         <div class="list-group-item d-flex align-items-center justify-content-between">
                             <span><i class="me-2 bx bx-credit-card"></i>Billing Management</span>
                             <span class="bg-success rounded-pill badge">Granted</span>
                         </div>
                     </div>
                 </div>
                 <div class="col-md-6">
                     <div class="list-group">
                         <div class="list-group-item d-flex align-items-center justify-content-between">
                             <span><i class="me-2 bx bx-wrench"></i>Maintenance Requests</span>
                             <span class="bg-success rounded-pill badge">Granted</span>
                         </div>
                         <div class="list-group-item d-flex align-items-center justify-content-between">
                             <span><i class="me-2 bx bx-shield-check"></i>Visitor Management</span>
                             <span class="bg-success rounded-pill badge">Granted</span>
                         </div>
                         <div class="list-group-item d-flex align-items-center justify-content-between">
                             <span><i class="me-2 bx bx-cog"></i>System Settings</span>
                             <span class="badge bg-<?= ($admin_details['access_level'] ?? '') === 'Super Admin' ? 'success' : 'warning'; ?> rounded-pill">
                                 <?= ($admin_details['access_level'] ?? '') === 'Super Admin' ? 'Granted' : 'Limited'; ?>
                             </span>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>