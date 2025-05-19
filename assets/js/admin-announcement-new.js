$(document).ready(function () {
   ("use strict");

   // DOM Elements
   const searchInput = $("#searchInput");
   const gridView = $(".announcement-grid-view");
   const listView = $(".announcement-list-view");
   const viewToggleBtns = $(".view-toggle-btn");
   const filterTags = $(".filter-tags");
   const noResults = $("#noResults");
   const announcementItems = $(".announcement-item");

   // Initialize variables to track current filters and sort
   let currentFilters = {
      search: "",
      priority: "all",
      audience: "all",
      sort: "newest",
   };

   // Initialize view preference (grid or list)
   let currentView = "grid";

   // View Toggle functionality with localStorage persistence
   const savedViewMode = localStorage.getItem("announcementViewMode") || "grid";

   // Apply the saved view mode on page load
   setViewMode(savedViewMode);

   // Handle view toggle button clicks
   $(".view-toggle-btn").on("click", function () {
      const viewMode = $(this).data("view");
      setViewMode(viewMode);

      // Save the preference to localStorage
      localStorage.setItem("announcementViewMode", viewMode);
   });

   // Function to set the view mode
   function setViewMode(viewMode) {
      // Update active button state
      $(".view-toggle-btn").removeClass("active");
      $(`.view-toggle-btn[data-view="${viewMode}"]`).addClass("active");

      // Show the appropriate view
      if (viewMode === "grid") {
         $(".announcement-grid-view").removeClass("d-none");
         $(".announcement-list-view").addClass("d-none");
      } else {
         $(".announcement-grid-view").addClass("d-none");
         $(".announcement-list-view").removeClass("d-none");
      }
   }

   /**
    * Handle view toggling between grid and list
    */
   viewToggleBtns.on("click", function () {
      const view = $(this).data("view");

      // Update active state
      viewToggleBtns.removeClass("active");
      $(this).addClass("active");

      // Toggle views
      if (view === "grid") {
         gridView.removeClass("d-none");
         listView.addClass("d-none");
         currentView = "grid";
      } else {
         gridView.addClass("d-none");
         listView.removeClass("d-none");
         currentView = "list";
      }

      // Apply current filters to the new view
      applyFilters();
   });

   /**
    * Handle search input
    */
   searchInput.on("keyup", function () {
      currentFilters.search = $(this).val().toLowerCase();
      applyFilters();
      updateFilterTags();
   });

   /**
    * Handle priority filter selection
    */
   $(".filter-priority").on("click", function () {
      const priority = $(this).data("value");

      // Update active state
      $(".filter-priority").removeClass("active");
      $(this).addClass("active");

      currentFilters.priority = priority;
      applyFilters();
      updateFilterTags();
   });

   /**
    * Handle audience filter selection
    */
   $(".filter-audience").on("click", function () {
      const audience = $(this).data("value");

      // Update active state
      $(".filter-audience").removeClass("active");
      $(this).addClass("active");

      currentFilters.audience = audience;
      applyFilters();
      updateFilterTags();
   });

   /**
    * Handle sort options
    */
   $(".sort-option").on("click", function () {
      const sort = $(this).data("sort");

      // Update active state
      $(".sort-option").removeClass("active");
      $(this).addClass("active");

      currentFilters.sort = sort;
      applyFilters();
      updateFilterTags();
   });

   /**
    * Apply all current filters and sort to the announcements
    */
   function applyFilters() {
      let visibleCount = 0;

      // First sort the announcements
      sortAnnouncements();

      // Then apply the filters
      announcementItems.each(function () {
         const $item = $(this);
         const title = $item.data("title").toLowerCase();
         const content = $item
            .find(".announcement-preview")
            .text()
            .toLowerCase();
         const priority = $item.data("priority");
         const audience = $item.data("audience");

         // Check if the announcement matches all active filters
         const matchesSearch =
            currentFilters.search === "" ||
            title.includes(currentFilters.search) ||
            content.includes(currentFilters.search);

         const matchesPriority =
            currentFilters.priority === "all" ||
            priority === currentFilters.priority;

         const matchesAudience =
            currentFilters.audience === "all" ||
            audience === currentFilters.audience;

         // Show or hide based on filter matches
         if (matchesSearch && matchesPriority && matchesAudience) {
            $item.removeClass("d-none");
            visibleCount++;
         } else {
            $item.addClass("d-none");
         }
      });

      // Show or hide "no results" message
      if (visibleCount === 0) {
         noResults.removeClass("d-none");
      } else {
         noResults.addClass("d-none");
      }
   }

   /**
    * Sort the announcements based on the current sort option
    */
   function sortAnnouncements() {
      // Define sort values for priorities
      const priorityValues = {
         Urgent: 4,
         High: 3,
         Medium: 2,
         Low: 1,
      };

      // Get the current container (grid or list)
      const container =
         currentView === "grid"
            ? $(".announcement-grid-view .row")
            : $(".announcement-list-view tbody");

      // Get all announcement items that will be sorted
      const items = container
         .children(".announcement-item, tr.announcement-item")
         .get();

      // Sort the items
      items.sort(function (a, b) {
         const $a = $(a);
         const $b = $(b);

         switch (currentFilters.sort) {
            case "newest":
               return $b.data("date") - $a.data("date");

            case "oldest":
               return $a.data("date") - $b.data("date");

            case "priority":
               return (
                  priorityValues[$b.data("priority")] -
                  priorityValues[$a.data("priority")]
               );

            case "title":
               return $a.data("title").localeCompare($b.data("title"));

            default:
               return 0;
         }
      });

      // Re-append the sorted items to the container
      $.each(items, function (index, item) {
         container.append(item);
      });
   }

   /**
    * Update the filter tags displayed above the announcements
    */
   function updateFilterTags() {
      // Clear current tags
      filterTags.empty();

      // Add search filter tag if there's a search query
      if (currentFilters.search) {
         addFilterTag("search", `Search: ${currentFilters.search}`);
      }

      // Add priority filter tag if not showing all priorities
      if (currentFilters.priority !== "all") {
         addFilterTag("priority", `Priority: ${currentFilters.priority}`);
      }

      // Add audience filter tag if not showing all audiences
      if (currentFilters.audience !== "all") {
         addFilterTag("audience", `Audience: ${currentFilters.audience}`);
      }

      // Add sort tag
      const sortLabels = {
         newest: "Newest First",
         oldest: "Oldest First",
         priority: "Priority (High to Low)",
         title: "Title (A-Z)",
      };
      addFilterTag("sort", `Sort: ${sortLabels[currentFilters.sort]}`);
   }

   /**
    * Add a filter tag to the filter tags container
    */
   function addFilterTag(type, text) {
      const tag = $(`
<div class="badge mb-1 filter-badge" data-type="${type}">
${text}
<i class="bx bx-x ms-1 clear-filter" data-type="${type}"></i>
</div>
`);

      filterTags.append(tag);

      // Add click handler to remove the filter
      tag.find(".clear-filter").on("click", function () {
         const filterType = $(this).data("type");

         if (filterType === "search") {
            searchInput.val("");
            currentFilters.search = "";
         } else if (filterType === "priority") {
            $('.filter-priority[data-value="all"]').addClass("active");
            $(".filter-priority")
               .not('[data-value="all"]')
               .removeClass("active");
            currentFilters.priority = "all";
         } else if (filterType === "audience") {
            $('.filter-audience[data-value="all"]').addClass("active");
            $(".filter-audience")
               .not('[data-value="all"]')
               .removeClass("active");
            currentFilters.audience = "all";
         } else if (filterType === "sort") {
            $('.sort-option[data-sort="newest"]').addClass("active");
            $(".sort-option").not('[data-sort="newest"]').removeClass("active");
            currentFilters.sort = "newest";
         }

         applyFilters();
         updateFilterTags();
      });
   }

   /**
    * Handle view announcement modal
    */
   $(".view-announcement").on("click", function () {
      const announcementId = $(this).data("id");
      const title = $(this).data("title");
      const content = $(this).data("content");
      const priority = $(this).data("priority");
      const targetAudience = $(this).data("target-audience");
      const date = $(this).data("date");

      // Set the modal content
      $("#view_title").text(title);
      $("#view_content").html(content);
      $("#view_date").text(date);

      // Set priority badge color
      let priorityBadgeClass = "";
      switch (priority) {
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
         .attr("class", `badge ${priorityBadgeClass} rounded-pill`)
         .html(
            `<span class="priority-indicator priority-${priority.toLowerCase()}"></span>${priority}`
         );

      // Set audience badge
      $("#view_audience_badge").html(
         `<i class="bx bx-group me-1"></i>${targetAudience}`
      );

      // Set edit link
      $("#editFromView").attr(
         "href",
         `/admin/announcements/edit/${announcementId}`
      );

      // Show the modal
      $("#viewAnnouncementModal").modal("show");
   });

   /**
    * Handle delete announcement
    */
   $(".delete-announcement").on("click", function () {
      const announcementId = $(this).data("id");

      // Show confirmation dialog
      Swal.fire({
         title: "Are you sure?",
         text: "You won't be able to revert this!",
         icon: "warning",
         showCancelButton: true,
         confirmButtonText: "Yes, delete it!",
         customClass: {
            confirmButton: "btn btn-danger me-3",
            cancelButton: "btn btn-label-secondary",
         },
         buttonsStyling: false,
      }).then(function (result) {
         if (result.isConfirmed) {
            // Send delete request
            $.ajax({
               url: `/api/announcements/${announcementId}`,
               type: "DELETE",
               success: function (response) {
                  // Show success message
                  Swal.fire({
                     icon: "success",
                     title: "Deleted!",
                     text: "The announcement has been deleted.",
                     customClass: {
                        confirmButton: "btn btn-success",
                     },
                     buttonsStyling: false,
                  }).then(function () {
                     // Reload the page to reflect changes
                     window.location.reload();
                  });
               },
               error: function (xhr) {
                  // Show error message
                  Swal.fire({
                     icon: "error",
                     title: "Error!",
                     text: "There was a problem deleting the announcement.",
                     customClass: {
                        confirmButton: "btn btn-primary",
                     },
                     buttonsStyling: false,
                  });
               },
            });
         }
      });
   });

   // Initialize the page on load
   updateFilterTags();
   applyFilters();
});
