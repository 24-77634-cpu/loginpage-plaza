<?php
require "guard.php";   // login + timeout check
$username = $_SESSION["username"];
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
      <div class="avatar"><?= htmlspecialchars(substr($username, 0, 1)) ?></div>
      <div>
        <h1>Welcome, <?= htmlspecialchars($username) ?></h1>
        <p>You are logged in. Your details are kept in the session.</p>
      </div>
    </section>

    <section class="stats">
      <div class="stat"><span>Username</span><strong><?= htmlspecialchars($username) ?></strong></div>
      <div class="stat"><span>Logged in at</span><strong><?= htmlspecialchars($_SESSION["login_time"]) ?></strong></div>
      <div class="stat"><span>Status</span><strong>Active</strong></div>
    </section>
  </main>
  <script src="app.js"></script>
</body>
</html>
