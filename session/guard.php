<?php
// guard.php - include this at the top of every page that needs a login.
session_start();

// How many seconds of inactivity before the session expires (300 = 5 minutes).
// Set it to 30 if you want to demo the timeout quickly.
$timeout = 300;

// Tell the browser NOT to keep a copy of the page, so the Back button
// can never show a protected page after you log out or time out.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// 1. Not logged in -> go to the login page.
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

// 2. Logged in, but idle for too long -> session timeout.
if (time() - ($_SESSION["last_activity"] ?? time()) > $timeout) {
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION["error"] = "Your session expired. Please log in again.";
    header("Location: index.php");
    exit;
}

// 3. Still active -> restart the idle timer.
$_SESSION["last_activity"] = time();
