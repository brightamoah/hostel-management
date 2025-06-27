
(function () {
    "use strict";
 
    const dt_billing_table = document.querySelector(".datatables-billings");
 
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
 
    if (dt_billing_table) {
       const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
       let dt;
 
       dt = new DataTable(dt_billing_table, {
          ajax: "/admin/billing-data",
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
                         placeholder: "Search Billing",
                         text: "_INPUT_",
                      },
                   },
                   {
                      buttons: [
                         {
                            extend: "collection",
                            className: "btn btn-label-secondary dropdown-toggle ms-4",
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
                                           return data.replace(/<[^>]+>/g, "");
                                        },
                                     },
                                  },
                                  customize: function (doc) {
                                     if (!doc || !doc.content || doc.content.length < 2 || !doc.content[1].table) {
                                        console.warn("PDF structure not as expected:", doc);
                                        return;
                                     }
                                     doc.styles = doc.styles || {};
                                     doc.styles.tableHeader = doc.styles.tableHeader || {};
                                     doc.styles.tableBodyOdd = doc.styles.tableBodyOdd || {};
                                     doc.styles.tableBodyEven = doc.styles.tableBodyEven || {};
                                     doc.defaultStyle = doc.defaultStyle || {};
                                     try {
                                        doc.content[1].table.widths = [
                                           "10%", "20%", "15%", "15%", "15%", "10%", "15%",
                                        ];
                                        doc.content[1].table.layout = {
                                           hLineWidth: () => 0.5,
                                           vLineWidth: () => 0.5,
                                           hLineColor: () => "#666",
                                           vLineColor: () => "#666",
                                           paddingLeft: () => 4,
                                           paddingRight: () => 4,
                                           paddingTop: () => 2,
                                           paddingBottom: () => 2,
                                        };
                                        doc.styles.tableHeader.fontSize = 10;
                                        doc.styles.tableHeader.fillColor = "#f3f3f3";
                                        doc.styles.tableHeader.alignment = "left";
                                        doc.styles.tableBodyOdd.fontSize = 9;
                                        doc.styles.tableBodyEven.fontSize = 9;
                                        doc.defaultStyle.alignment = "left";
                                        doc.styles.title = { fontSize: 14, bold: true, alignment: "center" };
                                        doc.styles.subtitle = { fontSize: 9, italics: true, alignment: "center" };
                                        doc.content.splice(0, 0, {
                                           text: [
                                              { text: "Kings Hostel - Billing Report\n", style: "title" },
                                              { text: `Generated on: ${new Date().toLocaleString()}`, style: "subtitle" },
                                           ],
                                           margin: [0, 0, 0, 10],
                                        });
                                        if (doc.content[1] && doc.content[1].table && doc.content[1].table.body && doc.content[1].table.body.length > 0) {
                                           doc.content[1].table.headerRows = 1;
                                        }
                                        doc.footer = function (currentPage, pageCount) {
                                           return {
                                              text: `Page ${currentPage} of ${pageCount}`,
                                              alignment: "center",
                                              fontSize: 8,
                                              margin: [0, 10, 0, 0],
                                           };
                                        };
                                     } catch (e) {
                                        console.error("Error customizing PDF:", e);
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
             { data: "building", visible: false },
             { data: null, defaultContent: "" },
          ],
          columnDefs: [
             {
                targets: 0,
                render: function (data) {
                   return `<span class="fw-medium">INV-${String(data).padStart(6, "0")}</span>`;
                },
             },
             {
                targets: 1,
                render: function (data) {
                   return `<span>${data}</span>`;
                },
             },
             {
                targets: 2,
                render: function (data) {
                   return formatCurrency(parseFloat(data).toFixed(2));
                },
             },
             {
                targets: 3,
                render: function (data) {
                   return formatDate(data);
                },
             },
             {
                targets: 4,
                render: function (data) {
                   return formatDate(data);
                },
             },
             {
                targets: 5,
                render: function (data) {
                   const statusObj = {
                      Unpaid: { class: "bg-label-warning", title: "Unpaid" },
                      "Fully Paid": { class: "bg-label-success", title: "Fully Paid" },
                      "Partially Paid": { class: "bg-label-info", title: "Partially Paid" },
                      Overdue: { class: "bg-label-danger", title: "Overdue" },
                      Cancelled: { class: "bg-label-secondary", title: "Cancelled" },
                   };
                   const statusInfo = statusObj[data] || { class: "bg-label-secondary", title: data };
                   return `<span class="badge ${statusInfo.class}">${statusInfo.title}</span>`;
                },
             },
             {
                targets: 6,
                render: function (data) {
                   return formatCurrency(parseFloat(data));
                },
             },
             {
                targets: 8,
                searchable: false,
                orderable: false,
                render: function (data, type, full) {
                   return `
                      <div class="d-flex align-items-center gap-2">
                         <a href="javascript:;" class="btn btn-sm btn-icon view-billing-details" 
                            data-billing-id="${full.billing_id}"
                            data-bs-toggle="tooltip" 
                            title="View details">
                            <i class="bx bx-show icon-md"></i>
                         </a>
                         <a href="javascript:;" class="btn btn-sm btn-icon record-payment" 
                            data-billing-id="${full.billing_id}"
                            data-bs-toggle="modal" 
                            data-bs-target="#recordPaymentModal"
                            title="Record payment">
                            <i class="bx bx-dollar-circle icon-md text-success"></i>
                         </a>
                         <a href="javascript:;" class="btn btn-sm btn-icon send-reminder" 
                            data-billing-id="${full.billing_id}"
                            data-bs-toggle="modal" 
                            data-bs-target="#sendReminderModal"
                            title="Send reminder">
                            <i class="bx bx-envelope icon-md text-info"></i>
                         </a>
                      </div>
                   `;
                },
             },
          ],
          order: [[1, "desc"]],
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
                api.column(5).search(val ? "^" + val + "$" : "", true, false).draw();
             });
 
             // Building filter
             $("#filterBuilding").on("change", function () {
                const val = $(this).val();
                api.column(7).search(val).draw();
             });
 
             // Refresh table
             $(".refresh-table").on("click", function () {
                api.ajax.reload();
             });
 
             // View details
             $(document).on("click", ".view-billing-details", function (e) {
                e.preventDefault();
                const billingId = $(this).data("billing-id");
                $.ajax({
                   url: `/admin/billing/${billingId}`,
                   method: "GET",
                   success: function (response) {
                      if (response.error) {
                         Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.error,
                         });
                         return;
                      }
                      const details = response.data;
                      if (!details) {
                         Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "No billing details found",
                         });
                         return;
                      }
                      $("#modalInvoiceId").text(`INV-${String(details.billing_id).padStart(6, "0")}`);
                      $("#modalStudentName").text(details.student_name || "Not specified");
                      $("#modalStudentId").text(`ID: ${details.student_id || "N/A"}`);
                      $("#modalStudentEmail").text(details.student_email || "No email provided");
                      $("#modalStudentPhone").text(details.student_phone || "No phone provided");
                      $("#modalDateIssued").text(formatDate(details.date_issued));
                      $("#modalDueDate").text(formatDate(details.date_due));
                      const itemsTable = $("#modalInvoiceItems tbody");
                      itemsTable.empty();
                      itemsTable.append(`
                         <tr>
                            <td>1</td>
                            <td>${details.description || "Hostel Fee"}</td>
                            <td class="text-end">${formatCurrency(details.amount)}</td>
                         </tr>
                      `);
                      $("#modalSubtotal").text(formatCurrency(details.amount));
                      $("#modalTotal").text(formatCurrency(details.amount));
                      $("#modalAmountPaid").text(formatCurrency(details.paid_amount));
                      $("#modalBalanceDue").text(formatCurrency(details.amount - details.paid_amount));
                      const transactionTable = $("#modalTransactionHistory tbody");
                      transactionTable.empty();
                      if (details.transactions && details.transactions.length > 0) {
                         details.transactions.forEach(function (transaction) {
                            transactionTable.append(`
                               <tr>
                                  <td>${moment(transaction.payment_date).format("MMM D, YYYY")}</td>
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
                      $("#viewInvoiceModal").modal("show");
                   },
                   error: function () {
                      Swal.fire({
                         icon: "error",
                         title: "Error",
                         text: "Failed to load billing details",
                      });
                   },
                });
             });
 
             // Record payment
             $(document).on("click", ".record-payment", function () {
                const billingId = $(this).data("billing-id");
                $("#recordPaymentBillingId").val(billingId);
                $("#recordPaymentModal").modal("show");
             });
 
             // Send reminder
             $(document).on("click", ".send-reminder", function () {
                const billingId = $(this).data("billing-id");
                $("#reminderBillingId").val(billingId);
                $("#sendReminderModal").modal("show");
             });
 
             // Load building options
             $.ajax({
                url: "/admin/building-data",
                method: "GET",
                success: function (response) {
                   const buildingSelect = $("#filterBuilding");
                   buildingSelect.find("option:not(:first)").remove();
                   if (response && Array.isArray(response)) {
                      response.forEach(function (building) {
                         buildingSelect.append($("<option>").val(building).text(building));
                      });
                   }
                },
                error: function () {
                   console.error("Failed to load building data");
                },
             });
          },
       });
    }
 
    // Initialize UI components
    document.addEventListener("DOMContentLoaded", function () {
       // Initialize Select2
       if ($.fn.select2) {
          $("#statusFilter").select2();
          $("#filterBuilding").select2();
       }
 
       // Initialize flatpickr
       if (typeof flatpickr !== "undefined") {
          flatpickr("#filterDueDate", {
             enableTime: true,
             dateFormat: "Y-m-d H:i",
             altInput: true,
             altFormat: "F j, Y",
             allowInput: true,
             onClose: function (selectedDates, dateStr) {
                if (dt && dateStr) {
                   dt.api().column(4).search(dateStr).draw();
                } else if (dt) {
                   dt.api().column(4).search("")).draw();
         
                },
             });
         }
 
       // Initialize form validation for create invoice
       const createInvoiceForm = document.querySelector("#createInvoiceForm");
       if (createInvoiceForm && typeof FormValidation !== "undefined") {
          const fv = FormValidation.formValidation(createInvoiceForm, {
             fields: {
                student_id: {
                   validators: {
                      notEmpty: {
                         message: "Please select a student",
                      },
                   },
                },
                amount: {
                   validators: {
                      notEmpty: {
                         message: "Amount is required",
                      },
                      numeric: {
                         message: "Amount must be a valid number",
                      },
                      greaterThan: {
                         message: "Amount must be greater than 0",
                         min: 0.01,
                      },
                   },
                },
                date_due: {
                   validators: {
                      notEmpty: {
                         message: "Due date is required",
                      },
                      date: {
                         format: "YYYY-MM-DD HH:mm",
                         message: "Invalid date format",
                      },
                   },
                },
                purpose: {
                   validators: {
                      notEmpty: {
                         message: "Invoice type is required",
                      },
                   },
                },
                academic_period: {
                   validators: {
                      notEmpty: {
                         message: "Academic period is required",
                      },
                   },
                },
                payment_terms: {
                   validators: {
                      notEmpty: {
                         message: "Payment terms are required",
                      },
                   },
                },
                description: {
                   validators: {
                      notEmpty: {
                         message: "Description is required",
                      },
                      stringLength: {
                         min: 1,
                         max: 255,
                         message: "Description must be between 1 and 255 characters",
                      },
                   },
                },
                },
                plugins: {
                  trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                   eleValidClass: "",
                   rowSelector: ".col-md-6",
                }),
                   autoFocus: new FormValidation.plugins.AutoFocus(),
                },
                init: (instance) => {
                   instance.on後に("core.element.validated", function (e) {
                      if (e.valid) {
                         const groupEle = FormValidation.utils.closest(e.element, e.g., ".col-md-6");
                         if (groupEle) {
                            groupEle.classList.remove("has-error");
                         }
                      }
                   });
                },
                }),
          });
       }
 
       // Create Invoice Modal handler
       $("#createInvoiceModal").on("show.bs.modal", function () {
          const studentSelect = $("#studentSelect");
 
          // Clear previous options
          studentSelect.empty().append('<option value="">Select a student</option>');
 
          // Initialize Select2
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
                placeholder: "Select period",
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
 
          // Initialize flatpickr
          if (typeof flatpickr !== "undefined") {
             $("#dueDateInput").flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                allowInput: true,
                altInput: true,
                altFormat: "F j, Y at h:i K",
                time_24hr: true,
                static: true,
                defaultDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000),
                plugins: [
                   new confirmDatePlugin({
                      confirmText: "OK",
                      showAlways: false,
                      theme: "dark",
                   }),
                ],
             });
          }
 
          // Load students
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
                   studentSelect.trigger("change");
                }
             },
             error: function () {
                console.error("Failed to load student data");
                Swal.fire({
                   icon: "error",
                   title: "Error",
                   text: "Failed to load student data",
                   "Please try again.",
                });
             },
          });
       });
 
       // Handle form submission
       $("#createInvoiceForm").on("submit", function (e) {
          e.preventDefault();
 
          // Validate form
          if (typeof FormValidation !== 'undefined') {
             const fv = FormValidation.formValidation(createInvoiceForm);
             fv.validate().then(function (status) {
                if (status === 'Valid') {
                   const formData = $(this).serializeArray();
                   $.ajax({
                      url: "/admin/create-invoice",
                      method: "POST",
                      data: formData,
                      headers: { "X-CSRF-Token": csrfToken },
                      success: function (response) {
                         if (response.success) {
                            $("#createInvoiceModal").modal("hide");
                            dt.api().ajax.reload();
                            Swal.fire({
                               icon: "success",
                               title: "Success",
                               text: "Invoice created successfully!",
                            });
                            createInvoiceForm.reset();
                            $("#studentSelect").trigger("change");
                         } else {
                            Swal.fire({
                               icon: "error",
                               title: "Error",
                               text: response.error || "Failed to create invoice",
                            });
                         }
                      },
                      error: function (xhr) {
                         Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: xhr.responseJSON?.error || "Failed to create invoice",
                            "Please try again.",
                         });
                      },
                   });
                }
             });
          }
       });
    })();
 })();
 