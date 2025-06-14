<?php
// cv_maker/delete_profile.php
session_start();

// --- Error Reporting ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Include the database connection file
require_once 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Ju lutemi kyçuni për të fshirë profilin tuaj.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = false;
$error_message = "";

// Handle POST request for deletion confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        // Start a transaction to ensure atomicity
        $pdo->beginTransaction();

        // Note: Due to 'ON DELETE CASCADE' constraints in your database schema,
        // deleting a user from the 'users' table will automatically delete
        // associated CVs, and then all data linked to those CVs (education, experience, skills, interests).
        // The explicit DELETE statements below are kept for clarity and as a safeguard,
        // but the core deletion depends on the 'users' table deletion.

        // 1. Delete user's CVs (assuming 'cvs' table exists and links to user_id)
        $stmt = $pdo->prepare("DELETE FROM cvs WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // --- Corrected Table Names based on provided schema ---
        // Removed 'personal_details' as it's not in the provided schema.
        // If personal details are in 'cvs' table, deleting cvs handles it.

        // 2. Delete work experiences (corrected from 'experience' to 'work_experiences')
        $stmt = $pdo->prepare("DELETE FROM work_experiences WHERE cv_id IN (SELECT id FROM cvs WHERE user_id = :user_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // 3. Delete education entries (corrected from 'education' to 'educations')
        $stmt = $pdo->prepare("DELETE FROM educations WHERE cv_id IN (SELECT id FROM cvs WHERE user_id = :user_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // 4. Delete skills entries (table name 'skills' is correct)
        $stmt = $pdo->prepare("DELETE FROM skills WHERE cv_id IN (SELECT id FROM cvs WHERE user_id = :user_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // 5. Delete interests entries (added as per schema, if not already handled by cascade from cvs)
        $stmt = $pdo->prepare("DELETE FROM interests WHERE cv_id IN (SELECT id FROM cvs WHERE user_id = :user_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // 6. Finally, delete the user from the users table
        // This is the most crucial step, and ON DELETE CASCADE should handle most linked data.
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id"); // Corrected to 'id'
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        $success = true;
        // Destroy the session after successful deletion
        session_unset();
        session_destroy();
        // Redirect to homepage (index.php) with a success message
        $_SESSION['info_message'] = "Profili juaj është fshirë me sukses dhe të gjitha të dhënat janë hequr.";
        header("Location: index.php");
        exit();

    } catch (PDOException $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        $error_message = "Gabim gjatë fshirjes së profilit: " . $e->getMessage();
        error_log("Delete Profile Error: " . $e->getMessage());
    }
}

// Define current page for header active link class
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fshi Profilin - CV Maker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Added CSS to center the buttons */
        .delete-form {
            display: flex;
            justify-content: center; /* Centers items horizontally */
            gap: 1rem; /* Adds space between buttons */
            flex-wrap: wrap; /* Allows buttons to wrap on smaller screens */
            margin-top: 1.5rem; /* Add some top margin for spacing */
        }

        .delete-button, .cancel-button {
            min-width: 150px; /* Ensure buttons have a reasonable minimum width */
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="background-cvs-container" aria-hidden="true"></div>

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
        <div class="profile-page-container page-container animate-in">
            <h2 class="reveal-on-scroll">Fshi Profilin</h2>
            <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Kjo veprim do të fshijë përgjithmonë llogarinë tuaj dhe të gjitha të dhënat e lidhura me të, duke përfshirë CV-të tuaja.</p>

            <div class="message-area">
                <?php if ($error_message): ?><p class="message error" role="alert"><?php echo htmlspecialchars($error_message); ?></p><?php endif; ?>
            </div>

            <div class="delete-profile-confirmation-box reveal-on-scroll" data-reveal-delay="200">
                <p>Jeni i sigurt që dëshironi të fshini profilin tuaj? Ky veprim nuk mund të anulohet.</p>
                <form action="delete_profile.php" method="POST" class="delete-form">
                    <button type="submit" name="confirm_delete" class="btn btn-danger delete-button">Po, Fshi Profilin Tim</button>
                    <a href="profile.php" class="btn btn-secondary cancel-button">Anulo</a>
                </form>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <div class="page-transition-overlay"></div>
    <script src="script.js"></script>
</body>
</html>