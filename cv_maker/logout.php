<?php
// cv_maker/logout.php
session_start(); // Resume the existing session

// Unset all of the session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Redirect to the homepage or login page after logout
// You can choose where you want users to go after logging out.
// Homepage is a common choice, often with a message.
header("Location: homepage.php?message=" . urlencode("Ju keni dalë me sukses."));
exit();
?>
