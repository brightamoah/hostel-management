(function () {
   "use strict";

   const dt_maintenance_table = document.querySelector(
      ".datatables-maintenance"
   );

   if (dt_maintenance_table) {
      const csrfToken =
         document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";
      let dt;

      dt = new DataTable(dt_maintenance_table, {
         ajax: "/admin/maintenance-data",
         processing: true,
         serverSide: false,
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
            topEnd: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     search: {
                        placeholder: "Search Request",
                        text: "_INPUT_",
                     },
                  },
                  {
                     buttons: [
                        {
                           extend: "collection",
                           className:
                              "btn btn-label-secondary dropdown-toggle ms-4",
                           text: '<span class="d-flex align-items-center gap-2"><i class="icon-base bx bx-export icon-sm"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
                           buttons: [
                              {
                                 extend: "csv",
                                 text: '<span class="d-flex align-items-center"><i class="icon-base bx bx-file me-2"></i>Csv</span>',
                                 className: "dropdown-item",
                                 exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6],
                                    format: {
                                       body: function (
                                          data,
                                          row,
                                          column,
                                          node
                                       ) {
                                          // Strip HTML tags for all columns
                                          return data.replace(/<[^>]+>/g, "");
                                       },
                                    },
                                 },
                              },
                              {
                                 extend: "excel",
                                 text: '<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-export me-2"></i>Excel</span>',
                                 className: "dropdown-item",
                                 exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6],
                                    format: {
                                       body: function (
                                          data,
                                          row,
                                          column,
                                          node
                                       ) {
                                          // Strip HTML tags for all columns
                                          return data.replace(/<[^>]+>/g, "");
                                       },
                                    },
                                 },
                              },
                              {
                                 extend: "pdf",
                                 text: '<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-pdf me-2"></i>Pdf</span>',
                                 className: "dropdown-item",
                                 exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6],
                                    format: {
                                       body: function (
                                          data,
                                          row,
                                          column,
                                          node
                                       ) {
                                          // Strip HTML tags and clean up for all columns
                                          let cleanData = data.replace(
                                             /<[^>]+>/g,
                                             ""
                                          );
                                          // For Room column, combine room_number and building
                                          if (column === 2) {
                                             const roomNumber =
                                                node.querySelector("span")
                                                   ?.textContent ||
                                                "Not specified";
                                             const building =
                                                node.querySelector("small")
                                                   ?.textContent ||
                                                "Not specified";
                                             cleanData = `${roomNumber} - ${building}`;
                                          }
                                          return cleanData;
                                       },
                                    },
                                 },
                                 // Fix for PDF export in admin-maintenance-list.js
                                 customize: function (doc) {
                                    // Check if doc structure is valid before manipulating it
                                    if (
                                       !doc ||
                                       !doc.content ||
                                       doc.content.length < 2 ||
                                       !doc.content[1].table
                                    ) {
                                       console.warn(
                                          "PDF structure not as expected:",
                                          doc
                                       );
                                       return;
                                    }

                                    // Initialize styles safely
                                    doc.styles = doc.styles || {};
                                    doc.styles.tableHeader =
                                       doc.styles.tableHeader || {};
                                    doc.styles.tableBodyOdd =
                                       doc.styles.tableBodyOdd || {};
                                    doc.styles.tableBodyEven =
                                       doc.styles.tableBodyEven || {};
                                    doc.defaultStyle = doc.defaultStyle || {};

                                    try {
                                       // Set specific column widths
                                       doc.content[1].table.widths = [
                                          "8%", // ID
                                          "20%", // Student
                                          "20%", // Room
                                          "15%", // Type
                                          "12%", // Priority
                                          "12%", // Status
                                          "13%", // Submitted
                                       ];

                                       // Table styling
                                       doc.content[1].table.layout = {
                                          hLineWidth: function () {
                                             return 0.5;
                                          },
                                          vLineWidth: function () {
                                             return 0.5;
                                          },
                                          hLineColor: function () {
                                             return "#666";
                                          },
                                          vLineColor: function () {
                                             return "#666";
                                          },
                                          paddingLeft: function () {
                                             return 4;
                                          },
                                          paddingRight: function () {
                                             return 4;
                                          },
                                          paddingTop: function () {
                                             return 2;
                                          },
                                          paddingBottom: function () {
                                             return 2;
                                          },
                                       };

                                       // Font sizes and alignment
                                       doc.styles.tableHeader.fontSize = 10;
                                       doc.styles.tableHeader.fillColor =
                                          "#f3f3f3";
                                       doc.styles.tableHeader.alignment =
                                          "left";
                                       doc.styles.tableBodyOdd.fontSize = 9;
                                       doc.styles.tableBodyEven.fontSize = 9;
                                       doc.defaultStyle.alignment = "left";

                                       // Define title style
                                       doc.styles.title = {
                                          fontSize: 14,
                                          bold: true,
                                          alignment: "center",
                                       };
                                       doc.styles.subtitle = {
                                          fontSize: 9,
                                          italics: true,
                                          alignment: "center",
                                       };

                                       // Add title and date
                                       doc.content.splice(0, 0, {
                                          text: [
                                             {
                                                text: "Kings Hostel - Maintenance Requests Report\n",
                                                style: "title",
                                             },
                                             {
                                                text: `Generated on: ${new Date().toLocaleString()}`,
                                                style: "subtitle",
                                             },
                                          ],
                                          margin: [0, 0, 0, 10],
                                       });

                                       // Only set headerRows if table and body exist
                                       if (
                                          doc.content[1] &&
                                          doc.content[1].table &&
                                          doc.content[1].table.body &&
                                          doc.content[1].table.body.length > 0
                                       ) {
                                          doc.content[1].table.headerRows = 1;
                                       }

                                       // Add footer with page numbers
                                       doc.footer = function (
                                          currentPage,
                                          pageCount
                                       ) {
                                          return {
                                             text: `Page ${currentPage} of ${pageCount}`,
                                             alignment: "center",
                                             fontSize: 8,
                                             margin: [0, 10, 0, 0],
                                          };
                                       };
                                    } catch (e) {
                                       console.error(
                                          "Error customizing PDF:",
                                          e
                                       );
                                    }
                                 },
                              },
                           ],
                        },
                     ],
                  },
               ],
            },
         },
         columns: [
            { data: "request_id" },
            { data: "student_name" },
            { data: "room_number" },
            { data: "issue_type" },
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
               targets: 1, // Student
               render: function (data) {
                  return `<span>${data}</span>`;
               },
            },
            {
               targets: 2, // Room
               render: function (data, type, full) {
                  if (full.room_number || full.building) {
                     const roomNumber = full.room_number || "Not specified";
                     const building = full.building || "Not specified";
                     return `
                        <div class="d-flex flex-column">
                           <span>${roomNumber}</span>
                           <small class="text-muted">${building}</small>
                        </div>
                     `;
                  }
                  return "Not specified";
               },
            },
            {
               targets: 4, // Priority
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
               targets: 5, // Status
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
               targets: 6, // Request Date
               render: function (data) {
                  return moment(data).format("MMM D, YYYY");
               },
            },
            {
               targets: 7, // Actions
               searchable: false,
               orderable: false,
               render: function (data, type, full) {
                  return `
                     <div class="d-flex align-items-center gap-2">
                        <a href="javascript:;" class="btn btn-sm btn-icon view-maintenance-details" 
                           data-request-id="${full.request_id}"
                           data-bs-toggle="tooltip" 
                           title="View details">
                           <i class="bx bx-show icon-md"></i>
                        </a>
                        <div class="dropdown">
                           <a href="javascript:;" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" 
                              data-bs-toggle="dropdown" 
                              title="Change status">
                              <i class="bx bx-cog icon-md text-primary"></i>
                           </a>
                           <div class="dropdown-menu dropdown-menu-end">
                              <a href="javascript:;" class="dropdown-item change-status" data-request-id="${full.request_id}" data-status="Pending">Pending</a>
                              <a href="javascript:;" class="dropdown-item change-status" data-request-id="${full.request_id}" data-status="Assigned">Assigned</a>
                              <a href="javascript:;" class="dropdown-item change-status" data-request-id="${full.request_id}" data-status="In-Progress">In-Progress</a>
                              <a href="javascript:;" class="dropdown-item change-status" data-request-id="${full.request_id}" data-status="Completed">Completed</a>
                              <a href="javascript:;" class="dropdown-item change-status" data-request-id="${full.request_id}" data-status="Rejected">Rejected</a>
                           </div>
                        </div>
                        <a href="javascript:;" class="btn btn-sm btn-icon add-response-btn" 
                           data-request-id="${full.request_id}"
                           data-bs-toggle="modal" 
                           data-bs-target="#addResponseModal"
                           title="Add response">
                           <i class="bx bx-comment-add icon-md text-info"></i>
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
            }

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(
               document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Search box
            $("#maintenanceSearch").on("keyup", function () {
               api.search(this.value).draw();
            });

            // Type filter
            $("#typeFilter").on("change", function () {
               const val = $(this).val();
               api.column(3)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Priority filter
            $("#priorityFilter").on("change", function () {
               const val = $(this).val();
               api.column(4)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Status filter
            $("#statusFilter").on("change", function () {
               const val = $(this).val();
               api.column(5)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Refresh table
            $(".refresh-table").on("click", function () {
               api.ajax.reload();
            });

            // View details
            $(document).on("click", ".view-maintenance-details", function (e) {
               e.preventDefault();
               const requestId = $(this).data("request-id");

               $.ajax({
                  url: `/admin/maintenance/${requestId}`,
                  method: "GET",
                  success: function (data) {
                     if (data.error) {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: data.error,
                        });
                        return;
                     }

                     const details = data.details;
                     $("#modalRequestId").text(details.request_id);
                     $("#modalStudentName").text(
                        details.student_name || "Not specified"
                     );
                     $("#modalIssueType").text(details.issue_type);
                     $("#modalRequestRoom").text(
                        details.room_number
                           ? `${details.room_number} - ${details.building}`
                           : "Not specified"
                     );
                     $("#modalRequestDescription").text(details.description);
                     $("#modalRequestPriority").html(
                        `<span class="badge bg-label-${
                           details.priority === "Emergency"
                              ? "danger"
                              : details.priority === "High"
                              ? "warning"
                              : details.priority === "Medium"
                              ? "info"
                              : "success"
                        }">${details.priority}</span>`
                     );
                     $("#modalRequestStatus").html(
                        `<span class="badge bg-label-${
                           details.status === "Completed"
                              ? "success"
                              : details.status === "Rejected"
                              ? "danger"
                              : details.status === "In-Progress"
                              ? "primary"
                              : details.status === "Assigned"
                              ? "info"
                              : "warning"
                        }">${details.status}</span>`
                     );
                     $("#modalRequestDate").text(
                        moment(details.request_date).format(
                           "MMM D, YYYY h:mm A"
                        )
                     );
                     $("#modalRequestDateTimeline").text(
                        moment(details.request_date).format(
                           "MMM D, YYYY h:mm A"
                        )
                     );
                     $("#modalSubmittedTimeAgo").text(
                        `Submitted ${moment(details.request_date).fromNow()}`
                     );

                     const responses = details.responses || [];
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
                           '<p class="text-muted">No responses yet.</p>'
                        );
                     }

                     $("#maintenanceDetailsModal").modal("show");
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

            // Change status
            $(document).on("click", ".change-status", function (e) {
               e.preventDefault();
               const requestId = $(this).data("request-id");
               const status = $(this).data("status");

               Swal.fire({
                  title: "Are you sure?",
                  text: `Change request #${requestId} status to ${status}?`,
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Yes, change it!",
                  cancelButtonText: "No, cancel!",
               }).then((result) => {
                  if (result.isConfirmed) {
                     $.ajax({
                        url: "/admin/maintenance/update-status",
                        method: "POST",
                        data: {
                           request_id: requestId,
                           status: status,
                           csrf: csrfToken,
                        },
                        success: function (response) {
                           if (response.success) {
                              Swal.fire({
                                 icon: "success",
                                 title: "Success",
                                 text: "Request status updated successfully!",
                                 timer: 2000,
                              }).then(() => {
                                 api.ajax.reload();
                              });
                           } else {
                              Swal.fire({
                                 icon: "error",
                                 title: "Error",
                                 text:
                                    response.error || "Failed to update status",
                              });
                           }
                        },
                        error: function () {
                           Swal.fire({
                              icon: "error",
                              title: "Error",
                              text: "Request failed",
                           });
                        },
                     });
                  }
               });
            });

            // Add response from DataTable
            $(document).on(
               "click",
               ".add-response-btn[data-request-id]",
               function () {
                  const requestId = $(this).data("request-id");
                  console.log(
                     "DataTable Add Response - Request ID:",
                     requestId
                  );
                  $("#responseRequestId").val(requestId);
                  $("#addResponseModal").modal("show");
               }
            );

            // Add response from View Details Modal
            $(document).on(
               "click",
               "#maintenanceDetailsModal .add-response-btn",
               function () {
                  const requestId = $("#modalRequestId").text();
                  console.log("Modal Add Response - Request ID:", requestId);
                  $("#responseRequestId").val(requestId);
                  $("#addResponseModal").modal("show");
                  $("#maintenanceDetailsModal").modal("hide"); // Close details modal
               }
            );

            // Handle response form submission
            $("#addResponseForm").on("submit", function (e) {
               e.preventDefault();
               const formData = $(this).serializeArray();
               console.log("Form Data:", formData);
               const serializedData = formData.concat({
                  name: "csrf",
                  value: csrfToken,
               });

               $.ajax({
                  url: "/admin/maintenance/add-response",
                  method: "POST",
                  data: serializedData,
                  success: function (response) {
                     if (response.success) {
                        Swal.fire({
                           icon: "success",
                           title: "Success",
                           text: "Response added successfully!",
                           timer: 2000,
                        }).then(() => {
                           $("#addResponseModal").modal("hide");
                           $("#addResponseForm")[0].reset();
                           api.ajax.reload();
                        });
                     } else {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: response.error || "Failed to add response",
                        });
                     }
                  },
                  error: function (xhr, status, error) {
                     console.error("AJAX Error:", status, error);
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Submission failed",
                     });
                  },
               });
            });
         },
      });
   }
})();
