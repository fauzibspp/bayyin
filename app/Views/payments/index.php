<?php
$moduleTitle = 'Payment';
$viewPath = 'payments';
$success = $success ?? null;
$error = $error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars($moduleTitle) ?> Listing</h1>
    <div>
        <a href="/<?= htmlspecialchars($viewPath) ?>/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New
        </a>
        <button type="button" id="btn-bulk-delete-payments" class="btn btn-danger">
            Bulk Delete
        </button>
    </div>
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
        <table id="datatable-payments" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="check-all-payments"></th>
                    <th width="80">ID</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Is Paid</th>
                    <th width="260">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
$(function () {
    const table = $('#datatable-payments').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/api/payments/datatable',
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" class="row-check" value="' + row.id + '">';
                }
            },
            { data: 'id' },
                { data: 'name' },
                { data: 'amount' },
                { data: 'status' },
                { data: 'notes' },
                { data: 'is_paid' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return ''
                        + '<a href="/payments/show?id=' + row.id + '" class="btn btn-info btn-sm mr-1">View</a>'
                        + '<a href="/payments/edit?id=' + row.id + '" class="btn btn-warning btn-sm mr-1">Edit</a>'
                        + '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' + row.id + '">Delete</button>';
                }
            }
        ]
    });

    $('#check-all-payments').on('change', function () {
        $('.row-check').prop('checked', $(this).is(':checked'));
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }

        $.ajax({
            url: '/api/payments/delete',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: id }),
            success: function () {
                table.ajax.reload(null, false);
                alert('Record deleted successfully.');
            },
            error: function () {
                alert('Failed to delete record.');
            }
        });
    });

    $('#btn-bulk-delete-payments').on('click', function () {
        const ids = $('.row-check:checked').map(function () {
            return parseInt($(this).val(), 10);
        }).get();

        if (!ids.length) {
            alert('Please select at least one record.');
            return;
        }

        if (!confirm('Are you sure you want to bulk delete selected records?')) {
            return;
        }

        $.ajax({
            url: '/api/payments/bulk-delete',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ ids: ids }),
            success: function () {
                table.ajax.reload(null, false);
                alert('Selected records deleted successfully.');
            },
            error: function () {
                alert('Failed to bulk delete records.');
            }
        });
    });
});
</script>