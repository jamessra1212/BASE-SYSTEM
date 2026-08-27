@php
    $isEdit = $menu->exists;
@endphp

<div class="modal fade" id="MENU_ENTRY_MODAL" tabindex="-1">
    <div class="modal-dialog">
        <form id="form_menu_entry" class="modal-content">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="modal-header">
                <h5 class="modal-title">{{ $isEdit ? 'Edit Menu Item' : 'New Menu Item' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="modal_error_summary" class="alert alert-danger d-none"></div>

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $menu->name }}" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Parent Menu</label>
                    <select name="parent_id" class="form-select">
                        <option value="">— Top level —</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" {{ $menu->parent_id == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Icon class (optional)</label>
                    <input type="text" name="icon" class="form-control" value="{{ $menu->icon }}" placeholder="fas fa-tachometer-alt">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Named route (optional)</label>
                    <input type="text" name="route" class="form-control" value="{{ $menu->route }}" placeholder="sida.main.home">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fallback URL (optional, used if no route)</label>
                    <input type="text" name="url" class="form-control" value="{{ $menu->url }}">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" class="form-control" value="{{ $menu->order ?? 0 }}" min="0">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                        {{ $menu->exists ? ($menu->is_active ? 'checked' : '') : 'checked' }}>
                    <label class="form-check-label">Active</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" id="btn_save_menu" class="btn btn-primary">Save</button>
            </div>

            @if ($isEdit)
                <input type="hidden" name="_action_url" value="{{ route('sida.admin.menus.update', $menu) }}">
            @else
                <input type="hidden" name="_action_url" value="{{ route('sida.admin.menus.store') }}">
            @endif
        </form>
    </div>
</div>
