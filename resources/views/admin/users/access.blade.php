@extends('BackEnd.layouts.master')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="h4 mb-3">User Access</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-auto">
                        <input type="text" name="q" class="form-control" placeholder="Search name or email"
                            value="{{ request('q') }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="list-group">
            @forelse ($users as $user)
                <a href="{{ route('core.access.index', ['user' => $user->id, 'q' => request('q')]) }}"
                    class="list-group-item list-group-item-action {{ $selectedUser?->id === $user->id ? 'active' : '' }}">
                    {{ $user->fullname }}
                    <div class="small {{ $selectedUser?->id === $user->id ? '' : 'text-muted' }}">{{ $user->email }}</div>
                </a>
            @empty
                <div class="list-group-item text-muted">No users found.</div>
            @endforelse
        </div>
        <div class="mt-2">{{ $users->links() }}</div>
    </div>

    <div class="col-md-8">
        @if ($selectedUser)
            <div class="card mb-3">
                <div class="card-body">
                    <h5>{{ $selectedUser->fullname }}</h5>
                    <p class="text-muted">
                        Roles: {{ $selectedUser->roles->pluck('name')->join(', ') ?: '— none —' }}
                    </p>

                    <form method="POST" action="{{ route('core.access.update', $selectedUser) }}">
                        @csrf
                        @method('PUT')

                        <h6 class="fw-bold mb-2">Menus</h6>
                        <x-menu-override-tree :items="$menus" :overrides="$overrides"
                            :permissionsByMenu="$permissionsByMenu" :permissionOverrides="$permissionOverrides" />

                        @if ($unassignedGrouped->isNotEmpty())
                            <hr class="my-4">
                            <h6 class="fw-bold mb-2">Other Permissions</h6>
                            <p class="text-muted small">Permissions not linked to a specific menu.</p>
                            <x-permission-override-list :grouped="$unassignedGrouped" :overrides="$permissionOverrides" />
                        @endif

                        <button type="submit" class="btn btn-primary mt-3">Save Access</button>
                    </form>
                </div>
            </div>
        @else
            <div class="text-muted">Select a user on the left to manage their access.</div>
        @endif
    </div>
</div>
@endsection
