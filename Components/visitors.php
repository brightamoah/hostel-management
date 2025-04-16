<?php
// session_start();
$user = $_SESSION['user'] ?? null;

// Dummy visitor data (replace with actual database query)
$visitors = [
    ['name' => 'John Doe', 'relationship' => 'Friend', 'date' => '2025-04-09', 'time_in' => '10:00 AM', 'time_out' => '12:00 PM', 'status' => 'Completed'],
    ['name' => 'Jane Smith', 'relationship' => 'Family', 'date' => '2025-04-08', 'time_in' => '02:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Alex Brown', 'relationship' => 'Colleague', 'date' => '2025-04-07', 'time_in' => '09:00 AM', 'time_out' => '11:00 AM', 'status' => 'Completed'],
    ['name' => 'Emily Davis', 'relationship' => 'Friend', 'date' => '2025-04-06', 'time_in' => '01:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Michael Johnson', 'relationship' => 'Family', 'date' => '2025-04-05', 'time_in' => '03:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Sarah Wilson', 'relationship' => 'Colleague', 'date' => '2025-04-04', 'time_in' => '11:00 AM', 'time_out' => '01:00 PM', 'status' => 'Completed'],
    ['name' => 'David Lee', 'relationship' => 'Friend', 'date' => '2025-04-03', 'time_in' => '10:30 AM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Laura Martinez', 'relationship' => 'Family', 'date' => '2025-04-02', 'time_in' => '12:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Chris Taylor', 'relationship' => 'Colleague', 'date' => '2025-04-01', 'time_in' => '09:30 AM', 'time_out' => '10:30 AM', 'status' => 'Completed'],
    ['name' => 'Jessica Anderson', 'relationship' => 'Friend', 'date' => '2025-03-31', 'time_in' => '02:30 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Daniel Thomas', 'relationship' => 'Family', 'date' => '2025-03-30', 'time_in' => '01:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Sophia White', 'relationship' => 'Colleague', 'date' => '2025-03-29', 'time_in' => '11:00 AM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'James Harris', 'relationship' => 'Friend', 'date' => '2025-03-28', 'time_in' => '10:00 AM', 'time_out' => '', 'status' => 'In Progress'],
    ['name'=> 'Olivia Clark', 'relationship' => 'Family', 'date' => '2025-03-27', 'time_in' => '03:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'William Lewis', 'relationship' => 'Colleague', 'date' => '2025-03-26', 'time_in' => '12:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Ava Walker', 'relationship' => 'Friend', 'date' => '2025-03-25', 'time_in' => '01:30 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name'=> 'Ethan Hall', 'relationship' => 'Family', 'date' => '2025-03-24', 'time_in' => '10:00 AM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Mia Young', 'relationship' => 'Colleague', 'date' => '2025-03-23', 'time_in' => '02:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Noah King', 'relationship' => 'Friend', 'date' => '2025-03-22', 'time_in' => '11:00 AM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Charlotte Scott', 'relationship' => 'Family', 'date' => '2025-03-21', 'time_in' => '09:00 AM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Lucas Green', 'relationship' => 'Colleague', 'date' => '2025-03-20', 'time_in' => '03:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Sophia Adams', 'relationship' => 'Friend', 'date' => '2025-03-19', 'time_in' => '12:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'James Wilson', 'relationship' => 'Family', 'date' => '2025-03-18', 'time_in' => '01:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name'=> 'Emily Taylor', 'relationship' => 'Colleague', 'date' => '2025-03-17', 'time_in' => '10:00 AM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Benjamin Harris', 'relationship' => 'Friend', 'date' => '2025-03-16', 'time_in' => '02:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Ava Martinez', 'relationship' => 'Family', 'date' => '2025-03-15', 'time_in' => '11:00 AM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Oliver Robinson', 'relationship' => 'Colleague', 'date' => '2025-03-14', 'time_in' => '03:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Isabella Lee', 'relationship' => 'Friend', 'date' => '2025-03-13', 'time_in' => '12:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name' => 'Liam Walker', 'relationship' => 'Family', 'date' => '2025-03-12', 'time_in' => '01:00 PM', 'time_out' => '', 'status' => 'In Progress'],
    ['name'=> 'Mason Hall', 'relationship' => 'Colleague', 'date' => '2025-03-11', 'time_in' => '10:00 AM', 'time_out' => '', 'status' => 'In Progress'],
];

// Replace with actual query, e.g.:
// $visitors = $db->query("SELECT * FROM visitors WHERE student_id = :id", ['id' => $user['id']])->fetchAll();
?>

<div class="container-xxl flex-grow-1 container-p-x">
    <div class="card p-5">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Visitor Log</h5>
            <div class="dropdown">
                <button class="btn text-body-secondary p-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                    <a class="dropdown-item" href="visitors.php">Register New Visitor</a>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-visitors table border-top">
                <thead>
                    <tr>
                        <th>Visitor Name</th>
                        <th>Relationship</th>
                        <th>Date of Visit</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($visitors)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No visitors found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($visitors as $visitor): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="icon-base bx bx-user icon-md"></i>
                                            </span>
                                        </div>
                                        <?= htmlspecialchars($visitor['name']) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($visitor['relationship']) ?></td>
                                <td><?= htmlspecialchars($visitor['date']) ?></td>
                                <td><?= htmlspecialchars($visitor['time_in']) ?></td>
                                <td><?= $visitor['time_out'] ? htmlspecialchars($visitor['time_out']) : '<span class="badge bg-label-warning">Pending</span>' ?></td>
                                <td>
                                    <span class="badge <?= $visitor['status'] === 'Completed' ? 'bg-label-success' : 'bg-label-info' ?>">
                                        <?= htmlspecialchars($visitor['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-icon btn-label-secondary" data-bs-toggle="tooltip" title="View Details">
                                        <i class="icon-base bx bx-show icon-md"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DataTable Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('.datatables-visitors').DataTable({
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "responsive": true,
        "order": [[2, 'desc']], // Sort by Date of Visit descending
        "language": {
            "emptyTable": "No visitors found."
        }
    });
});
</script>