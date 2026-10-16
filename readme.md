# Nexa AI Skeuomorphic Login

A tactile, skeuomorphic sign-in page for an AI workspace, with a static preview and a Laravel-ready version.

## Description

Nexa AI Skeuomorphic Login is a login-only page with raised buttons, soft depth, an animated neural-network background, and a card that tilts toward the cursor. It ships in two forms: a single `index.html` you can open in any browser to preview the design, and a set of Laravel files (Blade view, controller, FormRequest, routes) that turn the same page into a working, CSRF-protected sign-in flow.

## Key Features

- Skeuomorphic UI: pressable 3D buttons, layered shadows, and a four-color palette (`#425B9A`, `#76C0EC`, `#FFF6DC`, `#FF95A5`)
- Animated neural-network canvas background that reacts to the cursor
- Card tilt effect on devices with a fine pointer
- Client-side validation with inline error messages and a shake animation on invalid fields
- Show/hide password toggle and a "Remember me" checkbox
- Loading state ("Authenticating") on submit
- Respects `prefers-reduced-motion` (static background, no animation)
- Accessible markup: labels, focus rings, screen-reader status messages
- Laravel integration: FormRequest validation, session regeneration, rate limiting (5 attempts per minute), logout route
- Inline SVG icons only, no icon libraries, no build step

## Screenshot

![Nexa AI login page screenshot](/pic/Screenshot_15-10-2026_233857_.jpeg)



## Quick Start (Static Preview, about 1 minute)

You only need a modern web browser.

1. Download or clone the project:
   ```bash
   git clone https://github.com/Comelanggilesgiles/skeuomorphic-login-landing-page.git
   cd skeuomorphic-login-landing-page
   ```
2. Open `index.html` in your browser (double-click it, or run `start index.html` on Windows / `open index.html` on macOS).
3. Try it: submit an empty form to see validation, then enter any valid-looking email and a password to see the loading and success states.

The static preview does not contact a server. It only demonstrates the interface.

## Install in a Laravel App (about 5 minutes)

Requirements: PHP 8.1 or newer, Composer, and an existing Laravel project with the default `users` table.

1. Copy these files into the matching folders of your Laravel app:

   | File | Purpose |
   | --- | --- |
   | `resources/views/auth/login.blade.php` | Login page |
   | `resources/views/dashboard.blade.php` | Post-login page with logout |
   | `app/Http/Controllers/Auth/LoginController.php` | Show form, sign in, sign out |
   | `app/Http/Requests/Auth/LoginRequest.php` | Server-side validation |
   | `public/css/login.css` | Styles |
   | `public/js/login.js` | Interactions |

2. Merge `routes/web.php` into your app's `routes/web.php`.
3. Set up the database and create a test user:
   ```bash
   php artisan migrate
   php artisan tinker
   ```
   Then inside tinker:
   ```php
   User::factory()->create(['email' => 'test@example.com', 'password' => bcrypt('password')]);
   ```
4. Start the server:
   ```bash
   php artisan serve
   ```
5. Visit `http://127.0.0.1:8000/login` and sign in with the test user.

More detail is in [README-LARAVEL.md](README-LARAVEL.md).

## Optional Setup

- Social buttons point to `/auth/github/redirect` and `/auth/google/redirect`. Sample Laravel Socialite routes are included, commented out, at the bottom of `routes/web.php`.
- "Forgot password?" and "Create an account" link to `/forgot-password` and `/register`. Connect them to your own flow, for example Laravel Breeze or Fortify.

## Project Structure

```
.
├── index.html                          Static preview of the page
├── app/Http/Controllers/Auth/          LoginController
├── app/Http/Requests/Auth/             LoginRequest
├── public/css/login.css                Styles
├── public/js/login.js                  Canvas, tilt, validation, toggles
├── resources/views/auth/login.blade.php
├── resources/views/dashboard.blade.php
├── routes/web.php                      Guest and auth routes
└── docs/screenshot.png                 Screenshot (add your own)
```

## Tech Stack

- HTML5
- CSS3 (custom properties, gradients, layered shadows)
- Vanilla JavaScript (Canvas API, no libraries)
- PHP and Laravel (Blade, FormRequest, Auth, throttle middleware)
- Google Fonts: Space Grotesk, Inter, JetBrains Mono

## Browser Support

Current versions of Chrome, Edge, Firefox, and Safari.

## Contributing

Issues and pull requests are welcome. Fork the repository, create a branch, and open a pull request describing your change.

## Author

Created by [Comelanggilesgiles](https://github.com/Comelanggilesgiles).