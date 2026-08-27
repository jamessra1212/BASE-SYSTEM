{{-- Usage: <x-menu-checkbox-tree :items="$menus" :checked="$rolePermissionNames" /> --}}
@props(['items', 'checked'])

<ul class="list-unstyled ms-3">
    @foreach ($items as $item)
        <li class="mb-1">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="menu_ids[]"
                    id="menu-{{ $item->id }}" value="{{ $item->id }}"
                    {{ $checked->contains($item->permission_name) ? 'checked' : '' }}>
                <label class="form-check-label" for="menu-{{ $item->id }}">
                    @if ($item->icon) <i class="{{ $item->icon }} me-1"></i> @endif
                    {{ $item->name }}
                </label>
            </div>

            @if ($item->children->isNotEmpty())
                <x-menu-checkbox-tree :items="$item->children" :checked="$checked" />
            @endif
        </li>
    @endforeach
</ul>
