<?php
$moduleTitle = 'Sample';
$viewPath = 'samples';
$success = $success ?? null;
$error = $error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars($moduleTitle) ?> Listing</h1>
    <div>
        <a href="/<?= htmlspecialchars($viewPath) ?>/trash" class="btn btn-warning mr-2">Trash</a>
        <a href="/<?= htmlspecialchars($viewPath) ?>/export" class="btn btn-info mr-2">Export CSV</a>
        <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#modal-create-samples">
            <i class="fas fa-plus"></i> Add New
        </button>
        <button type="button" id="btn-bulk-delete-samples" class="btn btn-danger">
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
        <table id="datatable-samples" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="check-all-samples"></th>
                    <th width="80">ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Is Active</th>
                    <th width="260">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-create-samples" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-create-samples">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create <?= htmlspecialchars($moduleTitle) ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="create-errors-samples" class="alert alert-danger d-none"></div>

                    <div class="form-group">
                        <label for="create_name">Name</label>
                        <input type="text" name="name" id="create_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="create_status">Status</label>
                        <input type="text" name="status" id="create_status" class="form-control" required>
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

<div class="modal fade" id="modal-edit-samples" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-edit-samples">
            <input type="hidden" name="id" id="edit_id_samples">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit <?= htmlspecialchars($moduleTitle) ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="edit-errors-samples" class="alert alert-danger d-none"></div>

                    <div class="form-group">
                        <label for="edit_name">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <input type="text" name="status" id="edit_status" class="form-control" required>
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
    const table = $('#datatable-samples').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/api/samples/datatable',
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
                { data: 'status' },
                { data: 'notes' },
                { data: 'is_active' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return ''
                        + '<a href="/samples/show?id=' + row.id + '" class="btn btn-info btn-sm mr-1">View</a>'
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

    $('#form-create-samples').on('submit', function (e) {
        e.preventDefault();
        clearErrors('#create-errors-samples');

        const data = {};
        $(this).serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });

        $.ajax({
            url: '/api/samples/store',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function () {
                $('#modal-create-samples').modal('hide');
                $('#form-create-samples')[0].reset();
                table.ajax.reload(null, false);
                alert('Record created successfully.');
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                showErrors('#create-errors-samples', response.errors || { general: ['Failed to create record.'] });
            }
        });
    });

    $(document).on('click', '.btn-edit', function () {
        const row = $(this).data('row');
        clearErrors('#edit-errors-samples');
        $('#edit_id_samples').val(row.id);
        $('#edit_name').val(row.name || '');
        $('#edit_status').val(row.status || '');
        $('#edit_notes').val(row.notes || '');
        $('#edit_is_active').prop('checked', !!parseInt(row.is_active || 0, 10));
        $('#modal-edit-samples').modal('show');
    });

    $('#form-edit-samples').on('submit', function (e) {
        e.preventDefault();
        clearErrors('#edit-errors-samples');

        const data = {};
        $(this).serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });
        data['is_active'] = $('#edit_is_active').is(':checked') ? 1 : 0;
        $.ajax({
            url: '/api/samples/update',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function () {
                $('#modal-edit-samples').modal('hide');
                table.ajax.reload(null, false);
                alert('Record updated successfully.');
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                showErrors('#edit-errors-samples', response.errors || { general: ['Failed to update record.'] });
            }
        });
    });

    $('#check-all-samples').on('change', function () {
        $('.row-check').prop('checked', $(this).is(':checked'));
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }

        $.ajax({
            url: '/api/samples/delete',
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

    $('#btn-bulk-delete-samples').on('click', function () {
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
            url: '/api/samples/bulk-delete',
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