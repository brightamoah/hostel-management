(function () {
   "use strict";

   const dt_maintenance_table = document.querySelector(
      ".datatables-maintenance"
   );

   if (dt_maintenance_table) {
      const dt = new DataTable(dt_maintenance_table, {
         ajax: "/student/maintenance-data",
         layout: {
            topStart: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     pageLength: {
                        menu: [5, 10, 25, 50],
                        text: "Show _MENU_ entries",
                     },
                  },
               ],
            },
            bottomStart: {
               rowClass: "row mx-3 justify-content-between",
               features: ["info"],
            },
            bottomEnd: {
               paging: {
                  firstLast: false,
               },
            },
         },
         columns: [
            { data: "request_id" },
            { data: "issue_type" },
            { data: "description" },
            { data: "priority" },
            { data: "status" },
            { data: "request_date" },
            { data: null, defaultContent: "" }, // Actions
         ],
         columnDefs: [
            {
               targets: 0,
               render: function (data) {
                  return `<span class="fw-medium">${data}</span>`;
               },
            },
            {
               targets: 2, // Description
               render: function (data) {
                  const maxLength = 50;
                  return data.length > maxLength
                     ? data.substring(0, maxLength) + "..."
                     : data;
               },
            },
            {
               targets: 3, // Priority
               render: function (data) {
                  const priorityObj = {
                     Low: { class: "bg-label-success", title: "Low" },
                     Medium: { class: "bg-label-info", title: "Medium" },
                     High: { class: "bg-label-warning", title: "High" },
                     Emergency: {
                        class: "bg-label-danger",
                        title: "Emergency",
                     },
                  };
                  const priorityInfo = priorityObj[data] || {
                     class: "bg-label-secondary",
                     title: data,
                  };
                  return `<span class="badge ${priorityInfo.class}">${priorityInfo.title}</span>`;
               },
            },
            {
               targets: 4, // Status
               render: function (data) {
                  const statusObj = {
                     Pending: { class: "bg-label-warning", title: "Pending" },
                     Assigned: { class: "bg-label-info", title: "Assigned" },
                     "In-Progress": {
                        class: "bg-label-primary",
                        title: "In-Progress",
                     },
                     Completed: {
                        class: "bg-label-success",
                        title: "Completed",
                     },
                     Rejected: { class: "bg-label-danger", title: "Rejected" },
                  };
                  const statusInfo = statusObj[data] || {
                     class: "bg-label-secondary",
                     title: data,
                  };
                  return `<span class="badge ${statusInfo.class}">${statusInfo.title}</span>`;
               },
            },
            {
               targets: 5, // Request Date
               render: function (data) {
                  return moment(data).format("MMM D, YYYY");
               },
            },
            {
               targets: 6, // Actions
               searchable: false,
               orderable: false,
               render: function (data, type, full) {
                  return `
                            <div class="d-flex align-items-center">
                                <a href="javascript:;" class="btn btn-sm btn-icon view-maintenance-details" 
                                   data-request-id="${full.request_id}"
                                   data-bs-toggle="tooltip" 
                                   title="View details">
                                   <i class="bx bx-show icon-md"></i>
                                </a>
                            </div>
                        `;
               },
            },
         ],
         order: [[0, "desc"]],
         responsive: {
            details: {
               display: DataTable.Responsive.display.modal({
                  header: function (row) {
                     const data = row.data();
                     return `Maintenance Request #${data.request_id} Details`;
                  },
               }),
               renderer: function (api, rowIdx, columns) {
                  const data = columns
                     .map(function (col) {
                        return col.title !== "Actions"
                           ? `<tr><td>${col.title}:</td><td>${col.data}</td></tr>`
                           : "";
                     })
                     .join("");
                  return data ? `<table class="table">${data}</table>` : false;
               },
            },
         },
         language: {
            paginate: {
               next: '<i class="bx bx-chevron-right icon-18px"></i>',
               previous: '<i class="bx bx-chevron-left icon-18px"></i>',
            },
         },
         initComplete: function () {
            const api = this.api();

            // Initialize select2 for all select elements in the filters and modal
            if ($.fn.select2) {
               $("#typeFilter").select2({
                  placeholder: "Filter by Type",
                  allowClear: true,
                  width: "100%",
               });
               $("#priorityFilter").select2({
                  placeholder: "Filter by Priority",
                  allowClear: true,
                  width: "100%",
               });
               $("#statusFilter").select2({
                  placeholder: "Filter by Status",
                  allowClear: true,
                  width: "100%",
               });
               $("#issueType").select2({
                  placeholder: "Select Issue Type",
                  allowClear: true,
                  dropdownParent: $("#newMaintenanceModal"),
                  width: "100%",
               });
               $("#roomId").select2({
                  placeholder: "Select Room",
                  allowClear: true,
                  dropdownParent: $("#newMaintenanceModal"),
                  width: "100%",
               });
               $("#priority").select2({
                  placeholder: "Select Priority",
                  allowClear: true,
                  dropdownParent: $("#newMaintenanceModal"),
                  width: "100%",
               });
            }

            // Search box
            $("#maintenanceSearch").on("keyup", function () {
               api.search(this.value).draw();
            });

            // Type filter
            $("#typeFilter").on("change", function () {
               const val = $(this).val();
               api.column(1)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Priority filter
            $("#priorityFilter").on("change", function () {
               const val = $(this).val();
               api.column(3)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Status filter
            $("#statusFilter").on("change", function () {
               const val = $(this).val();
               api.column(4)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(
               document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Handle view details click
            $(document).on("click", ".view-maintenance-details", function (e) {
               e.preventDefault();
               const requestId = $(this).data("request-id");

               $.ajax({
                  url: `/student/maintenance/${requestId}`,
                  method: "GET",
                  success: function (data) {
                     // Populate modal fields
                     $("#modalRequestId").text(data.details.request_id);
                     $("#modalIssueType").text(data.details.issue_type);
                     $("#modalRequestRoom").text(
                        data.details.room_number
                           ? `${data.details.room_number} - ${data.details.building}`
                           : "Not specified"
                     );
                     $("#modalRequestDescription").text(
                        data.details.description
                     );
                     $("#modalRequestPriority").html(
                        `<span class="badge bg-label-${
                           data.details.priority === "Emergency"
                              ? "danger"
                              : data.details.priority === "High"
                              ? "warning"
                              : data.details.priority === "Medium"
                              ? "info"
                              : "success"
                        }">${data.details.priority}</span>`
                     );
                     $("#modalRequestStatus").html(
                        `<span class="badge bg-label-${
                           data.details.status === "Completed"
                              ? "success"
                              : data.details.status === "Rejected"
                              ? "danger"
                              : data.details.status === "In-Progress"
                              ? "primary"
                              : data.details.status === "Assigned"
                              ? "info"
                              : "warning"
                        }">${data.details.status}</span>`
                     );
                     $("#modalRequestDate").text(
                        moment(data.details.request_date).format(
                           "MMM D, YYYY h:mm A"
                        )
                     );
                     $("#modalSubmittedTimeAgo").text(
                        `Submitted ${moment(
                           data.details.request_date
                        ).fromNow()}`
                     );

                     // Show/hide follow-up button
                     $("#followUpBtn").css(
                        "display",
                        data.details.status === "Pending"
                           ? "inline-block"
                           : "none"
                     );

                     // Mock responses (implement actual response fetching in production)
                     $.ajax({
                        url: `/student/maintenance/${requestId}/response`,
                        method: "GET",
                        success: function (responseData) {
                           const responses = responseData.data || [];
                           if (responses.length > 0) {
                              $("#responseSection").html(
                                 responses
                                    .map(
                                       (r) => `
                                                <div class="timeline-item timeline-item-transparent">
                        <span class="timeline-indicator timeline-indicator-info">
                           <i class="bx bx-user-voice"></i>
                        </span>
                        <div class="timeline-event">
                           <div class="timeline-header mb-1">
                              <h6 class="mb-0">${
                                 r.role === "Admin"
                                    ? "Staff Response"
                                    : "Student Response"
                              }</h6>
                              <small class="text-muted">${moment(
                                 r.response_date
                              ).format("MMM D, YYYY h:mm A")}</small>
                           </div>
                           <p class="mb-2">${r.response_text}</p>
                           <div class="d-flex justify-content-between align-items-center">
                              <span class="badge bg-label-info">Role: ${
                                 r.role
                              }</span>
                              <span>${r.name}</span>
                           </div>
                        </div>
                     </div>
                                            `
                                    )
                                    .join("")
                              );
                           } else {
                              $("#responseSection").html(
                                 '<p class="text-muted">No staff responses yet.</p>'
                              );
                           }
                           $("#maintenanceDetailsModal").modal("show");
                        },

                        error: function () {
                           $("#responseSection").html(
                              '<p class="text-danger">Failed to load responses.</p>'
                           );
                           $("#maintenanceDetailsModal").modal("show");
                        },
                     });
                  },
                  error: function () {
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to load maintenance request details",
                     });
                  },
               });
            });

            // Handle form submission
            $("#newMaintenanceForm").on("submit", function (e) {
               e.preventDefault();
               $.ajax({
                  url: "/maintenance/submit",
                  method: "POST",
                  data: $(this).serialize(),
                  success: function (response) {
                     if (response.success) {
                        Swal.fire({
                           icon: "success",
                           title: "Maintenance Request Submitted",
                           text: "Your request has been submitted successfully!",
                           timer: 1500,
                        }).then(() => {
                           $("#newMaintenanceModal").modal("hide");
                           location.reload();
                           api.ajax.reload();
                        });
                     } else {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text:
                              response.error ||
                              "Failed to submit maintenance request",
                        });
                     }
                  },
                  error: function () {
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Submission failed",
                     });
                  },
               });
            });

            // Handle follow-up button (mock; implement in production)
            $("#followUpBtn").on("click", function () {
               Swal.fire({
                  icon: "info",
                  title: "Follow-up",
                  text: "This feature is not yet implemented. Please contact support for follow-ups.",
               });
            });
         },
      });
   }
})();
