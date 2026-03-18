<h1 class="mb-3">Users Management</h1>

<?php require dirname(__DIR__) . '/components/flash.php'; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Users List</h3>
        <a href="/users/create" class="btn btn-primary btn-sm">Add User</a>
    </div>

    <div class="card-body">
        <table id="usersTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="60">ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>State</th>
                    <th width="180">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/users-datatable',
            type: 'GET',
            dataSrc: 'data'
            // dataSrc: function (json) {
            //     return json.data.data || [];
            // }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'roles', render: function(data){ return (data || '').toUpperCase(); } },
            { data: 'state', render: function(data){ return data || '-'; } },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#usersTable').on('click', '.deleteBtn', function () {
        const id = $(this).data('id');

        if (!confirm('Delete this user?')) {
            return;
        }

        $.ajax({
            url: '/users/delete-ajax',
            type: 'POST',
            data: { id: id },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (res.success) {
                    table.ajax.reload(null, false);
                    alert(res.message);
                } else {
                    alert(res.message || 'Delete failed.');
                }
            },
            error: function () {
                alert('Server error while deleting user.');
            }
        });
    });
});
</script>