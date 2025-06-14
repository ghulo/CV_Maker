<?php
// login.php - User login page for CV Maker
// This file handles user authentication and displays the login form.
session_start(); // Start the session

// Redirect if already logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: view_cvs.php'); // Redirect to CV list page
    exit();
}

// Include database connection (assuming connect.php handles this)
// include 'connect.php'; // Uncomment if connect.php is needed here for some reason, usually not for just displaying form
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyçu - CV Maker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta name="description" content="Kyçuni në llogarinë tuaj në CV Maker për të menaxhuar dhe krijuar CV-të tuaja profesionale.">
    <meta name="keywords" content="kyçu, llogari, CV Maker, menaxho CV, hyrje">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="main">
        <div class="login-form-container animate-in">
            <h2>Kyçu në Llogarinë Tënde</h2>
            <p class="form-intro-text">
                Mirë se vini përsëri! Kyçuni për të vazhduar me krijimin ose menaxhimin e CV-ve tuaja.
                Nëse nuk keni një llogari, mund të regjistroheni shpejt dhe lehtë.
            </p>

            <div id="login-message-area" class="message-area" style="display:none;">
                <?php
                // Display messages from URL parameters (e.g., after signup success)
                if (isset($_GET['success'])) {
                    echo '<p class="message success" id="autoHidePageMessage">' . htmlspecialchars($_GET['success']) . '</p>';
                }
                if (isset($_GET['error'])) {
                    echo '<p class="message error" id="autoHidePageMessage">' . htmlspecialchars($_GET['error']) . '</p>';
                }
                ?>
            </div>

            <form class="login-form" action="process_login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="email@shembull.com" required autocomplete="email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Fjalëkalimi</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="********" required autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-primary-form">
                    Kyçu <i class="fas fa-sign-in-alt icon-right"></i>
                </button>
            </form>

            <div class="form-alternate-action">
                Nuk keni një llogari? <a href="signup.php">Regjistrohu Këtu</a>
            </div>
        </div>
    </main>

    <div class="background-cvs-container">
        <canvas id="particle-canvas"></canvas>
    </div>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
    <script>
        // Any page-specific JavaScript can go here or in script.js
    </script>
</body>
</html>
