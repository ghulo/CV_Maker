<?php
session_start();
// If user is already logged in, redirect them away from login page
if (isset($_SESSION['user_id'])) {
    header("Location: view_cvs.php"); // Or homepage.php
    exit();
}
$header_subtitle = "Kyçu në Llogari";

// Message handling from URL parameters (e.g., after successful signup)
$success_message_display = null;
$error_message_display = null;

if (isset($_GET['success'])) {
    $success_message_display = htmlspecialchars(urldecode($_GET['success']));
}
if (isset($_GET['error'])) { // From profile page if not logged in
    $error_message_display = htmlspecialchars(urldecode($_GET['error']));
}
if (isset($_SESSION['info_message'])) { // From other pages requiring login
    $error_message_display = htmlspecialchars($_SESSION['info_message']); // Display as error/info
    unset($_SESSION['info_message']);
}

// Define current page for header active link class
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kyçu - CV Maker</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

</head>
<body>
    <div class="background-cvs-container" aria-hidden="true">
        </div>

    <header class="header">
        <div class="header-content-wrapper">
            <div class="logo-box">
                <a href="index.php" class="logo-link">
                    <span class="gemini-icon">CV</span>
                    <div class="logo-text">
                        <h1>CV Maker</h1>
                        <p class="header-subtitle">Build your future</p>
                    </div>
                </a>
            </div>

            <button id="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>

            <nav class="header-nav" id="desktop-nav-menu-id">
                <a href="index.php" class="<?php echo ($current_page == 'index.php' || $current_page == 'homepage.php') ? 'active' : ''; ?>">Home</a>
                <a href="choose_template.php" class="<?php echo $current_page == 'choose_template.php' ? 'active' : ''; ?>">Templates</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="view_cvs.php" class="<?php echo $current_page == 'view_cvs.php' ? 'active' : ''; ?>">My CVs</a>
                    <a href="form.php" class="<?php echo $current_page == 'form.php' ? 'active' : ''; ?>">Create CV</a>
                <?php endif; ?>
            </nav>

            <div class="header-actions-group">
                 <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="profile-icon-btn" aria-label="View Profile">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="logout.php" class="auth-link logout-link btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="auth-link login-link <?php echo $current_page == 'login.php' ? 'active' : ''; ?>">Login</a>
                    <a href="signup.php" class="auth-link signup-link btn <?php echo $current_page == 'signup.php' ? 'active' : ''; ?>">Sign Up</a>
                <?php endif; ?>
            </div>
                      <div class="header-actions-group">
                <button id="theme-toggle-button" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
            </div>
        </div>
    </header>

    <nav class="mobile-nav-menu" id="mobile-nav-menu-id">
        <a href="index.php" class="<?php echo ($current_page == 'index.php' || $current_page == 'homepage.php') ? 'active' : ''; ?>">Home</a>
        <a href="choose_template.php" class="<?php echo $current_page == 'choose_template.php' ? 'active' : ''; ?>">Templates</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="view_cvs.php" class="<?php echo $current_page == 'view_cvs.php' ? 'active' : ''; ?>">My CVs</a>
            <a href="form.php" class="<?php echo $current_page == 'form.php' ? 'active' : ''; ?>">Create CV</a>
            <a href="profile.php" class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">Profile</a>
            <a href="logout.php" class="auth-link logout-link">Logout</a>
        <?php else: ?>
            <a href="login.php" class="auth-link login-link <?php echo $current_page == 'login.php' ? 'active' : ''; ?>">Login</a>
            <a href="signup.php" class="auth-link signup-link <?php echo $current_page == 'signup.php' ? 'active' : ''; ?>">Sign Up</a>
        <?php endif; ?>
    </nav>

  <main class="main">
    <div class="login-form-container page-container animate-in">
      <h2 class="reveal-on-scroll">Mirë se Vini Përsëri!</h2>
      <p class="form-intro-text reveal-on-scroll" data-reveal-delay="100">Kyçu në llogarinë tënde për të vazhduar menaxhimin e CV-ve tua.</p>

      <div class="message-area">
          <?php if ($success_message_display): ?>
              <p class="message success"><?php echo $success_message_display; ?></p>
          <?php endif; ?>
          <?php if ($error_message_display): ?>
              <p class="message error"><?php echo $error_message_display; ?></p>
          <?php endif; ?>
      </div>

      <form class="login-form" method="POST" action="process_login.php"> <div class="form-group reveal-on-scroll" data-reveal-delay="150">
          <label for="email">Email</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" id="email" required placeholder="Adresa juaj e emailit">
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="200">
          <label for="password">Fjalëkalimi</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="password" required placeholder="••••••••">
          </div>
        </div>
        <button type="submit" class="btn-primary-form btn btn-primary reveal-on-scroll" data-reveal-delay="250">Kyçu <i class="fas fa-sign-in-alt icon-right"></i></button>
      </form>
      <p class="form-alternate-action reveal-on-scroll" data-reveal-delay="300">Nuk keni llogari? <a href="signup.php">Regjistrohu këtu</a></p>
    </div>
  </main>
  <footer class="footer">
    <p>© <span id="current-year"><?php echo date("Y"); ?></span> CV Maker - Të gjitha të drejtat e rezervuara</p>
  </footer>
  <div class="page-transition-overlay"></div>
  <script src="script.js"></script>
</body>
</html>
