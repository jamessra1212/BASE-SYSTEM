<div class="modal fade" id="CHANGE_PASSWORD_MODAL" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-light py-3 border-bottom border-light">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="changePasswordModalLabel">
                    <i class="fas fa-key text-warning me-2"></i> Change Account Password
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form_change_password" autocomplete="off" novalidate>
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">

                <div class="modal-body p-4">

                    <div id="password_error_summary" class="alert alert-danger d-none py-2 px-3 small rounded mb-3 shadow-sm">
                        <i class="fas fa-exclamation-triangle me-1.5"></i> Please correct the highlighted errors below.
                    </div>

                    <div class="mb-2">
                        <span class="small fw-semibold text-secondary d-block mb-1">Target Account:</span>
                        <div class="p-2 bg-light border rounded small text-dark fw-medium">
                            <i class="fas fa-user-circle me-1 text-muted"></i> {{ $user->fname }} {{ $user->lname }} <span class="text-muted">({{ $user->username }})</span>
                        </div>
                    </div>

                    <hr class="my-3 opacity-50 border-light">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="new_password" class="form-label small fw-semibold text-muted mb-1">New Password</label>
                            <input type="password" class="form-control form-control-sm rounded" id="new_password" name="password" placeholder="Minimum 8 characters" required>
                            <div class="invalid-feedback small fw-medium"></div>
                        </div>
                        <div class="col-12">
                            <label for="new_password_confirmation" class="form-label small fw-semibold text-muted mb-1">Confirm New Password</label>
                            <input type="password" class="form-control form-control-sm rounded" id="new_password_confirmation" name="password_confirmation" placeholder="Repeat new password" required>
                            <div class="invalid-feedback small fw-medium"></div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light py-2.5 border-top border-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-white border px-3 text-secondary fw-semibold rounded" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn_update_password" class="btn btn-sm btn-warning px-4 fw-semibold rounded shadow-sm text-dark">
                        <i class="fas fa-shield-alt me-1.5"></i> Update Password
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
