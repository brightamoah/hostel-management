(function () {
   "use strict";

   const dt_visitor_table = document.querySelector(".datatables-visitors");
   let adminAccess = null; // Store admin access level information

   // Function to update access level indicator
   function updateAccessLevelIndicator(accessData) {
      const indicator = document.getElementById("accessLevelIndicator");
      if (indicator && accessData) {
         if (accessData.is_super_admin) {
            indicator.innerHTML =
               '<span class="badge bg-primary">Super Admin - Full Access</span>';
         } else {
            indicator.innerHTML =
               '<span class="badge bg-secondary">Regular Admin - Limited Access</span>';
         }
      }
   }

   // Retrieve CSRF token
   const csrfToken =
      document
         .querySelector('meta[name="csrf-token"]')
         ?.getAttribute("content") || "";

   if (dt_visitor_table) {
      const dt = new DataTable(dt_visitor_table, {
         ajax: {
            url: "/admin/visitors-data",
            data: function (d) {
               // Add date filter to AJAX request
               d.dateFilter = $("#dateFilter").val();
            },
            dataSrc: function (json) {
               // Store admin access information if available
               if (json.admin_access) {
                  adminAccess = json.admin_access;
                  updateAccessLevelIndicator(adminAccess);
               }
               return json.data || json;
            },
         },
         layout: {
            topStart: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     pageLength: {
                        menu: [7, 10, 25, 50, 100],
                        text: "Show_MENU_entries",
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
                        placeholder: "Search Visitor",
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
                                    columns: [1, 2, 3, 4, 5, 6, 7], // Exclude control (0) and actions (8) columns
                                 },
                              },
                              {
                                 extend: "excel",
                                 text: '<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-export me-2"></i>Excel</span>',
                                 className: "dropdown-item",
                                 exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7],
                                 },
                              },
                              {
                                 extend: "pdf",
                                 text: '<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-pdf me-2"></i>Pdf</span>',
                                 className: "dropdown-item",
                                 exportOptions: {
                                    columns: [1, 2, 3, 4, 5, 6, 7],
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
            { data: null, defaultContent: "" },
            { data: "visitor_name" },
            {
               data: "student_name",
               render: function (data, type, full) {
                  return `
                        <div class="d-flex flex-column">
                            <span class="fw-medium">${data || "N/A"}</span>
                            <small>${full.student_email || ""}</small>
                            <small>Room: ${
                               full.room_number
                                  ? full.room_number +
                                    " (" +
                                    full.building +
                                    ")"
                                  : "N/A"
                            }</small>
                        </div>
                    `;
               },
            },
            { data: "relation" },
            { data: "visit_date" },
            {
               data: "check_in_time",
               render: function (data) {
                  return data
                     ? `<div class="text-heading">
                           <span>${new Date(data).toLocaleTimeString("en-GH", {
                              hour: "2-digit",
                              minute: "2-digit",
                              second: "2-digit",
                              hour12: false,
                           })}</span>
                        </div>`
                     : "-";
               },
            },
            {
               data: "check_out_time",
               render: function (data) {
                  return data
                     ? `<div class="text-heading">
                           <span class="text-center">${new Date(
                              data
                           ).toLocaleTimeString("en-GH", {
                              hour: "2-digit",
                              minute: "2-digit",
                              second: "2-digit",
                              hour12: false,
                           })}</span>
                        </div>`
                     : "-";
               },
            },
            {
               data: "status",
               render: function (data) {
                  const statusObj = {
                     Pending: { class: "bg-label-warning", title: "Pending" },
                     Approved: { class: "bg-label-info", title: "Approved" },
                     "Checked-In": {
                        class: "bg-label-success",
                        title: "Checked-In",
                     },
                     "Checked-Out": {
                        class: "bg-label-primary",
                        title: "Checked-Out",
                     },
                     Cancelled: {
                        class: "bg-label-danger",
                        title: "Cancelled",
                     },
                     Denied: { class: "bg-label-danger", title: "Denied" },
                  };
                  const statusInfo = statusObj[data] || {
                     class: "bg-label-secondary",
                     title: data,
                  };
                  return `<span class="badge ${statusInfo.class}">${statusInfo.title}</span>`;
               },
            },
            {
               data: null,
               defaultContent: "",
               render: function (data, type, full, meta) {
                  return `
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" class="btn btn-icon view-visitor" data-id="${full.visitor_id}" data-bs-toggle="modal" data-bs-target="#visitorModal">
                                <i class="bx bx-show icon-md"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon admin-action" data-id="${full.visitor_id}" data-action="check_in" title="Check-In">
                                <i class="bx bx-log-in-circle icon-md text-success"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon admin-action" data-id="${full.visitor_id}" data-action="check_out" title="Check-Out">
                                <i class="bx bx-log-out-circle icon-md text-primary"></i>
                            </a>
                            <a href="javascript:;" class="btn btn-icon delete-visitor text-danger" data-id="${full.visitor_id}">
                                <i class="bx bx-trash icon-md"></i>
                            </a>
                        </div>
                    `;
               },
            },
         ],
         columnDefs: [
            {
               className: "control",
               searchable: false,
               orderable: false,
               responsivePriority: 2,
               targets: 0,
               render: function () {
                  return "";
               },
            },
            {
               targets: 1,
               responsivePriority: 1,
               render: function (data, type, full) {
                  const name = full["visitor_name"];
                  const initials = (name.match(/\b\w/g) || [])
                     .map((char) => char.toUpperCase())
                     .join("")
                     .substring(0, 2);
                  const avatar = `<span class="avatar-initial rounded-circle bg-label-primary">${initials}</span>`;
                  return `
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="avatar-wrapper">
                                    <div class="avatar avatar-sm me-3">${avatar}</div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">${name}</span>
                                    <small>${full["phone_number"]}</small>
                                </div>
                            </div>
                        `;
               },
            },
            {
               targets: 2,
               render: function (data, type, full) {
                  const studentName = full["student_name"] || "N/A";
                  const studentEmail = full["student_email"] || "";
                  return `
                            <div class="d-flex flex-column">
                                <span class="fw-medium">${studentName}</span>
                                <small>${studentEmail}</small>
                            </div>
                        `;
               },
            },
            {
               targets: 3,
               render: function (data) {
                  return `<span class="text-heading">${data}</span>`;
               },
            },
            {
               targets: 4,
               render: function (data) {
                  return `<span class="text-heading">${data}</span>`;
               },
            },
            {
               targets: 5,
               render: function (data) {
                  return data ? new Date(data).toLocaleString() : "-";
               },
            },
            {
               targets: 6,
               render: function (data) {
                  return data ? new Date(data).toLocaleString() : "-";
               },
            },
            {
               targets: 7,
               render: function (data) {
                  const statusObj = {
                     Pending: { class: "bg-label-warning", title: "Pending" },
                     Approved: { class: "bg-label-info", title: "Approved" },
                     "Checked-In": {
                        class: "bg-label-success",
                        title: "Checked-In",
                     },
                     "Checked-Out": {
                        class: "bg-label-primary",
                        title: "Checked-Out",
                     },
                     Cancelled: {
                        class: "bg-label-danger",
                        title: "Cancelled",
                     },
                     Denied: { class: "bg-label-danger", title: "Denied" },
                  };
                  const statusInfo = statusObj[data] || {
                     class: "bg-label-secondary",
                     title: data,
                  };
                  return `<span class="badge ${statusInfo.class}">${statusInfo.title}</span>`;
               },
            },
            {
               targets: 8,
               orderable: false,
               searchable: false,
               render: function (data, type, full, meta) {
                  return `
                            <div class="d-flex align-items-center">
                                <a href="javascript:;" class="btn btn-icon view-visitor" data-id="${full["visitor_id"]}" data-bs-toggle="modal" data-bs-target="#visitorModal">
                                    <i class="bx bx-show icon-md"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon admin-action" data-id="${full["visitor_id"]}" data-action="check_in" title="Check-In">
                                    <i class="bx bx-log-in-circle icon-md text-success"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon admin-action" data-id="${full["visitor_id"]}" data-action="check_out" title="Check-Out">
                                    <i class="bx bx-log-out-circle icon-md text-primary"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon delete-visitor text-danger" data-id="${full["visitor_id"]}">
                                    <i class="bx bx-trash icon-md"></i>
                                </a>
                            </div>
                        `;
               },
            },
         ],
         order: [[4, "desc"]],
         responsive: true,
         language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Search Visitor....",
            paginate: {
               next: '<i class="icon-base bx bx-chevron-right icon-18px"></i>',
               previous:
                  '<i class="icon-base bx bx-chevron-left icon-18px"></i>',
            },
         },
         initComplete: function () {
            // Initialize Select2 for filter dropdowns
            $("#statusFilter").select2({
               placeholder: "All Statuses",
               allowClear: true,
               width: "100%",
            });

            $("#dateFilter").select2({
               placeholder: "All Dates",
               allowClear: true,
               width: "100%",
            });

            // Status filter
            $("#statusFilter").on("change", function () {
               const val = $(this).val();
               dt.column(7)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Date filter
            $("#dateFilter").on("change", function () {
               dt.ajax.reload(); // Reload DataTable with new date filter
            });

            // Search input
            $("#searchInput").on("keyup", function () {
               dt.search(this.value).draw();
            });
         },
      });

      // View visitor action (populate modal)
      dt_visitor_table.addEventListener("click", function (e) {
         if (e.target.closest(".view-visitor")) {
            const visitorId = e.target
               .closest(".view-visitor")
               .getAttribute("data-id");

            // Fetch visitor details via AJAX
            fetch(`/admin/visitor/${visitorId}`, {
               method: "GET",
               headers: { "Content-Type": "application/json" },
            })
               .then((response) => response.json())
               .then((data) => {
                  if (data.success) {
                     const visitor = data.data;

                     // Fetch visitor logs separately
                     fetch(`/visitor/logs/${visitorId}`, {
                        method: "GET",
                        headers: { "Content-Type": "application/json" },
                     })
                        .then((response) => response.json())
                        .then((logData) => {
                           console.log("Visitor Logs:", logData); // Debug logs
                           const logs = logData.data || [];

                           // Calculate initials
                           const nameParts = visitor.visitor_name.split(" ");
                           const initials = nameParts
                              .map((part) => part.charAt(0).toUpperCase())
                              .join("")
                              .substring(0, 2);

                           // Populate modal fields
                           document.getElementById(
                              "visitorInitials"
                           ).textContent = initials;
                           document.getElementById("visitorName").textContent =
                              visitor.visitor_name;
                           document.getElementById(
                              "visitorRelation"
                           ).textContent = visitor.relation;
                           document.getElementById("visitorId").textContent =
                              visitor.visitor_id;
                           document.getElementById("visitorPhone").textContent =
                              visitor.phone_number;
                           document.getElementById(
                              "visitorVisitDate"
                           ).textContent = visitor.visit_date;

                           // Status with appropriate badge
                           const statusElement =
                              document.getElementById("visitorStatus");
                           statusElement.textContent = visitor.status;
                           statusElement.className = `badge ${getStatusBadgeClass(
                              visitor.status
                           )}`;

                           document.getElementById(
                              "visitorPurpose"
                           ).textContent = visitor.purpose;
                           document.getElementById(
                              "visitorStudentId"
                           ).textContent =
                              visitor.student_unique_id || visitor.student_id;
                           document.getElementById(
                              "visitorStudentName"
                           ).textContent = visitor.student_name || "N/A";
                           document.getElementById(
                              "visitorStudentEmail"
                           ).textContent = visitor.student_email || "N/A";
                           document.getElementById(
                              "visitorStudentPhone"
                           ).textContent = visitor.student_phone || "N/A";
                           document.getElementById(
                              "visitorBuilding"
                           ).textContent = visitor.building || "N/A";
                           document.getElementById("visitorRoom").textContent =
                              visitor.room_number
                                 ? `${visitor.room_number} (${visitor.room_type})`
                                 : "N/A";

                           // Populate check-in/check-out logs
                           const logsTable =
                              document.getElementById("visitorLogs");
                           logsTable.innerHTML = "";
                           if (logs && logs.length > 0) {
                              // In the visitor logs section of admin-visitor-list.js
                              logs.forEach((log) => {
                                 const checkInTime = log.check_in_time
                                    ? new Date(
                                         log.check_in_time
                                      ).toLocaleString()
                                    : "-";
                                 const checkOutTime = log.check_out_time
                                    ? new Date(
                                         log.check_out_time
                                      ).toLocaleString()
                                    : "-";

                                 // Calculate duration if both times exist
                                 let duration = "-";
                                 if (log.check_in_time && log.check_out_time) {
                                    try {
                                       const diff =
                                          new Date(log.check_out_time) -
                                          new Date(log.check_in_time);
                                       const hours = Math.floor(
                                          diff / (1000 * 60 * 60)
                                       );
                                       const minutes = Math.floor(
                                          (diff % (1000 * 60 * 60)) /
                                             (1000 * 60)
                                       );
                                       const seconds = Math.floor(
                                          (diff % (1000 * 60)) / 1000
                                       );

                                       // Format duration based on length
                                       if (hours > 0) {
                                          duration = `${hours}h ${minutes}m`;
                                       } else if (minutes > 0) {
                                          duration = `${minutes}m ${seconds}s`;
                                       } else {
                                          duration = `${seconds}s`;
                                       }
                                    } catch (e) {
                                       console.error(
                                          "Error calculating duration:",
                                          e
                                       );
                                       duration = "Invalid";
                                    }
                                 }

                                 const row = document.createElement("tr");
                                 row.innerHTML = `
        <td>${checkInTime}</td>
        <td>${checkOutTime}</td>
        <td>${duration}</td>
    `;
                                 logsTable.appendChild(row);
                              });
                           } else {
                              logsTable.innerHTML =
                                 '<tr><td colspan="3" class="text-center">No check-in/check-out records available</td></tr>';
                           }

                           // Populate actions based on status
                           // Populate actions based on status
                           const actionsDiv =
                              document.getElementById("visitorActions");
                           actionsDiv.innerHTML = "";
                           const today = new Date().toISOString().split("T")[0];
                           const isSameDay = visitor.visit_date === today;
                           switch (visitor.status) {
                              case "Pending":
                                 actionsDiv.innerHTML = `
                                <a href="javascript:;" class="btn btn-primary me-2 admin-action" data-id="${visitor.visitor_id}" data-action="approve">Approve</a>
                                <a href="javascript:;" class="btn btn-danger admin-action" data-id="${visitor.visitor_id}" data-action="deny">Deny</a>
                            `;
                                 break;
                              case "Approved":
                              case "Checked-Out":
                                 if (isSameDay) {
                                    actionsDiv.innerHTML = `
                                    <a href="javascript:;" class="btn btn-success admin-action" data-id="${visitor.visitor_id}" data-action="check_in">Check-In</a>
                                `;
                                 } else {
                                    actionsDiv.innerHTML = `<span class="text-muted">Check-in available on visit date (${visitor.visit_date})</span>`;
                                 }
                                 break;
                              case "Checked-In":
                                 if (isSameDay) {
                                    actionsDiv.innerHTML = `
                                    <a href="javascript:;" class="btn btn-primary admin-action" data-id="${visitor.visitor_id}" data-action="check_out">Check-Out</a>
                                `;
                                 } else {
                                    actionsDiv.innerHTML = `<span class="text-muted">Check-out available on visit date (${visitor.visit_date})</span>`;
                                 }
                                 break;
                              case "Cancelled":
                              case "Denied":
                                 actionsDiv.innerHTML = `<span class="text-muted">No actions available</span>`;
                                 break;
                              default:
                                 actionsDiv.innerHTML = `<span class="text-muted">Invalid status</span>`;
                                 break;
                           }
                        })
                        .catch((error) => {
                           console.error("Error fetching logs:", error);
                           Swal.fire({
                              icon: "error",
                              title: "Error",
                              text: "Error fetching logs: " + error.message,
                              confirmButtonColor: "#3085d6",
                           });
                           // Populate logs table with error message
                           const logsTable =
                              document.getElementById("visitorLogs");
                           logsTable.innerHTML =
                              '<tr><td colspan="3" class="text-center">Error loading check-in/check-out records</td></tr>';
                        });
                  } else {
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message || "Failed to fetch visitor details",
                        confirmButtonColor: "#3085d6",
                     });
                  }
               })
               .catch((error) => {
                  console.error("Error fetching visitor details:", error);
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Error: " + error.message,
                     confirmButtonColor: "#3085d6",
                  });
               });

            // Helper function for status badge classes
            function getStatusBadgeClass(status) {
               const statusClasses = {
                  Pending: "bg-label-warning",
                  Approved: "bg-label-info",
                  "Checked-In": "bg-label-success",
                  "Checked-Out": "bg-label-primary",
                  Cancelled: "bg-label-danger",
                  Denied: "bg-label-danger",
               };
               return statusClasses[status] || "bg-label-secondary";
            }
         }
      });

      // Handle admin actions (approve, deny, check-in, check-out)
      document.addEventListener("click", function (e) {
         if (e.target.closest(".admin-action")) {
            const visitorId = e.target
               .closest(".admin-action")
               .getAttribute("data-id");
            const action = e.target
               .closest(".admin-action")
               .getAttribute("data-action");
            const actionText =
               action === "approve"
                  ? "approve this visitor request"
                  : action === "deny"
                  ? "deny this visitor request"
                  : action === "check_in"
                  ? "check in this visitor"
                  : "check out this visitor";

            Swal.fire({
               title: "Are you sure?",
               text: `Do you want to ${actionText}?`,
               icon: "warning",
               showCancelButton: true,
               confirmButtonColor: "#3085d6",
               cancelButtonColor: "#d33",
               confirmButtonText: `Yes, ${action.replace("_", " ")}!`,
            }).then((result) => {
               if (result.isConfirmed) {
                  fetch(`/admin/visitor/${visitorId}/${action}`, {
                     method: "POST",
                     headers: { "Content-Type": "application/json" },
                     body: JSON.stringify({
                        visitor_id: visitorId,
                        csrf: csrfToken,
                     }),
                  })
                     .then((response) => response.json())
                     .then((data) => {
                        if (data.success) {
                           Swal.fire({
                              icon: "success",
                              title: "Success",
                              text: data.message,
                              confirmButtonColor: "#3085d6",
                           }).then(() => {
                              $("#visitorModal").modal("hide");
                              dt.ajax.reload();
                              location.reload();
                           });
                        } else {
                           Swal.fire({
                              icon: "error",
                              title: "Error",
                              text: data.message || "Action failed",
                              confirmButtonColor: "#3085d6",
                           });
                        }
                     })
                     .catch((error) => {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: "Error: " + error.message,
                           confirmButtonColor: "#3085d6",
                        });
                     });
               }
            });
         }

         // Delete visitor action
         if (e.target.closest(".delete-visitor")) {
            const visitorId = e.target
               .closest(".delete-visitor")
               .getAttribute("data-id");
            Swal.fire({
               title: "Are you sure?",
               text: `Do you want to delete visitor with ID: ${visitorId}?`,
               icon: "warning",
               showCancelButton: true,
               confirmButtonColor: "#3085d6",
               cancelButtonColor: "#d33",
               confirmButtonText: "Yes, delete it!",
            }).then((result) => {
               if (result.isConfirmed) {
                  fetch(`/visitor/delete/${visitorId}`, {
                     method: "POST",
                     headers: { "Content-Type": "application/json" },
                     body: JSON.stringify({
                        visitor_id: visitorId,
                        csrf: csrfToken,
                     }),
                  })
                     .then((response) => response.json())
                     .then((data) => {
                        if (data.success) {
                           dt.ajax.reload();
                           Swal.fire({
                              icon: "success",
                              title: "Deleted",
                              text: "Visitor deleted successfully!",
                              confirmButtonColor: "#3085d6",
                           }).then(() => {
                              location.reload();
                           });
                        } else {
                           Swal.fire({
                              icon: "error",
                              title: "Error",
                              text: data.message || "Unknown error",
                              confirmButtonColor: "#3085d6",
                           });
                        }
                     })
                     .catch((error) => {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: "Error: " + error.message,
                           confirmButtonColor: "#3085d6",
                        });
                     });
               }
            });
         }
      });
   }
})();
