(function () {
   "use strict";

   const dt_rooms_table = document.querySelector(".datatables-rooms");

   if (dt_rooms_table) {
      const csrfToken =
         document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

      const dt = new DataTable(dt_rooms_table, {
         ajax: "/admin/rooms-data",
         processing: true,
         serverSide: false,
         layout: {
            topStart: {
               rowClass: "row mx-3 my-0 justify-content-between",
               features: [
                  {
                     pageLength: {
                        menu: [7, 10, 25, 50, 100],
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
         },
         columns: [
            { data: null, defaultContent: "" },
            { data: "room_number" },
            { data: "building" },
            { data: "floor" },
            { data: "room_type" },
            { data: "capacity" },
            { data: "status" },
            { data: "amount" },
            { data: null, defaultContent: "" },
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
               render: function (data) {
                  return `<span class="fw-medium">${data}</span>`;
               },
            },
            {
               targets: 5,
               render: function (data, type, full) {
                  const available = full.capacity - full.current_occupancy;
                  let badgeClass = "bg-label-success";
                  if (available === 0) {
                     badgeClass = "bg-label-danger";
                  } else if (available < full.capacity) {
                     badgeClass = "bg-label-info";
                  }
                  if (full.status === "Under Maintenance") {
                     badgeClass = "bg-label-warning";
                  }
                  return `<span class="badge ${badgeClass}">${available} / ${data}</span>`;
               },
            },
            {
               targets: 6,
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
               targets: 8,
               searchable: false,
               orderable: false,
               render: function (data, type, full) {
                  return `
                            <div class="d-flex align-items-center gap-2">
                                <a href="javascript:;" class="btn btn-sm btn-icon view-room-btn" 
                                   data-room-id="${full.room_id}"
                                   data-bs-toggle="modal"
                                   data-bs-target="#roomDetailsModal"
                                   title="View details">
                                    <i class="bx bx-show icon-md"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-sm btn-icon edit-room-btn" 
                                   data-room-id="${full.room_id}"
                                   data-bs-toggle="modal"
                                   data-bs-target="#editRoomModal"
                                   title="Edit room">
                                    <i class="bx bx-edit icon-md text-primary"></i>
                                </a>
                                <a href="javascript:;" class="btn btn-sm btn-icon delete-room-btn" 
                                   data-room-id="${full.room_id}"
                                   title="Delete room">
                                    <i class="bx bx-trash icon-md text-danger"></i>
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

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(
               document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Update statistics cards and populate filter dropdowns
            $.ajax({
               url: "/admin/rooms-data",
               method: "GET",
               success: function (response) {
                  const rooms = response.data;
                  const totalRooms = rooms.length;
                  const vacantRooms = rooms.filter(
                     (r) => r.status === "Vacant"
                  ).length;
                  const occupiedRooms = rooms.filter(
                     (r) =>
                        r.status === "Fully Occupied" ||
                        r.status === "Partially Occupied"
                  ).length;
                  const maintenanceRooms = rooms.filter(
                     (r) => r.status === "Under Maintenance"
                  ).length;

                  $("#totalRooms").text(totalRooms);
                  $("#vacantRooms").text(vacantRooms);
                  $("#occupiedRooms").text(occupiedRooms);
                  $("#maintenanceRooms").text(maintenanceRooms);

                  // Populate building filter
                  const buildings = [
                     ...new Set(rooms.map((r) => r.building)),
                  ].sort();
                  buildings.forEach((building) => {
                     $("#buildingFilter").append(
                        `<option value="${building}">${building}</option>`
                     );
                  });

                  // Populate floor filter
                  const floors = [...new Set(rooms.map((r) => r.floor))].sort(
                     (a, b) => a - b
                  );
                  floors.forEach((floor) => {
                     $("#floorFilter").append(
                        `<option value="${floor}">${floor}</option>`
                     );
                  });
               },
            });

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

            // View room details
            $(document).on("click", ".view-room-btn", function () {
               const roomId = $(this).data("room-id");
               $.ajax({
                  url: `/admin/room/${roomId}`,
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
                     $("#modalAmount").text(
                        `GH₵${Number(data.amount).toFixed(2)}`
                     );

                     // Set status with appropriate badge
                     const statusClasses = {
                        Vacant: "bg-label-success",
                        "Partially Occupied": "bg-label-info",
                        "Fully Occupied": "bg-label-danger",
                        "Under Maintenance": "bg-label-warning",
                     };
                     const statusClass =
                        statusClasses[data.status] || "bg-label-secondary";
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

            // Edit room
            $(document).on("click", ".edit-room-btn", function () {
               const roomId = $(this).data("room-id");
               $.ajax({
                  url: `/admin/room/${roomId}`,
                  method: "GET",
                  success: function (data) {
                     $("#edit_room_id").val(data.room_id);
                     $("#edit_room_number").val(data.room_number);
                     $("#edit_building").val(data.building);
                     $("#edit_floor").val(data.floor);
                     $("#edit_room_type").val(data.room_type);
                     $("#edit_capacity").val(data.capacity);
                     $("#edit_amount").val(data.amount);
                     $("#edit_features").val(data.features || "");
                     $("#edit_status").val(data.status);
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

            // Save new room
            $("#saveRoomBtn").on("click", function () {
               const formData = $("#addRoomForm").serializeArray();
               console.log("Add Room Form Data:", formData); // Debug: Log form data
               $.ajax({
                  url: "/admin/room/add",
                  method: "POST",
                  data: formData.concat({ name: "csrf", value: csrfToken }),
                  success: function (response) {
                     if (response.success) {
                        $("#addRoomModal").modal("hide");
                        Swal.fire({
                           icon: "success",
                           title: "Success",
                           text: "Room added successfully!",
                           timer: 2000,
                        }).then(() => {
                           dt.ajax.reload();
                           location.reload();
                        });
                     } else {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: response.error || "Failed to add room",
                        });
                     }
                  },
                  error: function (xhr) {
                     console.log("Add Room Error:", xhr.responseText); // Debug: Log error
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Request failed",
                     });
                  },
               });
            });

            // Update room
            $("#updateRoomBtn").on("click", function () {
               const formData = $("#editRoomForm").serializeArray();
               console.log("Edit Room Form Data:", formData); // Debug: Log form data
               $.ajax({
                  url: "/admin/room/update",
                  method: "POST",
                  data: formData.concat({ name: "csrf", value: csrfToken }),
                  success: function (response) {
                     if (response.success) {
                        $("#editRoomModal").modal("hide");
                        Swal.fire({
                           icon: "success",
                           title: "Success",
                           text: "Room updated successfully!",
                           timer: 2000,
                        }).then(() => {
                           dt.ajax.reload();
                           location.reload();
                        });
                     } else {
                        Swal.fire({
                           icon: "error",
                           title: "Error",
                           text: response.error || "Failed to update room",
                        });
                     }
                  },
                  error: function (xhr) {
                     console.log("Update Room Error:", xhr.responseText); // Debug: Log error
                     Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Request failed",
                     });
                  },
               });
            });

            // Delete room
            $(document).on("click", ".delete-room-btn", function () {
               const roomId = $(this).data("room-id");
               Swal.fire({
                  title: "Are you sure?",
                  text: "You won't be able to revert this!",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Yes, delete it!",
                  cancelButtonText: "No, cancel!",
               }).then((result) => {
                  if (result.isConfirmed) {
                     $.ajax({
                        url: "/admin/room/delete",
                        method: "POST",
                        data: { room_id: roomId, csrf: csrfToken },
                        success: function (response) {
                           if (response.success) {
                              Swal.fire({
                                 icon: "success",
                                 title: "Deleted!",
                                 text: "Room has been deleted.",
                                 timer: 2000,
                              }).then(() => {
                                 dt.ajax.reload();
                                 location.reload();   
                              });
                           } else {
                              Swal.fire({
                                 icon: "error",
                                 title: "Error",
                                 text:
                                    response.error || "Failed to delete room",
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
