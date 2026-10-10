<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// End the session and start a clean one just to show a message.
$_SESSION = [];
session_regenerate_id(true);
$_SESSION["success"] = "You have been logged out.";

header("Location: index.php");
exit;
