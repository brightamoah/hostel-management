$(document).ready(function () {
   // Recent Bookings Table
   $("#bookingsTable").DataTable({
      responsive: true,
      pageLength: 5,
      lengthChange: false,
      searching: false,
      ordering: true,
      info: false,
      columnDefs: [
         { orderable: false, targets: 3 }, // Disable sorting on Status column
         { className: "col-student", targets: 0 },
         { className: "col-room", targets: 1 },
         { className: "text-nowrap", targets: "_all" },
      ],
      order: [[2, "desc"]], // Sort by Start Date (descending)
      language: {
         emptyTable: "No recent bookings available",
      },
      drawCallback: function () {
         // Initialize tooltips after table draw
         $('[data-bs-toggle="tooltip"]').tooltip();
      },
   });

   // Recent Maintenance Requests Table
   $("#maintenanceTable").DataTable({
      responsive: true,
      pageLength: 5,
      lengthChange: false,
      searching: false,
      ordering: true,
      info: false,
      scrollX: true,
      columnDefs: [
         { orderable: false, targets: [3, 4] }, // Disable sorting on Priority and Status columns
         { className: "col-student", targets: 0 },
         { className: "col-room", targets: 1 },
         { className: "col-issue", targets: 2 },
         { className: "col-priority", targets: 3 },
         { className: "col-status", targets: 4 },
         { className: "text-nowrap", targets: "_all" },
      ],
      order: [[2, "desc"]], // Sort by Issue Type (descending)
      language: {
         emptyTable: "No recent maintenance requests available",
      },
      drawCallback: function () {
         // Initialize tooltips after table draw
         $('[data-bs-toggle="tooltip"]').tooltip();
      },
   });

   // Recent Payments Table
   const paymentsTable = $("#paymentsTable").DataTable({
      ajax: {
         url: "/admin/recent-payments",
         dataSrc: function (json) {
            if (json.success) {
               return json.data;
            } else {
               console.error("Error fetching payments: ", json.message);
               return [];
            }
         },
      },
      responsive: true,
      pageLength: 5,
      lengthChange: false,
      searching: false,
      ordering: true,
      info: false,
      scrollX: true,
      columns: [
         {
            data: "student_name",
            defaultContent: "Unknown",
            className: "col-student",
            render: function (data, type, row) {
               return `<span data-bs-toggle="tooltip" title="${data}">${data}</span>`;
            },
         },
         {
            data: "amount",
            defaultContent: "0.00",
            className: "text-nowrap",
            render: function (data, type, row) {
               const amount = parseFloat(data) || 0;
               return "GH₵ " + amount.toFixed(2);
            },
         },
         {
            data: "purpose",
            defaultContent: "N/A",
            render: function (data, type, row) {
               if (type === "display" && data && data.length > 20) {
                  return `<span class="text-truncate-custom" data-bs-toggle="tooltip" title="${data}">${data}</span>`;
               }
               return data;
            },
         },
         {
            data: "payment_date",
            defaultContent: new Date().toLocaleDateString("en-GB", {
               day: "2-digit",
               month: "short",
               year: "numeric",
            }),
            render: function (data, type, row) {
               return data
                  ? new Date(data).toLocaleDateString("en-GB", {
                       day: "2-digit",
                       month: "short",
                       year: "numeric",
                    })
                  : this.defaultContent;
            },
            className: "text-nowrap",
         },
         {
            data: "status",
            defaultContent: "Unknown",
            render: function (data, type, row) {
               const normalizedStatus = data
                  ? data
                       .trim()
                       .toLowerCase()
                       .replace(/^\w/, (c) => c.toUpperCase())
                  : "Unknown";
               const statusObj = {
                  Completed: { class: "bg-label-success", title: "Completed" },
                  Pending: { class: "bg-label-warning", title: "Pending" },
                  Failed: { class: "bg-label-danger", title: "Failed" },
                  Unknown: { class: "bg-label-secondary", title: "Unknown" },
               };
               const statusInfo = statusObj[normalizedStatus] || {
                  class: "bg-label-danger",
                  title: normalizedStatus,
               };
               if (!statusObj[normalizedStatus]) {
                  console.warn(
                     `Unexpected payment status: ${normalizedStatus}`
                  );
               }
               return `<span class="badge ${statusInfo.class}">${statusInfo.title}</span>`;
            },
            className: "col-status",
         },
         {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
               return `
       <a href="javascript:;" class="view-payment" data-id="${row.payment_id}" data-bs-toggle="modal" data-bs-target="#paymentModal" data-bs-placement="top" title="View Payment details">
            <i class="bx bx-show fs-4"></i>
         </a>
      </div>
   `;
            },
         },
      ],
      columnDefs: [
         { orderable: false, targets: [4, 5] }, // Disable sorting on Status and Actions columns
      ],
      order: [[3, "desc"]], // Sort by Date (descending)
      language: {
         emptyTable: "No recent payments available",
      },
      drawCallback: function () {
         // Initialize tooltips after table draw
         $('[data-bs-toggle="tooltip"]').tooltip();
      },
      initComplete: function () {
         // Status filter
         $("#statusFilter").on("change", function () {
            const val = $(this).val();
            paymentsTable
               .column(4)
               .search(val ? "^" + val + "$" : "", true, false)
               .draw();
         });
      },
   });

   // View payment action (populate modal)
   $("#paymentsTable").on("click", ".view-payment", function (e) {
      const paymentId = $(this).data("id");

      // Fetch payment details via AJAX
      fetch(`/admin/payment/${paymentId}`, {
         method: "GET",
         headers: { "Content-Type": "application/json" },
      })
         .then((response) => response.json())
         .then((data) => {
            if (data.success) {
               const payment = data.data;

               // Calculate initials
               const nameParts = payment.student_name.split(" ");
               const initials = nameParts
                  .map((part) => part.charAt(0).toUpperCase())
                  .join("")
                  .substring(0, 2);

               // Populate modal fields
               document.getElementById("paymentInitials").textContent =
                  initials;
               document.getElementById("paymentStudentName").textContent =
                  payment.student_name;
               document
                  .getElementById("paymentId")
                  .querySelector("span").textContent = payment.payment_id;
               document.getElementById("paymentAmount").textContent = `GH₵ ${(
                  parseFloat(payment.amount) || 0
               ).toFixed(2)}`;
               document.getElementById("paymentPurpose").textContent =
                  payment.purpose || "N/A";
               document.getElementById("paymentDate").textContent =
                  payment.payment_date
                     ? new Date(payment.payment_date).toLocaleDateString(
                          "en-GB",
                          {
                             day: "2-digit",
                             month: "short",
                             year: "numeric",
                          }
                       )
                     : "N/A";
               document.getElementById("paymentStatus").textContent =
                  payment.status;
               document.getElementById("paymentStatus").className = `badge ${
                  payment.status === "Completed"
                     ? "bg-label-success"
                     : payment.status === "Pending"
                     ? "bg-label-warning"
                     : payment.status === "Failed"
                     ? "bg-label-danger"
                     : "bg-label-secondary"
               }`;
               document.getElementById("paymentStudentId").textContent =
                  payment.student_id;
            } else {
               Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: data.message || "Failed to fetch payment details",
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
});
