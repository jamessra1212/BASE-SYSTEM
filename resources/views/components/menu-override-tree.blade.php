{{-- Usage: <x-menu-override-tree :items="$menus" :overrides="$overrides" /> --}}
{{-- $overrides is a [menu_id => 'allow'|'deny'] map; anything absent = inherit --}}
@props(['items', 'overrides'])

<ul class="list-unstyled ms-3">
    @foreach ($items as $item)
        @php $current = $overrides->get($item->id, 'inherit'); @endphp
        <li class="mb-2">
            <div class="d-flex align-items-center gap-3">
                <span style="min-width: 220px;">
                    @if ($item->icon) <i class="{{ $item->icon }} me-1"></i> @endif
                    {{ $item->name }}
                </span>

                @foreach (['inherit' => 'Inherit from role', 'allow' => 'Allow', 'deny' => 'Deny'] as $value => $label)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio"
                            name="overrides[{{ $item->id }}]" id="ov-{{ $item->id }}-{{ $value }}"
                            value="{{ $value }}" {{ $current === $value ? 'checked' : '' }}>
                        <label class="form-check-label small" for="ov-{{ $item->id }}-{{ $value }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>

            @if ($item->children->isNotEmpty())
                <x-menu-override-tree :items="$item->children" :overrides="$overrides" />
            @endif
        </li>
    @endforeach
</ul>
