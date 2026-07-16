<div class="modal fade" id="MENU_ENTRY_MODAL" tabindex="-1" aria-labelledby="userEntryModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-light py-3 border-bottom border-light">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="userEntryModalLabel">
                    @if(isset($menu))
                        <i class="fas fa-cube text-warning me-2"></i> Update Menu
                    @else
                        <i class="fas fa-cube text-primary me-2"></i> Create Menu
                    @endif
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form_menu" autocomplete="off" novalidate>
                @csrf

                @if(isset($menu))
                    <input type="hidden" name="id" value="{{ $menu->slug }}">
                @endif

                <div class="modal-body p-3">

                    <div id="modal_error_summary" class="alert alert-danger d-none py-2 px-3 small rounded mb-3 shadow-sm">
                        <i class="fas fa-exclamation-triangle me-1.5"></i> Please correct the highlighted errors below.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="name" class="form-label small fw-semibold text-muted mb-1">Name</label>
                            <input type="text" class="form-control form-control-sm rounded" id="name" name="name" value="{{ $menu->name ?? '' }}" required>
                            <div class="invalid-feedback small fw-medium"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="route" class="form-label small fw-semibold text-muted mb-1">Route</label>
                            <input type="text" class="form-control form-control-sm rounded" id="route" name="route" value="{{ $menu->route ?? '' }}">
                            <div class="invalid-feedback small fw-medium"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="category" class="form-label small fw-semibold text-muted mb-1">Category</label>
                            <input type="text" class="form-control form-control-sm rounded" id="category" name="category" value="{{ $menu->category ?? '' }}" required>
                            <div class="invalid-feedback small fw-medium"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="icon" class="form-label small fw-semibold text-muted mb-1">Icon</label>
                            <input type="text" class="form-control form-control-sm rounded" id="icon" name="icon" value="{{ $menu->icon ?? '' }}" required>
                        </div>
                        <div class="col-md-3">

                            <div class="form-check form-switch mt-4">
                                <input type="checkbox" class="form-check-input" role="switch" id="is_menu" name="is_menu" value="1" {{ isset($menu) && $menu->is_menu ? 'checked' : '' }}>
                                <label for="is_menu" class="form-check-label small">Is Menu</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-check form-switch mt-4">
                                <input type="checkbox" class="form-check-input" role="switch" id="is_dropdown" name="is_dropdown" value="1" {{ isset($menu) && $menu->is_dropdown ? 'checked' : '' }}>
                                <label for="is_dropdown" class="form-check-label small">Is Dropdown</label>
                            </div>
                        </div>
                    </div>

                    <small class="text-uppercase fw-bold text-secondary tracking-wider d-block mb-3 mt-3" style="font-size: 0.75rem;">
                        <i class="fas fa-sliders-h me-1"></i> Create sub-menus
                    </small>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="su_create" name="submenus[]" value="create" {{ isset($menu) && $menu->create_submenu ? 'checked' : '' }}>
                                <label class="form-check-label small">Create</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="su_store" name="submenus[]" value="store" {{ isset($menu) && $menu->store_submenu ? 'checked' : '' }}>
                                <label class="form-check-label small">Store</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="su_edit" name="submenus[]" value="edit" {{ isset($menu) && $menu->edit_submenu ? 'checked' : '' }}>
                                <label class="form-check-label small">Edit</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="su_update" name="submenus[]" value="update" {{ isset($menu) && $menu->update_submenu ? 'checked' : '' }}>
                                <label class="form-check-label small">Update</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="su_show" name="submenus[]" value="show" {{ isset($menu) && $menu->show_submenu ? 'checked' : '' }}>
                                <label class="form-check-label small">Show</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="su_delete" name="submenus[]" value="destroy" {{ isset($menu) && $menu->destroy_submenu ? 'checked' : '' }}>
                                <label class="form-check-label small">Destroy</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="su_print" name="submenus[]" value="print" {{ isset($menu) && $menu->print_submenu ? 'checked' : '' }}>
                                <label class="form-check-label small">Print</label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light py-2.5 border-top border-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-white border px-3 text-secondary fw-semibold rounded" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_save_menu" class="btn btn-sm {{ isset($menu) ? 'btn-warning' : 'btn-primary' }} px-4 fw-semibold rounded shadow-sm">
                        <i class="fas {{ isset($menu) ? 'fa-menu-check' : 'fa-save' }} me-1.5"></i>
                        {{ isset($menu) ? 'Apply Update' : 'Save Menu' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
