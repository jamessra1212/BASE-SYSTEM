{{-- Usage: <x-menu-tree :items="$menuTree" /> --}}
{{-- Renders <li> elements only — call this directly inside your existing
     <ul class="nav sidebar-menu ..." data-lte-toggle="treeview">, no extra wrapper needed. --}}
@props(['items'])

@foreach ($items as $item)
    @php
        $hasChildren = $item->children->isNotEmpty();
        $isActive = $item->route && request()->routeIs($item->route);
        $childIsActive = $hasChildren && $item->children->contains(
            fn ($child) => $child->route && request()->routeIs($child->route)
        );
    @endphp

    @if ($hasChildren)
        <li class="nav-item {{ $childIsActive ? 'menu-open' : '' }}">
            <a href="#" class="nav-link px-3 py-2 rounded d-flex align-items-center justify-content-between transition-all text-white-50 hover-mini-item" style="font-size: 12.5px;">
                <span class="d-flex align-items-center">
                    @if ($item->icon)
                        <i class="nav-icon {{ $item->icon }} me-2-5 text-center" style="width: 16px; font-size: 13px; opacity: 0.7;"></i>
                    @endif
                    <span>{{ $item->name }}</span>
                </span>
                <i class="fas fa-angle-left" style="font-size: 10px; opacity: 0.5;"></i>
            </a>
            <ul class="nav-treeview">
                <x-menu-tree :items="$item->children" />
            </ul>
        </li>
    @else
        <li class="nav-item">
            <a href="{{ $item->resolvedUrl() }}"
                class="nav-link px-3 py-2 rounded d-flex align-items-center transition-all {{ $isActive ? 'text-white fw-medium' : 'text-white-50 hover-mini-item' }}"
                style="font-size: 12.5px;">
                @if ($item->icon)
                    <i class="nav-icon {{ $item->icon }} me-2-5 text-center" style="width: 16px; font-size: 13px; {{ $isActive ? 'color: #3b82f6;' : 'opacity: 0.7;' }}"></i>
                @endif
                <span>{{ $item->name }}</span>
            </a>
        </li>
    @endif
@endforeach
