<?php
session_start();

/* ===============================
   SAVE ROLE BEFORE DESTROY
================================ */
$role = $_SESSION['user_role'] ?? '';

/* ===============================
   CLEAR SESSION
================================ */
$_SESSION = [];
session_destroy();

/* ===============================
   DELETE REMEMBER ME COOKIE
================================ */
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, '/');
}

/* ===============================
   PREVENT BACK BUTTON ACCESS
================================ */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

/* ===============================
   ROLE BASED REDIRECT
================================ */
switch (strtolower($role)) {
    case 'admin':
    case 'hr':
        header("Location: ../login.php");
        break;

    case 'employee':
        header("Location: ../login.php");
        break;
    default:
        header("Location: ../login.php");
}
exit();
?>
