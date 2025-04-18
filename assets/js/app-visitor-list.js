(function () {
   "use strict";

   const dt_visitor_table = document.querySelector(".datatables-visitors");

   if (dt_visitor_table) {
      const dt = new DataTable(dt_visitor_table, {
         ajax: "/visitors-data",
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
         },

         columns: [
            { data: null, defaultContent: "" },
            { data: "id", orderable: false },
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
               orderable: false,
               searchable: false,
               responsivePriority: 4,
               render: function () {
                  return '<input type="checkbox" class="dt-checkboxes form-check-input">';
               },
               checkboxes: {
                  selectAllRender:
                     '<input type="checkbox" class="form-check-input">',
               },
            },
            {
               targets: 2,
               responsivePriority: 1,
               render: function (data, type, full) {
                  const name = full["full_name"];
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
                                    <small>${full["email"]}</small>
                                </div>
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
                  return data
                     ? `<span class="text-heading">${new Date(
                          data
                       ).toLocaleTimeString()}</span>`
                     : "-";
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
                                <a href="javascript:;" class="btn btn-icon view-visitor" data-id="${full["id"]}" data-bs-toggle="modal" data-bs-target="#visitorModal">
                                    <i class="bx bx-show icon-md"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-icon delete-visitor text-danger" data-id="${full["id"]}">
                                    <i class="bx bx-trash icon-md"></i>
                                </a>
                            </div>
                        `;
               },
            },
         ],
         order: [[4, "desc"]],

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
                     exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                           body: function (inner, coldex, rowdex) {
                              if (inner.length <= 0) return inner;
                              const el = new DOMParser().parseFromString(
                                 inner,
                                 "text/html"
                              ).body.childNodes;
                              let result = "";
                              el.forEach((item) => {
                                 if (
                                    item.classList &&
                                    item.classList.contains("user-name")
                                 ) {
                                    result +=
                                       item.lastChild.firstChild.textContent;
                                 } else {
                                    result +=
                                       item.textContent || item.innerText || "";
                                 }
                              });
                              return result;
                           },
                        },
                     },
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
                     exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                           body: function (inner, coldex, rowdex) {
                              if (inner.length <= 0) return inner;
                              const el = new DOMParser().parseFromString(
                                 inner,
                                 "text/html"
                              ).body.childNodes;
                              let result = "";
                              el.forEach((item) => {
                                 if (
                                    item.classList &&
                                    item.classList.contains("user-name")
                                 ) {
                                    result +=
                                       item.lastChild.firstChild.textContent;
                                 } else {
                                    result +=
                                       item.textContent || item.innerText || "";
                                 }
                              });
                              return result;
                           },
                        },
                     },
                  },
                  {
                     extend: "excel",
                     text: `<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-export me-2"></i>Excel</span>`,
                     className: "dropdown-item",
                     exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                           body: function (inner, coldex, rowdex) {
                              if (inner.length <= 0) return inner;
                              const el = new DOMParser().parseFromString(
                                 inner,
                                 "text/html"
                              ).body.childNodes;
                              let result = "";
                              el.forEach((item) => {
                                 if (
                                    item.classList &&
                                    item.classList.contains("user-name")
                                 ) {
                                    result +=
                                       item.lastChild.firstChild.textContent;
                                 } else {
                                    result +=
                                       item.textContent || item.innerText || "";
                                 }
                              });
                              return result;
                           },
                        },
                     },
                  },
                  {
                     extend: "pdf",
                     text: `<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-pdf me-2"></i>Pdf</span>`,
                     className: "dropdown-item",
                     exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                           body: function (inner, coldex, rowdex) {
                              if (inner.length <= 0) return inner;
                              const el = new DOMParser().parseFromString(
                                 inner,
                                 "text/html"
                              ).body.childNodes;
                              let result = "";
                              el.forEach((item) => {
                                 if (
                                    item.classList &&
                                    item.classList.contains("user-name")
                                 ) {
                                    result +=
                                       item.lastChild.firstChild.textContent;
                                 } else {
                                    result +=
                                       item.textContent || item.innerText || "";
                                 }
                              });
                              return result;
                           },
                        },
                     },
                  },
                  {
                     extend: "copy",
                     text: `<i class="icon-base bx bx-copy me-1"></i>Copy`,
                     className: "dropdown-item",
                     exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                           body: function (inner, coldex, rowdex) {
                              if (inner.length <= 0) return inner;
                              const el = new DOMParser().parseFromString(
                                 inner,
                                 "text/html"
                              ).body.childNodes;
                              let result = "";
                              el.forEach((item) => {
                                 if (
                                    item.classList &&
                                    item.classList.contains("user-name")
                                 ) {
                                    result +=
                                       item.lastChild.firstChild.textContent;
                                 } else {
                                    result +=
                                       item.textContent || item.innerText || "";
                                 }
                              });
                              return result;
                           },
                        },
                     },
                  },
               ],
            },
            {
               text: '<i class="icon-base bx bx-plus icon-sm me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add New User</span>',
               className: "add-new btn btn-primary",
               attr: {
                  "data-bs-toggle": "offcanvas",
                  "data-bs-target": "#offcanvasAddUser",
               },
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
            $("#visitorSearch").on("keyup", function () {
               dt.search(this.value).draw();
            });

            $("#statusFilter").on("change", function () {
               const val = $(this).val();
               dt.column(7)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            $(".dt-buttons > .btn-group > button").removeClass("btn-secondary");
         },
      });

      // View visitor action (populate modal)
      dt_visitor_table.addEventListener("click", function (e) {
         if (e.target.closest(".view-visitor")) {
            const visitorId = e.target
               .closest(".view-visitor")
               .getAttribute("data-id");

            // Fetch visitor details via AJAX
            fetch(`/visitor/view/${visitorId}`, {
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
                                    <a href="javascript:;" class="btn btn-primary me-4 edit-visitor" data-id="${visitor.visitor_id}">Edit</a>
                                    <a href="javascript:;" class="btn btn-label-danger cancel-visitor" data-id="${visitor.visitor_id}">Cancel</a>
                                `;
                           break;
                        case "Approved":
                           actionsDiv.innerHTML = `
                                    <a href="javascript:;" class="btn btn-label-danger cancel-visitor" data-id="${visitor.visitor_id}">Cancel</a>
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
                        dt.ajax.reload(); // Refresh DataTable
                        location.reload();
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

      // Use event delegation on the document for dynamically added buttons in the modal
      document.addEventListener("click", function (e) {
         // Cancel visitor action
         if (e.target.closest(".cancel-visitor")) {
            console.log("Cancel visitor clicked");

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
            }).then((result) => {
               if (result.isConfirmed) {
                  fetch(`/visitor/cancel/${visitorId}`, {
                     method: "POST",
                     headers: { "Content-Type": "application/json" },
                     body: JSON.stringify({ visitor_id: visitorId }),
                  })
                     .then((response) => response.json())
                     .then((data) => {
                        if (data.success) {
                           dt.ajax.reload(); // Refresh DataTable
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
            window.location.href = `/visitor/edit/${visitorId}`;
         }
      });

      // Delete visitor action
      dt_visitor_table.addEventListener("click", function (e) {
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
                     body: JSON.stringify({ visitor_id: visitorId }),
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
                           // location.reload();
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
