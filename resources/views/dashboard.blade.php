<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Nexa AI</title>
<style>
  body{
    margin:0; min-height:100vh; display:grid; place-items:center;
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    color:#26355C; background:#FFF6DC;
  }
  main{ text-align:center; padding:24px; }
  h1{ color:#2C3E6B; margin:0 0 8px; }
  p{ margin:0 0 20px; color:rgba(38,53,92,.65); }
  button{
    font:inherit; font-weight:600; color:#fff; cursor:pointer;
    padding:11px 22px; border:1px solid #2C3E6B; border-radius:12px;
    background:linear-gradient(180deg,#5D78B8,#425B9A);
    box-shadow:0 4px 0 #2C3E6B, 0 10px 20px rgba(66,91,154,.3);
  }
  button:active{ transform:translateY(3px); box-shadow:0 1px 0 #2C3E6B; }
</style>
</head>
<body>
  <main>
    <h1>Welcome back, {{ auth()->user()->name ?? auth()->user()->email }}</h1>
    <p>You are signed in to your Nexa AI workspace.</p>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit">Log out</button>
    </form>
  </main>
</body>
</html>
