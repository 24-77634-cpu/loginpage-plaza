<?php
require "guard.php";   // login + timeout check
require "db.php";

$id = (int)$_SESSION["user_id"];

// Token that protects the forms from being submitted by other websites.
if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(16));
}

function flash($type, $text) {
    $_SESSION["flash"] = ["type" => $type, "text" => $text];
    header("Location: home2.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION["csrf"], $_POST["csrf"] ?? "")) {
        flash("error", "Your session expired. Please try again.");
    }

    $action = $_POST["action"] ?? "";

    // ----- Change username -----
    if ($action === "username") {
        $new = trim($_POST["username"] ?? "");

        if (strlen($new) < 3 || strlen($new) > 30 || !preg_match('/^[A-Za-z0-9_.-]+$/', $new)) {
            flash("error", "Username must be 3-30 characters: letters, numbers, dot, dash or underscore.");
        }

        $check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id <> ?");
        $check->bind_param("si", $new, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            flash("error", "That username is already taken.");
        }

        $up = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $up->bind_param("si", $new, $id);
        $up->execute();
        flash("ok", "Username updated.");
    }

    // ----- Change password -----
    if ($action === "password") {
        $current = $_POST["current"] ?? "";
        $new     = $_POST["new"] ?? "";
        $confirm = $_POST["confirm"] ?? "";

        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row || !password_verify($current, $row["password"])) {
            flash("error", "Your current password is wrong.");
        }
        if (strlen($new) < 6) {
            flash("error", "New password must be at least 6 characters.");
        }
        if ($new !== $confirm) {
            flash("error", "New passwords do not match.");
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $up = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $up->bind_param("si", $hash, $id);
        $up->execute();
        flash("ok", "Password updated.");
    }
}

$flash = $_SESSION["flash"] ?? null;
unset($_SESSION["flash"]);

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Account settings</title>
  <link rel="stylesheet" href="style.css">
</head>
<body data-timeout="<?= $timeout ?>">
  <header class="topbar">
    <span class="brand">MyApp</span>
    <nav>
      <a href="home.php">Home</a>
      <a class="active" href="home2.php">Account settings</a>
    </nav>
    <a class="btn logout" href="logout.php">Log out</a>
  </header>

  <main class="content">
    <h1 class="page-title">Account settings</h1>
    <p class="page-sub">Change your username or password.</p>

    <?php if ($flash): ?>
      <div class="msg <?= $flash["type"] === "ok" ? "ok" : "error" ?>"><?= htmlspecialchars($flash["text"]) ?></div>
    <?php endif; ?>

    <section class="panel">
      <h2>Username</h2>
      <p class="desc">This is the name you use to log in.</p>
      <form method="POST" action="home2.php">
        <input type="hidden" name="csrf" value="<?= $_SESSION["csrf"] ?>">
        <input type="hidden" name="action" value="username">
        <div class="field">
          <label for="username">New username</label>
          <input type="text" id="username" name="username" value="<?= htmlspecialchars($user["username"]) ?>" required>
        </div>
        <button type="submit" class="btn">Save username</button>
      </form>
    </section>

    <section class="panel">
      <h2>Password</h2>
      <p class="desc">Enter your current password, then choose a new one.</p>
      <form method="POST" action="home2.php">
        <input type="hidden" name="csrf" value="<?= $_SESSION["csrf"] ?>">
        <input type="hidden" name="action" value="password">
        <div class="field">
          <label for="current">Current password</label>
          <div class="pw">
            <input type="password" id="current" name="current" required>
            <button type="button" class="toggle">Show</button>
          </div>
        </div>
        <div class="field">
          <label for="new">New password (6+ characters)</label>
          <div class="pw">
            <input type="password" id="new" name="new" required>
            <button type="button" class="toggle">Show</button>
          </div>
        </div>
        <div class="field">
          <label for="confirm">Confirm new password</label>
          <div class="pw">
            <input type="password" id="confirm" name="confirm" required>
            <button type="button" class="toggle">Show</button>
          </div>
        </div>
        <button type="submit" class="btn">Save password</button>
      </form>
    </section>
  </main>
  <script src="app.js"></script>
</body>
</html>
