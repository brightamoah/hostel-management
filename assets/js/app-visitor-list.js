(function () {
   "use strict";

   const dt_visitor_table = document.querySelector(".datatables-visitors");

   // Retrieve CSRF token
   const csrfToken =
      document
         .querySelector('meta[name="csrf-token"]')
         ?.getAttribute("content") || "";

   let originalEditVisitorData = null;

   if (dt_visitor_table) {
      const dt = new DataTable(dt_visitor_table, {
         ajax: {
            url: "/student/visitors-data",
            data: function (d) {
               // Add date filter to AJAX request
               d.dateFilter = $("#dateFilter").val();
            },
         },
         layout: {
            topStart: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     pageLength: {
                        menu: [7, 10, 25, 50, 100],
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
            { data: null, defaultContent: "" },
            // { data: "id", orderable: false },
            { data: "full_name" },
            { data: "role" },
            { data: "visit_date" },
            { data: "check_in" },
            { data: "check_out" },
            { data: "status" },
            { data: null, defaultContent: "" },
         ],
         columnDefs: [
            {
               className: "control",
               searchable: true,
               orderable: true,
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
                  const name = full["full_name"];
                  const initials = (name.match(/\b\w/g) || [])
                     .map((char) => char.toUpperCase())
                     .join("")
                     .substring(0, 2);
                  const avatar = `<span class="avatar-initial rounded-circle bg-label-primary">${initials}</span>`;

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

                  const statusInfo = statusObj[full["status"]] || {
                     class: "bg-label-secondary",
                     title: full["status"],
                  };

                  return `
                <div class="d-flex justify-content-start align-items-center">
                    <div class="avatar-wrapper">
                        <div class="avatar avatar-sm me-3">${avatar}</div>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fw-medium">${name}</span>
                        <small class="text-muted">${full["email"]}</small>
                        <span class="badge ${statusInfo.class} mt-1 d-md-none" style="width: fit-content; font-size: 0.65rem;">${statusInfo.title}</span>
                    </div>
                </div>
            `;
               },
            },
            {
               targets: 2,
               render: function (data) {
                  return `<span class="text-heading">${data}</span>`;
               },
            },
            {
               targets: 3,
               responsivePriority: 3,
               render: function (data) {
                  return `<span class="text-heading">${data}</span>`;
               },
            },
            {
               targets: 4,
               render: function (data) {
                  return data
                     ? `<span class="text-heading">${new Date(
                          data
                       ).toLocaleTimeString()}</span>`
                     : "-";
               },
            },
            {
               targets: 5,
               render: function (data) {
                  return data
                     ? `<span class="text-heading">${new Date(
                          data
                       ).toLocaleTimeString()}</span>`
                     : "-";
               },
            },
            {
               targets: 6,
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
               targets: 7,
               orderable: false,
               searchable: false,
               render: function (data, type, full, meta) {
                  return `
         <div class="d-flex align-items-center">
            <a href="javascript:;" 
               class="btn btn-icon view-visitor"
               data-id="${full["id"]}"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               data-bs-title="View Details">
               <i class="bx bx-show icon-md"></i>
            </a>
            <a href="javascript:;" 
               class="btn btn-icon delete-visitor text-danger"
               data-id="${full["id"]}"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               data-bs-title="Delete Visitor">
               <i class="bx bx-trash icon-md"></i>
            </a>
         </div>
      `;
               },
            },
         ],
         order: [[3, "desc"]],
         buttons: [
            {
               extend: "collection",
               className: "btn btn-label-secondary dropdown-toggle",
               text: '<span class="d-flex align-items-center gap-2"><i class="icon-base bx bx-export icon-sm"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
               buttons: [
                  {
                     extend: "print",
                     text: `<span class="d-flex align-items-center"><i class="icon-base bx bx-printer me-2"></i>Print</span>`,
                     className: "dropdown-item",
                     exportOptions: { columns: [2, 3, 4, 5, 6] },
                     customize: function (win) {
                        win.document.body.style.color =
                           config.colors.headingColor;
                        win.document.body.style.borderColor =
                           config.colors.borderColor;
                        win.document.body.style.backgroundColor =
                           config.colors.bodyBg;
                        const table = win.document.body.querySelector("table");
                        table.classList.add("compact");
                        table.style.color = "inherit";
                        table.style.borderColor = "inherit";
                        table.style.backgroundColor = "inherit";
                     },
                  },
                  {
                     extend: "csv",
                     text: `<span class="d-flex align-items-center"><i class="icon-base bx bx-file me-2"></i>Csv</span>`,
                     className: "dropdown-item",
                     exportOptions: { columns: [3, 4, 5, 6, 7] },
                  },
                  {
                     extend: "excel",
                     text: `<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-export me-2"></i>Excel</span>`,
                     className: "dropdown-item",
                     exportOptions: { columns: [3, 4, 5, 6, 7] },
                  },
                  {
                     extend: "pdf",
                     text: `<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-pdf me-2"></i>Pdf</span>`,
                     className: "dropdown-item",
                     exportOptions: { columns: [3, 4, 5, 6, 7] },
                  },
                  {
                     extend: "copy",
                     text: `<i class="icon-base bx bx-copy me-1"></i>Copy`,
                     className: "dropdown-item",
                     exportOptions: { columns: [3, 4, 5, 6, 7] },
                  },
               ],
            },
         ],
         responsive: true,
         language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Search User....",
            paginate: {
               next: '<i class="icon-base bx bx-chevron-right icon-18px"></i>',
               previous:
                  '<i class="icon-base bx bx-chevron-left icon-18px"></i>',
            },
         },
         initComplete: function () {
            if ($.fn.select2) {
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
            }

            let tooltipList = [];

            function initializeTooltips() {
               // Dispose existing tooltips first
               tooltipList.forEach(function (tooltip) {
                  if (
                     tooltip &&
                     tooltip._element &&
                     typeof tooltip.dispose === "function"
                  ) {
                     try {
                        tooltip.dispose();
                     } catch (e) {
                        // Ignore disposal errors
                     }
                  }
               });
               tooltipList = []; // Clear the array

               // Initialize new tooltips
               const tooltipTriggerList = [].slice.call(
                  document.querySelectorAll('[data-bs-toggle="tooltip"]')
               );
               tooltipList = tooltipTriggerList.map(function (
                  tooltipTriggerEl
               ) {
                  return new bootstrap.Tooltip(tooltipTriggerEl, {
                     trigger: "hover",
                     delay: { show: 500, hide: 100 },
                     customClass: "custom-tooltip",
                  });
               });
            }

            initializeTooltips();

            // Re-initialize tooltips after table draw
            dt.on("draw", function () {
               initializeTooltips();
            });

            $("#visitorSearch").on("keyup", function () {
               dt.search(this.value).draw();
            });

            $("#statusFilter").on("change", function () {
               const val = $(this).val();
               dt.column(6)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            $("#dateFilter").on("change", function () {
               dt.ajax.reload(); // Fixed syntax error
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
            fetch(`/student/visitor/${visitorId}`, {
               method: "GET",
               headers: { "Content-Type": "application/json" },
            })
               .then((response) => response.json())
               .then((data) => {
                  if (data.success) {
                     const visitor = data.data;

                     // Calculate initials
                     const nameParts = visitor.visitor_name.split(" ");
                     const initials = nameParts
                        .map((part) => part.charAt(0).toUpperCase())
                        .join("")
                        .substring(0, 2);

                     // Populate modal fields
                     document.getElementById("visitorInitials").textContent =
                        initials;
                     document.getElementById("visitorName").textContent =
                        visitor.visitor_name;
                     document.getElementById("visitorRelation").textContent =
                        visitor.relation;
                     document
                        .getElementById("visitorId")
                        .querySelector("span").textContent = visitor.visitor_id;
                     document.getElementById("visitorPhone").textContent =
                        visitor.phone_number;
                     document.getElementById("visitorVisitDate").textContent =
                        visitor.visit_date;
                     document.getElementById("visitorCheckIn").textContent =
                        visitor.check_in_time || "N/A";
                     document.getElementById("visitorCheckOut").textContent =
                        visitor.check_out_time || "N/A";
                     document.getElementById("visitorStatus").textContent =
                        visitor.status;
                     document.getElementById(
                        "visitorStatus"
                     ).className = `badge ${
                        visitor.status === "Checked-In"
                           ? "bg-label-success"
                           : visitor.status === "Checked-Out"
                           ? "bg-label-primary"
                           : visitor.status === "Approved"
                           ? "bg-label-info"
                           : visitor.status === "Denied"
                           ? "bg-label-danger"
                           : visitor.status === "Cancelled"
                           ? "bg-label-danger"
                           : visitor.status === "Pending"
                           ? "bg-label-warning"
                           : "bg-label-secondary"
                     }`;
                     document.getElementById("visitorPurpose").textContent =
                        visitor.purpose;
                     document.getElementById("visitorStudentId").textContent =
                        visitor.student_id;

                     // Populate actions based on status
                     const actionsDiv =
                        document.getElementById("visitorActions");
                     actionsDiv.innerHTML = "";
                     switch (visitor.status) {
                        case "Pending":
                           actionsDiv.innerHTML = `
                                    <a href="javascript:;" 
                        class="btn btn-primary me-4 edit-visitor" 
                        data-id="${visitor.visitor_id}"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        data-bs-title="Edit visitor details">
                        Edit Details
                     </a>
                     <a href="javascript:;" 
                        class="btn btn-label-danger cancel-visitor" 
                        data-id="${visitor.visitor_id}"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        data-bs-title="Cancel visitor request">
                        Cancel Visit Request
                     </a>
                           
                     `;
                           break;
                        case "Approved":
                           actionsDiv.innerHTML = `
                                   <a href="javascript:;" 
                        class="btn btn-primary me-4 edit-visitor" 
                        data-id="${visitor.visitor_id}"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        data-bs-title="Edit visitor details">
                        Edit
                     </a>
                     <a href="javascript:;" 
                        class="btn btn-label-danger cancel-visitor" 
                        data-id="${visitor.visitor_id}"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        data-bs-title="Cancel visitor request">
                        Cancel
                     </a>
                                `;
                           break;
                        case "Checked-In":
                        case "Checked-Out":
                        case "Cancelled":
                        case "Denied":
                           actionsDiv.innerHTML = `<span class="text-muted">No actions available</span>`;
                           break;
                        default:
                           actionsDiv.innerHTML = `<span class="text-muted">Invalid status</span>`;
                           break;
                     }

                     // Initialize tooltips for modal action buttons (with error handling)
                     setTimeout(() => {
                        const modalTooltips = [].slice.call(
                           actionsDiv.querySelectorAll(
                              '[data-bs-toggle="tooltip"]'
                           )
                        );
                        modalTooltips.forEach(function (tooltipTriggerEl) {
                           try {
                              new bootstrap.Tooltip(tooltipTriggerEl, {
                                 trigger: "hover",
                                 delay: { show: 500, hide: 100 },
                                 customClass: "custom-tooltip",
                              });
                           } catch (e) {
                              console.warn("Failed to initialize tooltip:", e);
                           }
                        });
                     }, 100);

                     $("#visitorModal").modal("show");
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
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Error: " + error.message,
                     confirmButtonColor: "#3085d6",
                  });
               });
         }
      });

      // Handle register visitor form submission
      const registerVisitorForm = document.getElementById(
         "registerVisitorForm"
      );
      if (registerVisitorForm) {
         registerVisitorForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Client-side validation for phone number
            const phoneNumber = formData.get("phone_number");
            if (!/^(\+233|0)\d{9}$/.test(phoneNumber)) {
               Swal.fire({
                  icon: "error",
                  title: "Invalid Phone Number",
                  text: "Phone number must be in +233XXXXXXXXX or 0XXXXXXXXX format",
                  confirmButtonColor: "#3085d6",
               });
               return;
            }

            // Add CSRF token to form data
            formData.append("csrf", csrfToken);

            fetch("/visitor/register", {
               method: "POST",
               body: formData,
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
                        $("#registerVisitorModal").modal("hide");
                        registerVisitorForm.reset();
                        dt.ajax.reload();
                     });
                  } else {
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message || "Failed to register visitor",
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
         });
      }

      // Handle edit visitor form submission
      const editVisitorForm = document.getElementById("editVisitorForm");
      if (editVisitorForm) {
         editVisitorForm.addEventListener("submit", function (e) {
            e.preventDefault();

            // Get current form values - add null checks
            const visitorNameEl = document.getElementById("editVisitorName");
            const relationEl = document.getElementById("editRelation");
            const phoneNumberEl = document.getElementById("editPhoneNumber");
            const visitDateEl = document.getElementById("editVisitDate");
            const purposeEl = document.getElementById("editPurpose");

            // Check if elements exist before accessing their values
            if (
               !visitorNameEl ||
               !relationEl ||
               !phoneNumberEl ||
               !visitDateEl ||
               !purposeEl
            ) {
               Swal.fire({
                  icon: "error",
                  title: "Form Error",
                  text: "Could not find form elements. Please refresh the page and try again.",
                  confirmButtonColor: "#3085d6",
               });
               return;
            }

            const visitor_name = visitorNameEl.value.trim();
            const relation = relationEl.value.trim();
            const phone_number = phoneNumberEl.value.trim();
            const visit_date = visitDateEl.value.trim();
            const purpose = purposeEl.value.trim();

            // Compare with original data
            if (
               originalEditVisitorData &&
               visitor_name === originalEditVisitorData.visitor_name &&
               relation === originalEditVisitorData.relation &&
               phone_number === originalEditVisitorData.phone_number &&
               visit_date === originalEditVisitorData.visit_date &&
               purpose === originalEditVisitorData.purpose
            ) {
               Swal.fire({
                  icon: "info",
                  title: "No Changes Detected",
                  text: "You have not made any changes to the visitor details.",
                  confirmButtonColor: "#3085d6",
               });
               return;
            }

            const formData = new FormData(this);
            const visitorId = formData.get("visitor_id");

            // Client-side validation for phone number
            const phoneNumber = formData.get("phone_number");
            if (!/^(\+233|0)\d{9}$/.test(phoneNumber)) {
               Swal.fire({
                  icon: "error",
                  title: "Invalid Phone Number",
                  text: "Phone number must be in +233XXXXXXXXX or 0XXXXXXXXX format",
                  confirmButtonColor: "#3085d6",
               });
               return;
            }

            // Validate visit date (today or future)
            const visitDate = formData.get("visit_date");
            const today = new Date().toISOString().split("T")[0];
            if (visitDate < today) {
               Swal.fire({
                  icon: "error",
                  title: "Invalid Visit Date",
                  text: "Visit date must be today or in the future",
                  confirmButtonColor: "#3085d6",
               });
               return;
            }

            // Add CSRF token to form data
            formData.append("csrf", csrfToken);

            fetch(`/visitor/edit/${visitorId}`, {
               method: "POST",
               body: formData,
            })
               .then((response) => response.json())
               .then((data) => {
                  if (data.success) {
                     let title = "Visitor Updated Successfully!";
                     let text = data.message;
                     let icon = "success";

                     // Show different styling if status was changed to pending
                     if (data.status_changed) {
                        icon = "warning";
                        title = "Visitor Updated - Approval Required";
                     }

                     Swal.fire({
                        icon: icon,
                        title: title,
                        text: text,
                        customClass: {
                           confirmButton: "btn btn-primary",
                        },
                        buttonsStyling: false,
                        timer: data.status_changed ? 8000 : 5000,
                        timerProgressBar: true,
                        showCloseButton: true,
                        confirmButtonColor: "#3085d6",
                     }).then(() => {
                        $("#editVisitorModal").modal("hide");
                        editVisitorForm.reset();
                        dt.ajax.reload();
                        $("#visitorModal").modal("hide");
                        originalEditVisitorData = null;
                     });
                  } else {
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message || "Failed to update visitor",
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
         });
      }

      // Use event delegation on the document for dynamically added buttons in the modal
      document.addEventListener("click", function (e) {
         // Cancel visitor action
         if (e.target.closest(".cancel-visitor")) {
            const visitorId = e.target
               .closest(".cancel-visitor")
               .getAttribute("data-id");
            Swal.fire({
               title: "Are you sure?",
               text: "Do you want to cancel this visitor request?",
               icon: "warning",
               showCancelButton: true,
               confirmButtonColor: "#3085d6",
               cancelButtonColor: "#d33",
               confirmButtonText: "Yes, cancel it!",
               cancelButtonText: "No, keep it",
            }).then((result) => {
               if (result.isConfirmed) {
                  fetch(`/visitor/cancel/${visitorId}`, {
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
                              title: "Cancelled",
                              text: "Visitor request cancelled successfully!",
                              confirmButtonColor: "#3085d6",
                           }).then(() => {
                              $("#visitorModal").modal("hide");
                           });
                        } else {
                           Swal.fire({
                              icon: "error",
                              title: "Error",
                              text:
                                 data.message ||
                                 "Failed to cancel visitor request",
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

         // Edit visitor action
         if (e.target.closest(".edit-visitor")) {
            const visitorId = e.target
               .closest(".edit-visitor")
               .getAttribute("data-id");

            // Fetch visitor details to populate edit form
            fetch(`/student/visitor/${visitorId}`, {
               method: "GET",
               headers: { "Content-Type": "application/json" },
            })
               .then((response) => response.json())
               .then((data) => {
                  if (data.success) {
                     const visitor = data.data;

                     // Store the original data for change detection
                     originalEditVisitorData = {
                        visitor_name: visitor.visitor_name,
                        relation: visitor.relation,
                        phone_number: visitor.phone_number,
                        visit_date: visitor.visit_date,
                        purpose: visitor.purpose,
                     };

                     // Populate edit form fields with null checks
                     const editVisitorIdEl =
                        document.getElementById("editVisitorId");
                     const editVisitorNameEl =
                        document.getElementById("editVisitorName");
                     const editRelationEl =
                        document.getElementById("editRelation");
                     const editPhoneNumberEl =
                        document.getElementById("editPhoneNumber");
                     const editVisitDateEl =
                        document.getElementById("editVisitDate");
                     const editPurposeEl =
                        document.getElementById("editPurpose");

                     if (editVisitorIdEl)
                        editVisitorIdEl.value = visitor.visitor_id;
                     if (editVisitorNameEl)
                        editVisitorNameEl.value = visitor.visitor_name;
                     if (editRelationEl)
                        editRelationEl.value = visitor.relation;
                     if (editPhoneNumberEl)
                        editPhoneNumberEl.value = visitor.phone_number;
                     if (editVisitDateEl)
                        editVisitDateEl.value = visitor.visit_date;
                     if (editPurposeEl) editPurposeEl.value = visitor.purpose;

                     // Show the edit modal and hide the view modal
                     $("#visitorModal").modal("hide");
                     $("#editVisitorModal").modal("show");
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
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Error: " + error.message,
                     confirmButtonColor: "#3085d6",
                  });
               });
         }
      });

      // Delete visitor action
      dt_visitor_table.addEventListener("click", function (e) {
         if (e.target.closest(".delete-visitor")) {
            const visitorId = e.target
               .closest(".delete-visitor")
               .getAttribute("data-id");

            // Get the row data to check visitor status
            const row = dt.row(e.target.closest("tr"));
            const rowData = row.data();

            if (rowData.status !== "Pending") {
               Swal.fire({
                  icon: "warning",
                  title: "Cannot Delete Visitor",
                  text: `You can only delete visitors with 'Pending' status. This visitor's status is '${rowData.status}'.`,
                  confirmButtonColor: "#3085d6",
               });
               return;
            }

            Swal.fire({
               title: "Are you sure?",
               text: `Do you want to delete visitor with Name: ${rowData.visitor_name} and ID: ${visitorId}?`,
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
                           dt.row(e.target.closest("tr")).remove().draw();
                           Swal.fire({
                              icon: "success",
                              title: "Deleted",
                              text: "Visitor deleted successfully!",
                              confirmButtonColor: "#3085d6",
                           });
                           dt.ajax.reload();
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
