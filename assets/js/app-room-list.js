(function () {
   "use strict";

   const dt_rooms_table = document.querySelector(".datatables-rooms");

   if (dt_rooms_table) {
      const csrfToken =
         document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

      const dt = new DataTable(dt_rooms_table, {
         ajax: "/room-data",

         layout: {
            topStart: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     pageLength: {
                        menu: [7, 10, 25, 50, 100],
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
            { data: "room_number" },
            { data: "building" },
            { data: "floor" },
            { data: "room_type" },
            { data: "capacity" }, // Availability
            { data: "status" },
            { data: "amount" }, // Changed from features
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
               targets: 1,
               responsivePriority: 1,
               render: function (data, type, full) {
                  return `<span class="fw-medium">${data}</span>`;
               },
            },
            {
               targets: 5, // Availability
               render: function (data, type, full) {
                  const available = full.capacity - full.current_occupancy;
                  let badgeClass = "bg-label-success"; // Default to success

                  if (available === 0) {
                     badgeClass = "bg-label-danger"; // Fully occupied
                  } else if (available < full.capacity) {
                     badgeClass = "bg-label-info"; // Partially occupied
                  }

                  if (full.status === "Under Maintenance") {
                     badgeClass = "bg-label-warning"; // Maintenance
                  }

                  return `<span class="badge ${badgeClass}">${available} / ${data}</span>`;
               },
            },
            {
               targets: 6, // Status
               render: function (data) {
                  const statusObj = {
                     Vacant: { class: "bg-label-success", title: "Vacant" },
                     "Partially Occupied": {
                        class: "bg-label-info",
                        title: "Partially Occupied",
                     },
                     "Fully Occupied": {
                        class: "bg-label-danger",
                        title: "Fully Occupied",
                     },
                     "Under Maintenance": {
                        class: "bg-label-warning",
                        title: "Maintenance",
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
               targets: 7,
               render: function (data) {
                  return `<span data-bs-toggle="tooltip" title="Room cost per year">GH₵${Number(
                     data
                  ).toFixed(2)}</span>`;
               },
            },
            {
               targets: 8, // Actions
               searchable: false,
               orderable: false,
               render: function (data, type, full) {
                  return `
                     <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-primary book-room-btn" 
                           data-room-id="${full.room_id}"
                           data-room-number="${full.room_number}"
                           data-building="${full.building}"
                           data-bs-toggle="modal"
                           data-bs-target="#bookingConfirmationModal">
                           <i class="bx bx-check-circle me-1"></i>Book
                        </button>
                        <a href="javascript:;" class="btn btn-sm btn-icon view-room-details" 
                           data-room-id="${full.room_id}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="top" 
                           title="View details">
                           <i class="bx bx-show icon-md"></i>
                        </a>
                     </div>
                  `;
               },
            },
         ],
         order: [[1, "asc"]],
         responsive: {
            details: {
               display: DataTable.Responsive.display.modal({
                  header: function (row) {
                     const data = row.data();
                     return `Details for Room ${data.room_number}`;
                  },
               }),
               renderer: function (api, rowIdx, columns) {
                  const data = columns
                     .map(function (col) {
                        return col.title !== "" && col.title !== "Actions"
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

            // Search box
            $("#roomSearch").on("keyup", function () {
               api.search(this.value).draw();
            });

            // Building filter
            $("#buildingFilter").on("change", function () {
               const val = $(this).val();
               api.column(2)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Room type filter
            $("#roomTypeFilter").on("change", function () {
               const val = $(this).val();
               api.column(4)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Floor filter
            $("#floorFilter").on("change", function () {
               const val = $(this).val();
               api.column(3)
                  .search(val ? "^" + val + "$" : "", true, false)
                  .draw();
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(
               document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Handle view details click
            $(document).on("click", ".view-room-details", function (e) {
               e.preventDefault();
               const roomId = $(this).data("room-id");

               $.ajax({
                  url: `/student/room/${roomId}`,
                  method: "GET",
                  success: function (data) {
                     // Set header information
                     $("#roomModalTitle").text(
                        `Room ${data.room_number} Details`
                     );
                     $("#modalRoomNumberHeader").text(
                        `Room ${data.room_number}`
                     );
                     $("#modalRoomTypeHeader").text(data.room_type);
                     $("#modalBuildingHeader").text(data.building);
                     $("#modalFloorHeader").text(data.floor);

                     // Set detailed information
                     $("#modalRoomNumber").text(data.room_number);
                     $("#modalBuilding").text(data.building);
                     $("#modalFloor").text(data.floor);
                     $("#modalRoomType").text(data.room_type);
                     $("#modalCapacity").text(data.capacity);
                     $("#modalOccupancy").text(data.current_occupancy);
                     $("#modalStatus").text(data.status);
                     $("#modalAmount").text(
                        `GH₵${Number(data.amount).toFixed(2)}`
                     );

                     // Set status with appropriate badge
                     const statusClasses = {
                        Vacant: "bg-success",
                        "Partially Occupied": "bg-info",
                        "Fully Occupied": "bg-danger",
                        "Under Maintenance": "bg-warning",
                     };

                     const statusClass =
                        statusClasses[data.status] || "bg-secondary";
                     $("#modalStatus").html(
                        `<span class="badge ${statusClass}">${data.status}</span>`
                     );

                     // Calculate and update occupancy progress bar
                     const occupancyPercentage =
                        (data.current_occupancy / data.capacity) * 100;
                     const availableSpaces =
                        data.capacity - data.current_occupancy;
                     $("#occupancyProgressBar").css(
                        "width",
                        `${occupancyPercentage}%`
                     );

                     // Set appropriate color based on occupancy
                     if (occupancyPercentage >= 80) {
                        $("#occupancyProgressBar")
                           .removeClass("bg-primary bg-success bg-warning")
                           .addClass("bg-danger");
                     } else if (occupancyPercentage >= 50) {
                        $("#occupancyProgressBar")
                           .removeClass("bg-primary bg-success bg-danger")
                           .addClass("bg-warning");
                     } else {
                        $("#occupancyProgressBar")
                           .removeClass("bg-warning bg-danger")
                           .addClass("bg-success");
                     }

                     $("#occupancyProgressText").text(
                        `${availableSpaces} of ${data.capacity} spaces available`
                     );

                     // Process features
                     const features = data.features
                        ? data.features.split(",").map((f) => f.trim())
                        : [];
                     const featureIcons = {
                        "Air Conditioning": "bx-wind",
                        WiFi: "bx-wifi",
                        TV: "bx-tv",
                        Balcony: "bx-building",
                        "Private Bathroom": "bx-bath",
                        Desk: "bx-table",
                        Wardrobe: "bx-cabinet",
                        "Shared bathroom": "bx-bath",
                        "Smart TV": "bx-tv",
                        "Mini fridge": "bx-fridge",
                        "Air-conditioning": "bx-wind",
                        "High-speed Wi-Fi": "bx-wifi",
                     };

                     $("#modalFeatures").html(
                        features.length > 0
                           ? features
                                .map((f) => {
                                   const icon = featureIcons[f] || "bx-check";
                                   return `<div class="badge bg-label-primary p-2 me-2 mb-2">
                            <i class="bx ${icon} me-1"></i>${f}
                        </div>`;
                                })
                                .join("")
                           : '<small class="text-muted">No special features available</small>'
                     );

                     // Update booking button data
                     $(".book-room-btn-modal")
                        .data("room-id", roomId)
                        .data("room-number", data.room_number)
                        .data("building", data.building);

                     // Show the modal
                     $("#roomDetailsModal").modal("show");
                  },
                  error: function () {
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to load room details",
                     });
                  },
               });
            });

            // Handle book button click
            $(document).on(
               "click",
               ".book-room-btn, .book-room-btn-modal",
               function () {
                  const roomId = $(this).data("room-id");
                  const roomNumber = $(this).data("room-number");
                  const building = $(this).data("building");

                  $("#confirmRoomNumber").text(roomNumber);
                  $("#confirmBuilding").text(building);
                  $(".confirm-book-btn").data("room-id", roomId);
               }
            );

            // Confirm booking
            $(document).on("click", ".confirm-book-btn", function () {
               const roomId = $(this).data("room-id");

               $.ajax({
                  url: `/student/room/book/${roomId}`,
                  method: "POST",
                  data: {
                     room_id: roomId,
                     csrf: csrfToken,
                  },
                  success: function (response) {
                     $("#bookingConfirmationModal").modal("hide");
                     if (response.success) {
                        Swal.fire({
                           icon: "success",
                           title: "Success",
                           text: "Room booked successfully!",
                           timer: 2000,
                        }).then(() => {
                           location.reload();
                        });
                     } else {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: response.error || "Failed to book room",
                        });
                     }
                  },
                  error: function () {
                     $("#bookingConfirmationModal").modal("hide");
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Booking request failed",
                     });
                  },
               });

               // Fetch announcement count
               $.ajax({
                  url: "/announcements",
                  method: "GET",
                  success: function (data) {
                     $("#announcementCount").text(data.length || 0);
                  },
               });

               // Fetch available rooms count
               $.ajax({
                  url: "/room-data",
                  method: "GET",
                  success: function (data) {
                     $("#availableRoomCount").text(
                        data.data ? data.data.length : 0
                     );
                  },
               });
            });
         },
      });
   }
})();
