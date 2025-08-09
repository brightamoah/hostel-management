(function () {
   "use strict";

   const dt_billings_table = document.querySelector(".datatables-billings");

   const formatCurrency = (amount) => {
      return new Intl.NumberFormat("en-GH", {
         style: "currency",
         currency: "GHS",
         minimumFractionDigits: 2,
      }).format(amount);
   };

   const formatDate = (date) => {
      return new Intl.DateTimeFormat("en-US", {
         year: "numeric",
         month: "long",
         day: "numeric",
      }).format(new Date(date));
   };

   if (dt_billings_table) {
      const csrfToken =
         document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

      let _dt;
      _dt = new DataTable(dt_billings_table, {
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
            { data: "outstanding_amount" },
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
                     Unpaid: { class: "bg-label-warning", title: "Unpaid" },
                     "Fully Paid": {
                        class: "bg-label-success",
                        title: "Fully Paid",
                     },
                     "Partially Paid": {
                        class: "bg-label-info",
                        title: "Partially Paid",
                     },
                     Overdue: { class: "bg-label-danger", title: "Overdue" },
                     Cancelled: {
                        class: "bg-label-secondary",
                        title: "Cancelled",
                     },
                  };
                  const statusInfo = statusObj[data] || {
                     class: "bg-label-secondary",
                     title: data,
                  };
                  return `<span class="badge ${statusInfo.class}">${statusInfo.title}</span>`;
               },
            },
            {
               targets: 6, // Outstanding Amount
               render: function (data) {
                  return `GH₵${Number(data).toFixed(2)}`;
               },
            },
            {
               targets: 7, // Actions
               searchable: false,
               orderable: false,
               render: function (data, type, full) {
                  const outstandingAmount = Number(
                     full.outstanding_amount
                  ).toFixed(2);

                  let actions = `
                      <button class="btn btn-sm btn-outline-secondary view-billing-details d-flex align-items-center gap-1 me-2" 
                        data-billing-id="${full.billing_id}"
                        data-bs-toggle="modal" 
                         data-bs-target="#viewInvoiceModal"
                        title="View Details"
                        style="white-space:nowrap;">
                        <i class="bx bx-show"></i>
                        <span class="d-none d-md-inline">View</span>
                      </button>
                    `;

                  if (
                     full.status === "Unpaid" ||
                     full.status === "Partially Paid" ||
                     full.status === "Overdue"
                  ) {
                     const buttonClass =
                        full.status === "Overdue"
                           ? "btn btn-sm btn-danger pay-billing-btn"
                           : "btn btn-sm btn-primary pay-billing-btn";

                     const buttonText =
                        full.status === "Overdue" ? "Pay Overdue" : "Pay Now";

                     actions += `
                        <button class="${buttonClass} d-flex align-items-center gap-1"
                           data-billing-id="${full.billing_id}"
                           data-max-amount="${outstandingAmount}"
                           data-description="${full.description || ""}"
                           data-purpose="${full.billing_type}"
                           data-billing-type="${full.billing_type || "General"}"
                           data-bs-toggle="modal"
                           data-bs-target="#paymentConfirmationModal"
                           style="white-space:nowrap;">
                           <i class="bx bx-credit-card"></i>
                           <span class="d-none d-md-inline">${buttonText}</span>
                        </button>
                     `;
                  }
                  return `<div class="d-flex gap-1">${actions}</div>`;
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

                        if (index === 7) {
                           // Actions column
                           const rowData = api.row(rowIdx).data();

                           const outstandingAmount = (
                              rowData.amount - (rowData.paid_amount || 0)
                           ).toFixed(2);

                           let actions = `
                      <button class="btn btn-sm btn-outline-secondary view-billing-details d-flex align-items-center gap-1 me-2" 
                        data-billing-id="${rowData.billing_id}"
                        data-bs-toggle="modal" 
                        data-bs-target="#viewInvoiceModal"
                        title="View Details"
                        style="white-space:nowrap;">
                        <i class="bx bx-show"></i>
                        <span class="d-none d-md-inline">View</span>
                      </button>
                    `;
                           if (
                              rowData.status === "Unpaid" ||
                              rowData.status === "Partially Paid" ||
                              rowData.status === "Overdue"
                           ) {
                              const buttonClass =
                                 full.status === "Overdue"
                                    ? "btn btn-sm btn-danger pay-billing-btn"
                                    : "btn btn-sm btn-primary pay-billing-btn";

                              const buttonText =
                                 full.status === "Overdue"
                                    ? "Pay Overdue"
                                    : "Pay Now";

                              actions += `
                        <button class="${buttonClass} d-flex align-items-center gap-1"
                           data-billing-id="${rowData.billing_id}"
                           data-max-amount="${outstandingAmount}"
                           data-description="${rowData.description || ""}"
                           data-purpose="${rowData.billing_type}"
                           data-billing-type="${
                              rowData.billing_type || "General"
                           }"
                           data-bs-toggle="modal"
                           data-bs-target="#paymentConfirmationModal"
                           style="white-space:nowrap;">
                           <i class="bx bx-credit-card"></i>
                           <span class="d-none d-md-inline">${buttonText}</span>
                        </button>
                     `;
                           }
                           return ` <tr>
                                 <td>Actions:</td>
                                 <td>${actions}</td>
                              </tr>`;
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

            $(document).on("click", ".view-billing-details", function (e) {
               e.preventDefault();

               const billingId = $(this).data("billing-id");

               if (!billingId) {
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Billing ID is missing",
                  });
                  return;
               }

               $.ajax({
                  url: `/student/billing/${billingId}`,
                  method: "GET",
                  success: function (response) {
                     if (!response.data) {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text:
                              response.error ||
                              "Failed to load billing details",
                        });
                        return;
                     }

                     const details = response.data.details;

                     if (!details) {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: "No billing details found",
                        });
                        return;
                     }

                     // Hide download and email invoice buttons for students
                     $("#downloadInvoiceBtn").hide();
                     $("#emailInvoiceBtn").hide();

                     $("#modalInvoiceId").text(
                        `INV-${String(details.billing_id).padStart(6, "0")}`
                     );
                     $("#modalStudentName").text(
                        details.student_name || "Not specified"
                     );
                     $("#modalStudentId").text(
                        `ID: ${details.student_id || "N/A"}`
                     );
                     $("#modalStudentEmail").text(
                        details.student_email || "No email provided"
                     );
                     $("#modalStudentPhone").text(
                        details.student_phone || "No phone provided"
                     );

                     // Format dates
                     $("#modalDateIssued").text(
                        formatDate(details.date_issued)
                     );
                     $("#modalDueDate").text(formatDate(details.date_due));

                     // Build invoice items
                     const itemsTable = $("#modalInvoiceItems tbody");
                     itemsTable.empty();

                     // Add main billing item
                     itemsTable.append(`
                        <tr>
                           <td>1</td>
                           <td>${details.description || "Hostel Fee"}</td>
                           <td class="text-end">${formatCurrency(
                              details.amount
                           )}</td>
                        </tr>
                     `);

                     // Update totals
                     $("#modalSubtotal").text(formatCurrency(details.amount));
                     $("#modalLatePaymentFee").text(
                        formatCurrency(details.late_payment_fee)
                     );
                     $("#modalTotal").text(
                        formatCurrency(
                           Number(details.amount) +
                              Number(details.late_payment_fee)
                        )
                     );
                     $("#modalAmountPaid").text(
                        formatCurrency(details.paid_amount)
                     );
                     $("#modalBalanceDue").text(
                        formatCurrency(details.outstanding_balance)
                     );

                     // Update transaction history if available
                     const transactionTable = $(
                        "#modalTransactionHistory tbody"
                     );
                     transactionTable.empty();

                     const transactions = response.data.transactions || [];

                     if (transactions && transactions.length > 0) {
                        transactions.forEach(function (transaction) {
                           transactionTable.append(`
                              <tr>
                                 <td>${formatDate(
                                    transaction.payment_date
                                 )}</td>
                                 <td>${transaction.payment_method}</td>
                                 <td>${formatCurrency(transaction.amount)}</td>
                              </tr>
                           `);
                        });
                     } else {
                        transactionTable.append(`
                           <tr>
                              <td colspan="3" class="text-center">No payment transactions recorded</td>
                           </tr>
                        `);
                     }

                     // Show the modal
                     $("#viewInvoiceModal").modal("show");
                  },
                  error: function (xhr) {
                     let errorMessage = "Failed to load billing details";
                     if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                     }
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: errorMessage,
                     });
                  },
               });
            });

            $(document).on("click", ".pay-billing-btn", function () {
               const billingId = $(this).data("billing-id");
               const maxAmount = parseFloat($(this).data("max-amount"));
               const description = $(this).data("description");
               const purpose = $(this).data("purpose");
               const billingType = $(this).data("billing-type");

               // Reset form
               $("#paymentForm")[0].reset();
               $("#fullPayment").prop("checked", true);
               $("#partialAmountSection").hide();
               $("#amountError").hide();

               $("#confirmBillingId").text(`#${billingId}`);
               $("#confirmDescription").text(description);
               $("#confirmPurpose").text(purpose);
               $("#confirmMaxAmount").text(`GH₵${maxAmount.toFixed(2)}`);
               $("#fullAmountDisplay").text(`GH₵${maxAmount.toFixed(2)}`);
               $("#maxAmountText").text(`GH₵${maxAmount.toFixed(2)}`);
               $("#paymentAmount").attr("max", maxAmount);

               $(".confirm-pay-btn").data({
                  "billing-id": billingId,
                  "max-amount": maxAmount,
                  description: description,
                  purpose: purpose,
                  "billing-type": billingType,
               });
            });

            // Handle payment type radio buttons
            $(document).on("change", "input[name='paymentType']", function () {
               if ($(this).val() === "partial") {
                  $("#partialAmountSection").show();
                  $("#paymentAmount").focus();
               } else {
                  $("#partialAmountSection").hide();
                  $("#amountError").hide();
               }
            });

            // Validate partial payment amount
            $(document).on("input", "#paymentAmount", function () {
               const amount = parseFloat($(this).val());
               const maxAmount = parseFloat(
                  $(".confirm-pay-btn").data("max-amount")
               );
               const errorDiv = $("#amountError");

               errorDiv.hide();

               if (amount < 1) {
                  errorDiv.text("Minimum payment amount is GH₵1.00").show();
               } else if (amount > maxAmount) {
                  errorDiv
                     .text(
                        `Amount cannot exceed outstanding balance of GH₵${maxAmount.toFixed(
                           2
                        )}`
                     )
                     .show();
               }
            });

            $(document).on("click", ".confirm-pay-btn", function () {
               const billingId = $(this).data("billing-id");
               const maxAmount = parseFloat($(this).data("max-amount"));
               const description = $(this).data("description");
               const purpose = $(this).data("purpose");
               const billingType = $(this).data("billing-type");

               // Determine payment amount
               let paymentAmount;
               const paymentType = $("input[name='paymentType']:checked").val();

               if (paymentType === "partial") {
                  paymentAmount = parseFloat($("#paymentAmount").val());

                  // Validate amount
                  if (!paymentAmount || paymentAmount < 1) {
                     $("#amountError")
                        .text("Please enter a valid amount (minimum GH₵1.00)")
                        .show();
                     return;
                  }

                  if (paymentAmount > maxAmount) {
                     $("#amountError")
                        .text(
                           `Amount cannot exceed outstanding balance of GH₵${maxAmount.toFixed(
                              2
                           )}`
                        )
                        .show();
                     return;
                  }
               } else {
                  paymentAmount = maxAmount;
               }

               if (!billingId) {
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Billing ID is missing",
                  });
                  return;
               }

               // Close any existing SweetAlert before showing the loading spinner
               Swal.close();

               Swal.fire({
                  title: '<span class="fw-bold">Initializing Payment</span>',
                  html: `
                       <div class="mb-2 text-center">Setting up your payment, please wait...</div>
                    `,
                  allowOutsideClick: false,
                  showConfirmButton: false,
                  customClass: {
                     popup: "p-4 rounded-3",
                  },
                  didOpen: () => {
                     Swal.showLoading();
                  },
               });

               $.ajax({
                  url: `/student/billing/${billingId}/pay`,
                  method: "POST",
                  data: {
                     billing_id: billingId,
                     payment_amount: paymentAmount,
                     csrf: csrfToken,
                  },
                  success: function (response) {
                     $("#paymentConfirmationModal").modal("hide");
                     if (response.success) {
                        if (response.authorization_url) {
                           // Redirect to Paystack
                           Swal.fire({
                              title: "Redirecting to Payment...",
                              html: `
                                  <div>
                                    <div>
                                       <strong>Purpose:</strong>
                                       <span>${purpose}</span>
                                    </div>
                                    <div>
                                       <strong>Amount:</strong>
                                       <span>GH₵${parseFloat(
                                          paymentAmount
                                       ).toFixed(2)}</span>
                                    </div>
                                    <div>
                                       <strong>Reference:</strong>
                                       <span>${
                                          response.reference || "Generating..."
                                       }</span>
                                    </div>
                                    <div style="margin-top:10px;">
                                       You will be redirected to Paystack to complete your payment.
                                    </div>
                                  </div>
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
