<?php
session_start();
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm"] ?? "";

    if (strlen($username) < 3 || strlen($username) > 30 || !preg_match('/^[A-Za-z0-9_.-]+$/', $username)) {
        $error = "Username must be 3-30 characters: letters, numbers, dot, dash or underscore.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $error = "That username is taken. Try another one.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $ins->bind_param("ss", $username, $hash);
            $ins->execute();

            $_SESSION["success"] = "Account created. You can log in now.";
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create account</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth">
  <div class="card">
    <div class="badge"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></div>
    <h1>Create account</h1>
    <p class="sub">It only takes a few seconds</p>

    <?php if ($error): ?><div class="msg error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST" action="register.php">
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>
      </div>
      <div class="field">
        <label for="password">Password (6+ characters)</label>
        <div class="pw">
          <input type="password" id="password" name="password" required>
          <button type="button" class="toggle">Show</button>
        </div>
      </div>
      <div class="field">
        <label for="confirm">Confirm password</label>
        <div class="pw">
          <input type="password" id="confirm" name="confirm" required>
          <button type="button" class="toggle">Show</button>
        </div>
      </div>
      <button type="submit" class="btn">Create account</button>
    </form>

    <p class="link">Already registered? <a href="index.php">Log in</a></p>
  </div>
  <script src="app.js"></script>
</body>
</html>
