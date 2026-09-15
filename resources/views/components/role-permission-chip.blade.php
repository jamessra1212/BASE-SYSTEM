{{-- Usage: <x-role-permission-chip name="menu_ids" :id="$menu->id" :value="$menu->id" label="View / Access" :checked="$checked" /> --}}
{{-- 'id' is only used to build a unique DOM id; 'value' is what actually submits (menu id vs permission name differ). --}}
@props(['name', 'id', 'value', 'label', 'checked' => false])

<div class="form-check border rounded px-2 py-1 mb-1">
    <input class="form-check-input" type="checkbox" name="{{ $name }}[]"
        id="{{ $name }}-{{ $id }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }}>
    <label class="form-check-label small" for="{{ $name }}-{{ $id }}">{{ $label }}</label>
</div>