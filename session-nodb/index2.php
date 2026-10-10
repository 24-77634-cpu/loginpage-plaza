<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Coming back to the login page while logged in = automatic logout.
$notice = "";
if (isset($_SESSION["username"])) {
    $_SESSION = [];
    session_regenerate_id(true);
    $notice = "You were logged out because you went back to the login page.";
}

// No database: users are saved in users.json (username => hashed password).
$users = json_decode(file_get_contents(__DIR__ . "/users.json"), true) ?: [];

$error = $_SESSION["error"] ?? "";
$success = $_SESSION["success"] ?? "";
unset($_SESSION["error"], $_SESSION["success"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = strtolower(trim($_POST["username"] ?? ""));
    $password = $_POST["password"] ?? "";

    if (isset($users[$username]) && password_verify($password, $users[$username])) {
        session_regenerate_id(true);
        $_SESSION["username"] = $username;
        $_SESSION["last_activity"] = time();
        $_SESSION["login_time"] = date("M d, Y h:i A");
        header("Location: home2.php");
        exit;
    }
    $error = "Wrong username or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth alt">
  <div class="card">
    <div class="badge"><svg viewBox="0 0 24 24"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg></div>
    <h1>Sign in</h1>
    <p class="sub">Use your username and password</p>

    <?php if ($error): ?><div class="msg error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="msg ok"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($notice): ?><div class="msg info"><?= htmlspecialchars($notice) ?></div><?php endif; ?>

    <form method="POST" action="index2.php">
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <div class="pw">
          <input type="password" id="password" name="password" required>
          <button type="button" class="toggle">Show</button>
        </div>
      </div>
      <button type="submit" class="btn">Sign in</button>
    </form>

    <p class="hint">Demo: admin / admin123 or user / user123</p>
  </div>
  <script src="app.js"></script>
</body>
</html>
