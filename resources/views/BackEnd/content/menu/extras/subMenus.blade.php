<div class="modal fade" id="SUBMENU_VIEW_MODAL" tabindex="-1" aria-labelledby="userEntryModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-light py-3 border-bottom border-light">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="userEntryModalLabel">
                    <i class="fas fa-cube text-primary me-2"></i> Manage Sub-menus
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="px-4 pb-4 pt-3 overflow-hidden">
                <div class="table-responsive">
                    {{ $dataTable->table(['class' => 'table align-middle border-0 w-100 mb-0']) }}
                </div>

            </div>

        </div>
    </div>
</div>

{{ $dataTable->scripts() }}
