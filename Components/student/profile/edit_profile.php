 <div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-lg modal-dialog-centered modal-simple modal-edit-user">
         <div class="p-3 modal-content">
             <div class="modal-body">
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 <div class="mb-4 text-center">
                     <h3>Edit User Information</h3>
                     <p>Updating user details will receive a privacy audit.</p>
                 </div>
                 <form id="editUserForm" class="row g-3" method="POST" action="/student/profile/update">
                     <?php set_csrf(); ?>
                     <div class="col-12 col-md-6">
                         <label class="form-label" for="modalEditUserFirstName">First Name</label>
                         <input
                             type="text"
                             id="modalEditUserFirstName"
                             name="first_name"
                             class="form-control"
                             value="<?= htmlspecialchars($student_data['first_name']); ?>"
                             required />
                     </div>
                     <div class="col-12 col-md-6">
                         <label class="form-label" for="modalEditUserLastName">Last Name</label>
                         <input
                             type="text"
                             id="modalEditUserLastName"
                             name="last_name"
                             class="form-control"
                             value="<?= htmlspecialchars($student_data['last_name']); ?>"
                             required />
                     </div>
                     <div class="col-12 col-md-6">
                         <label class="form-label" for="modalEditUserEmail">Email</label>
                         <input
                             type="email"
                             id="modalEditUserEmail"
                             name="email"
                             class="form-control"
                             value="<?= htmlspecialchars($student_data['email']); ?>"
                             readonly />
                     </div>
                     <div class="col-12 col-md-6">
                         <label class="form-label" for="modalEditUserPhone">Phone Number</label>
                         <div class="input-group">
                             <span class="input-group-text">GH (+233)</span>
                             <input
                                 type="text"
                                 id="modalEditUserPhone"
                                 name="phone_number"
                                 class="form-control phone-number-mask"
                                 value="<?= htmlspecialchars($student_data['phone_number']); ?>"
                                 required />
                         </div>
                     </div>
                     <div class="col-12 col-md-6">
                         <label class="form-label" for="modalEditUserGender">Gender</label>
                         <select id="modalEditUserGender" name="gender" class="form-select" required>
                             <option value="Male" <?= $student_data['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                             <option value="Female" <?= $student_data['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                             <option value="Other" <?= $student_data['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                         </select>
                     </div>
                     <div class="col-12 col-md-6">
                         <label class="form-label" for="modalEditUserAddress">Address</label>
                         <input
                             type="text"
                             id="modalEditUserAddress"
                             name="address"
                             class="form-control"
                             value="<?= htmlspecialchars($student_data['address'] ?: ''); ?>"
                             required />
                     </div>
                     <div class="col-12 col-md-6">
                         <label class="form-label" for="modalEditEmergencyContactName">Emergency Contact Name</label>
                         <input
                             type="text"
                             id="modalEditEmergencyContactName"
                             name="emergency_contact_name"
                             class="form-control"
                             value="<?= htmlspecialchars($student_data['emergency_contact_name']); ?>"
                             required />
                     </div>
                     <div class="col-12 col-md-6">
                         <label class="form-label" for="modalEditEmergencyContactNumber">Emergency Contact Number</label>
                         <div class="input-group">
                             <span class="input-group-text">GH (+233)</span>
                             <input
                                 type="text"
                                 id="modalEditEmergencyContactNumber"
                                 name="emergency_contact_number"
                                 class="form-control phone-number-mask"
                                 value="<?= htmlspecialchars($student_data['emergency_contact_number']); ?>"
                                 required />
                         </div>
                     </div>
                     <div class="col-12">
                         <label class="form-label" for="modalEditHealthCondition">Health Condition</label>
                         <textarea
                             id="modalEditHealthCondition"
                             name="health_condition"
                             class="form-control"
                             rows="4"><?= htmlspecialchars($student_data['health_condition'] ?: ''); ?></textarea>
                     </div>
                     <div class="text-center col-12">
                         <button type="submit" class="me-1 me-sm-3 btn btn-primary">Submit</button>
                         <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>