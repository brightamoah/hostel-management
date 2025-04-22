document.addEventListener("DOMContentLoaded", function () {
   let borderColor, bodyBg, headingColor;

   borderColor = config.colors.borderColor;
   bodyBg = config.colors.bodyBg;
   headingColor = config.colors.headingColor;


    const csrfToken =
       document
          .querySelector('meta[name="csrf-token"]')
          ?.getAttribute("content") || "";

   const dt_user_table = document.querySelector(".datatables-users");
   const statusObj = {
      0: { title: "Unverified", class: "bg-label-warning" },
      1: { title: "Verified", class: "bg-label-success" },
   };

   let dt_user;

   // Function to handle View Details
   const handleViewDetails = (userId, role) => {
      if (role === "Student") {
         fetch(`/admin/student/${userId}`)
            .then((response) => response.json())
            .then((data) => {
               if (data.success) {
                  const student = data.data;
                  const details = `
                     <tr><td>Name</td><td>${student.first_name} ${
                     student.last_name
                  }</td></tr>
                     <tr><td>Email</td><td>${student.email}</td></tr>
                     <tr><td>Gender</td><td>${student.gender}</td></tr>
                     <tr><td>Date of Birth</td><td>${
                        student.date_of_birth
                     }</td></tr>
                     <tr><td>Phone Number</td><td>${
                        student.phone_number
                     }</td></tr>
                     <tr><td>Address</td><td>${student.address}</td></tr>
                     <tr><td>Emergency Contact</td><td>${
                        student.emergency_contact_name
                     } (${student.emergency_contact_number})</td></tr>
                     <tr><td>Health Condition</td><td>${
                        student.health_condition || "None"
                     }</td></tr>
                     <tr><td>Resident Status</td><td>${
                        student.resident_status
                     }</td></tr>
                     <tr><td>Room</td><td>${
                        student.room_number
                           ? `${student.building} ${student.room_number}`
                           : "Not Allocated"
                     }</td></tr>
                  `;
                  document.getElementById("studentDetailsContent").innerHTML =
                     details;
                  new bootstrap.Modal(
                     document.getElementById("studentDetailsModal")
                  ).show();
               } else {
                  Swal.fire("Error", data.message, "error");
               }
            })
            .catch((error) => {
               Swal.fire(
                  "Error",
                  "Failed to fetch student details: " + error.message,
                  "error"
               );
            });
      } else {
         Swal.fire(
            "Info",
            "Detailed view is only available for students.",
            "info"
         );
      }
   };

   // Function to handle Toggle Role
   const handleToggleRole = (userId, currentRole) => {
      const newRole = currentRole === "Admin" ? "Student" : "Admin";
      Swal.fire({
         title: "Confirm Role Change",
         text: `Change role from ${currentRole} to ${newRole}?`,
         icon: "warning",
         showCancelButton: true,
         confirmButtonText: "Yes, change it!",
      }).then((result) => {
         if (result.isConfirmed) {
            // Create form data object with CSRF token
            const formData = new URLSearchParams();;
            formData.append("user_id", userId);
            formData.append("new_role", newRole);
            formData.append("csrf", csrfToken);

            fetch("/admin/user/change-role", {
               method: "POST",
               headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
               },
               body: formData,
            })
               .then((response) => response.json())
               .then((data) => {
                  if (data.success) {
                     Swal.fire(
                        "Success",
                        "Role updated successfully",
                        "success"
                     );
                     dt_user.ajax.reload();
                     location.reload();
                  } else {
                     Swal.fire("Error", data.message, "error");
                  }
               })
               .catch((error) => {
                  Swal.fire(
                     "Error",
                     "Failed to toggle role: " + error.message,
                     "error"
                  );
               });
         }
      });
   };

   // Function to handle Delete User
   const handleDeleteUser = (userId) => {
      Swal.fire({
         title: "Are you sure?",
         text: "This action cannot be undone!",
         icon: "warning",
         showCancelButton: true,
         confirmButtonText: "Yes, delete it!",
      }).then((result) => {
         if (result.isConfirmed) {
            fetch("/admin/user/delete", {
               method: "POST",
               headers: { "Content-Type": "application/json" },
               body: JSON.stringify({ user_id: userId }),
            })
               .then((response) => response.json())
               .then((data) => {
                  if (data.success) {
                     Swal.fire("Deleted", "User has been deleted.", "success");
                     dt_user.ajax.reload();
                     location.reload();
                  } else {
                     Swal.fire("Error", data.message, "error");
                  }
               })
               .catch((error) => {
                  Swal.fire(
                     "Error",
                     "Failed to delete user: " + error.message,
                     "error"
                  );
               });
         }
      });
   };

   if (dt_user_table) {
      dt_user = new DataTable(dt_user_table, {
         ajax: {
            url: "/admin/users-data",
            dataSrc: "data",
         },
         columns: [
            {
               data: "user_id",
               className: "control",
               orderable: false,
               searchable: false,
               render: () => "",
            },
            { data: "name" },
            { data: "role" },
            { data: "resident_status" },
            { data: "email" },
            { data: "is_email_verified" },
            { data: "user_id" },
         ],
         columnDefs: [
            {
               targets: 0,
               responsivePriority: 2,
               render: () => "",
            },
            
            {
               targets: 1,
               responsivePriority: 3,
               render: function (data, type, full) {
                  const name = full.name;
                  const email = full.email;
                  const initials = name
                     .match(/\b\w/g)
                     .map((char) => char.toUpperCase())
                     .join("")
                     .slice(0, 2);
                  return `
                     <div class="d-flex justify-content-start align-items-center user-name">
                         <div class="avatar-wrapper">
                             <div class="avatar avatar-sm me-4">
                                 <span class="avatar-initial rounded-circle bg-label-primary">${initials}</span>
                             </div>
                         </div>
                         <div class="d-flex flex-column">
                             <span class="fw-medium">${name}</span>
                             <small>${email}</small>
                         </div>
                     </div>
                  `;
               },
            },
            {
               targets: 2,
               data: "role",
               render: function (data, type, full, meta) {
                  if (type === "filter" || type === "sort") return data;
                  const roleBadgeObj = {
                     Student:
                        '<i class="icon-base bx bx-book-reader icon-lg text-success me-2"></i>',
                     Admin: '<i class="icon-base bx bx-desktop icon-lg text-danger me-2"></i>',
                  };
                  return `
                     <span class="text-truncate d-flex align-items-center text-heading">
                         ${roleBadgeObj[data] || ""}${data}
                     </span>`;
               },
            },
            {
               targets: 3,
               render: function (data) {
                  return data || "N/A";
               },
            },
            {
               targets: 5,
               render: function (data) {
                  return `
                     <span class="badge ${statusObj[data].class}" text-capitalized>
                         ${statusObj[data].title}
                     </span>
                  `;
               },
            },
            {
               targets: -1,
               title: "Actions",
               searchable: false,
               orderable: false,
               render: function (data, type, full) {
                  return `
                     <div class="d-flex align-items-center">
                         <a href="javascript:;" class="btn btn-icon view-details" data-user-id="${full.user_id}" data-role="${full.role}">
                             <i class="icon-base bx bx-show icon-md"></i>
                         </a>
                         <a href="javascript:;" class="btn btn-icon toggle-role" data-user-id="${full.user_id}" data-role="${full.role}">
                             <i class="icon-base bx bx-refresh icon-md"></i>
                         </a>
                         <a href="javascript:;" class="btn btn-icon delete-record" data-user-id="${full.user_id}">
                             <i class="icon-base bx bx-trash icon-md"></i>
                         </a>
                     </div>
                  `;
               },
            },
         ],
       
         order: [[2, "desc"]],
         layout: {
            topStart: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     pageLength: {
                        menu: [10, 25, 50, 100],
                        text: "_MENU_",
                     },
                  },
               ],
            },
            topEnd: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     search: {
                        placeholder: "Search User",
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
                                 exportOptions: { columns: [2, 3, 4, 5, 6] },
                              },
                              {
                                 extend: "excel",
                                 text: '<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-export me-2"></i>Excel</span>',
                                 className: "dropdown-item",
                                 exportOptions: { columns: [2, 3, 4, 5, 6] },
                              },
                              {
                                 extend: "pdf",
                                 text: '<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-pdf me-2"></i>Pdf</span>',
                                 className: "dropdown-item",
                                 exportOptions: { columns: [2, 3, 4, 5, 6] },
                              },
                           ],
                        },
                        {
                           text: '<i class="icon-base bx bx-plus icon-sm me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add New User</span>',
                           className: "add-new btn bg-primary ms-4",
                           attr: {
                              "data-bs-toggle": "offcanvas",
                              "data-bs-target": "#offcanvasAddUser",
                           },
                        },
                     ],
                  },
               ],
            },
            bottomStart: {
               rowClass: "row mx-3 justify-content-between",
               features: ["info"],
            },
            bottomEnd: {
               paging: { firstLast: false },
            },
         },
         language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Search User",
            paginate: {
               next: '<i class="icon-base bx bx-chevron-right icon-18px"></i>',
               previous:
                  '<i class="icon-base bx bx-chevron-left icon-18px"></i>',
            },
         },
         responsive: {
            details: {
               display: DataTable.Responsive.display.modal({
                  header: function (row) {
                     const data = row.data();
                     return "Details of " + data.name;
                  },
               }),
               type: "column",
               renderer: function (api, rowIdx, columns) {
                  const data = columns
                     .map(function (col) {
                        if (col.columnIndex === 0) return "";
                        return col.title !== ""
                           ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                 <td>${col.title}:</td>
                                 <td>${col.data}</td>
                              </tr>`
                           : "";
                     })
                     .join("");
                  if (data) {
                     const div = document.createElement("div");
                     div.classList.add("table-responsive");
                     const table = document.createElement("table");
                     table.classList.add("table");
                     table.innerHTML = `<tbody>${data}</tbody>`;
                     div.appendChild(table);
                     return div;
                  }
                  return false;
               },
            },
         },
         initComplete: function () {
            const api = this.api();
            const createFilter = (
               columnIndex,
               containerClass,
               selectId,
               defaultOptionText
            ) => {
               const column = api.column(columnIndex);
               const select = document.createElement("select");
               select.id = selectId;
               select.className = "form-select text-capitalize";
               select.innerHTML = `<option value="">${defaultOptionText}</option>`;
               document.querySelector(containerClass).appendChild(select);
               const uniqueValues = new Map();

               column.data().each(function (value, index) {
                  if (value === null || value === undefined || value === "") {
                     uniqueValues.set("N/A", "N/A");
                  } else {
                     uniqueValues.set(value, value);
                  }
               });

               Array.from(uniqueValues.keys())
                  .sort()
                  .forEach((value) => {
                     const option = document.createElement("option");
                     option.value = value;
                     option.textContent = value;
                     select.appendChild(option);
                  });

               select.addEventListener("change", function () {
                  const val = this.value;
                  if (val === "") {
                     column.search("").draw();
                  } else {
                     column
                        .search(
                           "^" + $.fn.dataTable.util.escapeRegex(val) + "$",
                           true,
                           false
                        )
                        .draw();
                  }
               });
            };
            createFilter(2, ".user_role", "UserRole", "Select Role");
            createFilter(
               3,
               ".user_status",
               "UserStatus",
               "Select Resident Status"
            );
         },
      });

      // Document-level event listeners for action buttons (table and modal)
      document.addEventListener("click", function (e) {
         const target = e.target.closest(
            ".view-details, .toggle-role, .delete-record"
         );
         if (!target) return;

         const userId = target.dataset.userId;
         const role = target.dataset.role;

         if (target.classList.contains("view-details")) {
            handleViewDetails(userId, role);
         } else if (target.classList.contains("toggle-role")) {
            handleToggleRole(userId, role);
         } else if (target.classList.contains("delete-record")) {
            handleDeleteUser(userId);
         }
      });
   }

   // Form Handling for Add User
   const addNewUserForm = document.getElementById("addNewUserForm");
   if (addNewUserForm) {
      addNewUserForm.addEventListener("submit", function (e) {
         e.preventDefault();
         const formData = new FormData(addNewUserForm);

         fetch("/admin/user/add", {
            method: "POST",
            body: formData,
         })
            .then((response) => response.json())
            .then((data) => {
               if (data.success) {
                  Swal.fire({
                     icon: "success",
                     title: "Success",
                     text: data.message || "User added successfully",
                     confirmButtonColor: "#3085d6",
                  }).then(() => {
                     addNewUserForm.reset();
                     const offcanvas = bootstrap.Offcanvas.getInstance(
                        document.getElementById("offcanvasAddUser")
                     );
                     offcanvas.hide();
                     dt_user.ajax.reload();
                     location.reload()
                  });
               } else {
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: data.message || "Failed to add user",
                     confirmButtonColor: "#3085d6",
                  });
               }
            })
            .catch((error) => {
               console.error("Fetch error:", error);
               Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "Failed to add user: " + error.message,
                  confirmButtonColor: "#3085d6",
               });
            });
      });
   } else {
      console.error("addNewUserForm not found in DOM");
   }
});
