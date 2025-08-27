 <div class="mb-4 row admin-stats">
     <div class="col-md-3">
         <div class="h-100 card profile-stats">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="me-3 avatar">
                         <div class="bg-label-primary rounded avatar-initial">
                             <i class="bx bx-user fs-4"></i>
                         </div>
                     </div>
                     <div>
                         <h4 class="mb-0"><?= $stats['total_students'] ?? 0; ?></h4>
                         <span>Total Students</span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="col-md-3">
         <div class="h-100 card profile-stats">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="me-3 avatar">
                         <div class="bg-label-success rounded avatar-initial">
                             <i class="bx bx-home fs-4"></i>
                         </div>
                     </div>
                     <div>
                         <h4 class="mb-0"><?= $stats['total_rooms'] ?? 0; ?></h4>
                         <span>Total Rooms</span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="col-md-3">
         <div class="h-100 card profile-stats">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="me-3 avatar">
                         <div class="bg-label-warning rounded avatar-initial">
                             <i class="bx bx-credit-card fs-4"></i>
                         </div>
                     </div>
                     <div>
                         <h4 class="mb-0"><?= $stats['pending_payments'] ?? 0; ?></h4>
                         <span>Pending Payments</span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="col-md-3">
         <div class="h-100 card profile-stats">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="me-3 avatar">
                         <div class="bg-label-danger rounded avatar-initial">
                             <i class="bx bx-wrench fs-4"></i>
                         </div>
                     </div>
                     <div>
                         <h4 class="mb-0"><?= $stats['pending_maintenance'] ?? 0; ?></h4>
                         <span>Pending Maintenance</span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>