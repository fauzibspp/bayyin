<?php
$moduleTitle = 'Archive';
$viewPath = 'archives';
$success = $success ?? null;
$error = $error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars($moduleTitle) ?> Listing</h1>
    <div>
        <a href="/<?= htmlspecialchars($viewPath) ?>/trash" class="btn btn-warning mr-2">Trash</a>
        <a href="/<?= htmlspecialchars($viewPath) ?>/export" class="btn btn-info mr-2">Export CSV</a>
        <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#modal-create-archives">
            <i class="fas fa-plus"></i> Add New
        </button>
        <button type="button" id="btn-bulk-delete-archives" class="btn btn-danger">
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
        <table id="datatable-archives" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="check-all-archives"></th>
                    <th width="80">ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Notes</th>
                    <th>Is Active</th>
                    <th width="260">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-create-archives" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-create-archives">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create <?= htmlspecialchars($moduleTitle) ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="create-errors-archives" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label for="create_name">Name</label>
                        <input type="text" name="name" id="create_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="create_code">Code</label>
                        <input type="text" name="code" id="create_code" class="form-control" required>
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

<div class="modal fade" id="modal-edit-archives" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-edit-archives">
            <input type="hidden" name="id" id="edit_id_archives">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit <?= htmlspecialchars($moduleTitle) ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="edit-errors-archives" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label for="edit_name">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_code">Code</label>
                        <input type="text" name="code" id="edit_code" class="form-control" required>
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
    const table = $('#datatable-archives').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/api/archives/datatable',
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
                { data: 'code' },
                { data: 'notes' },
                { data: 'is_active' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return ''
                        + '<a href="/archives/show?id=' + row.id + '" class="btn btn-info btn-sm mr-1">View</a>'
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

    $('#form-create-archives').on('submit', function (e) {
        e.preventDefault();
        clearErrors('#create-errors-archives');

        const data = {};
        $(this).serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });

        $.ajax({
            url: '/api/archives/store',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function () {
                $('#modal-create-archives').modal('hide');
                $('#form-create-archives')[0].reset();
                table.ajax.reload(null, false);
                alert('Record created successfully.');
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                showErrors('#create-errors-archives', response.errors || { general: ['Failed to create record.'] });
            }
        });
    });

    $(document).on('click', '.btn-edit', function () {
        const row = $(this).data('row');
        clearErrors('#edit-errors-archives');
        $('#edit_id_archives').val(row.id);
        $('#edit_name').val(row.name || '');
        $('#edit_code').val(row.code || '');
        $('#edit_notes').val(row.notes || '');
        $('#edit_is_active').prop('checked', !!parseInt(row.is_active || 0, 10));
        $('#modal-edit-archives').modal('show');
    });

    $('#form-edit-archives').on('submit', function (e) {
        e.preventDefault();
        clearErrors('#edit-errors-archives');

        const data = {};
        $(this).serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });
        data['is_active'] = $('#edit_is_active').is(':checked') ? 1 : 0;
        $.ajax({
            url: '/api/archives/update',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function () {
                $('#modal-edit-archives').modal('hide');
                table.ajax.reload(null, false);
                alert('Record updated successfully.');
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                showErrors('#edit-errors-archives', response.errors || { general: ['Failed to update record.'] });
            }
        });
    });

    $('#check-all-archives').on('change', function () {
        $('.row-check').prop('checked', $(this).is(':checked'));
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }

        $.ajax({
            url: '/api/archives/delete',
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

    $('#btn-bulk-delete-archives').on('click', function () {
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
            url: '/api/archives/bulk-delete',
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