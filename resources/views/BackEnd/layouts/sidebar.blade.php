{{-- AdminLTE 4 Sidebar --}}
<aside
    class="app-sidebar shadow"
    data-bs-theme="dark"
    style="
        background-color: #111827;
        border-right: 1px solid rgba(255,255,255,0.03);
    "
>

    {{-- Sidebar Brand --}}
    <div class="sidebar-brand">

        <span
            class="brand-text fw-bold text-white"
            style="
                font-size: 13px;
                letter-spacing: 1.5px;
                opacity: .9;
            "
        >
            {{ setting('app_name', 'PORTAL') }}
        </span>

    </div>


    {{-- Sidebar Wrapper --}}
    <div class="sidebar-wrapper">

        <nav class="mt-2" aria-label="Main navigation">

            <ul
                class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                data-accordion="false"
                id="navigation"
            >

                @php
                    $menuTree = app(\App\Core\Services\MenuService::class)
                        ->getMenuTreeForUser(auth()->user());
                @endphp

                <x-menu-tree :items="$menuTree" />

            </ul>

        </nav>

    </div>

</aside>
```
