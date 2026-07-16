<!-- AdminLTE 4 Sidebar container -->
<aside class="app-sidebar shadow-sm" data-bs-theme="dark" style="background-color: #111827; border-right: 1px solid rgba(255,255,255,0.03); width: 240px;">

    <!-- Brand Logo Space -->
    <div class="sidebar-brand border-bottom border-secondary-subtle py-3 px-4 d-flex align-items-center" style="height: 56px; border-color: rgba(255,255,255,0.05) !important;">
        <a href="#" class="brand-link text-decoration-none d-flex align-items-center gap-2">
            <span class="brand-text fw-bold text-white tracking-wider" style="font-size: 13px; letter-spacing: 1.5px; opacity: 0.9;">PORTAL</span>
        </a>
    </div>

    <!-- Navigation Sidebar Wrapper -->
    <div class="sidebar-wrapper py-2">
        <nav class="px-2">
            <ul class="nav sidebar-menu flex-column gap-1" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <li class="nav-header text-uppercase tracking-widest text-muted fw-bold ps-3 mb-1 mt-2" style="font-size: 10px; letter-spacing: 0.8px; opacity: 0.4;">
                    Main Menu
                </li>

                <li class="nav-item">
                    <!-- Active State: uses a minimal clean text accent color shift instead of a heavy background block -->
                    <a href="#" class="nav-link px-3 py-2 rounded d-flex align-items-center transition-all text-white fw-medium" style="font-size: 12.5px;">
                        <i class="nav-icon fas fa-file-alt me-2-5 text-center" style="width: 16px; font-size: 13px; color: #3b82f6;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('sida.main.user') }}" class="nav-link px-3 py-2 rounded d-flex align-items-center transition-all text-white-50 hover-mini-item" style="font-size: 12.5px;">
                        <i class="nav-icon fas fa-user me-2-5 text-center" style="width: 16px; font-size: 13px; opacity: 0.7;"></i>
                        <span>Manage Users</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('sida.menu.index') }}" class="nav-link px-3 py-2 rounded d-flex align-items-center transition-all text-white-50 hover-mini-item" style="font-size: 12.5px;">
                        <i class="nav-icon fas fa-file-alt me-2-5 text-center" style="width: 16px; font-size: 13px; opacity: 0.7;"></i>
                        <span>Manage Menu</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
