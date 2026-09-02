@php $isEdit = $permission->exists; @endphp

<div class="modal fade" id="PERMISSION_ENTRY_MODAL" tabindex="-1">
    <div class="modal-dialog">
        <form id="form_permission_entry" class="modal-content">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="modal-header">
                <h5 class="modal-title">{{ $isEdit ? 'Edit Permission' : 'New Permission' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal_error_summary" class="alert alert-danger d-none"></div>

                <div class="mb-3">
                    <label class="form-label">Permission name</label>
                    <input type="text" name="name" class="form-control" required
                        value="{{ $permission->name }}" placeholder="e.g. user.store">
                    <div class="form-text">Lowercase, dot-separated (e.g. user.store, report.print). Avoid starting with "menu." — that's reserved.</div>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Related Menu (optional)</label>
                    <select name="menu_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach ($menus as $menu)
                            <option value="{{ $menu->id }}" {{ $permission->menu_id == $menu->id ? 'selected' : '' }}>
                                {{ $menu->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        If set, this permission shows nested under that menu on the User Access and Roles pages,
                        and its "group" label is taken from the menu's name automatically. If left blank, it falls
                        under a general grouping derived from the permission's name.
                    </div>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" id="btn_save_permission" class="btn btn-primary">Save</button>
            </div>

            @if ($isEdit)
                <input type="hidden" name="_action_url" value="{{ route('core.permissions.update', $permission) }}">
            @else
                <input type="hidden" name="_action_url" value="{{ route('core.permissions.store') }}">
            @endif
        </form>
    </div>
</div>