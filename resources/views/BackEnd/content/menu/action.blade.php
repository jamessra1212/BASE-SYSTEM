<div class="dropdown">
    <button class="btn btn-link text-secondary p-0 border-0 lh-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-ellipsis-h"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow border border-light custom-menu-item" style="min-width: 160px; font-size: 0.85rem;">
        <!-- Edit Details -->
        <li>
            <button class="dropdown-item py-1.5 btn_edit" href="javascript:void(0)" data-slug="{{ $menu->menu_id }}">
                <i class="fas fa-edit text-primary me-2 fw-semibold" style="width: 16px;"></i> Edit Menu
            </button>
        </li>

        <li>
            <button class="dropdown-item py-1.5 btn_view" href="javascript:void(0)" data-slug="{{ $menu->menu_id }}">
                <i class="fas fa-cubes text-primary me-2 fw-semibold" style="width: 16px;"></i> View Sub-Menus
            </button>
        </li>

        <!-- Divider -->
        <li><hr class="dropdown-divider my-1 opacity-50"></li>

        <!-- Delete User -->
        <li>
            <button class="dropdown-item py-1.5 text-danger hover-danger-bg btn-delete-menu" type="button" data-id="{{ $menu->menu_id }}">
                <i class="fas fa-trash me-2 fw-semibold" style="width: 16px;"></i> Delete Menu
            </button>
        </li>
    </ul>
</div>
