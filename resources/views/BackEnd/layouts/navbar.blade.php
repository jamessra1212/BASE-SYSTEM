<nav class="app-header navbar navbar-expand bg-white border-bottom shadow-sm px-3">
    <div class="container-fluid px-0">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link text-secondary hover-bg rounded-circle d-flex align-items-center justify-content-center"
                   data-lte-toggle="sidebar"
                   href="javascript:void(0)"
                   role="button"
                   style="width: 38px; height: 38px; transition: all 0.2s;">
                    <i class="fas fa-bars fs-5"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center gap-2">
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link text-secondary hover-bg rounded-circle d-flex align-items-center justify-content-center"
                   data-lte-toggle="fullscreen"
                   href="javascript:void(0)"
                   role="button"
                   style="width: 38px; height: 38px; transition: all 0.2s;">
                    <i class="fas fa-expand-arrows-alt fs-5"></i>
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link p-1 d-flex align-items-center gap-2 border rounded-pill bg-light shadow-sm hover-border transition-all"
                   data-bs-toggle="dropdown"
                   href="javascript:void(0)"
                   aria-expanded="false"
                   style="padding-right: 12px !important;">

                    @if(auth()->check() && !empty(auth()->user()->img_slug) && auth()->user()->img_slug !== 'avatar-default.png')
                        <img src="{{ asset('storage/avatars/' . auth()->user()->img_slug) }}"
                             alt="Avatar"
                             class="rounded-circle bg-white border"
                             width="30"
                             height="30"
                             style="object-fit: cover;">
                    @else
                        <img src="{{ asset('storage/avatars/avatar-default.png') }}"
                             alt="Avatar"
                             class="rounded-circle bg-white border"
                             width="30"
                             height="30"
                             style="object-fit: cover;">
                    @endif

                    @if(auth()->check())
                        <span class="d-none d-md-inline-block fw-semibold text-dark small">
                            {{ ucwords(strtolower(auth()->user()->fname ?? 'User')) }}
                        </span>
                    @endif
                    <i class="fas fa-chevron-down text-muted entry-arrow" style="font-size: 10px;"></i>
                </a>

                @if(auth()->check())
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end border-0 shadow-lg py-0 overflow-hidden rounded-3 mt-2" style="min-width: 290px; z-index: 9999;">
                        <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                            <div class="position-relative d-inline-block mb-2">

                                @if(!empty(auth()->user()->img_slug) && auth()->user()->img_slug !== 'avatar-default.png')
                                    <img src="{{ asset('storage/avatars/' . auth()->user()->img_slug) }}"
                                         alt="Display Avatar"
                                         class="rounded-circle border border-2 border-white shadow-sm"
                                         width="68"
                                         height="68"
                                         style="object-fit: cover;">
                                @else
                                    <img src="{{ asset('storage/avatars/avatar-default.png') }}"
                                         alt="Display Avatar"
                                         class="rounded-circle border border-2 border-white shadow-sm"
                                         width="68"
                                         height="68"
                                         style="object-fit: cover;">
                                @endif

                            </div>
                            <h6 class="fw-bold text-truncate mb-0" style="letter-spacing: -0.1px;">
                                {{ ucwords(strtolower(auth()->user()->fname ?? '')) }} {{ ucwords(strtolower(auth()->user()->lname ?? '')) }}
                            </h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mt-2 fw-medium text-uppercase tracking-wider" style="font-size: 10px;">
                                @switch(auth()->user()->roletype)
                                    @case(0) Pending Account @break
                                    @case(1) Scholar @break
                                    @case(2) HRDP Admin @break
                                    @case(3) Super Admin @break
                                    @default System Profile
                                @endswitch
                            </span>
                        </div>

                        <div class="p-2 bg-white">
                            <a href="{{ route('app.main.profile') }}" class="dropdown-item px-3 py-2 d-flex align-items-center text-secondary rounded-2 custom-menu-item">
                                <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                    <i class="fas fa-user-circle fs-6"></i>
                                </div>
                                <span class="fw-medium text-dark" style="font-size: 14px;">Account Settings</span>
                            </a>
                        </div>

                        <div class="p-2 bg-light border-top">
                            <a href="javascript:void(0)"
                               class="dropdown-item px-3 py-2 d-flex align-items-center text-danger rounded-2 custom-menu-item hover-danger-bg"
                               onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                                <div class="bg-danger-subtle text-danger rounded d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                    <i class="fas fa-sign-out-alt fs-6"></i>
                                </div>
                                <span class="fw-semibold" style="font-size: 14px;">Sign Out</span>
                            </a>
                            <form id="frm-logout" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                @else
                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-end border-0 shadow-lg p-2 rounded-3 mt-2">
                        <a href="{{ route('auth.login') }}" class="btn btn-primary w-100 fw-bold py-2 small d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-sign-in-alt"></i> Sign In to Portal
                        </a>
                    </div>
                @endif
            </li>
        </ul>
    </div>
</nav>
