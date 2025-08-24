<div class="tab-pane fade" id="profile-room" role="tabpanel">
    <?php if ($room_allocation): ?>
        <div class="row">
            <div class="mt-5 col-12 col-md-6">
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-center">
                        <i class="me-2 text-primary bx bx-door-open icon-xl"></i>
                        <div>
                            <span class="d-block fw-medium">Room Number</span>
                            <span class="text-muted">
                                <?= htmlspecialchars($room_allocation['room_number']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-center">
                        <i class="me-2 text-primary bx bx-home-alt icon-xl"></i>
                        <div>
                            <span class="d-block fw-medium">Room Type</span>
                            <span class="text-muted">
                                <?= htmlspecialchars($room_allocation['room_type']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-center">
                        <i class="me-2 text-primary bx bx-building icon-xl"></i>
                        <div>
                            <span class="d-block fw-medium">Building</span>
                            <span class="text-muted">
                                <?= htmlspecialchars($room_allocation['building']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-center">
                        <i class="me-2 text-primary bx bx-layer icon-xl"></i>
                        <div>
                            <span class="d-block fw-medium">Floor</span>
                            <span class="text-muted">
                                <?= htmlspecialchars($room_allocation['floor']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-center">
                        <i class="bx-group me-2 text-primary bx icon-xl"></i>
                        <div>
                            <span class="d-block fw-medium">Capacity</span>
                            <span class="text-muted">
                                <?= htmlspecialchars($room_allocation['capacity']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-12 col-md-6">
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-center">
                        <i class="me-2 text-primary bx bx-check-circle icon-xl"></i>
                        <div>
                            <span class="d-block fw-medium">Status</span>
                            <span class="badge bg-<?= $room_allocation['status'] === 'Vacant' ? 'success' : ($room_allocation['status'] === 'Fully Occupied' ? 'danger' : 'warning') ?>">
                                <?= htmlspecialchars($room_allocation['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-center">
                        <i class="me-2 text-primary bx bx-calendar-check icon-xl"></i>
                        <div>
                            <span class="d-block fw-medium">Date In</span>
                            <span class="text-muted">
                                <?= htmlspecialchars($room_allocation['start_date'] ?? $student_data['enrollment_date']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-center">
                        <i class="me-2 text-primary bx bx-calendar-x icon-xl"></i>
                        <div>
                            <span class="d-block fw-medium">Expected Date Out</span>
                            <span class="text-muted">
                                <?= htmlspecialchars($room_allocation['end_date'] ?? 'N/A') ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-start">
                        <i class="me-2 text-primary bx bx-user-voice icon-xl"></i>
                        <div class="w-100">
                            <span class="d-block fw-medium">Roommates</span>
                            <div class="text-muted">
                                <?php if (!empty($room_allocation['other_residents'])): ?>
                                    <div class="mt-2">
                                        <?php foreach ($room_allocation['other_residents'] as $roommate): ?>
                                            <div class="d-flex flex-wrap align-items-center mb-2">
                                                <i class="me-1 text-secondary bx bx-user"></i>
                                                <span class="me-2 fw-semibold"><?= htmlspecialchars($roommate['resident_name']) ?></span>
                                                <?php if (!empty($roommate['phone_number'])): ?>
                                                    <span class="me-2 text-muted small">
                                                        <i class="bx bx-phone"></i> <?= htmlspecialchars($roommate['phone_number']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($roommate['health_condition']) && $roommate['health_condition'] !== 'None'): ?>
                                                    <span class="bg-info badge"><?= htmlspecialchars($roommate['health_condition']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span>No roommates</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="mb-3 info-item">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="me-2 text-warning bx bx-info-circle icon-xl"></i>
                        <div class="text-center">
                            <span class="d-block fw-medium">Room Status</span>
                            <span class="text-muted">No room allocated</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>