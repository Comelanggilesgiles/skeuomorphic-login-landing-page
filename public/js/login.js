/* ================================================================
   Nexa AI — login page behaviour (vanilla JS, no libraries).
   The form itself posts to Laravel (real CSRF-protected POST);
   this script only enhances UX: canvas, tilt, validation,
   password toggle, and submit progress state.
   ================================================================ */
(function () {
  "use strict";

  var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var finePointer   = window.matchMedia("(hover: hover) and (pointer: fine)").matches;

  /* ------------------------------------------------------------
     1. NEURAL NETWORK CANVAS
     Drifting nodes in blue / sky / pink; lines fade in between
     near neighbours and toward the cursor. One static frame
     is drawn for reduced-motion users.
     ------------------------------------------------------------ */
  (function neural() {
    var canvas = document.getElementById("neural");
    if (!canvas) return;
    var ctx = canvas.getContext("2d");
    var W = 0, H = 0, nodes = [], raf = null;
    var mouse = { x: -9999, y: -9999 };
    var DPR = Math.min(window.devicePixelRatio || 1, 2);
    var LINK = 130;
    var MOUSE_LINK = 170;

    function resize() {
      W = window.innerWidth; H = window.innerHeight;
      canvas.width = W * DPR; canvas.height = H * DPR;
      ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
      seed();
      if (reducedMotion) draw();
    }

    function seed() {
      var count = Math.min(90, Math.floor((W * H) / 20000));
      nodes = [];
      for (var i = 0; i < count; i++) {
        nodes.push({
          x: Math.random() * W,
          y: Math.random() * H,
          vx: (Math.random() - 0.5) * 0.35,
          vy: (Math.random() - 0.5) * 0.35,
          r: Math.random() * 1.6 + 0.7,
          tone: Math.random()  // picks blue / sky / pink per node
        });
      }
    }

    function draw() {
      ctx.clearRect(0, 0, W, H);
      var i, j, a, b, dx, dy, d;

      for (i = 0; i < nodes.length; i++) {
        a = nodes[i];
        for (j = i + 1; j < nodes.length; j++) {
          b = nodes[j];
          dx = a.x - b.x; dy = a.y - b.y;
          d = Math.sqrt(dx * dx + dy * dy);
          if (d < LINK) {
            ctx.strokeStyle = "rgba(118,192,236," + ((1 - d / LINK) * 0.35).toFixed(3) + ")";
            ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
          }
        }
        dx = a.x - mouse.x; dy = a.y - mouse.y;
        d = Math.sqrt(dx * dx + dy * dy);
        if (d < MOUSE_LINK) {
          ctx.strokeStyle = "rgba(66,91,154," + ((1 - d / MOUSE_LINK) * 0.4).toFixed(3) + ")";
          ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(mouse.x, mouse.y); ctx.stroke();
        }
      }

      for (i = 0; i < nodes.length; i++) {
        a = nodes[i];
        ctx.fillStyle = a.tone < 0.55 ? "rgba(66,91,154,.55)"
                      : a.tone < 0.85 ? "rgba(118,192,236,.8)"
                      : "rgba(255,149,165,.8)";
        ctx.beginPath(); ctx.arc(a.x, a.y, a.r, 0, Math.PI * 2); ctx.fill();
      }
    }

    function step() {
      for (var i = 0; i < nodes.length; i++) {
        var n = nodes[i];
        n.x += n.vx; n.y += n.vy;
        if (n.x < -20) n.x = W + 20; if (n.x > W + 20) n.x = -20;
        if (n.y < -20) n.y = H + 20; if (n.y > H + 20) n.y = -20;
      }
      draw();
      raf = window.requestAnimationFrame(step);
    }

    window.addEventListener("resize", resize);
    window.addEventListener("pointermove", function (e) { mouse.x = e.clientX; mouse.y = e.clientY; });
    window.addEventListener("pointerleave", function () { mouse.x = -9999; mouse.y = -9999; });
    document.addEventListener("visibilitychange", function () {
      if (reducedMotion) return;
      if (document.hidden) { if (raf) window.cancelAnimationFrame(raf); raf = null; }
      else if (!raf) raf = window.requestAnimationFrame(step);
    });

    resize();
    if (!reducedMotion) raf = window.requestAnimationFrame(step);
  })();

  /* ------------------------------------------------------------
     2. 3D TILT + CURSOR SPOTLIGHT (clamped to +/-6deg, RAF-lerped)
     ------------------------------------------------------------ */
  var scene  = document.querySelector(".scene");
  var tiltEl = document.getElementById("cardTilt");
  var card   = tiltEl ? tiltEl.querySelector(".card") : null;

  if (scene && tiltEl && card && finePointer && !reducedMotion) {
    var MAX_TILT = 6;
    var targetX = 0, targetY = 0, currX = 0, currY = 0, tiltRaf = null;

    function clamp(v) { return Math.max(-MAX_TILT, Math.min(MAX_TILT, v)); }

    function tick() {
      currX += (targetX - currX) * 0.085;
      currY += (targetY - currY) * 0.085;
      tiltEl.style.transform = "rotateX(" + currX.toFixed(3) + "deg) rotateY(" + currY.toFixed(3) + "deg)";
      var settled = Math.abs(targetX - currX) < 0.01 && Math.abs(targetY - currY) < 0.01;
      tiltRaf = settled ? null : window.requestAnimationFrame(tick);
    }
    function kick() { if (tiltRaf === null) tiltRaf = window.requestAnimationFrame(tick); }

    scene.addEventListener("pointermove", function (e) {
      var r = card.getBoundingClientRect();
      var px = (e.clientX - r.left) / r.width - 0.5;
      var py = (e.clientY - r.top) / r.height - 0.5;
      targetY = clamp(px * MAX_TILT * 2);
      targetX = clamp(-py * MAX_TILT * 2);
      card.style.setProperty("--mx", ((px + 0.5) * 100).toFixed(1) + "%");
      card.style.setProperty("--my", ((py + 0.5) * 100).toFixed(1) + "%");
      kick();
    });
    scene.addEventListener("pointerleave", function () {
      targetX = 0; targetY = 0;
      card.style.setProperty("--mx", "50%");
      card.style.setProperty("--my", "-10%");
      kick();
    });
  }

  /* ------------------------------------------------------------
     3. LIVE LATENCY TICKER (status pill)
     ------------------------------------------------------------ */
  var latencyEl = document.getElementById("latency");
  if (latencyEl) {
    window.setInterval(function () {
      latencyEl.textContent = (8 + Math.floor(Math.random() * 17)) + "ms";
    }, 2200);
  }

  /* ------------------------------------------------------------
     4. PASSWORD TOGGLE + CAPS LOCK HINT
     ------------------------------------------------------------ */
  var pwInput  = document.getElementById("password");
  var pwToggle = document.getElementById("togglePw");
  var capsHint = document.getElementById("capsHint");

  pwToggle.addEventListener("click", function () {
    var show = pwInput.type === "password";
    pwInput.type = show ? "text" : "password";
    pwToggle.setAttribute("aria-pressed", String(show));
    pwToggle.setAttribute("aria-label", show ? "Hide password" : "Show password");
    pwInput.focus();
  });

  pwInput.addEventListener("keydown", function (e) {
    if (e.getModifierState) capsHint.hidden = !e.getModifierState("CapsLock");
  });
  pwInput.addEventListener("keyup", function (e) {
    if (e.getModifierState) capsHint.hidden = !e.getModifierState("CapsLock");
  });
  pwInput.addEventListener("blur", function () { capsHint.hidden = true; });

  /* ------------------------------------------------------------
     5. VALIDATION + SUBMIT HAND-OFF
     Client-side checks mirror the Laravel FormRequest messages.
     - Invalid: submission is blocked, fields shake, focus moves.
     - Valid:   button shows a brief "Authenticating" state, then
                the form performs its real POST to Laravel, which
                verifies credentials and redirects to /dashboard.
     ------------------------------------------------------------ */
  var form        = document.getElementById("loginForm");
  var emailInput  = document.getElementById("email");
  var signInBtn   = document.getElementById("signInBtn");
  var signInText  = document.getElementById("signInText");
  var formStatus  = document.getElementById("formStatus");
  var EMAIL_RE    = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var submitting  = false;

  function setError(fieldId, input, errorId, message) {
    var field = document.getElementById(fieldId);
    var error = document.getElementById(errorId);
    if (message) {
      error.textContent = message;
      error.hidden = false;
      field.classList.add("has-error");
      input.setAttribute("aria-invalid", "true");
      field.classList.remove("shake");
      void field.offsetWidth; // reflow so the shake replays
      field.classList.add("shake");
    } else {
      error.textContent = "";
      error.hidden = true;
      field.classList.remove("has-error");
      input.removeAttribute("aria-invalid");
    }
  }

  function validateEmail() {
    var v = emailInput.value.trim();
    if (!v)                { setError("emailField", emailInput, "emailError", "Please enter your email address."); return false; }
    if (!EMAIL_RE.test(v)) { setError("emailField", emailInput, "emailError", "That does not look like a valid email address."); return false; }
    setError("emailField", emailInput, "emailError", "");
    return true;
  }

  function validatePassword() {
    var v = pwInput.value;
    if (!v)           { setError("passwordField", pwInput, "passwordError", "Please enter your password."); return false; }
    if (v.length < 8) { setError("passwordField", pwInput, "passwordError", "Password must be at least 8 characters."); return false; }
    setError("passwordField", pwInput, "passwordError", "");
    return true;
  }

  emailInput.addEventListener("input", function () { setError("emailField", emailInput, "emailError", ""); });
  pwInput.addEventListener("input",    function () { setError("passwordField", pwInput, "passwordError", ""); });
  emailInput.addEventListener("blur",  function () { if (emailInput.value) validateEmail(); });
  pwInput.addEventListener("blur",     function () { if (pwInput.value) validatePassword(); });

  /* If Laravel sent the user back with errors, Blade already added
     .has-error — shake those fields once and focus the first one. */
  var serverErrored = document.querySelectorAll(".field.has-error");
  if (serverErrored.length) {
    for (var i = 0; i < serverErrored.length; i++) {
      (function (field) {
        field.classList.remove("shake");
        void field.offsetWidth;
        field.classList.add("shake");
      })(serverErrored[i]);
    }
    var firstInvalid = serverErrored[0].querySelector("input");
    if (firstInvalid) firstInvalid.focus();
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (submitting) return;

    var emailOk = validateEmail();
    var pwOk = validatePassword();
    if (!emailOk || !pwOk) {
      formStatus.textContent = "Please fix the highlighted fields and try again.";
      (emailOk ? pwInput : emailInput).focus();
      return;
    }

    /* Brief progress state, then the real POST to Laravel. */
    submitting = true;
    signInBtn.disabled = true;
    signInBtn.classList.add("is-loading");
    signInText.textContent = "Authenticating";
    formStatus.textContent = "Authenticating your credentials.";

    window.setTimeout(function () {
      form.submit(); // native submit — bypasses this handler, sends @csrf token
    }, 650);
  });
})();
