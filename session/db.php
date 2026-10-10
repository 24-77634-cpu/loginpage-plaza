<?php
// Database connection. XAMPP defaults: user "root", empty password.
$host = "localhost";
$user = "root";
$pass = "";
$name = "login_system";

$conn = mysqli_connect($host, $user, $pass, $name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error()
        . "<br>Did you start MySQL in XAMPP and import login_system.sql?");
}
