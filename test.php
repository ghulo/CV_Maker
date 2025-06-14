<?php
// cv_maker/test.php
// A simple script to test if PHP is working and display server information.
echo "<h1>PHP is Working!</h1>";
echo "<p>If you see this, your server is processing PHP.</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<h2>PDO Drivers:</h2><pre>";
// List available PDO drivers
print_r(PDO::getAvailableDrivers());
echo "</pre>";
// Display extensive PHP configuration information (useful for debugging)
phpinfo();
?>
