@extends('BackEnd.layouts.master')

@section('content')
<h1 class="h4 mb-3">User Menu Access</h1>

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

<div class="row">
    <div class="col-md-4">
        <div class="list-group">
            @foreach ($users as $user)
                <a href="{{ route('sida.admin.users.access', ['user' => $user->id, 'q' => request('q')]) }}"
                    class="list-group-item list-group-item-action {{ $selectedUser?->id === $user->id ? 'active' : '' }}">
                    {{ $user->name }}
                    <div class="small {{ $selectedUser?->id === $user->id ? '' : 'text-muted' }}">{{ $user->fullname }}</div>
                </a>
            @endforeach
        </div>
        <div class="mt-2">{{ $users->links() }}</div>
    </div>

    <div class="col-md-8">
        @if ($selectedUser)
            <div class="card">
                <div class="card-body">
                    <h5>{{ $selectedUser->name }}</h5>
                    <p class="text-muted">
                        Roles: {{ $selectedUser->roles->pluck('name')->join(', ') ?: '— none —' }}
                    </p>

                    <form method="POST" action="{{ route('sida.admin.users.access.update', $selectedUser) }}">
                        @csrf
                        @method('PUT')
                        <x-menu-override-tree :items="$menus" :overrides="$overrides" />
                        <button type="submit" class="btn btn-primary mt-3">Save Overrides</button>
                    </form>
                </div>
            </div>
        @else
            <div class="text-muted">Select a user on the left to manage their menu overrides.</div>
        @endif
    </div>
</div>
@endsection
