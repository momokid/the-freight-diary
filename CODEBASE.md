# Freight Diary v2.0 — Codebase Documentation

**Built by:** Anwar Sadat (AnwarVerse Ltd)  
**Stack:** Laravel 12, Blade, Tailwind CSS, MySQL  
**Database:** `primesur_freight` (legacy MySQL database)  
**Local Environment:** Laravel Herd at `E:\Project\the-freight-app\`  
**Last Updated:** March 2026

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Directory Structure](#2-directory-structure)
3. [Database Tables](#3-database-tables)
4. [Models](#4-models)
5. [Controllers](#5-controllers)
6. [Middleware](#6-middleware)
7. [Routes](#7-routes)
8. [Views](#8-views)
9. [Providers](#9-providers)
10. [Permission System](#10-permission-system)
11. [Password Reset Flow](#11-password-reset-flow)
12. [Security Features](#12-security-features)
13. [Common Tasks](#13-common-tasks)

---

## 1. Project Overview

Freight Diary is a freight forwarding management system for Prime Survivors International Ltd (PSIL). This is a full rebuild of a legacy PHP system using Laravel 12.

**Key Design Decisions:**

- The existing MySQL database is reused without structural changes (except for new columns we added)
- Blade templates are used instead of a separate React frontend
- Tailwind CSS is used for styling with CSS variables for theming
- All DB table names and column names are preserved from the legacy system

---

## 2. Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php                  — handles login, logout, password reset
│   │   ├── DashboardController.php             — handles dashboard page
│   │   └── Settings/
│   │       └── UserPrivilegeController.php     — handles user permission management
│   └── Middleware/
│       ├── CheckPermission.php                 — blocks routes based on user permissions
│       ├── ForcePasswordChange.php             — redirects users who must change password
│       └── SecurityHeaders.php                — adds HTTP security headers to all responses
├── Models/
│   ├── User.php                                — maps to kaina table (system users)
│   └── UserAuth.php                            — maps to user_auth table (permissions)
└── Providers/
    └── AppServiceProvider.php                  — registers rate limiters, view composers

bootstrap/
└── app.php                                     — registers middleware aliases and groups

database/
└── migrations/                                 — all database migrations

resources/
└── views/
    ├── auth/
    │   ├── login.blade.php                     — login and forgot password page
    │   └── change-password.blade.php           — force password change page
    ├── layouts/
    │   ├── app.blade.php                       — main app layout (sidebar, topbar)
    │   └── auth.blade.php                      — auth pages layout (dark background)
    ├── settings/
    │   └── user-privilege.blade.php            — user permission management page
    └── dashboard.blade.php                     — dashboard page

routes/
└── web.php                                     — all application routes
```

---

## 3. Database Tables

### `kaina` — System Users

The main users table. Every person who logs into the system has a row here.

| Column                 | Type    | Description                                   |
| ---------------------- | ------- | --------------------------------------------- |
| `ID`                   | varchar | Primary key — the username used to log in     |
| `FullName`             | varchar | User's full name                              |
| `HashPassword`         | varchar | Bcrypt hashed password                        |
| `Nature`               | varchar | User role label e.g. `Admin-0`, `FrontDesk`   |
| `Stats`                | int     | Account status — `1` = active, `0` = inactive |
| `BranchID`             | varchar | Branch the user belongs to                    |
| `remember_token`       | varchar | Laravel remember me token                     |
| `reset_requested`      | tinyint | `1` = user has requested a password reset     |
| `must_change_password` | tinyint | `1` = user must change password on next login |

### `user_auth` — User Permissions

Stores what each user is allowed to do. One row per user.

| Column                     | Type    | Description                                     |
| -------------------------- | ------- | ----------------------------------------------- |
| `Username`                 | varchar | Primary key — matches `kaina.ID`                |
| `BasicConfig`              | tinyint | Can access Basic Setup section                  |
| `ConsignmentRegister`      | tinyint | Can access Consignment Register                 |
| `GenerateInvoice`          | tinyint | Can generate invoices                           |
| `PaymentTransaction`       | tinyint | Can do payment transactions                     |
| `GLTransaction`            | tinyint | Can do GL transactions                          |
| `Disbursement`             | tinyint | Can access disbursement                         |
| `ConsignmentExpense`       | tinyint | Can access consignment expenses                 |
| `Transport`                | tinyint | Can access transport                            |
| `EditData`                 | tinyint | Can edit data                                   |
| `AssignConsignmentOfficer` | tinyint | Can assign consignment officers                 |
| `DisbursementAnalysis`     | tinyint | Can view disbursement analysis                  |
| `DisbursementRevenue`      | tinyint | Can view disbursement revenue                   |
| `DisbursementOtherExpense` | tinyint | Can view disbursement other expenses            |
| `DisbursementApproval`     | tinyint | Can approve disbursements                       |
| `ReverseTransaction`       | tinyint | Can reverse transactions                        |
| `AccountingReport`         | tinyint | Can view accounting reports                     |
| `CashExpenditure`          | tinyint | Can access cash expenditure                     |
| `TransportTrip`            | tinyint | Can access transport trips                      |
| `TransportExpense`         | tinyint | Can access transport expenses                   |
| `PettyCash`                | tinyint | Can access petty cash                           |
| `CnsAwaitingClearance`     | tinyint | Can view consignments awaiting clearance widget |
| `PendingGateOutDashboard`  | tinyint | Can view pending gate out widget                |
| `VehicleHubDashboard`      | tinyint | Can view vehicle hub widget                     |
| `UserPrivilege`            | tinyint | Can manage user permissions                     |
| `Hashing`                  | tinyint | Can access hashing feature                      |

### `user_login_logs` — Login Audit Trail

Records every login attempt for security auditing.

| Column       | Type      | Description                         |
| ------------ | --------- | ----------------------------------- |
| `id`         | int       | Auto increment primary key          |
| `username`   | varchar   | The ID that was used in the attempt |
| `ip_address` | varchar   | IP address of the request           |
| `user_agent` | varchar   | Browser/device information          |
| `status`     | varchar   | `success`, `failed`, or `inactive`  |
| `created_at` | timestamp | When the attempt happened           |

---

## 4. Models

### `app/Models/User.php`

Maps to the `kaina` table. Extends `Authenticatable` so Laravel's auth system can use it.

**Key things to know:**

- Primary key is `ID` (string) not the default `id` (integer)
- Password column is `HashPassword` not the default `password`
- No timestamps (`created_at`, `updated_at` don't exist in `kaina`)
- `reset_requested` and `must_change_password` are cast to boolean

**How to fetch a user:**

```php
$user = User::where('ID', 'someusername')->first();
```

**How to update a user:**

```php
$user = User::where('ID', 'someusername')->firstOrFail();
$user->FullName = 'New Name';
$user->save();
```

**Important — always use `User::where()` to fetch fresh from DB before saving:**

```php
// CORRECT — fetches a proper Eloquent model instance
$user = User::where('ID', Auth::user()->ID)->firstOrFail();
$user->FullName = 'New Name';
$user->save();

// INCORRECT — Auth::user() may not always support save()
$user = Auth::user();
$user->save(); // may throw "undefined method" error
```

---

### `app/Models/UserAuth.php`

Maps to the `user_auth` table. Stores all permission columns.

**Key things to know:**

- Primary key is `Username` (string) — matches `kaina.ID`
- No timestamps
- All permission columns are cast to boolean
- `PERMISSIONS` constant is the single source of truth for all permission column names

**How to check a permission:**

```php
$userAuth = UserAuth::where('Username', 'someusername')->first();

if ($userAuth->hasPermission('BasicConfig')) {
    // user has BasicConfig permission
}
```

**How to add a new permission in the future:**

1. Add the column to `user_auth` table via a migration
2. Add the column name to `const PERMISSIONS` in `UserAuth.php`
3. Add the label and group to `$permissionGroups` in `UserPrivilegeController::index()`
4. That's it — everything else updates automatically

---

## 5. Controllers

### `app/Http/Controllers/AuthController.php`

Handles everything related to authentication.

| Method                 | Route                   | What it does                                           |
| ---------------------- | ----------------------- | ------------------------------------------------------ |
| `showLogin()`          | `GET /login`            | Shows the login page                                   |
| `login()`              | `POST /login`           | Processes login attempt                                |
| `logout()`             | `POST /logout`          | Logs the user out                                      |
| `forgotPassword()`     | `POST /forgot-password` | Sets `reset_requested = 1` for the user                |
| `showChangePassword()` | `GET /change-password`  | Shows the change password form                         |
| `changePassword()`     | `POST /change-password` | Updates the password and clears `must_change_password` |

**Login flow in detail:**

```
1. Validate input (ID and password required)
2. Attempt Auth::attempt() with credentials
3. If successful — check Stats column
   - Stats = 0 → logout immediately, show "account inactive" error
   - Stats = 1 → log success, regenerate session
4. Check must_change_password
   - = 1 → redirect to /change-password
   - = 0 → redirect to /dashboard
5. If Auth::attempt() failed → log failure, show "invalid credentials" error
```

---

### `app/Http/Controllers/DashboardController.php`

Handles the dashboard page.

| Method    | Route            | What it does                                         |
| --------- | ---------------- | ---------------------------------------------------- |
| `index()` | `GET /dashboard` | Loads dashboard, passes `$pendingResetCount` to view |

---

### `app/Http/Controllers/Settings/UserPrivilegeController.php`

Handles user permission management.

| Method            | Route                                          | What it does                                                         |
| ----------------- | ---------------------------------------------- | -------------------------------------------------------------------- |
| `index()`         | `GET /settings/user-privilege`                 | Shows the user privilege page with users and permission groups       |
| `show()`          | `GET /settings/user-privilege/{userId}`        | Returns a user's permissions as JSON for AJAX                        |
| `initialise()`    | `POST /settings/user-privilege/initialise`     | Creates a `user_auth` row for a user with all permissions set to `0` |
| `toggle()`        | `POST /settings/user-privilege/toggle`         | Flips a single permission between `0` and `1`                        |
| `resetPassword()` | `POST /settings/user-privilege/reset-password` | Generates a temporary password and forces change on next login       |

---

## 6. Middleware

### `app/Http/Middleware/CheckPermission.php`

Protects routes based on user permissions.

**How it works:**

1. Gets the logged in user
2. Looks up their `user_auth` row
3. Checks if the required permission column is `1`
4. If not — redirects to dashboard with a generic error message

**How to use it on a route:**

```php
Route::get('/some-page', [SomeController::class, 'index'])
    ->middleware('permission:BasicConfig');
```

**How to protect a group of routes:**

```php
Route::middleware('permission:BasicConfig')->group(function () {
    Route::get('/ledger-control', [...]);
    Route::post('/ledger-control', [...]);
});
```

---

### `app/Http/Middleware/ForcePasswordChange.php`

Runs on every web request automatically. If the logged in user has `must_change_password = 1`, redirects them to `/change-password` before they can access anything else.

**Excluded routes** (allowed even when must change password):

- `password.change` — the change password page itself
- `password.update` — the form submission
- `logout` — so user can log out if needed
- `login` and `login.submit` — for unauthenticated users

---

### `app/Http/Middleware/SecurityHeaders.php`

Runs on every web request automatically. Adds HTTP security headers to every response to protect against common browser-based attacks.

**Headers added:**

- `X-Frame-Options: SAMEORIGIN` — prevents clickjacking (embedding in iframes)
- `X-Content-Type-Options: nosniff` — prevents MIME type sniffing
- `X-XSS-Protection: 1; mode=block` — enables browser XSS filter
- `Referrer-Policy: strict-origin-when-cross-origin` — controls referrer information
- `Permissions-Policy` — disables unused browser features (camera, microphone, etc.)

---

## 7. Routes

All routes are in `routes/web.php`.

### Guest Routes (only accessible when NOT logged in)

```
GET  /login           — show login page
POST /login           — process login (rate limited: 5 attempts/min per IP+UserID)
POST /forgot-password — request password reset (rate limited: 3 attempts/min per IP)
```

### Authenticated Routes (must be logged in)

```
POST /logout                — log out
GET  /change-password       — show change password form
POST /change-password       — process password change
GET  /dashboard             — dashboard
```

### Settings Routes (must be logged in + have UserPrivilege permission)

```
GET  /settings/user-privilege                — user privilege page
GET  /settings/user-privilege/{userId}       — get user permissions (AJAX)
POST /settings/user-privilege/initialise     — initialise user permissions
POST /settings/user-privilege/toggle         — toggle a single permission
POST /settings/user-privilege/reset-password — reset a user's password
```

---

## 8. Views

### `resources/views/layouts/app.blade.php`

The main layout used by all authenticated pages. Contains:

- Dark green sidebar with navigation links
- Topbar with theme toggle, notification bell, user dropdown
- Permission-based sidebar visibility using `$userAuth->hasPermission()`
- Dark/light theme toggle stored in `localStorage`
- Notification bell badge showing pending password reset count

**CSS Variables for theming:**

```css
--sidebar-bg      — sidebar background color
--topbar-bg       — topbar background color
--content-bg      — main content area background
--card-bg         — card/panel background
--text-primary    — main text color
--text-muted      — secondary/muted text color
--sidebar-text    — sidebar text (always white)
--accent          — green accent color
```

**To add a new sidebar section:**

```blade
@if(isset($userAuth) && $userAuth->hasPermission('YourPermission'))
    <div class="nav-section-label">Section Name</div>
    <div class="nav-item-wrapper">
        <a href="{{ route('your.route') }}" class="nav-link
            {{ request()->routeIs('your.route*') ? 'active' : '' }}">
            <svg>...</svg>
            <span class="nav-label">Menu Item</span>
        </a>
    </div>
@endif
```

---

### `resources/views/layouts/auth.blade.php`

Used by login and change password pages. Dark green background with dot pattern. No sidebar or topbar.

---

### `resources/views/auth/login.blade.php`

Two panels on the same page toggled by JavaScript:

- `panel-login` — the normal login form with username, password, remember me
- `panel-forgot` — the forgot password form with just a User ID field

`showPanel('login')` and `showPanel('forgot')` switch between them.

---

### `resources/views/auth/change-password.blade.php`

Simple form shown when `must_change_password = 1`. Requires new password and confirmation. Minimum 6 characters. Uses the auth layout so the user cannot access the app until they change their password.

---

### `resources/views/dashboard.blade.php`

Currently shows a red alert banner if there are pending password reset requests AND the logged in user has `UserPrivilege` permission. Dashboard content to be built out as features are added.

---

### `resources/views/settings/user-privilege.blade.php`

Two panel layout with fixed height and internal scroll:

**Left panel — Users list:**

- Shows all users from `kaina` ordered by name
- Search input filters the list in real time
- Red RESET badge on users with `reset_requested = 1`
- Clicking a user loads their permissions via AJAX

**Right panel — Three states:**

- **Empty** — shown when no user is selected
- **Initialise** — shown when selected user has no `user_auth` row yet
- **Permissions** — shows all permission toggles grouped by section

All interactions are AJAX — flipping a toggle, initialising, and resetting passwords all happen without page reloads.

---

## 9. Providers

### `app/Providers/AppServiceProvider.php`

Runs when the application boots. Does three things:

**1. Rate Limiters**

```php
// Login — 5 attempts per minute per IP + User ID combination
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->input('ID') . '|' . $request->ip());
});

// Forgot password — 3 attempts per minute per IP
RateLimiter::for('forgot-password', function (Request $request) {
    return Limit::perMinute(3)->by($request->ip());
});
```

**2. View Composer**
Shares two variables with every view automatically so you don't have to pass them from every controller:

- `$pendingResetCount` — number of users with `reset_requested = 1`
- `$userAuth` — the logged in user's `user_auth` record (their permissions)

Uses `static` variables to cache DB results — DB is only hit once per request regardless of how many views are rendered.

---

## 10. Permission System

### How It Works

Every user has a row in `user_auth` with a column for each feature. Each column is either `1` (allowed) or `0` (denied).

```
User logs in
    → View composer loads their user_auth row into $userAuth
    → Sidebar checks $userAuth->hasPermission('X') to show/hide menu items
    → Middleware checks $userAuth->hasPermission('X') to allow/deny route access
```

### Two Layers of Protection

**Layer 1 — UI (sidebar hiding)**
Menu items only render in HTML if the user has the permission. A user without permission never even sees the link.

```blade
@if(isset($userAuth) && $userAuth->hasPermission('BasicConfig'))
    {{-- menu item only renders if user has permission --}}
@endif
```

**Layer 2 — Route middleware**
Even if someone types the URL directly into the browser, the middleware blocks them.

```php
Route::middleware('permission:BasicConfig')->group(function () {
    // blocked unless user has BasicConfig = 1 in user_auth
});
```

**Both layers are always needed.** UI hiding alone is not enough.

### Permission Groups (as displayed in UI)

| Group             | Permissions                                                                                             |
| ----------------- | ------------------------------------------------------------------------------------------------------- |
| Setup & Config    | BasicConfig                                                                                             |
| Consignment       | ConsignmentRegister, AssignConsignmentOfficer                                                           |
| Transactions      | GenerateInvoice, PaymentTransaction, GLTransaction, ReverseTransaction                                  |
| Disbursement      | Disbursement, DisbursementAnalysis, DisbursementRevenue, DisbursementOtherExpense, DisbursementApproval |
| Expenses          | ConsignmentExpense, CashExpenditure, PettyCash                                                          |
| Transport         | Transport, TransportTrip, TransportExpense                                                              |
| Reports           | AccountingReport                                                                                        |
| Dashboard Widgets | CnsAwaitingClearance, PendingGateOutDashboard, VehicleHubDashboard                                      |
| Admin             | UserPrivilege, EditData, Hashing                                                                        |

---

## 11. Password Reset Flow

### User Side

```
1. User clicks "Forgot Password?" on login page
2. Enters their User ID
3. System silently sets reset_requested = 1 in kaina table
   (same success message shown whether ID exists or not — security)
4. User sees: "Your request has been submitted. Please contact your administrator."
```

### Admin Side

```
1. Admin sees red badge on notification bell in topbar
2. Admin sees red alert banner on dashboard with count
3. Admin goes to Settings → Basic Setup → User Privilege
4. Users with pending reset show a red RESET badge in the users list
5. Admin clicks the user → permissions panel loads
6. "Reset Password" button appears in the panel header
7. Admin clicks "Reset Password" → confirms the action
8. Modal shows a 6-character cryptographically secure temporary password
9. Admin copies it and shares with user via WhatsApp or phone
10. System saves bcrypt hashed password
11. System sets must_change_password = 1
12. System clears reset_requested = 0
13. Modal closes — RESET badge disappears from user list
```

### After Reset — User Side

```
1. User logs in with the temporary password
2. System detects must_change_password = 1
3. ForcePasswordChange middleware redirects to /change-password
4. User cannot access any other page until password is changed
5. User sets new password (minimum 6 characters, confirmed)
6. System hashes new password with bcrypt
7. System sets must_change_password = 0
8. User is redirected to dashboard
```

---

## 12. Security Features

| Feature                         | How it's implemented                                         |
| ------------------------------- | ------------------------------------------------------------ |
| Password hashing                | `bcrypt()` — industry standard one-way hash                  |
| CSRF protection                 | `@csrf` on all forms, Laravel verifies on every POST         |
| Rate limiting — login           | Max 5 attempts/min per IP + User ID combination              |
| Rate limiting — forgot password | Max 3 attempts/min per IP                                    |
| Session regeneration            | `$request->session()->regenerate()` after login              |
| Session invalidation            | `$request->session()->invalidate()` after logout             |
| Brute force protection          | Rate limiting stops password guessing attacks                |
| Permission middleware           | `CheckPermission` blocks unauthorised route access           |
| Force password change           | `ForcePasswordChange` middleware on all web routes           |
| Security headers                | `SecurityHeaders` middleware on all responses                |
| Audit logging                   | All login attempts logged to `user_login_logs`               |
| Cryptographic passwords         | `random_int()` for temporary password generation             |
| Account blocking                | `Stats = 0` users cannot log in                              |
| User enumeration prevention     | Forgot password shows same message for valid and invalid IDs |
| Whitelist validation            | Toggle endpoint validates permission name against known list |

---

## 13. Common Tasks

### How to Add a New Page

**Step 1 — Create the controller:**

```powershell
php artisan make:controller YourController
```

**Step 2 — Add the route in `routes/web.php`:**

```php
Route::middleware('auth')->group(function () {
    // add inside this group
    Route::get('/your-page', [YourController::class, 'index'])
        ->name('your-page.index');
});
```

**Step 3 — Create the view:**

```powershell
php artisan make:view your-page
```

**Step 4 — Extend the app layout in the view:**

```blade
@extends('layouts.app')

@section('title', 'Your Page Title')
@section('page-title', 'Your Page Title')

@section('content')
    {{-- your content here --}}
@endsection
```

---

### How to Add a New Permission

**Step 1 — Create a migration:**

```powershell
php artisan make:migration add_newpermission_to_user_auth
```

**Step 2 — Fill in the migration file:**

```php
public function up(): void
{
    Schema::table('user_auth', function (Blueprint $table) {
        $table->tinyInteger('NewPermission')->default(0)->after('Hashing');
    });
}

public function down(): void
{
    Schema::table('user_auth', function (Blueprint $table) {
        $table->dropColumn('NewPermission');
    });
}
```

**Step 3 — Run the migration:**

```powershell
php artisan migrate
```

**Step 4 — Add to `UserAuth::PERMISSIONS` in `app/Models/UserAuth.php`:**

```php
const PERMISSIONS = [
    // ... existing permissions
    'NewPermission', // add here — everything else updates automatically
];
```

**Step 5 — Add to `$permissionGroups` in `UserPrivilegeController::index()`:**

```php
'Your Group' => [
    // ... existing permissions in this group
    'NewPermission' => 'Human Readable Label',
],
```

**Step 6 — Protect the route:**

```php
Route::middleware('permission:NewPermission')->group(function () {
    Route::get('/new-feature', [NewController::class, 'index'])->name('new-feature.index');
});
```

**Step 7 — Hide the sidebar link:**

```blade
@if(isset($userAuth) && $userAuth->hasPermission('NewPermission'))
    <a href="{{ route('new-feature.index') }}" class="submenu-link">
        New Feature
    </a>
@endif
```

---

### How to Add a New Item to Basic Setup Submenu

The Basic Setup section in the sidebar currently contains User Privilege. To add Ledger Control:

1. Add the route inside the settings prefix group with the correct permission
2. Add the link inside the existing submenu div in `app.blade.php`:

```blade
<div id="submenu-setup" class="submenu closed">

    @if(isset($userAuth) && $userAuth->hasPermission('UserPrivilege'))
        <a href="{{ route('settings.user-privilege.index') }}" class="submenu-link
            {{ request()->routeIs('settings.user-privilege.*') ? 'active' : '' }}">
            User Privilege
        </a>
    @endif

    {{-- Add new items below --}}
    @if(isset($userAuth) && $userAuth->hasPermission('BasicConfig'))
        <a href="{{ route('settings.ledger-control.index') }}" class="submenu-link
            {{ request()->routeIs('settings.ledger-control.*') ? 'active' : '' }}">
            Ledger Control
        </a>
    @endif

</div>
```

---

### How to Debug a Permission Issue

If a user can't access something they should be able to:

**Step 1 — Check their `user_auth` row directly in MySQL:**

```sql
SELECT * FROM user_auth WHERE Username = 'theiruserid';
```

**Step 2 — Check the relevant column:**

- If the row doesn't exist → go to User Privilege screen and click "Initialise Access Permissions"
- If the row exists but the column is `0` → toggle it to `1` in the User Privilege screen
- If the row exists and column is `1` → check the route has the correct permission name in its middleware

---

### How to Reset a User's Password Manually via Database

Use this only if the admin UI is not available:

```sql
-- Step 1: Generate a bcrypt hash in PHP
-- Run this in any PHP file or tinker: echo bcrypt('temporarypassword');

-- Step 2: Update the user's record
UPDATE kaina
SET HashPassword = '$2y$10$your_generated_hash_here',
    must_change_password = 1,
    reset_requested = 0
WHERE ID = 'theiruserid';
```

Or use Laravel Tinker:

```powershell
php artisan tinker
```

```php
$user = App\Models\User::where('ID', 'theiruserid')->first();
$user->HashPassword = bcrypt('temporarypassword');
$user->must_change_password = 1;
$user->reset_requested = 0;
$user->save();
```

---

### How to Check What's in the Login Logs

```sql
-- See all recent login attempts
SELECT * FROM user_login_logs ORDER BY created_at DESC LIMIT 50;

-- See failed attempts for a specific user
SELECT * FROM user_login_logs
WHERE username = 'theiruserid' AND status = 'failed'
ORDER BY created_at DESC;

-- Count attempts by status
SELECT status, COUNT(*) as total
FROM user_login_logs
GROUP BY status;
```

---

### How to Clear All Caches (run when something looks wrong)

```powershell
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

### How to Run Database Migrations

```powershell
# Run all pending migrations
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback the last migration (careful — this drops columns/tables)
php artisan migrate:rollback
```

---

### Git Workflow

We commit after every completed feature:

```powershell
# Stage all changes
git add .

# Check what's being committed
git status

# Commit with a descriptive message
git commit -m "feat: description of what you built"

# Push to GitHub
git push origin master
```

**Commit message prefixes we use:**

- `feat:` — new feature
- `fix:` — bug fix
- `docs:` — documentation update
- `refactor:` — code restructure without changing behaviour
- `security:` — security improvement

---

_This document should be updated whenever a new feature is added to the system._
_Last updated: March 2026 by Anwar Sadat (AnwarVerse Ltd)_
