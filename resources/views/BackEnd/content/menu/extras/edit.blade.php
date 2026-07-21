
<div class="modal fade" id="MENU_EDIT_MODAL" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3 border-bottom border-light">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="editModalLabel">
                    <i class="fas fa-cube text-primary me-2"></i> Edit Menu
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="edit_menu_form" autocomplete="off" novalidate>
                @csrf

                <div class="modal-body p-3">

                    <div id="modal_error_summary" class="alert alert-danger d-none py-2 px-3 small rounded mb-3 shadow-sm">
                        <i class="fas fa-exclamation-triangle me-1.5"></i> Please correct the highlighted errors below.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="name" class="form-label small fw-semibold text-muted mb-1">Name</label>
                            <input type="text" class="form-control form-control-sm rounded" id="name" name="name" value="{{ $menu->name ?? '' }}">
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
                                <input type="checkbox" class="form-check-input" role="switch" id="is_menu" name="is_menu" value="1" {{ $menu->is_menu ?? false ? 'checked' : ''}}>
                                <label for="is_menu" class="form-check-label small">Is Menu</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-check form-switch mt-4">
                                <input type="checkbox" class="form-check-input" role="switch" id="is_dropdown" name="is_dropdown" value="1" {{ $menu->is_dropdown ?? false ? 'checked' : ''}}>
                                <label for="is_dropdown" class="form-check-label small">Is Dropdown</label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light py-2.5 border-top border-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-white border px-3 text-secondary fw-semibold rounded" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_update_menu" class="btn btn-sm btn-primary px-4 fw-semibold rounded shadow-sm">
                        <i class="fas fa-save me-1.5"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
