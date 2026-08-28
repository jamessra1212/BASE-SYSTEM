<div class="modal fade" id="PERMISSION_ENTRY_MODAL" tabindex="-1">
    <div class="modal-dialog">
        <form id="form_permission_entry" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">New Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal_error_summary" class="alert alert-danger d-none"></div>

                <div class="mb-3">
                    <label class="form-label">Permission name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. user.store">
                    <div class="form-text">Lowercase, dot-separated (e.g. user.store, report.print). Avoid starting with "menu." — that's reserved.</div>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Group (optional)</label>
                    <input type="text" name="group" class="form-control" placeholder="e.g. User">
                    <div class="form-text">Used to cluster related permissions on the Roles screen. Leave blank to auto-derive from the name (e.g. "user.store" → "User").</div>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" id="btn_save_permission" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
