// File: assets/js/admin-billing-list.js
(function () {
   ("use strict");

   const dt_billing_table = document.querySelector(".datatables-billings");

   let flatpickrInstance;

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

   const formatDateSafe = (dateStr) => {
      if (!dateStr) return "Not specified";
      const date = new Date(dateStr);
      if (isNaN(date.getTime())) return "Invalid date";
      return formatDate(dateStr);
   };

   if (dt_billing_table) {
      const csrfToken =
         document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

      let dt;

      dt = new DataTable(dt_billing_table, {
         ajax: "/admin/billing-data",
         pageLength: 7,
         layout: {
            topStart: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     pageLength: {
                        menu: [5, 7, 10, 25],
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
                                    columns: [1, 2, 3, 4, 5, 6, 7],
                                    format: {
                                       body: function (data) {
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
                                    columns: [1, 2, 3, 4, 5, 6, 7],
                                    format: {
                                       body: function (data) {
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
                                    columns: [1, 2, 3, 4, 5, 6, 7],
                                    format: {
                                       body: function (data) {
                                          let cleanData = data.replace(
                                             /<[^>]+>/g,
                                             ""
                                          );
                                          return cleanData;
                                       },
                                    },
                                 },
                                 customize: function (doc) {
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

                                    doc.styles = doc.styles || {};
                                    doc.styles.tableHeader =
                                       doc.styles.tableHeader || {};
                                    doc.styles.tableBodyOdd =
                                       doc.styles.tableBodyOdd || {};
                                    doc.styles.tableBodyEven =
                                       doc.styles.tableBodyEven || {};
                                    doc.defaultStyle = doc.defaultStyle || {};

                                    try {
                                       doc.content[1].table.widths = [
                                          "10%", // Invoice ID
                                          "20%", // Student
                                          "15%", // Amount
                                          "15%", // Date Issued
                                          "15%", // Due Date
                                          "10%", // Status
                                          "15%", // Paid Amount
                                       ];

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

                                       doc.styles.tableHeader.fontSize = 10;
                                       doc.styles.tableHeader.fillColor =
                                          "#f3f3f3";
                                       doc.styles.tableHeader.alignment =
                                          "left";
                                       doc.styles.tableBodyOdd.fontSize = 9;
                                       doc.styles.tableBodyEven.fontSize = 9;
                                       doc.defaultStyle.alignment = "left";

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

                                       doc.content.splice(0, 0, {
                                          text: [
                                             {
                                                text: "Kings Hostel - Billing Report\n",
                                                style: "title",
                                             },
                                             {
                                                text: `Generated on: ${new Date().toLocaleString()}`,
                                                style: "subtitle",
                                             },
                                          ],
                                          margin: [0, 0, 0, 10],
                                       });

                                       if (
                                          doc.content[1] &&
                                          doc.content[1].table &&
                                          doc.content[1].table.body &&
                                          doc.content[1].table.body.length > 0
                                       ) {
                                          doc.content[1].table.headerRows = 1;
                                       }

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
            { data: "billing_id" },
            { data: "student_name" },
            { data: "amount" },
            { data: "date_issued" },
            { data: "date_due" },
            { data: "status" },
            { data: "paid_amount" },
            { data: "building", visible: false }, // Building
            { data: "null", defaultContent: "" }, // Actions
         ],
         columnDefs: [
            {
               targets: 0, // Invoice ID
               render: function (data) {
                  // Custom formatting: prefix with "INV-" and pad to 6 digits
                  const formattedId = `INV-${String(data).padStart(6, "0")}`;
                  return `<span class="fw-medium ">${formattedId}</span>`;
               },
            },
            {
               targets: 1, // Student
               render: function (data) {
                  return `<span>${data}</span>`;
               },
            },
            {
               targets: 2, // Amount
               render: function (data) {
                  return formatCurrency(parseFloat(data).toFixed(2));
               },
            },
            {
               targets: 3, // Date Issued
               render: function (data) {
                  return formatDate(data);
               },
            },
            {
               targets: 4, // Due Date
               render: function (data) {
                  return formatDate(data);
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
               targets: 6, // Paid Amount
               render: function (data) {
                  return formatCurrency(parseFloat(data));
               },
            },
            {
               targets: 8, // Actions
               searchable: false,
               orderable: false,
               render: function (data, type, full) {
                  const sendReminderAndPaymentButton =
                     full.status === "Fully Paid"
                        ? ""
                        : `
         <li>
            <a class="dropdown-item send-reminder" href="javascript:;" 
               data-billing-id="${full.billing_id}"
               data-bs-toggle="modal" 
               data-bs-target="#sendReminderModal">
               <i class="bx bx-envelope me-2 text-info"></i>Send Reminder
            </a>
         </li>
         <li>
                  <a class="dropdown-item record-payment" href="javascript:;" 
                     data-billing-id="${full.billing_id}"
                     data-bs-toggle="modal" 
                     data-bs-target="#recordPaymentModal">
                     <i class="bx bx-dollar-circle me-2 text-success"></i>Record Payment
                  </a>
               </li>

               <li>
                  <a class="dropdown-item edit-billing" href="javascript:;" 
                     data-billing-id="${full.billing_id}"
                     data-bs-toggle="modal" 
                     data-bs-target="#editBillingModal">
                     <i class="bx bx-edit me-2"></i>Edit Billing
                  </a>
               </li>
         `;

                  return `
         <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                    data-bs-toggle="dropdown" aria-expanded="false">
               <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <ul class="dropdown-menu">
               <li>
                  <a class="dropdown-item view-billing-details" href="javascript:;" 
                     data-billing-id="${full.billing_id}">
                     <i class="bx bx-show me-2"></i>View Details
                  </a>
               </li>
               
               <li><hr class="dropdown-divider"></li>
               
               ${sendReminderAndPaymentButton}

                    <li><hr class="dropdown-divider"></li>
               
               <li>
                  <a class="dropdown-item text-danger delete-billing" href="javascript:;" 
                     data-billing-id="${full.billing_id}">
                     <i class="bx bx-trash me-2"></i>Delete Billing
                  </a>
               </li>
            </ul>
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
                     return `Billing #${data.billing_id} Details`;
                  },
               }),
               renderer: function (api, rowIdx, columns) {
                  const data = columns
                     .map(function (col) {
                        return col.title !== "Actions" && col.title !== ""
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

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(
               document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Search box
            $("#billingSearch").on("keyup", function () {
               api.search(this.value).draw();
            });

            // Status filter
            $("#statusFilter").on("change", function () {
               const val = $(this).val();
               if (val) {
                  // Use exact match for status values
                  api.column(5)
                     .search("^" + val + "$", true, false)
                     .draw();
               } else {
                  // Clear the filter when "All Statuses" is selected
                  api.column(5).search("").draw();
               }
            });

            // Building filter
            $("#filterBuilding").on("change", function () {
               const val = $(this).val();
               if (val) {
                  api.column(7) // Building column (index 7)
                     .search(val)
                     .draw();
               } else {
                  api.column(7).search("").draw();
               }
            });

            // Refresh table
            $(".refresh-table").on("click", function () {
               dt.ajax.reload(null, false);
            });

            // View details
            $(document).on("click", ".view-billing-details", function (e) {
               e.preventDefault();
               const billingId = $(this).data("billing-id");

               $.ajax({
                  url: `/admin/billing/${billingId}`,
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

                     console.table({
                        Amount: details.amount,
                        "Paid Amount": details.paid_amount,
                        "Outstanding Balance": details.outstanding_balance,
                        "Late Payment Fee": details.late_payment_fee,
                        Total: formatCurrency(
                           parseFloat(details.amount) +
                              parseFloat(details.late_payment_fee)
                        ),
                     });

                     $("#downloadInvoiceBtn").data(
                        "billing-id",
                        details.billing_id
                     );
                     $("#emailInvoiceBtn").data(
                        "billing-id",
                        details.billing_id
                     );
                     $("#emailInvoiceBtn").data(
                        "student-email",
                        details.student_email
                     );

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
                        formatDateSafe(details.date_issued)
                     );
                     $("#modalDueDate").text(formatDateSafe(details.date_due));

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

            // Download invoice
            $(document).on("click", "#downloadInvoiceBtn", function () {
               const billingId = $(this).data("billing-id");
               if (!billingId) {
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Billing ID is missing",
                  });
                  return;
               }
               // Redirect to download endpoint
               window.location.href = `/admin/generate-invoice-pdf?action=download&id=${billingId}`;
            });

            // Email invoice
            $(document).on("click", "#emailInvoiceBtn", function () {
               const billingId = $(this).data("billing-id");
               const studentEmail = $(this).data("student-email");

               if (!billingId || !studentEmail) {
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Billing ID or student email is missing",
                  });
                  return;
               }

               $.ajax({
                  url: `/admin/email-invoice`,
                  method: "GET",
                  data: {
                     action: "email",
                     id: billingId,
                     email: studentEmail,
                  },
                  dataType: "json",
                  beforeSend: function () {
                     $("#emailInvoiceBtn")
                        .prop("disabled", true)
                        .html(
                           '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...'
                        );
                  },
                  success: function (response) {
                     if (response.success) {
                        Swal.fire({
                           icon: "success",
                           title: "Success",
                           text:
                              response.message ||
                              "Invoice emailed successfully",
                           timer: 3000,
                           timerProgressBar: true,
                        });
                     } else {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text:
                              response.error || "Failed to send invoice email",
                        });
                     }
                  },
                  error: function (xhr) {
                     let errorMessage = "Failed to send invoice email";
                     // Try to parse the response even if it contains HTML
                     try {
                        const responseText = xhr.responseText;
                        // Look for JSON in the response
                        const jsonMatch = responseText.match(/\{.*\}/);
                        if (jsonMatch) {
                           const jsonResponse = JSON.parse(jsonMatch[0]);
                           if (jsonResponse.error) {
                              errorMessage = jsonResponse.error;
                           } else if (jsonResponse.success) {
                              // If we found successful JSON, show success
                              Swal.fire({
                                 icon: "success",
                                 title: "Success",
                                 text:
                                    jsonResponse.message ||
                                    "Invoice emailed successfully",
                                 timer: 3000,
                                 timerProgressBar: true,
                              });
                              return;
                           }
                        }
                     } catch (e) {
                        console.error("Failed to parse response:", e);
                     }

                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: errorMessage,
                     });
                  },
                  complete: function () {
                     $("#emailInvoiceBtn")
                        .prop("disabled", false)
                        .html("Email Invoice");
                  },
               });
            });

            // Edit billing
            $(document).on("click", ".edit-billing", function () {
               const billingId = $(this).data("billing-id");

               $.ajax({
                  url: `/admin/billing/${billingId}`,
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

                     const details = response.data;

                     // Populate edit form with current data
                     $("#editBillingId").val(details.billing_id);
                     $("#editStudentId").val(details.student_id);
                     $("#editStudentName").val(details.student_name);

                     // Edit billing section - update the field population
                     const billingType =
                        details.purpose || details.billing_type || "";
                     $("#editInvoiceType").val(billingType).trigger("change");
                     $("#editAcademicPeriod")
                        .val(details.academic_period)
                        .trigger("change");

                     // Calculate payment terms from date difference
                     const dateIssued = new Date(details.date_issued);
                     const dateDue = new Date(details.date_due);
                     const daysDifference = Math.ceil(
                        (dateDue - dateIssued) / (1000 * 60 * 60 * 24)
                     );

                     let paymentTermsValue = "30"; // default
                     switch (true) {
                        case daysDifference <= 7:
                           paymentTermsValue = "immediate";
                           break;
                        case daysDifference <= 15:
                           paymentTermsValue = "15";
                           break;
                        case daysDifference <= 30:
                           paymentTermsValue = "30";
                           break;
                        case daysDifference <= 45:
                           paymentTermsValue = "45";
                           break;
                        default:
                           paymentTermsValue = "30"; // fallback for longer periods
                     }

                     $("#editPaymentTerms")
                        .val(paymentTermsValue)
                        .trigger("change");

                     $("#editAmount").val(details.amount);
                     $("#editDescription").val(details.description);

                     // Format the date to match the datetime-local input format (YYYY-MM-DDTHH:MM)
                     const formattedDate = details.date_due
                        .replace(" ", "T")
                        .substring(0, 16);
                     $("#editDueDateInput").val(formattedDate);

                     $("#editInvoiceDescription").val(details.description);

                     $("#editBillingModal").modal("show");
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

            // Delete billing
            $(document).on("click", ".delete-billing", function (e) {
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

               Swal.fire({
                  title: "Are you sure?",
                  text: `This action will permanently delete billing #INV-${String(
                     billingId
                  ).padStart(6, "0")}`,
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
                  confirmButtonText: "Yes, delete it!",
                  cancelButtonText: "Cancel",
               }).then((result) => {
                  if (result.isConfirmed) {
                     $.ajax({
                        url: `/admin/delete-invoice/${billingId}`,
                        method: "POST",
                        data: {
                           csrf: csrfToken,
                        },
                        beforeSend: function () {
                           $(this).prop("disabled", true);
                        },
                        success: function (response) {
                           if (response.success) {
                              Swal.fire({
                                 icon: "success",
                                 title: "Success",
                                 text:
                                    response.message ||
                                    "Invoice deleted successfully",
                                 timer: 2000,
                                 timerProgressBar: true,
                              });

                              dt.ajax.reload(null, false);
                           } else {
                              Swal.fire({
                                 icon: "error",
                                 title: "Error",
                                 text:
                                    response.error ||
                                    "Failed to delete invoice",
                              });
                           }
                        },
                        error: function (xhr) {
                           let errorMessage = "Failed to delete invoice";
                           try {
                              if (xhr.responseJSON && xhr.responseJSON.error) {
                                 errorMessage = xhr.responseJSON.error;
                              } else if (xhr.responseText) {
                                 // Attempt to parse responseText if it's JSON
                                 const parsed = JSON.parse(xhr.responseText);
                                 errorMessage = parsed.error || errorMessage;
                              }
                           } catch (e) {
                              // If response is not JSON, check for common error patterns
                              if (xhr.status === 403) {
                                 errorMessage =
                                    "Unauthorized request or invalid CSRF token";
                              } else if (xhr.status === 404) {
                                 errorMessage = "Invoice not found";
                              } else if (xhr.status === 400) {
                                 errorMessage =
                                    "Invalid request. Please check the invoice details.";
                              } else {
                                 errorMessage =
                                    "Server error occurred. Please try again.";
                              }
                              console.log("Error parsing response:", e);
                           }
                           Swal.fire({
                              icon: "error",
                              title: "Error",
                              text: errorMessage,
                           });
                        },
                        complete: function () {
                           $(this).prop("disabled", false);
                        },
                     });
                  }
               });
            });

            // Record payment
            $(document).on("click", ".record-payment", function () {
               const billingId = $(this).data("billing-id");

               // Get billing details for the payment modal
               $.ajax({
                  url: `/admin/billing/${billingId}`,
                  method: "GET",
                  success: function (response) {
                     if (response.data) {
                        const billing = response.data.details;

                        console.table(billing);

                        // Set form values
                        $("#paymentBillingId").val(billingId);
                        $("#paymentInvoiceNumber").text(
                           `INV-${String(billingId).padStart(6, "0")}`
                        );
                        $("#outstandingAmount").text(
                           formatCurrency(billing.outstanding_balance)
                        );

                        // Set the payment amount to the outstanding balance by default
                        $("#paymentAmount").val(
                           Number(billing.outstanding_balance).toFixed(2)
                        );

                        // Set purpose based on billing type (readonly)
                        $("#paymentPurpose").val(billing.mapped_purpose);

                        // Set current date as default
                        const currentDate = new Date();
                        const formattedDate = formatDateSafe(currentDate);
                        $("#paymentDate").val(formattedDate);

                        console.log(formattedDate);

                        // Generate default transaction reference
                        const defaultTxnRef = `TXN-INV-${String(
                           billingId
                        ).padStart(6, "0")}`;
                        $("#transactionId").val(defaultTxnRef);

                        $("#recordPaymentModal").modal("show");
                     } else {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: "Failed to load billing details.",
                        });
                     }
                  },
                  error: function () {
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to load billing details.",
                     });
                  },
               });
            });

            // Send reminder
            $(document).on("click", ".send-reminder", function () {
               const billingId = $(this).data("billing-id");
               $("#reminderBillingId").val(billingId);

               $.ajax({
                  url: `/admin/billing/${billingId}`,
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

                     console.log(details);

                     const dueDate = new Date(details.date_due);
                     const today = new Date();
                     const timeDiff = dueDate.getTime() - today.getTime();
                     const daysLeft = Math.ceil(timeDiff / (1000 * 3600 * 24));
                     const status = details.status;
                     const outstandingAmount = details.outstanding_balance;

                     // Populate invoice details
                     $("#reminderInvoiceDetails").text(
                        `Invoice #INV-${String(details.billing_id).padStart(
                           6,
                           "0"
                        )} for ${details.student_name}`
                     );
                     $("#reminderAmountDue").text(
                        `Amount Due: ${formatCurrency(outstandingAmount)}`
                     );

                     // Payment information
                     const paymentInfo =
                        `\n\nPAYMENT METHODS & DETAILS:\n` +
                        `• Bank Transfer:\n` +
                        `  Bank: Ghana Commercial Bank\n` +
                        `  Account Name: Kings Hostel Management\n` +
                        `  Account Number: 1234567890\n\n` +
                        `• Mobile Money: +233 54 968 4848\n\n` +
                        `• Cash payment at the office\n` +
                        `• Online payment portal (if available)\n\n` +
                        `Please use your invoice number INV-${String(
                           details.billing_id
                        ).padStart(6, "0")} as reference for all payments.`;

                     // Generate status-specific content
                     let subject, message;

                     switch (status) {
                        case "Partially Paid":
                           const remainingDays =
                              daysLeft > 0
                                 ? `${daysLeft} days`
                                 : `${Math.abs(daysLeft)} days overdue`;
                           subject = `Payment Reminder: Outstanding Balance - Invoice #INV-${String(
                              details.billing_id
                           ).padStart(6, "0")}`;
                           message =
                              `Dear ${details.student_name},\n\n` +
                              `Thank you for your partial payment of ${formatCurrency(
                                 details.paid_amount
                              )} ` +
                              `towards invoice #INV-${String(
                                 details.billing_id
                              ).padStart(6, "0")}.\n\n` +
                              `OUTSTANDING BALANCE: ${formatCurrency(
                                 outstandingAmount
                              )}\n` +
                              `Original Amount: ${formatCurrency(
                                 details.amount
                              )}\n` +
                              `Amount Paid: ${formatCurrency(
                                 details.paid_amount
                              )}\n` +
                              `Balance Due: ${formatCurrency(
                                 outstandingAmount
                              )}\n\n` +
                              (daysLeft > 0
                                 ? `This remaining balance is due in ${remainingDays} on ${formatDate(
                                      details.date_due
                                   )}.\n\n` +
                                   `Please complete your payment to avoid late fees.`
                                 : `This balance was due on ${formatDate(
                                      details.date_due
                                   )} and is now ${remainingDays}.\n\n` +
                                   `Please make the outstanding payment immediately to avoid additional charges.`) +
                              paymentInfo +
                              `\nIf you have any questions or need assistance with your payment, please don't hesitate to contact us.\n\n` +
                              `Thank you for your continued cooperation.\n\n` +
                              `Best regards,\nKings Hostel Management\n` +
                              `Phone: +233 54 968 4848\n` +
                              `Email: kingshostelmgt@gmail.com`;
                           break;

                        case "Overdue":
                           subject = `FINAL NOTICE: Overdue Payment - Invoice #INV-${String(
                              details.billing_id
                           ).padStart(6, "0")}`;
                           message =
                              `Dear ${details.student_name},\n\n` +
                              `*** FINAL NOTICE - IMMEDIATE ACTION REQUIRED ***\n\n` +
                              `Your payment of ${formatCurrency(
                                 outstandingAmount
                              )} for invoice #INV-${String(
                                 details.billing_id
                              ).padStart(6, "0")} ` +
                              `is now ${Math.abs(
                                 daysLeft
                              )} days overdue (due date: ${formatDate(
                                 details.date_due
                              )}).\n\n` +
                              `CONSEQUENCES OF NON-PAYMENT:\n` +
                              `• Additional late fees and penalties\n` +
                              `• Suspension of hostel services\n` +
                              `• Potential termination of accommodation\n` +
                              `• Referral to collections\n\n` +
                              `This is your final notice before escalation procedures begin. You have 48 hours from ` +
                              `receipt of this notice to make full payment or contact our office to arrange an ` +
                              `acceptable payment plan.\n\n` +
                              `IMMEDIATE PAYMENT REQUIRED:` +
                              paymentInfo +
                              `\nIMPORTANT: Contact us immediately at +233 54 968 4848 or visit our office ` +
                              `during business hours to resolve this matter.\n\n` +
                              `We strongly encourage you to take immediate action to avoid further complications.\n\n` +
                              `Kings Hostel Management\nFinance Department\n` +
                              `Phone: +233 54 968 4848\n` +
                              `Email: kingshostelmgt@gmail.com`;
                           break;

                        case "Cancelled":
                           subject = `Information: Cancelled Invoice #INV-${String(
                              details.billing_id
                           ).padStart(6, "0")}`;
                           message =
                              `Dear ${details.student_name},\n\n` +
                              `This is to inform you that invoice #INV-${String(
                                 details.billing_id
                              ).padStart(6, "0")} ` +
                              `has been cancelled as requested.\n\n` +
                              `Invoice Details:\n` +
                              `• Original Amount: ${formatCurrency(
                                 details.amount
                              )}\n` +
                              `• Cancellation Date: ${formatDate(
                                 new Date()
                              )}\n` +
                              `• Reference: INV-${String(
                                 details.billing_id
                              ).padStart(6, "0")}\n\n` +
                              `If any payments were made towards this invoice, they will be processed according to ` +
                              `our refund policy. Please allow 5-7 business days for refund processing.\n\n` +
                              `Refunds will be processed to the original payment method or via:\n` +
                              `• Bank Transfer: Ghana Commercial Bank - Account: 1234567890\n` +
                              `• Mobile Money: +233 54 968 4848\n\n` +
                              `If you have any questions about this cancellation or need clarification, ` +
                              `please contact our finance office at +233 54 968 4848.\n\n` +
                              `Thank you for your understanding.\n\n` +
                              `Best regards,\nKings Hostel Management\n` +
                              `Phone: +233 54 968 4848\n` +
                              `Email: kingshostelmgt@gmail.com`;
                           break;

                        default: // 'Unpaid' and any other status
                           if (daysLeft > 0) {
                              subject = `Payment Reminder: Invoice #INV-${String(
                                 details.billing_id
                              ).padStart(6, "0")} - Due in ${daysLeft} days`;
                              message =
                                 `Dear ${details.student_name},\n\n` +
                                 `This is a friendly reminder that your payment of ${formatCurrency(
                                    outstandingAmount
                                 )} ` +
                                 `for invoice #INV-${String(
                                    details.billing_id
                                 ).padStart(
                                    6,
                                    "0"
                                 )} is due in ${daysLeft} days ` +
                                 `on ${formatDate(details.date_due)}.\n\n` +
                                 `To avoid any late fees or service interruptions, please ensure your payment is made before the due date.` +
                                 paymentInfo +
                                 `\nFor any assistance or questions regarding your payment, please contact us:\n` +
                                 `Phone: +233 54 968 4848\n` +
                                 `Email: kingshostelmgt@gmail.com\n\n` +
                                 `Thank you for your attention to this matter.\n\n` +
                                 `Best regards,\nKings Hostel Management`;
                           } else {
                              subject = `URGENT: Payment Overdue - Invoice #INV-${String(
                                 details.billing_id
                              ).padStart(6, "0")}`;
                              message =
                                 `Dear ${details.student_name},\n\n` +
                                 `This is an urgent notice that your payment of ${formatCurrency(
                                    outstandingAmount
                                 )} ` +
                                 `for invoice #INV-${String(
                                    details.billing_id
                                 ).padStart(6, "0")} was due on ${formatDate(
                                    details.date_due
                                 )} ` +
                                 `and is now ${Math.abs(
                                    daysLeft
                                 )} days overdue.\n\n` +
                                 `IMMEDIATE ACTION REQUIRED:\n` +
                                 `Please make your payment immediately to avoid additional late fees and potential service suspension.\n\n` +
                                 `Late fees may apply as per our hostel policy. Please contact the finance office immediately ` +
                                 `if you are experiencing financial difficulties so we can discuss payment arrangements.` +
                                 paymentInfo +
                                 `\nPlease retain your payment receipt and contact us once payment is completed.\n\n` +
                                 `Urgent Contact: +233 54 968 4848 | kingshostelmgt@gmail.com\n\n` +
                                 `Thank you for your immediate attention.\n\n` +
                                 `Kings Hostel Management`;
                           }
                     }

                     // Set the generated subject and message
                     $("#reminderSubject").val(subject);
                     $("#reminderMessage").val(message);

                     // Ensure attach invoice is checked by default
                     $("#attachInvoice").prop("checked", true);

                     $("#sendReminderModal").modal("show");
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

            // Load building options dynamically
            $.ajax({
               url: "/admin/building-data",
               method: "GET",
               success: function (response) {
                  const buildingSelect = $("#filterBuilding");
                  // Clear existing options except the first one
                  buildingSelect.find("option:not(:first)").remove();

                  if (response && Array.isArray(response)) {
                     // The response is a simple array of building names
                     response.forEach(function (building) {
                        buildingSelect.append(
                           $("<option>").val(building).text(building)
                        );
                     });
                  }
               },
               error: function () {
                  console.error("Failed to load building data");
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Failed to load building data",
                  });
               },
            });
         },
      });
   }

   // Initialize UI components when document is ready
   document.addEventListener("DOMContentLoaded", function () {
      // Initialize Select2 for all filter dropdowns
      if ($.fn.select2) {
         $("#statusFilter").select2();
         $("#filterBuilding").select2();

         // Initialize Select2 for payment method in record payment modal
         $("#paymentMethod").select2({
            placeholder: "Select payment method",
            allowClear: false,
            width: "100%",
            dropdownParent: $("#recordPaymentModal .modal-content"),
         });

         $("#editInvoiceType").select2({
            placeholder: "Select invoice type",
            allowClear: true,
            width: "100%",
            dropdownParent: $("#editBillingModal .modal-content"),
         });
         $("#editAcademicPeriod").select2({
            placeholder: "Select academic period",
            allowClear: true,
            width: "100%",
            dropdownParent: $("#editBillingModal .modal-content"),
         });
         $("#editPaymentTerms").select2({
            placeholder: "Select payment terms",
            allowClear: true,
            width: "100%",
            dropdownParent: $("#editBillingModal .modal-content"),
         });
      }

      // Add this event handler to ensure button is reset when modal is closed
      $("#editBillingModal").on("hidden.bs.modal", function () {
         const submitButton = $("#editBillingButton");
         submitButton.prop("disabled", false);
         submitButton.html("Save Changes");
      });

      // Initialize flatpickr properly
      if (typeof flatpickr !== "undefined") {
         flatpickr("#paymentDate", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:s",
            altInput: true,
            altFormat: "F j, Y H:i",
            allowInput: true,
            defaultDate: new Date(),
         });

         // flatpickrInstance = $("#editDueDateInput").flatpickr({
         //    enableTime: true,
         //    dateFormat: "Y-m-d H:i:s",
         //    altInput: true,
         //    altFormat: "F j, Y H:i",
         //    allowInput: false,
         //    time_24hr: true,
         //    static: true,
         //    altInputClass: "form-control flatpickr-date",
         //    onChange: function (selectedDates, dateStr) {
         //       $("#editDueDateHidden").val(dateStr);
         //    },
         // });
      }
   });

   //Create Invoice Modal handler
   $("#createInvoiceModal").on("show.bs.modal", function () {
      const studentSelect = $("#studentSelect"); // Define the variable properly

      // Clear previous options
      studentSelect
         .empty()
         .append('<option value="">Select a student</option>');

      // Initialize Select2 on the student select dropdown
      if ($.fn.select2) {
         studentSelect.select2({
            placeholder: "Select a student",
            allowClear: true,
            width: "100%",
            dropdownParent: $("#createInvoiceModal .modal-content"),
         });

         $("#invoiceType").select2({
            placeholder: "Select invoice type",
            allowClear: true,
            width: "100%",
            dropdownParent: $("#createInvoiceModal .modal-content"),
         });

         $("#academicPeriod").select2({
            placeholder: "Select academic period",
            allowClear: true,
            width: "100%",
            dropdownParent: $("#createInvoiceModal .modal-content"),
         });

         $("#paymentTerms").select2({
            placeholder: "Select payment terms",
            allowClear: true,
            width: "100%",
            dropdownParent: $("#createInvoiceModal .modal-content"),
         });
      }

      if (typeof flatpickr !== "undefined") {
         if (flatpickrInstance) {
            flatpickrInstance.destroy();
         }

         flatpickrInstance = $("#dueDateInput").flatpickr({
            enableTime: true,
            noCalendar: false,
            dateFormat: "Y-m-d H:i:S",
            minDate: "today",
            allowInput: false,
            altInput: true,
            animate: true,
            altFormat: "F j, Y at H:i",
            time_24hr: true,
            static: true, // Important for modal
            defaultDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000), // Default to 30 days from now
            plugins: [
               new confirmDatePlugin({
                  confirmText: "OK",
                  showAlways: false,
                  theme: "dark",
               }),
            ],
            onChange: function (selectedDates, dateStr) {
               $("#dueDateHidden").val(dateStr);
            },
         });

         // Trigger initial update based on default payment terms
         const defaultPaymentTerms = $("#paymentTerms").val() || "30";
         let dueDate;
         switch (defaultPaymentTerms) {
            case "Net 15 Days":
               dueDate = new Date(Date.now() + 15 * 24 * 60 * 60 * 1000);
               break;
            case "Net 30 Days":
               dueDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000);
               break;
            case "Net 45 Days":
               dueDate = new Date(Date.now() + 45 * 24 * 60 * 60 * 1000);
               break;
            case "Immediate Payment":
               dueDate = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
               break;
            default:
               dueDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000);
         }
         flatpickrInstance.setDate(dueDate, true);
         $("#dueDateHidden").val(
            flatpickrInstance.formatDate(dueDate, "Y-m-d H:i")
         );
      }

      // Update due date based on payment terms
      $("#paymentTerms").on("change", function () {
         const paymentTerms = $(this).val();
         let dueDate;

         switch (paymentTerms) {
            case "15":
               dueDate = new Date(Date.now() + 15 * 24 * 60 * 60 * 1000);
               break;
            case "30":
               dueDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000);
               break;
            case "45":
               dueDate = new Date(Date.now() + 45 * 24 * 60 * 60 * 1000);
               break;
            case "immediate":
               dueDate = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
               break;
            default:
               dueDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000); // Fallback
         }

         if (flatpickrInstance) {
            flatpickrInstance.setDate(dueDate, true); // Trigger onChange to update UI
            $("#dueDateHidden").val(
               flatpickrInstance.formatDate(dueDate, "Y-m-d H:i")
            );
         }
      });

      // Load students from API
      $.ajax({
         url: "/admin/students-data",
         method: "GET",
         success: function (response) {
            if (response && Array.isArray(response)) {
               response.forEach(function (student) {
                  studentSelect.append(
                     $("<option>").val(student.student_id).text(student.name)
                  );
               });

               // Refresh Select2 after populating options
               studentSelect.trigger("change");
            }
         },
         error: function () {
            console.error("Failed to load student data");
            Swal.fire({
               icon: "error",
               title: "Error",
               text: "Failed to load student data. Please try again.",
            });
         },
      });
   });

   // Handle the create invoice form submission
   $("#createInvoiceForm").on("submit", function (e) {
      e.preventDefault();

      const formData = $(this).serialize();
      const submitButton = $("#createInvoiceButton");

      // Check for empty required fields
      const requiredFields = [
         { id: "studentSelect", label: "Student" },
         { id: "invoiceType", label: "Invoice Type" },
         { id: "academicPeriod", label: "Academic Period" },
         { id: "amount", label: "Amount" },
         { id: "paymentTerms", label: "Payment Terms" },
         { id: "invoiceDescription", label: "Description" },
      ];
      const emptyFields = [];

      requiredFields.forEach((field) => {
         const fieldValue = $(`#${field.id}`).val();
         if (!fieldValue || fieldValue.trim() === "") {
            emptyFields.push(field.label);
         }
      });

      if (emptyFields.length > 0) {
         Swal.fire({
            icon: "warning",
            title: "Missing Information",
            text: `Please fill in all required fields: ${emptyFields.join(
               ", "
            )}`,
         });
         return;
      }

      $.ajax({
         url: "/admin/create-invoice",
         method: "POST",
         data: formData,
         beforeSend: function () {
            submitButton.prop("disabled", true);
            submitButton.html(
               '<span class="spinner-border spinner-border-lg me-2" role="status" aria-hidden="true"></span> Creating Invoice...'
            );
         },
         success: function (response) {
            if (response.success) {
               //clear form
               $("#createInvoiceForm")[0].reset();

               // Reset Select2 dropdowns
               $("#studentSelect").val(null).trigger("change");
               $("#invoiceType").val(null).trigger("change");
               $("#academicPeriod").val(null).trigger("change");
               $("#paymentTerms").val("30").trigger("change"); // Reset to default

               // Clear flatpickr
               if (flatpickrInstance) {
                  flatpickrInstance.clear();
               }

               // Close modal and refresh table
               $("#createInvoiceModal").modal("hide");

               setTimeout(() => {
                  let successMessage = "Invoice created successfully!";
                  let icon = "success";

                  // Check email status from response
                  const emailSent = response.email_sent === true;
                  const emailResult = response.email_result;

                  if (emailSent && emailResult && emailResult.success) {
                     successMessage +=
                        '<br><span class="text-success">✓ Email notification sent successfully</span>';
                  } else {
                     // Email failed - show warning instead of success
                     icon = "warning";
                     successMessage +=
                        '<br><span class="text-warning">⚠ Invoice created but email notification failed</span>';

                     // Add error details if available
                     const emailError =
                        response.email_error ||
                        (emailResult && emailResult.error) ||
                        (emailResult && emailResult.message);

                     if (emailError) {
                        successMessage += `<br><small class="text-muted">${emailError}</small>`;
                     }
                  }

                  // Show the alert
                  Swal.fire({
                     icon: icon,
                     title: emailSent ? "Success!" : "Invoice Created",
                     html: successMessage,
                     timer: emailSent ? 3500 : 5500, // Show longer if there's an email error
                     timerProgressBar: true,
                     showConfirmButton: true,
                  });
               }, 300);
               // Refresh the DataTable
               dt.ajax.reload(null, false);
            } else {
               // Show error message
               Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.error || "Failed to create invoice",
               });
            }
         },
         complete: function () {
            submitButton.prop("disabled", false);
            submitButton.html("Create Invoice");
         },
         error: function (xhr, status, error) {
            console.error("AJAX Error:", { xhr, status, error });

            let errorMessage = "Failed to create invoice. Please try again.";

            // Try to get more specific error from response
            if (xhr.responseJSON && xhr.responseJSON.error) {
               errorMessage = xhr.responseJSON.error;
            } else if (xhr.responseText) {
               try {
                  const response = JSON.parse(xhr.responseText);
                  if (response.error) {
                     errorMessage = response.error;
                  }
               } catch (e) {
                  // If JSON parsing fails, use default message
                  console.error("Error parsing response:", e);
               }
            }

            // Show error message to user
            Swal.fire({
               icon: "error",
               title: "Error",
               text: errorMessage,
            });
         },
      });
   });

   $("#editBillingForm").on("submit", function (e) {
      e.preventDefault();
      const formData = $(this).serialize();
      const billingId = $("#editBillingId").val();
      const submitButton = $("#editBillingButton");

      $.ajax({
         url: `/admin/update-invoice/${billingId}`,
         method: "POST",
         data: formData,
         beforeSend: function () {
            submitButton.prop("disabled", true);
            submitButton.html(
               '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...'
            );
         },
         success: function (response) {
            if (response.success) {
               $("#editBillingModal").modal("hide");

               // Different messages based on whether changes were made
               let title = "Success!";
               let text = response.message || "Billing updated successfully";
               let icon = "success";
               let timer = 3000;

               // If no changes were detected
               if (response.no_changes) {
                  icon = "info";
                  title = "No Changes";
                  timer = 2000;
               }

               Swal.fire({
                  icon: icon,
                  title: title,
                  text: text,
                  timer: timer,
                  timerProgressBar: true,
               });

               // Refresh the DataTable only if there were actual changes
               if (!response.no_changes) {
                  dt.ajax.reload(null, false);
               }
            } else {
               Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.error || "Failed to update billing",
               });
            }
         },
         error: function (xhr) {
            let errorMessage = "Failed to update billing";
            if (xhr.responseJSON && xhr.responseJSON.error) {
               errorMessage = xhr.responseJSON.error;
            }
            Swal.fire({
               icon: "error",
               title: "Error",
               text: errorMessage,
            });
         },
         complete: function () {
            submitButton.prop("disabled", false);
            submitButton.html("Update Billing");
         },
      });
   });

   $("#sendReminderForm").on("submit", function (e) {
      e.preventDefault();
      const formData = $(this).serialize();
      const submitButton = $("#sendReminderButton");
      $.ajax({
         url: "/admin/billing/send-reminder",
         method: "POST",
         data: formData,
         beforeSend: function () {
            submitButton.prop("disabled", true);
            submitButton.html(
               '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...'
            );
         },
         success: function (response) {
            if (response.success) {
               $("#sendReminderModal").modal("hide");
               Swal.fire({
                  icon: "success",
                  title: "Success",
                  text: response.message || "Reminder sent successfully",
                  timer: 3000,
                  timerProgressBar: true,
               });
            } else {
               Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.error || "Failed to send reminder",
               });
            }
         },
         error: function (xhr) {
            let errorMessage = "Failed to send reminder";
            if (xhr.responseJSON && xhr.responseJSON.error) {
               errorMessage = xhr.responseJSON.error;
            }
            Swal.fire({
               icon: "error",
               title: "Error",
               text: errorMessage,
            });
         },
         complete: function () {
            submitButton.prop("disabled", false);
            submitButton.html("Send Reminder");
         },
      });
   });

   // Reset record payment form when modal is closed
   $("#recordPaymentModal").on("hidden.bs.modal", function () {
      $("#recordPaymentForm")[0].reset();
      $("#paymentInvoiceNumber").text("");
      $("#outstandingAmount").text("");
      $("#paymentPurpose").val(""); // Reset purpose (now readonly input)
      $("#paymentMethod").val("Cash").trigger("change"); // Reset payment method to default
      const submitButton = $('button[form="recordPaymentForm"]');
      submitButton.prop("disabled", false);
      submitButton.html("Record Payment");
   });

   // Record payment form submission
   $("#recordPaymentForm").on("submit", function (e) {
      e.preventDefault();
      const formData = $(this).serialize();
      const submitButton = $('button[form="recordPaymentForm"]');

      // Validate payment amount
      const paymentAmount = parseFloat($("#paymentAmount").val());
      const outstandingAmountText = $("#outstandingAmount").text();
      const outstandingAmount = parseFloat(
         outstandingAmountText.replace(/[^\d.-]/g, "")
      );

      if (paymentAmount <= 0) {
         Swal.fire({
            icon: "warning",
            title: "Invalid Amount",
            text: "Please enter a valid payment amount greater than 0.",
         });
         return;
      }

      if (paymentAmount > outstandingAmount) {
         Swal.fire({
            icon: "warning",
            title: "Payment Exceeds Outstanding Balance",
            text: `Payment amount (${formatCurrency(
               paymentAmount
            )}) cannot exceed outstanding balance (${formatCurrency(
               outstandingAmount
            )}).`,
         });
         return;
      }

      $.ajax({
         url: "/admin/billing/record-payment",
         method: "POST",
         data: formData,
         beforeSend: function () {
            submitButton.prop("disabled", true);
            submitButton.html(
               '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Recording...'
            );
         },
         success: function (response) {
            if (response.success) {
               Swal.fire({
                  icon: "success",
                  title: "Success",
                  text: "Payment recorded successfully!",
               });
               $("#recordPaymentModal").modal("hide");
               $("#recordPaymentForm")[0].reset();
            } else {
               Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.error || "Failed to record payment",
               });
            }
            dt.ajax.reload(null, false);
         },
         error: function (xhr) {
            let errorMessage = "Failed to record payment";
            if (xhr.responseJSON && xhr.responseJSON.error) {
               errorMessage = xhr.responseJSON.error;
            }
            Swal.fire({
               icon: "error",
               title: "Error",
               text: errorMessage,
            });
         },
         complete: function () {
            submitButton.prop("disabled", false);
            submitButton.html("Record Payment");
         },
      });
   });
})();
