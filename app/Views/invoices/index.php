<?php
$moduleTitle = 'Invoice';
$viewPath = 'invoices';
$success = $success ?? null;
$error = $error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars($moduleTitle) ?> Listing</h1>
    <a href="/<?= htmlspecialchars($viewPath) ?>/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New
    </a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0"><?= htmlspecialchars($moduleTitle) ?> Records</h3>
    </div>
    <div class="card-body">
        <table id="datatable-invoices" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Is Paid</th>
                    <th width="260">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
$(function () {
    $('#datatable-invoices').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/api/invoices/datatable',
        columns: [
            { data: 'id' },
                { data: 'name' },
                { data: 'amount' },
                { data: 'status' },
                { data: 'remarks' },
                { data: 'is_paid' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<a href="/invoices/show?id=' + row.id + '" class="btn btn-info btn-sm mr-1">View</a>' + '<a href="/invoices/edit?id=' + row.id + '" class="btn btn-warning btn-sm mr-1">Edit</a>' + '<a href="/invoices/delete?id=' + row.id + '" class="btn btn-danger btn-sm btn-delete">Delete</a>';
                }
            }
        ]
    });

    $(document).on('click', '.btn-delete', function (e) {
        if (!confirm('Are you sure you want to delete this record?')) {
            e.preventDefault();
        }
    });
});
</script>