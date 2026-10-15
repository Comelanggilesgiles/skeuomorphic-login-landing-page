<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#FFF6DC">
<meta name="description" content="Sign in to Nexa AI — your artificial intelligence workspace.">
<title>Nexa AI — Sign in</title>

<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='24' fill='%23425B9A'/%3E%3Ccircle cx='50' cy='36' r='12' fill='%23FFF6DC'/%3E%3Ccircle cx='29' cy='68' r='9' fill='%2376C0EC'/%3E%3Ccircle cx='71' cy='68' r='9' fill='%23FF95A5'/%3E%3C/svg%3E">

<!-- Typography: Space Grotesk (display), Inter (UI), JetBrains Mono (status) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

  <!-- Animated neural-network background (vanilla JS canvas) -->
  <canvas id="neural" aria-hidden="true"></canvas>
  <div class="grid-overlay" aria-hidden="true"></div>
  <div class="ambient" aria-hidden="true">
    <span class="orb orb-sky"></span>
    <span class="orb orb-pink"></span>
    <span class="orb orb-blue"></span>
  </div>

  <!-- LOGIN ONLY — a single centered card, nothing else -->
  <main class="scene">
    <div class="card-tilt" id="cardTilt">
      <div class="card-frame">
        <section class="card" aria-labelledby="page-title">
          <div class="card-inner">

            <!-- Brand + live status -->
            <div class="card-head reveal" style="--d:.08s">
              <span class="brand">
                <span class="logo-mark" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.7" stroke-linecap="round">
                    <circle cx="12" cy="5" r="2.2" fill="#fff" stroke="none"/>
                    <circle cx="5" cy="18" r="2.2" fill="#fff" stroke="none"/>
                    <circle cx="19" cy="18" r="2.2" fill="#fff" stroke="none"/>
                    <circle cx="12" cy="12.5" r="1.6" fill="#fff" stroke="none"/>
                    <path d="M12 7.2v3.7M10.9 13.9 6.6 16.4M13.1 13.9l4.3 2.5M7 17.2h10"/>
                  </svg>
                </span>
                NEXA <em>AI</em>
              </span>
              <span class="status" role="status" aria-label="System online">
                <span class="led" aria-hidden="true">
                  <span class="led-halo"></span>
                  <span class="led-core"></span>
                </span>
                <span class="status-label" aria-hidden="true">Online</span>
                <span class="status-latency" id="latency" aria-hidden="true">12ms</span>
              </span>
            </div>

            <!-- Heading -->
            <header class="heading reveal" style="--d:.2s">
              <h1 id="page-title">Welcome <span class="grad">back</span></h1>
              <p>Sign in to your AI workspace to continue where you left off.</p>
            </header>

            {{-- Flash message (e.g. after password reset or logout) --}}
            @if (session('status'))
              <div class="flash reveal" style="--d:.3s" role="status">{{ session('status') }}</div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login.attempt') }}" novalidate>
              @csrf

              <!-- Email -->
              <div class="field reveal @error('email') has-error @enderror" style="--d:.32s" id="emailField">
                <label for="email">Email address</label>
                <div class="control">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>
                  </svg>
                  <input id="email" name="email" type="email" inputmode="email"
                         placeholder="you@example.com" autocomplete="email"
                         value="{{ old('email') }}"
                         @error('email') aria-invalid="true" @enderror
                         aria-describedby="emailError">
                </div>
                <p class="error" id="emailError" role="alert" @unless($errors->has('email')) hidden @endunless>{{ $errors->first('email') }}</p>
              </div>

              <!-- Password + show/hide toggle -->
              <div class="field reveal @error('password') has-error @enderror" style="--d:.44s" id="passwordField">
                <label for="password">Password</label>
                <div class="control has-toggle">
                  <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                  </svg>
                  <input id="password" name="password" type="password"
                         placeholder="Enter your password" autocomplete="current-password"
                         @error('password') aria-invalid="true" @enderror
                         aria-describedby="passwordError capsHint">
                  <button type="button" class="toggle-pw" id="togglePw"
                          aria-label="Show password" aria-pressed="false">
                    <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M9.9 4.24A9.5 9.5 0 0 1 12 4c6.5 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19M6.61 6.61A13.46 13.46 0 0 0 2 12s3.5 8 10 8a9.74 9.74 0 0 0 5.39-1.61"/>
                      <line x1="2" y1="2" x2="22" y2="22"/><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                    </svg>
                  </button>
                </div>
                <p class="error" id="passwordError" role="alert" @unless($errors->has('password')) hidden @endunless>{{ $errors->first('password') }}</p>
                <p class="hint" id="capsHint" hidden>Caps Lock is on</p>
              </div>

              <!-- Remember / Forgot -->
              <div class="row reveal" style="--d:.56s">
                <label class="remember" for="remember">
                  <input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))>
                  <span class="box" aria-hidden="true">
                    <svg viewBox="0 0 12 10" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M1 5.5 4.2 8.5 11 1.5"/>
                    </svg>
                  </span>
                  <span>Remember me</span>
                </label>
                <a class="forgot" href="{{ url('/forgot-password') }}">Forgot password?</a>
              </div>

              <!-- Primary CTA -->
              <div class="reveal" style="--d:.66s">
                <button type="submit" class="btn-primary" id="signInBtn">
                  <span class="btn-label">
                    <span class="spinner" aria-hidden="true"></span>
                    <svg class="check" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M20 6 9 17l-5-5"/>
                    </svg>
                    <span id="signInText">Sign In</span>
                    <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M5 12h14M13 6l6 6-6 6"/>
                    </svg>
                  </span>
                </button>
              </div>

              <p class="sr-only" role="status" id="formStatus"></p>
            </form>

            <!-- Divider -->
            <div class="divider reveal" style="--d:.76s" aria-hidden="true">
              <span>Or continue with</span>
            </div>

            <!-- Social sign-in (Laravel Socialite endpoints) -->
            <div class="social reveal" style="--d:.84s">
              <a class="btn-social" href="{{ url('/auth/github/redirect') }}" aria-label="Continue with GitHub">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.91.58.11.79-.25.79-.55 0-.27-.01-1.17-.02-2.12-3.2.7-3.87-1.36-3.87-1.36-.52-1.33-1.28-1.68-1.28-1.68-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.19 1.76 1.19 1.03 1.76 2.69 1.25 1.88-.73.4-1.25 1.28-1.54-2.55-.29-5.23-1.28-5.23-5.68 0-1.26.45-2.28 1.19-3.09-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.17 1.18a11.1 11.1 0 0 1 5.78 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.83 1.19 3.09 0 4.41-2.69 5.38-5.25 1.28.44.38.83 1.1.83 2.22 0 1.6-.01 2.9-.01 3.29 0 .3.21.67.8.55A11.51 11.51 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5Z"/>
                </svg>
                GitHub
              </a>
              <a class="btn-social" href="{{ url('/auth/google/redirect') }}" aria-label="Continue with Google">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path fill="#4285F4" d="M23.5 12.27c0-.85-.08-1.66-.22-2.45H12v4.64h6.45a5.52 5.52 0 0 1-2.39 3.58v2.97h3.87c2.26-2.09 3.57-5.16 3.57-8.74Z"/>
                  <path fill="#34A853" d="M12 24c3.24 0 5.96-1.07 7.94-2.91l-3.87-2.97c-1.07.72-2.45 1.15-4.07 1.15-3.13 0-5.78-2.11-6.73-4.96H1.29v3.07A12 12 0 0 0 12 24Z"/>
                  <path fill="#FBBC05" d="M5.27 14.31A7.2 7.2 0 0 1 4.89 12c0-.81.14-1.59.38-2.31V6.62H1.29a12 12 0 0 0 0 10.76l3.98-3.07Z"/>
                  <path fill="#EA4335" d="M12 4.73c1.76 0 3.35.61 4.6 1.8l3.44-3.44A11.98 11.98 0 0 0 12 0 12 12 0 0 0 1.29 6.62l3.98 3.07C6.22 6.84 8.87 4.73 12 4.73Z"/>
                </svg>
                Google
              </a>
            </div>

            <!-- Footer -->
            <p class="card-footer reveal" style="--d:.92s">
              New here? <a href="{{ url('/register') }}">Create an account</a>
            </p>

          </div>
        </section>
      </div>
    </div>
  </main>

<script src="{{ asset('js/login.js') }}" defer></script>
</body>
</html>
