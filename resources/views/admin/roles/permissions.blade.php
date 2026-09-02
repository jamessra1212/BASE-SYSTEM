@extends('BackEnd.layouts.master')

@section('content')
<h1 class="h4 mb-3">Permissions for role: {{ $role->name }}</h1>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('core.roles.permissions.update', $role) }}">
    @csrf
    @method('PUT')

    <div class="card mb-3">
        <div class="card-body">
            <h6 class="fw-bold mb-2">Menus</h6>
            <p class="text-muted small">
                Check the menu items this role should see by default, and any action-level permission nested
                under it. Individual users can still be given an allow/deny override under Settings &rarr; User Access.
            </p>
            <x-menu-checkbox-tree :items="$menus" :checked="$rolePermissionNames" :permissionsByMenu="$permissionsByMenu" />
        </div>
    </div>

    @if ($unassignedGrouped->isNotEmpty())
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-2">Other Permissions</h6>
                <p class="text-muted small">Permissions not linked to a specific menu.</p>

                @foreach ($unassignedGrouped as $groupName => $permissionsInGroup)
                    <div class="mb-3">
                        <div class="fw-semibold text-uppercase text-muted small mb-1" style="letter-spacing: 0.5px;">
                            {{ $groupName }}
                        </div>
                        <div class="row">
                            @foreach ($permissionsInGroup as $permission)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permission_names[]"
                                            id="perm-{{ $permission->id }}" value="{{ $permission->name }}"
                                            {{ $rolePermissionNames->contains($permission->name) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm-{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('core.roles.index') }}" class="btn btn-outline-secondary">Back</a>
</form>
@endsection