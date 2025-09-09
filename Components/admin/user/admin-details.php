 <div class="modal fade" id="adminDetailsModal" tabindex="-1"
     aria-labelledby="adminDetailsModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="adminDetailsModalLabel">Admin Details</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"
                     aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="table-responsive">
                     <table class="table table-bordered">
                         <tbody id="adminDetailsContent"></tbody>
                     </table>
                 </div>
                 <div class="mt-4" id="hostelAssignmentSection">
                     <h6>Hostel Assignment</h6>
                     <div class="row">
                         <div class="col-md-8">
                             <select class="form-select" id="hostelSelect">
                                 <option value="">Select Hostel</option>
                             </select>
                         </div>
                         <div class="col-md-4">
                             <button type="button" class="btn btn-primary" id="assignHostelBtn">
                                 Assign Hostel
                             </button>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-primary"
                     data-bs-dismiss="modal">Close</button>
             </div>
         </div>
     </div>
 </div>