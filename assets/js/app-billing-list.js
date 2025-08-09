(function () {
   "use strict";

   const dt_billings_table = document.querySelector(".datatables-billings");

   if (dt_billings_table) {
      const csrfToken =
         document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

      const dt = new DataTable(dt_billings_table, {
         ajax: "/student/billing-data",
         layout: {
            topStart: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     pageLength: {
                        menu: [5, 10, 25],
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
            { data: null, defaultContent: "" }, // Control column
            { data: "billing_id" },
            { data: "description" },
            { data: "amount" },
            { data: "date_due" },
            { data: "status" },
            { data: null, defaultContent: "" }, // Actions
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
               targets: 1, // Billing ID
               responsivePriority: 1,
               render: function (data) {
                  return `<span class="fw-medium">#${data}</span>`;
               },
            },
            {
               targets: 2, // Description
               render: function (data) {
                  return `<span>${data}</span>`;
               },
            },
            {
               targets: 3, // Amount
               render: function (data) {
                  return `GH₵${Number(data).toFixed(2)}`;
               },
            },
            {
               targets: 4, // Date Due
               render: function (data) {
                  return moment(data).format("MMM DD, YYYY");
               },
            },
            {
               targets: 5, // Status
               render: function (data) {
                  const statusObj = {
                     Unpaid: { class: "bg-label-danger", title: "Unpaid" },
                     "Partially Paid": {
                        class: "bg-label-warning",
                        title: "Partially Paid",
                     },
                     "Fully Paid": {
                        class: "bg-label-success",
                        title: "Fully Paid",
                     },
                     Overdue: { class: "bg-label-danger", title: "Overdue" },
                  };
                  const statusInfo = statusObj[data] || {
                     class: "bg-label-secondary",
                     title: data,
                  };
                  return `<span class="badge ${statusInfo.class}">${statusInfo.title}</span>`;
               },
            },
            {
               targets: 6, // Actions
               searchable: false,
               orderable: false,
               render: function (data, type, full) {
                  if (
                     full.status === "Unpaid" ||
                     full.status === "Partially Paid"
                  ) {
                     const outstandingAmount = (
                        full.amount - (full.paid_amount || 0)
                     ).toFixed(2);
                     return `
                         <button class="btn btn-sm btn-primary pay-billing-btn d-flex align-items-center gap-1"
               data-billing-id="${full.billing_id}"
               data-amount="${outstandingAmount}"
               data-description="${full.description || ""}"
               data-purpose="${full.billing_type}"
               data-billing-type="${full.billing_type || "General"}"
               data-bs-toggle="modal"
               data-bs-target="#paymentConfirmationModal"
               style="white-space:nowrap;">
               <i class="bx bx-credit-card"></i>
               <span class="d-none d-md-inline">Pay Now</span>
            </button>
                     `;
                  }
                  return "";
               },
            },
         ],
         order: [[1, "desc"]],
         responsive: {
            details: {
               display: DataTable.Responsive.display.modal({
                  header: function (row) {
                     const data = row.data();
                     return `Details for Billing #${data.billing_id}`;
                  },
               }),
               renderer: function (api, rowIdx, columns) {
                  const data = columns
                     .map(function (col, index) {
                        if (index === 0 || col.title === "") {
                           return "";
                        }

                        if (index === 6) {
                           // Actions column
                           const rowData = api.row(rowIdx).data();
                           if (
                              rowData.status === "Unpaid" ||
                              rowData.status === "Partially Paid"
                           ) {
                              return `
                                 <tr>
                                    <td>Actions:</td>
                                    <td>
                                       <button class="btn btn-sm btn-primary pay-billing-btn d-flex align-items-center gap-1"
                                          data-billing-id="${rowData.billing_id}"
                                          data-amount="${rowData.amount}"
                                          data-description="${rowData.description}"
                                          data-bs-toggle="modal"
                                          data-bs-target="#paymentConfirmationModal">
                                          <i class="bx bx-credit-card"></i>
                                          Pay Now
                                       </button>
                                    </td>
                                 </tr>
                              `;
                           }
                           return "";
                        }

                        return `<tr><td>${col.title}:</td><td>${col.data}</td></tr>`;
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
               $("#statusFilter").select2({
                  placeholder: "All Statuses",
                  allowClear: true,
                  width: "100%",
               });
            }

            $("#billingSearch").on("keyup", function () {
               api.search(this.value).draw();
            });

            $("#statusFilter").on("change", function () {
               const val = $(this).val();
               api.column(5)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            $(document).on("click", ".pay-billing-btn", function () {
               const billingId = $(this).data("billing-id");
               const amount = $(this).data("amount");
               const description = $(this).data("description");
               const purpose = $(this).data("purpose");
               const billingType = $(this).data("billing-type");

               console.log("Payment button clicked:", {
                  billingId,
                  amount,
                  description,
                  purpose,
                  billingType,
               });

               $("#confirmBillingId").text(`#${billingId}`);
               $("#confirmAmount").text(`GH₵${Number(amount).toFixed(2)}`);
               $("#confirmDescription").text(description);
               $("#confirmPurpose").text(purpose);
               $("#confirmBillingType").text(billingType);

               $(".confirm-pay-btn").data({
                  "billing-id": billingId,
                  amount: amount,
                  description: description,
                  purpose: purpose,
                  "billing-type": billingType,
               });
            });

            $(document).on("click", ".confirm-pay-btn", function () {
               const billingId = $(this).data("billing-id");
               const amount = $(this).data("amount");
               const description = $(this).data("description");
               const purpose = $(this).data("purpose");
               const billingType = $(this).data("billing-type");

               console.log("Confirm pay clicked:", {
                  billingId,
                  amount,
                  description,
                  purpose,
                  billingType,
               });

               if (!billingId) {
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Billing ID is missing",
                  });
                  return;
               }

               Swal.fire({
                  title: "Initializing Payment...",
                  text: "Please wait while we set up your payment",
                  allowOutsideClick: false,
                  didOpen: () => {
                     Swal.showLoading();
                  },
               });

               $.ajax({
                  url: `/student/billing/${billingId}/pay`,
                  method: "POST",
                  data: {
                     billing_id: billingId,
                     csrf: csrfToken,
                  },
                  success: function (response) {
                     console.log("Payment response:", response);
                     $("#paymentConfirmationModal").modal("hide");
                     if (response.success) {
                        if (response.authorization_url) {
                           // Redirect to Paystack
                           Swal.fire({
                              title: "Redirecting to Payment...",
                              html: `
                     <div class="text-start">
                        <p><strong>Purpose:</strong> ${purpose}</p>
                        <p><strong>Amount:</strong> GH₵${parseFloat(
                           amount
                        ).toFixed(2)}</p>
                        <p><strong>Reference:</strong> ${
                           response.reference || "Generating..."
                        }</p>
                     </div>
                     <hr>
                     <p class="text-center">You will be redirected to Paystack to complete your payment.</p>
                  `,
                              icon: "info",
                              timer: 3000,
                              showConfirmButton: false,
                           }).then(() => {
                              window.location.href = response.authorization_url;
                           });
                        } else {
                           Swal.fire({
                              icon: "success",
                              title: "Success",
                              text: "Payment initiated successfully!",
                              timer: 2000,
                           }).then(() => {
                              location.reload();
                           });
                        }
                     } else {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: response.error || "Failed to initiate payment",
                        });
                     }
                  },
                  error: function () {
                     $("#paymentConfirmationModal").modal("hide");
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Payment request failed",
                     });
                  },
               });
            });
         },
      });
   }
})();
