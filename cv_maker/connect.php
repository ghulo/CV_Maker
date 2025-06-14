<?php // cv_maker/connect.php
// FIXME: Change $username and $password to secure credentials for any non-local environment.
// FIXME: Use a dedicated database user with limited privileges.
$servername = "localhost";
$username = "labi"; // Replace with your actual database username
$password = "labi1234_"; // Replace with your actual database password
$dbname = "cv_maker";

try {
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Fetch results as associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                // Use native prepared statements
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Log the error for server admin, do not expose details to user from this low-level script.
    error_log("CRITICAL Database Connection Failed in connect.php: " . $e->getMessage(), 0);
    // Re-throw the exception to be caught by the calling script,
    // which can then decide how to handle it (e.g., JSON error, HTML error page).
    throw $e;
}
