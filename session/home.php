<?php
require "guard.php";   // login + timeout check
require "db.php";

$stmt = $conn->prepare("SELECT username, created_at, last_login, login_count FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Account was deleted while the session was still open.
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
  <title>Home</title>
  <link rel="stylesheet" href="style.css">
</head>
<body data-timeout="<?= $timeout ?>">
  <header class="topbar">
    <span class="brand">MyApp</span>
    <nav>
      <a class="active" href="home.php">Home</a>
      <a href="home2.php">Account settings</a>
    </nav>
    <a class="btn logout" href="logout.php">Log out</a>
  </header>

  <main class="content">
    <section class="hero">
      <div class="avatar"><?= htmlspecialchars(substr($user["username"], 0, 1)) ?></div>
      <div>
        <h1>Welcome, <?= htmlspecialchars($user["username"]) ?></h1>
        <p>You are logged in. Your details are loaded from the database.</p>
      </div>
    </section>

    <section class="stats">
      <div class="stat"><span>Member since</span><strong><?= htmlspecialchars(date("M d, Y", strtotime($user["created_at"]))) ?></strong></div>
      <div class="stat"><span>Last login</span><strong><?= $user["last_login"] ? htmlspecialchars(date("M d, Y h:i A", strtotime($user["last_login"]))) : "First login" ?></strong></div>
      <div class="stat"><span>Total logins</span><strong><?= (int)$user["login_count"] ?></strong></div>
    </section>
  </main>
  <script src="app.js"></script>
</body>
</html>
