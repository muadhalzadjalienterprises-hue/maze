<?php
session_start();

// If already logged in, go to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin-dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Fixed credentials
    if ($user === 'mazeadm' && $pass === 'Ff1988@Ff1988') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user;
        header('Location: admin-dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login | Muadh Al Zadjali</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
      background: #0b3c5d;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .card {
      background: rgba(0,0,0,0.6);
      padding: 24px 22px;
      border-radius: 10px;
      width: 100%;
      max-width: 380px;
      box-shadow: 0 3px 12px rgba(0,0,0,0.4);
    }
    h1 { font-size: 20px; margin-bottom: 14px; }
    label { font-size: 13px; display: block; margin-bottom: 4px; }
    input {
      width: 100%;
      padding: 8px 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-bottom: 10px;
      font-size: 14px;
    }
    button {
      width: 100%;
      padding: 9px 12px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      background: #ffd54f;
      color: #163b73;
      font-size: 14px;
      font-weight: 600;
    }
    .error {
      color: #ff8a80;
      font-size: 13px;
      margin-bottom: 10px;
    }
    .subtitle {
      font-size: 12px;
      color: #ddd;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>Admin Login</h1>
    <div class="subtitle">Enter your admin credentials to manage services.</div>
    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>

      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
