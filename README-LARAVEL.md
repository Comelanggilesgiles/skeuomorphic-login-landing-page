# Nexa AI — Laravel Login

A login-only page (no landing content) built as a native Laravel feature:
Blade view + FormRequest validation + Auth controller, styled with the
palette `#425B9A` / `#76C0EC` / `#FFF6DC` / `#FF95A5`. No emojis —
all icons are inline SVG.

## Files

| File | Purpose |
| --- | --- |
| `resources/views/auth/login.blade.php` | The login page (Blade) |
| `resources/views/dashboard.blade.php` | Post-login redirect target + logout |
| `app/Http/Controllers/Auth/LoginController.php` | Show form, attempt login, logout |
| `app/Http/Requests/Auth/LoginRequest.php` | Server-side validation rules/messages |
| `routes/web.php` | Guest/auth routes with `throttle:5,1` on login POST |
| `public/css/login.css` | All styles (plain asset, no build step) |
| `public/js/login.js` | Vanilla JS: neural canvas, tilt, validation, toggles |

## Install into an existing Laravel app

1. Copy the files above into the matching directories of your app,
   merging `routes/web.php` with your existing routes.
2. Make sure the default `users` table/migration and auth config are in
   place (`php artisan migrate`), and create a test user, e.g. via
   `php artisan tinker` → `User::factory()->create()`.
3. Run `php artisan serve` and visit `/login`.

No Vite/npm build is needed — CSS and JS are served straight from
`public/` via the `asset()` helper.

## Behaviour

- Client-side validation (vanilla JS) mirrors the FormRequest messages;
  invalid submissions shake the offending field and never hit the server.
- Valid submissions show a short "Authenticating" state, then perform a
  real CSRF-protected POST. Failed credentials return with Laravel's
  generic `auth.failed` error rendered inline by Blade (`@error`,
  `old('email')`, `@checked(old('remember'))`).
- Successful login regenerates the session and redirects to the intended
  page or `/dashboard`. "Remember me" uses Laravel's recaller cookie
  (`Auth::attempt($credentials, $remember)`).
- Login POST is rate-limited to 5 attempts/minute (`throttle:5,1`).

## Optional

- Social buttons link to `/auth/github/redirect` and
  `/auth/google/redirect` — sample Socialite routes are included,
  commented out, at the bottom of `routes/web.php`.
- "Forgot password?" and "Create an account" point to `/forgot-password`
  and `/register` — wire them to your password-reset/registration flow
  (e.g. Laravel Breeze/Fortify) or your own controllers.

Note: `index.html` in this workspace is only a static visual preview of
what the Blade view renders (PHP can't run here) — the Laravel files
above are the actual deliverable.
