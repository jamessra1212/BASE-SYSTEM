@extends('BackEnd.layouts.master')

@section('content')
<h1 class="h4 mb-3">Menus for role: {{ $role->name }}</h1>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('sida.admin.roles.permissions.update', $role) }}">
    @csrf
    @method('PUT')

    <div class="card mb-3">
        <div class="card-body">
            <p class="text-muted">
                Check the menu items this role should see by default. Individual users can still
                be given an allow/deny override under Settings &rarr; User Access.
            </p>
            <x-menu-checkbox-tree :items="$menus" :checked="$rolePermissionNames" />
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('sida.admin.roles.index') }}" class="btn btn-outline-secondary">Back</a>
</form>
@endsection
