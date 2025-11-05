<?php
// Simple logout script for UTME users
session_start();

// clear session data
$_SESSION = [];

// destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// destroy session
session_unset();
session_destroy();

// redirect back to eligibility checker / login page
header('Location: index.php');
exit();
?>