@extends('BackEnd.layouts.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Roles</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal">+ New Role</button>
</div>

<div class="card">
    <div class="card-body">
        <table id="roles-table" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>Name</th>
                    <th># Permissions</th>
                    <th># Users</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('sida.admin.roles.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Role name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Editor">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#roles-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('sida.admin.roles.data') }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'permissions_count', name: 'permissions_count' },
            { data: 'users_count', name: 'users_count' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
    });
});
</script>
@endpush
