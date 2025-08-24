 <div class="tab-pane fade show active" id="profile-info" role="tabpanel">
     <div class="row">
         <div class="mt-5 col-12 col-md-6">
             <div class="mb-3 info-item">
                 <div class="d-flex align-items-center">
                     <i class="me-2 text-primary bx bx-user icon-xl"></i>
                     <div>
                         <span class="d-block fw-medium">Full Name</span>
                         <span class="text-muted">
                             <?= htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']); ?>
                         </span>
                     </div>
                 </div>
             </div>
             <div class="mb-3 info-item">
                 <div class="d-flex align-items-center">
                     <i class="me-2 text-primary bx bx-envelope icon-xl"></i>
                     <div>
                         <span class="d-block fw-medium">Email</span>
                         <span class="text-muted">
                             <?= htmlspecialchars($student_data['email']); ?>
                         </span>
                     </div>
                 </div>
             </div>
             <div class="mb-3 info-item">
                 <div class="d-flex align-items-center">
                     <i class="me-2 text-primary bx bx-phone icon-xl"></i>
                     <div>
                         <span class="d-block fw-medium">Contact</span>
                         <span class="text-muted">
                             <?= htmlspecialchars($student_data['phone_number']); ?>
                         </span>
                     </div>
                 </div>
             </div>
             <div class="mb-3 info-item">
                 <div class="d-flex align-items-center">
                     <i class="me-2 text-primary bx bx-user-check icon-xl"></i>
                     <div>
                         <span class="d-block fw-medium">Status</span>
                         <span class="bg-label-success badge">Active</span>
                     </div>
                 </div>
             </div>
         </div>
         <div class="mt-3 col-12 col-md-6">
             <div class="mb-3 info-item">
                 <div class="d-flex align-items-center">
                     <i class="me-2 text-primary bx bx-calendar icon-xl"></i>
                     <div>
                         <span class="d-block fw-medium">Date of Birth</span>
                         <span class="text-muted">
                             <?= htmlspecialchars($student_data['date_of_birth']); ?>
                         </span>
                     </div>
                 </div>
             </div>
             <div class="mb-3 info-item">
                 <div class="d-flex align-items-center">
                     <i class="me-2 text-primary bx bx-home icon-xl"></i>
                     <div>
                         <span class="d-block fw-medium">Address</span>
                         <span class="text-muted">
                             <?= htmlspecialchars($student_data['address'] ?: 'Not provided'); ?>
                         </span>
                     </div>
                 </div>
             </div>
             <div class="mb-3 info-item">
                 <div class="d-flex align-items-center">
                     <i class="me-2 text-primary bx bx-user-voice icon-xl"></i>
                     <div>
                         <span class="d-block fw-medium">Emergency Contact</span>
                         <span class="text-muted">
                             <?= htmlspecialchars($student_data['emergency_contact_name'] . ': ' . $student_data['emergency_contact_number']); ?>
                         </span>
                     </div>
                 </div>
             </div>
             <div class="mb-3 info-item">
                 <div class="d-flex align-items-center">
                     <i class="me-2 text-primary bx bx-heart icon-xl"></i>
                     <div>
                         <span class="d-block fw-medium">Health Condition</span>
                         <span class="text-muted">
                             <?= htmlspecialchars($student_data['health_condition'] ?: 'None'); ?>
                         </span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>