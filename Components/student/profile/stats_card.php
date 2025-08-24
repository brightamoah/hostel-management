 <!-- Stats Cards -->
 <div class="mb-4 row g-3">
     <div class="col-12 col-md-4">
         <div class="h-100 card profile-stats">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="flex-shrink-0 me-3 avatar">
                         <div class="bg-label-primary rounded avatar-initial">
                             <i class="icon-base bx bx-calendar icon-lg"></i>
                         </div>
                     </div>
                     <div>
                         <h4 class="mb-0">
                             <?php
                                $enrollment_date = new DateTime($student_data['enrollment_date']);
                                $today = new DateTime();
                                $days = $today->diff($enrollment_date)->days;
                                echo $days;
                                ?>
                         </h4>
                         <span>Days at Hostel</span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="col-12 col-md-4">
         <div class="h-100 card profile-stats">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="flex-shrink-0 me-3 avatar">
                         <div class="bg-label-warning rounded avatar-initial">
                             <i class="icon-base bx bx-wrench icon-lg"></i>
                         </div>
                     </div>
                     <div>
                         <h4 class="mb-0"><?= (int)$open_maintenance_requests ?></h4>
                         <span>Open Maintenance Requests</span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="col-12 col-md-4">
         <div class="h-100 card profile-stats">
             <div class="card-body">
                 <div class="d-flex align-items-center">
                     <div class="flex-shrink-0 me-3 avatar">
                         <div class="bg-label-info rounded avatar-initial">
                             <i class="icon-base bx bx-user-plus icon-lg"></i>
                         </div>
                     </div>
                     <div>
                         <h4 class="mb-0"><?= (int)$pending_visitors ?></h4>
                         <span>Pending Visitors (30 days)</span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>