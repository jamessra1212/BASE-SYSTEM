{{-- Usage: <x-permission-chip stateField="menu_state" overrideField="menu_override"
        :id="$menu->id" label="View / Access" :checked="$effective" :overridden="$hasOverride" /> --}}
@props(['stateField', 'overrideField', 'id', 'label', 'checked' => false, 'overridden' => false])

@php
    $stateId = "state-{$stateField}-{$id}";
    $overrideId = "override-{$overrideField}-{$id}";
@endphp

<div class="d-flex align-items-center justify-content-between border rounded px-2 py-1 mb-1">
    <div class="form-check mb-0 flex-grow-1">
        <input class="form-check-input chip-state" type="checkbox"
            name="{{ $stateField }}[{{ $id }}]" id="{{ $stateId }}" value="1"
            {{ $checked ? 'checked' : '' }} {{ $overridden ? '' : 'disabled' }}>
        <label class="form-check-label small" for="{{ $stateId }}">{{ $label }}</label>
    </div>
    <div class="form-check form-switch mb-0 ms-2" title="Override this user's access">
        <input class="form-check-input chip-override" type="checkbox"
            name="{{ $overrideField }}[{{ $id }}]" id="{{ $overrideId }}" value="1"
            {{ $overridden ? 'checked' : '' }} data-target="{{ $stateId }}">
    </div>
</div>