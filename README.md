# BASE-SYSTEM — RBAC & Dynamic Menu Base Template

A reusable Laravel foundation for future projects: dynamic, nested sidebar menus; role-based access control via `spatie/laravel-permission`; per-user allow/deny overrides on top of roles; and an audit trail via `spatie/laravel-activitylog`. This document is the reference for setting this template up on a new machine or a new project, and for understanding how the pieces fit together.

## Stack

- PHP ^8.3, Laravel ^13.8
- `spatie/laravel-permission` — roles & permissions
- `spatie/laravel-activitylog` — audit log
- `yajra/laravel-datatables` — all admin list screens
- `laravel/socialite` — Google login

## First-time setup (new clone / new machine)

```bash
composer install
copy .env.example .env        # or: cp .env.example .env
php artisan key:generate
```

Edit `.env`: set `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` for your local database (MySQL). Make sure `APP_ENV=local` and `APP_DEBUG=true` for development.

```bash
php artisan migrate
php artisan db:seed
```

That single `db:seed` call runs everything in order: base roles/permissions → a default Super Admin account → the starter menu tree.

**Default login after seeding:**
- Email: `admin@example.com`
- Password: `password`

⚠️ Change this before any real deployment — it's a placeholder for local dev convenience only.

If you're on Windows/XAMPP and something looks broken after pulling changes, the two most common fixes throughout this project were:

```bash
composer dump-autoload
php artisan config:clear
```

## Architecture overview

### Namespaces

Everything reusable/project-agnostic lives under `App\Core\*`, mirroring `app/Core/*` on disk:

| What | Namespace |
|---|---|
| Menu, MenuUserOverride, PermissionUserOverride models | `App\Core\Models` |
| MenuService, UserService | `App\Core\Services` |
| MenusDataTable, RolesDataTable, PermissionsDataTable, UsersDataTable, LogsDataTable | `App\Core\DataTables` |
| MenuController, RoleController, PermissionController, UserAccessController, LogController, UserController | `App\Core\Http\Controllers` |
| StoreUserRequest, UpdatePasswordRequest | `App\Core\Http\Requests` |
| CheckPermissionOverride (the `perm:` middleware) | `App\Core\Http\Middleware` |

`App\Models\User` deliberately stays put — Laravel's auth config, factories, and internals assume it lives there, and moving it caused real breakage when tried.

`LoginController` and `MainController` (general app shell, not RBAC-specific) stay in `App\Http\Controllers\BackEnd`.

### Route structure (`routes/web.php`)

- `auth.*` — login/logout, unprotected
- `app.*` (prefix `app/`) — general authenticated pages (dashboard, profile)
- `core.*` (prefix `core/`) — every RBAC admin screen: menus, roles, permissions, users, access, logs

### The permission model

Two independent layers of access, and they compose:

1. **Page/menu-level** — every row in the `menus` table auto-generates a matching Spatie permission (`menu.<slug>`) the moment it's created. This controls whether a menu item shows in the sidebar, and (when the route is protected by it) whether the page loads at all.
2. **Action-level** — permissions created manually via **Settings → Permissions** (e.g. `manage menus`, `user.destroy`, `report.print`). These gate specific routes/buttons, independent of menu visibility.

**The finalized two-tier convention per admin resource:** one base permission (`manage menus`, `manage roles`, `manage users`, `manage permissions`, `manage access`) gates the *entire* viewable block — list, create, edit. Deletion alone gets pulled into its own separate permission (`menus.destroy`, `roles.destroy`, etc.). This lets you grant someone full working access to a section while withholding the ability to delete things, or vice versa, per role or per individual.

**Every permission-protected route uses the custom `perm:` middleware**, not Spatie's built-in `permission:`. This is not optional — `perm:` is override-aware (checks `permission_user_overrides` before falling back to the role), while plain `permission:` only ever checks roles and silently ignores any override you set. Using the wrong one was the single most common bug throughout this project's build.

### Per-user overrides

Two override tables, same shape, same idea — allow/deny for one specific person, on top of whatever their role grants:

- `menu_user_overrides` — per-menu-item
- `permission_user_overrides` — per-action-permission

Resolution order (in `MenuService` for menus, `User::canAccessPermission()` for permissions): **override first, role second.** A menu stays visible if it, or any of its children, are accessible — so parent group headers don't disappear empty.

Manage these from **Settings → User Access**: pick a user, and every menu/permission shows an Inherit / Allow / Deny control.

### Linking permissions to menus

A permission can optionally have a **Related Menu** (Settings → Permissions → Edit). When set:
- It displays nested under that menu (instead of the flat "Other Permissions" list) on both the Roles page and the User Access page.
- Its `group` label auto-derives from the menu's name.
- On the User Access page, if the permission is one of the five base-access names, allowing the *menu* automatically allows the *permission* too (a small JS cascade) — since granting someone a page with no way to actually enter it makes little sense. Anything else nested there (like `menus.destroy`) is never auto-cascaded, so it stays fully independent for per-account overrides.

### Super Admin

Bypasses every permission check entirely via `Gate::before` in `AuthServiceProvider` — never blocked by a missing permission, override, or otherwise. This is why the Super Admin role can't be deleted from the Roles screen.

### UI conventions (every admin screen follows this)

- Layout: `@extends('BackEnd.layouts.master')`, `@section('content')`, `@push('script')` — **singular**, not `scripts`.
- Lists: a generated Yajra `DataTable` class (`app/Core/DataTables/*.php`), rendered via `$dataTable->render('view')`.
- Create/edit: a GET `entry` endpoint returns modal HTML, loaded via AJAX into a shared `#modal-body` div. Submit is a single JS handler reading a hidden `_action_url` field so the same code handles both create (`POST`) and edit (`PUT` spoofed via `@method`).
- Responses: JSON `{status: 'success'|'error', message: '...'}`.
- Feedback: `toastr` for success/error, `SweetAlert2` (`Swal.fire`) for delete/destructive confirmations.

### Activity log

Not automatic per-model logging — deliberately explicit `activity()->causedBy()->performedOn()->log('...')` calls at each mutating action across the admin controllers/services. This keeps log messages human-readable ("created menu \"Reports\"") instead of generic model-diff noise, and avoids needing to extend Spatie's own `Role`/`Permission` model classes. View it at **Settings → Activity Logs**; clearing it requires the separate `logs.clear` permission.

## Adding a new page to the system

1. Build the route, controller, and view as normal. Protect the route with `perm:<permission-name>` — this is what actually enforces access; hiding a link from the sidebar is UX, not security.
2. **Settings → Menu Management** — add a row (name, icon, parent, the route's name). Its `menu.<slug>` permission is created automatically.
3. **Settings → Roles & Permissions** — grant the menu (and any action permissions it needs) to whichever roles should have it.
4. Optionally, **Settings → User Access** — allow/deny it for one specific person as an exception to their role.

That's the whole workflow, and it doesn't change as the project grows — it's the same four steps whether this is the first custom page or the fiftieth.