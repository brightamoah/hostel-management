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

   // Initialize Summernote
   let summernoteInitialized = false;
   if (!summernoteInitialized) {
      $("#content").summernote({
         height: 200,
         placeholder: "Edit announcement content...",
         iconPrefix: "fa",
         toolbar: [
            ["style", ["style"]],
            ["font", ["bold", "italic", "underline", "clear"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["table", ["table"]],
            ["insert", ["link", "picture", "video"]],
            ["view", ["fullscreen", "codeview", "help"]],
         ],
      });
      summernoteInitialized = true;
   }

   // Initialize Select2
   $("#priority, #target_audience").select2({
      width: "100%",
   });

   // Handle form submission
   $("#editAnnouncementForm").on("submit", function (e) {
      // Ensure Summernote content is updated
      $("#content").val($("#content").summernote("code"));
   });

   // Destroy Summernote on page unload
   $(window).on("unload", function () {
      if (summernoteInitialized) {
         $("#content").summernote("destroy");
         summernoteInitialized = false;
      }
   });
});
