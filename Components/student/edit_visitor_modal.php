 <!-- Edit Visitor Modal -->
 <div class="modal fade" id="editVisitorModal" tabindex="-1" aria-labelledby="editVisitorModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="editVisitorModalLabel">Edit Visitor Details</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <form id="editVisitorForm">
                     <input type="hidden" id="editVisitorId" name="visitor_id">
                     <div class="mb-3">
                         <label for="editVisitorName" class="form-label">Visitor Name</label>
                         <input type="text" class="form-control" id="editVisitorName" name="visitor_name" required>
                     </div>
                     <div class="mb-3">
                         <label for="editRelation" class="form-label">Relationship</label>
                         <input type="text" class="form-control" id="editRelation" name="relation" required>
                     </div>
                     <div class="mb-3">
                         <label for="editPhoneNumber" class="form-label">Phone Number</label>
                         <input type="text" class="form-control" id="editPhoneNumber" name="phone_number" required>
                     </div>
                     <div class="mb-3">
                         <label for="editVisitDate" class="form-label">Visit Date</label>
                         <input type="date" class="form-control" id="editVisitDate" name="visit_date" required>
                     </div>
                     <div class="mb-3">
                         <label for="editPurpose" class="form-label">Purpose of Visit</label>
                         <textarea class="form-control" id="editPurpose" name="purpose" rows="3" required></textarea>
                     </div>
                     <button type="submit" class="btn btn-primary">Update Visitor</button>
                 </form>
             </div>
         </div>
     </div>
 </div>