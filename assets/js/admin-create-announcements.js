$(document).ready(function () {
   // Configure Summernote icons
   $.extend($.summernote.options.icons, {
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
      video: "fa fa-video",
      picture: "fa fa-image",
   });

   // Initialize Summernote
   let summernoteInitialized = false;
   if (!summernoteInitialized) {
      $("#content").summernote({
         height: 200,
         placeholder: "Compose announcement content...",
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
   $(
      "#priority, #bulk_target_audience, #specific_target_type, #specific_target_id"
   ).select2({
      width: "100%",
   });
   
   // Reset the specific target ID dropdown on page load
   $("#specific_target_id")
      .empty()
      .append('<option value="">Select Target Type First</option>')
      .prop("disabled", true)
      .prop("required", false); // Initially remove required attribute

   // Initialize the form based on the default tab (bulk)
   updateFormFieldRequirements("bulk");

   // Update tab handling to ensure proper validation state
   $("#announcementTabs a").on("shown.bs.tab", function (e) {
      const activeTab = $(e.target).attr("href").substring(1); // #bulk -> bulk
      $("#target_mode").val(activeTab);
      
      // Update form field requirements based on the active tab
      updateFormFieldRequirements(activeTab);
   });
   
   function updateFormFieldRequirements(activeTab) {
      if (activeTab === "bulk") {
         // Bulk tab is active
         $("#bulk_target_audience").prop("required", true);
         
         // Remove required from specific fields when in bulk tab
         $("#specific_target_type, #specific_target_id").prop("required", false);
         
         // Reset specific fields
         if (!$("#specific_target_id").prop("disabled")) {
            $("#specific_target_id")
               .val("")
               .trigger("change")
               .prop("disabled", true);
            $("#specific_target_type")
               .val("")
               .trigger("change");
         }
      } else {
         // Specific tab is active
         $("#bulk_target_audience").prop("required", false);
         $("#specific_target_type").prop("required", true);
         
         // Only set specific_target_id as required if it's not disabled
         $("#specific_target_id").prop("required", !$("#specific_target_id").prop("disabled"));
      }
   }

   // Populate specific target dropdown based on target type
   $("#specific_target_type").on("change", function () {
      const targetType = $(this).val();
      const $specificTargetId = $("#specific_target_id");

      if (!targetType) {
         $specificTargetId
            .empty()
            .append('<option value="">Select Target Type First</option>')
            .prop("disabled", true)
            .prop("required", false);
         return;
      }

      // Show loading indicator
      $specificTargetId
         .empty()
         .append('<option value="">Loading options...</option>')
         .prop("disabled", true);

      // Add debugging output
      console.log("Fetching targets for type:", targetType);

      // Use a direct AJAX call to the API endpoint
      $.ajax({
         type: "GET",
         url: `/admin/announcements/fetch-targets/${targetType}`,
         dataType: "json",
         // Important: Include credentials
         xhrFields: {
            withCredentials: true,
         },
         beforeSend: function (xhr) {
            // Log the request for debugging
            console.log("Sending request to:", this.url);
         },
         success: function (response) {
            console.log("Success response:", response);
            if (
               response.status === "success" &&
               response.data &&
               response.data.length > 0
            ) {
               $specificTargetId
                  .empty()
                  .append('<option value="">Select Target</option>');
               $.each(response.data, function (index, item) {
                  $specificTargetId.append(
                     `<option value="${item.id}">${item.name}</option>`
                  );
               });
               $specificTargetId.prop("disabled", false).prop("required", true);
            } else {
               console.error("Empty or error response:", response);
               $specificTargetId
                  .empty()
                  .append('<option value="">No targets available</option>');
               $specificTargetId.prop("disabled", true).prop("required", false);

               Swal.fire({
                  icon: "warning",
                  title: "No Targets Found",
                  text: "No targets available for the selected type",
                  customClass: { confirmButton: "btn btn-primary" },
               });
            }
         },
         error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.log("Response text:", xhr.responseText);
            console.log("Status code:", xhr.status);

            $specificTargetId
               .empty()
               .append('<option value="">Error loading options</option>');
            $specificTargetId.prop("disabled", true).prop("required", false);

            Swal.fire({
               icon: "error",
               title: "Error",
               text: "Failed to fetch targets. Please try again.",
               customClass: { confirmButton: "btn btn-primary" },
            });
         },
      });
   });

   // Handle form submission
   $("#createAnnouncementForm").on("submit", function (e) {
      const activeTab = $(".nav-link.active").attr("href").substring(1); // #bulk or #specific

      // If specific tab is active, make sure we have a valid selection
      if (activeTab === "specific") {
         const targetType = $("#specific_target_type").val();
         const targetId = $("#specific_target_id").val();

         // If no target is selected, prevent form submission and show error
         if (!targetType || !targetId) {
            e.preventDefault();

            // Show error message
            Swal.fire({
               icon: "error",
               title: "Validation Error",
               text: "Please select both target type and specific target",
               customClass: { confirmButton: "btn btn-primary" },
            });

            return false;
         }
      }

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
   
   // Trigger target load for student type on page load if specific tab is active
   if ($("#specific-tab").hasClass("active")) {
      // If the specific tab is active on page load, load student data
      $("#specific_target_type").trigger("change");
   }
});