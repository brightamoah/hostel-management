(function () {
   "use strict";

   const dt_announcements_table = document.querySelector(
      ".datatables-announcements"
   );

   if (dt_announcements_table) {
      const csrfToken =
         document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";
      let dt;

      dt = new DataTable(dt_announcements_table, {
         ajax: "/admin/announcements-data",
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
                                    columns: [0, 1, 2, 3, 4],
                                    format: {
                                       body: function (
                                          data,
                                          row,
                                          column,
                                          node
                                       ) {
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
                                    columns: [0, 1, 2, 3, 4],
                                    format: {
                                       body: function (
                                          data,
                                          row,
                                          column,
                                          node
                                       ) {
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
                                    columns: [0, 1, 2, 3, 4],
                                    format: {
                                       body: function (
                                          data,
                                          row,
                                          column,
                                          node
                                       ) {
                                          return data.replace(/<[^>]+>/g, "");
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
                                          "30%",
                                          "20%",
                                          "15%",
                                          "15%",
                                          "20%",
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
                                                text: "Kings Hostel - Announcements Report\n",
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
            { data: "title" },
            { data: "posted_by_name" },
            { data: "priority" },
            { data: "target_audience" },
            { data: "date_posted" },
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
               targets: 2, // Priority
               render: function (data) {
                  const priorityObj = {
                     Urgent: {
                        class: "bg-label-danger",
                        title: "Urgent",
                        description:
                           "Requires immediate attention or action from recipients",
                     },
                     High: {
                        class: "bg-label-warning",
                        title: "High",
                        description:
                           "Important information that should be read promptly",
                     },
                     Medium: {
                        class: "bg-label-info",
                        title: "Medium",
                        description:
                           "Standard announcement with moderate importance",
                     },
                     Low: {
                        class: "bg-label-success",
                        title: "Low",
                        description:
                           "General information with no urgent action required",
                     },
                  };
                  const priorityInfo = priorityObj[data] || {
                     class: "bg-label-secondary",
                     title: data,
                     description: "Priority level not specified",
                  };
                  return `<span class="badge ${priorityInfo.class}" 
                               data-bs-toggle="tooltip" 
                               data-bs-placement="top" 
                               title="${priorityInfo.description}">
                           <span class="text-center priority-${data.toLowerCase()}"></span>${
                     priorityInfo.title
                  }
                        </span>`;
               },
            },
            {
               targets: 4, // Date Posted
               render: function (data) {
                    const date = new Date(data);
                    return new Intl.DateTimeFormat('en-GH', {
                      year: 'numeric',
                      month: 'short',
                      day: 'numeric',
                      hour: 'numeric',
                      minute: '2-digit',
                      hour12: true
                    }).format(date);
               },
            },
            {
               targets: 5, // Actions
               searchable: false,
               orderable: false,
               render: function (data, type, full) {
                  return `
                           <div class="d-flex align-items-center gap-2">
                               <a href="javascript:;" class="btn btn-sm btn-icon view-announcement"
                                  data-announcement-id="${full.announcement_id}"
                                  data-bs-toggle="tooltip" 
                                  title="View details">
                                  <i class="bx bx-show icon-md"></i>
                               </a>
                               <a href="/admin/announcements/edit/${full.announcement_id}" 
                                    class="btn btn-sm btn-icon" 
                                    data-bs-toggle="tooltip" 
                                  title="Edit">
                                  <i class="bx bx-edit icon-md"></i>
                               </a>
                               <a href="javascript:;" class="btn btn-sm btn-icon delete-announcement"
                                  data-announcement-id="${full.announcement_id}"
                                  data-bs-toggle="tooltip" 
                                  title="Delete">
                                  <i class="bx bx-trash icon-md"></i>
                               </a>
                           </div>
                       `;
               },
            },
         ],
         order: [[4, "desc"]],
         responsive: {
            details: {
               display: DataTable.Responsive.display.modal({
                  header: function (row) {
                     const data = row.data();
                     return `Announcement: ${data.title}`;
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
            emptyTable: "No announcements found",
            zeroRecords: "No announcements match your filters",
         },
         initComplete: function () {
            const api = this.api();

            // Initialize Select2 for filters
            $("#priorityFilter, #audienceFilter").select2({
               width: "100%",
               minimumResultsForSearch: Infinity,
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(
               document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Search box
            $("#announcementSearch").on("keyup", function () {
               api.search(this.value).draw();
            });

            // Priority filter
            $("#priorityFilter").on("change", function () {
               const val = $(this).val();
               // If empty string (All Priorities), clear the search
               api.column(2)
                  .search(val || "", false, false)
                  .draw();
            });

            // Audience filter
            $("#audienceFilter").on("change", function () {
               const val = $(this).val();
               // If empty string (All Audiences), clear the search
               api.column(3)
                  .search(val || "", false, false)
                  .draw();
            });

            // Add reset filters button functionality
            $(".reset-filters").on("click", function () {
               // Reset search box
               $("#announcementSearch").val("");

               // Reset select dropdowns
               $("#priorityFilter, #audienceFilter").val("").trigger("change");

               // Clear all filters and redraw table
               api.search("").columns().search("").draw();
            });
            // Refresh table
            $(".refresh-table").on("click", function () {
               api.ajax.reload();
            });

            // View announcement
            $(document).on("click", ".view-announcement", function (e) {
               e.preventDefault();
               const announcementId = $(this).data("announcement-id");

               $.ajax({
                  url: `/admin/announcements/${announcementId}`,
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

                     const announcement = data.announcement;
                     $("#view_title").text(announcement.title);
                     $("#view_content").html(announcement.content);
                     $("#view_date").text(
                        moment(announcement.date_posted).format(
                           "MMM D, YYYY h:mm A"
                        )
                     );

                     let priorityBadgeClass = "";
                     switch (announcement.priority) {
                        case "Urgent":
                           priorityBadgeClass = "bg-label-danger";
                           break;
                        case "High":
                           priorityBadgeClass = "bg-label-warning";
                           break;
                        case "Medium":
                           priorityBadgeClass = "bg-label-info";
                           break;
                        default:
                           priorityBadgeClass = "bg-label-success";
                     }
                     $("#view_priority_badge")
                        .attr(
                           "class",
                           `badge ${priorityBadgeClass} rounded-pill`
                        )
                        .html(
                           `<span class="priority-indicator priority-${announcement.priority.toLowerCase()}"></span>${
                              announcement.priority
                           }`
                        );

                     $("#view_audience_badge").html(
                        `<i class="bx bx-group me-1"></i>${announcement.target_audience}`
                     );
                     $("#editFromView").attr(
                        "href",
                        `/admin/announcements/edit/${announcementId}`
                     );

                     $("#view_read_stats").html(
                        `<i class="bx bx-check-circle me-1"></i>${announcement.read_count}/${announcement.total_users}`
                     );

                     $("#view_posted_by").html(
                        `<span class="text-muted small">Posted by</span>
                            <h6 class="mb-0" id="view_posted_by">${
                               announcement.posted_by
                                  ? announcement.posted_by
                                  : "Admin"
                            }</h6>`
                     );

                     $("#viewAnnouncementModal").modal("show");
                  },
                  error: function () {
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to load announcement details",
                     });
                  },
               });
            });

            // Modal content formatting
            $("#viewAnnouncementModal")
               .on("show.bs.modal", function () {
                  $(this).find(".modal-content");
                  // .addClass("animate__animated animate__fadeIn");
               })
               .on("hide.bs.modal", function () {
                  $(this)
                     .find(".modal-content")
                     .removeClass("animate__animated animate__bounceIn");
               });

            // Delete announcement
            $(document).on("click", ".delete-announcement", function (e) {
               e.preventDefault();
               const announcementId = $(this).data("announcement-id");

               Swal.fire({
                  title: "Are you sure?",
                  text: `Delete announcement #${announcementId}?`,
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Yes, delete it!",
                  cancelButtonText: "No, cancel!",
               }).then((result) => {
                  if (result.isConfirmed) {
                     $.ajax({
                        url: "/admin/announcements/action",
                        method: "POST",
                        data: {
                           action: "delete",
                           announcement_id: announcementId,
                           csrf: csrfToken,
                        },
                        success: function (response) {
                           // Change this line to check for response.status instead of response.success
                           if (response.status === "success") {
                              Swal.fire({
                                 icon: "success",
                                 title: "Success",
                                 text:
                                    response.message ||
                                    "Announcement deleted successfully!",
                                 timer: 2000,
                              }).then(() => {
                                 api.ajax.reload();
                              });
                           } else {
                              Swal.fire({
                                 icon: "error",
                                 title: "Error",
                                 text:
                                    response.message ||
                                    "Failed to delete announcement",
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
         },
      });
   }
})();
