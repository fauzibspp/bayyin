<?php
$moduleTitle = 'Customer';
$viewPath = 'customers';
$success = $success ?? null;
$error = $error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars($moduleTitle) ?> Listing</h1>
    <div>
        <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#modal-create-customers">
            <i class="fas fa-plus"></i> Add New
        </button>
        <button type="button" id="btn-bulk-delete-customers" class="btn btn-danger">
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
        <table id="datatable-customers" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="check-all-customers"></th>
                    <th width="80">ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Notes</th>
                    <th>Is Active</th>
                    <th width="260">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-create-customers" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-create-customers">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create <?= htmlspecialchars($moduleTitle) ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="create-errors-customers" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label for="create_name">Name</label>
                        <input type="text" name="name" id="create_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="create_email">Email</label>
                        <input type="text" name="email" id="create_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="create_phone">Phone</label>
                        <input type="text" name="phone" id="create_phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="create_notes">Notes</label>
                        <textarea name="notes" id="create_notes" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="create_is_active" value="1" class="form-check-input">
                        <label class="form-check-label" for="create_is_active">Is Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal-edit-customers" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-edit-customers">
            <input type="hidden" name="id" id="edit_id_customers">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit <?= htmlspecialchars($moduleTitle) ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="edit-errors-customers" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label for="edit_name">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="text" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_phone">Phone</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_notes">Notes</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="form-check-input">
                        <label class="form-check-label" for="edit_is_active">Is Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    const table = $('#datatable-customers').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/api/customers/datatable',
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
                { data: 'email' },
                { data: 'phone' },
                { data: 'notes' },
                { data: 'is_active' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return ''
                        + '<a href="/customers/show?id=' + row.id + '" class="btn btn-info btn-sm mr-1">View</a>'
                        + '<button type="button" class="btn btn-warning btn-sm mr-1 btn-edit" data-row=\'' + JSON.stringify(row) + '\'>Edit</button>'
                        + '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' + row.id + '">Delete</button>';
                }
            }
        ]
    });

    function showErrors(container, errors) {
        let html = '<ul class="mb-0">';
        $.each(errors || {}, function (field, messages) {
            $.each(messages, function (_, message) {
                html += '<li>' + message + '</li>';
            });
        });
        html += '</ul>';
        $(container).removeClass('d-none').html(html);
    }

    function clearErrors(container) {
        $(container).addClass('d-none').html('');
    }

    $('#form-create-customers').on('submit', function (e) {
        e.preventDefault();
        clearErrors('#create-errors-customers');

        const data = {};
        $(this).serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });

        $.ajax({
            url: '/api/customers/store',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function () {
                $('#modal-create-customers').modal('hide');
                $('#form-create-customers')[0].reset();
                table.ajax.reload(null, false);
                alert('Record created successfully.');
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                showErrors('#create-errors-customers', response.errors || { general: ['Failed to create record.'] });
            }
        });
    });

    $(document).on('click', '.btn-edit', function () {
        const row = $(this).data('row');
        clearErrors('#edit-errors-customers');
        $('#edit_id_customers').val(row.id);
        $('#edit_name').val(row.name || '');
        $('#edit_email').val(row.email || '');
        $('#edit_phone').val(row.phone || '');
        $('#edit_notes').val(row.notes || '');
        $('#edit_is_active').prop('checked', !!parseInt(row.is_active || 0, 10));
        $('#modal-edit-customers').modal('show');
    });

    $('#form-edit-customers').on('submit', function (e) {
        e.preventDefault();
        clearErrors('#edit-errors-customers');

        const data = {};
        $(this).serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });
        data['is_active'] = $('#edit_is_active').is(':checked') ? 1 : 0;
        $.ajax({
            url: '/api/customers/update',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function () {
                $('#modal-edit-customers').modal('hide');
                table.ajax.reload(null, false);
                alert('Record updated successfully.');
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                showErrors('#edit-errors-customers', response.errors || { general: ['Failed to update record.'] });
            }
        });
    });

    $('#check-all-customers').on('change', function () {
        $('.row-check').prop('checked', $(this).is(':checked'));
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }

        $.ajax({
            url: '/api/customers/delete',
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

    $('#btn-bulk-delete-customers').on('click', function () {
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
            url: '/api/customers/bulk-delete',
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