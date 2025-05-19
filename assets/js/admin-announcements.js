$(document).ready(function () {
   // Configure Summernote icons
   $.extend($.summernote.options, {
      icons: {
         align: "fa fa-align",
         alignCenter: "fa fa-align-center",
         alignJustify: "fa fa-align-justify",
         alignLeft: "fa fa-align-left",
         alignRight: "fa fa-align-right",
         indent: "fa fa-indent",
         outdent: "fa fa-outdent",
         arrowsAlt: "fa fa-arrows-alt",
         bold: "fa fa-bold",
         caret: "fa fa-caret-down",
         circle: "fa fa-circle",
         close: "fa fa-close",
         code: "fa fa-code",
         eraser: "fa fa-eraser",
         font: "fa fa-font",
         italic: "fa fa-italic",
         link: "fa fa-link",
         unlink: "fa fa-chain-broken",
         magic: "fa fa-magic",
         menuCheck: "fa fa-check",
         minus: "fa fa-minus",
         orderedlist: "fa fa-list-ol",
         pencil: "fa fa-pencil",
         picture: "fa fa-picture-o",
         question: "fa fa-question",
         redo: "fa fa-repeat",
         square: "fa fa-square",
         strikethrough: "fa fa-strikethrough",
         subscript: "fa fa-subscript",
         superscript: "fa fa-superscript",
         table: "fa fa-table",
         textHeight: "fa fa-text-height",
         trash: "fa fa-trash",
         underline: "fa fa-underline",
         undo: "fa fa-undo",
         unorderedlist: "fa fa-list-ul",
         video: "fa-solid fa-video",
         picture: "fa-solid fa-image",
      },
   });

   let currentAnnouncementId = null;

   // DataTables initialization
   const dt = $("#announcementsTable").DataTable({
      dom: '<"card-header d-flex flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      buttons: [
         {
            extend: "collection",
            className: "btn btn-label-primary dropdown-toggle me-2",
            text: '<i class="bx bx-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
            buttons: [
               {
                  extend: "print",
                  text: '<i class="bx bx-printer me-1"></i>Print',
                  className: "dropdown-item",
               },
               {
                  extend: "csv",
                  text: '<i class="bx bx-file me-1"></i>Csv',
                  className: "dropdown-item",
               },
               {
                  extend: "excel",
                  text: '<i class="bx bxs-file-excel me-1"></i>Excel',
                  className: "dropdown-item",
               },
               {
                  extend: "pdf",
                  text: '<i class="bx bxs-file-pdf me-1"></i>Pdf',
                  className: "dropdown-item",
               },
            ],
         },
      ],
      order: [[4, "desc"]],
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      responsive: true,
      columnDefs: [{ targets: 5, orderable: false }],
   });

   // Search functionality
   $("#searchInput").on("keyup", function () {
      dt.search(this.value).draw();
   });

   // Filter functionality
   $(".filter-item").on("change", function () {
      const filterType = $(this).data("filter");
      const value = $(this).val();

      if (value === "all") {
         dt.column(filterType === "priority" ? 2 : 3)
            .search("")
            .draw();
      } else {
         dt.column(filterType === "priority" ? 2 : 3)
            .search(value)
            .draw();
      }
   });

   // Handle view announcement
   $(document).on("click", ".view-announcement", function () {
      const id = $(this).data("id");
      const title = $(this).data("title");
      const content = $(this).data("content");
      const priority = $(this).data("priority");
      const targetAudience = $(this).data("target-audience");
      const date = $(this).data("date");

      currentAnnouncementId = id;
      $("#view_title").text(title);
      $("#view_content").html(content);
      $("#view_priority").text(priority);
      $("#view_target_audience").text(targetAudience);
      $("#view_date").text(`Posted on ${date}`);
      $("#editFromView").attr("href", `/admin/announcements/edit/${id}`);
      $("#viewAnnouncementModal").modal("show");
   });

   // Handle delete announcement
   $(document).on("click", ".delete-announcement", function () {
      const id = $(this).data("id");

      Swal.fire({
         title: "Are you sure?",
         text: "You won't be able to revert this!",
         icon: "warning",
         showCancelButton: true,
         confirmButtonText: "Yes, delete it!",
         customClass: {
            confirmButton: "btn btn-primary me-3",
            cancelButton: "btn btn-label-secondary",
         },
         buttonsStyling: false,
      }).then(function (result) {
         if (result.isConfirmed) {
            $.ajax({
               type: "POST",
               url: "/admin/announcements/action",
               data: { action: "delete", announcement_id: id },
               dataType: "json",
               success: function (response) {
                  Swal.fire({
                     icon: response.status,
                     title: response.status === "success" ? "Success" : "Error",
                     text: response.message,
                     customClass: { confirmButton: "btn btn-primary" },
                     buttonsStyling: false,
                  }).then(() => {
                     if (response.status === "success") location.reload();
                  });
               },
               error: function () {
                  Swal.fire({
                     icon: "error",
                     title: "Error",
                     text: "Something went wrong. Please try again.",
                     customClass: { confirmButton: "btn btn-primary" },
                     buttonsStyling: false,
                  });
               },
            });
         }
      });
   });
});
