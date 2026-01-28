<?php
session_start();
$userInitial = '';
$isAdmin = false;
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    // Controleer of gebruiker admin is (role_id == 2)
    if (isset($user['role_id']) && $user['role_id'] == 2) {
        $isAdmin = true;
    }
    $userInitial = isset($user['first_name']) ? strtoupper(substr($user['first_name'], 0, 1)) : (isset($user['voornaam']) ? strtoupper(substr($user['voornaam'], 0, 1)) : '');
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gouden Schoen - Club voor iedereen</title>
    <link rel="stylesheet" href="assets/css/home.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="assets/images/fc_team_zonder_plan.png" alt="FC Team zonder plan logo">
            </div>
            <nav>
                <a href="index.php" class="nav-icon home-icon" title="Home">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                    </svg>
                </a>
                <a href="views/agenda.php" class="nav-icon calendar-icon" title="Kalender">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z" />
                    </svg>
                </a>
                <?php if ($isAdmin): ?>
                    <a href="views/admin_dashboard.php" class="nav-icon admin-icon" title="Admin" style="color:#6b5b95;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                        </svg>
                    </a>
                <?php endif; ?>
                <?php if ($userInitial): ?>
                    <div class="nav-icon profile-icon" title="Profiel" style="cursor:pointer;">
                        <div class="profile-circle" id="profileBtn"><?php echo $userInitial; ?></div>
                    </div>
                <?php else: ?>
                    <a href="views/login.php" class="nav-icon profile-icon" title="Profiel" id="profileBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-image">
                        <div class="circle-placeholder" style="background-image: url('https://images.pexels.com/photos/274506/pexels-photo-274506.jpeg?auto=compress&cs=tinysrgb&w=500&h=500&dpr=2'); background-size: cover; background-position: center;"></div>
                    </div>
                    <div class="hero-text">
                        <p class="subtitle">I AM</p>
                        <h1>Gouden schoen</h1>
                        <h2>Club voor iedereen</h2>
                        <p class="description">
                            De Gouden Schoen draait op vrijwilligers! Via dit platform schrijf je je eenvoudig in voor taken en help je onze club bruisend en sportief te houden. Samen maken we het verschil voor alle leden. Doe mee en draag bij aan het succes van onze vereniging!
                        </p>
                        <a href="views/register.php" class="cta-button">Meld je aan</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="awards">
            <div class="container">
                <div class="awards-grid">
                    <div class="award-badge">
                        <img src="assets/images/ultraclear.png" alt="Ultra Clean Award">
                    </div>
                    <div class="award-badge">
                        <img src="assets/images/megastandard.png" alt="International Standard Award">
                    </div>
                    <div class="award-badge">
                        <img src="assets/images/hyperbest.png" alt="Hyper Best Award">
                    </div>
                    <div class="award-badge">
                        <img src="assets/images/ultimatewinner.png" alt="Award Badge">
                    </div>
                    <div class="award-badge">
                        <img src="assets/images/ultrapres.png" alt="Ultra Performance Winner">
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Uitloggen Modal -->
    <div id="logoutModal" class="taak-modal">
        <div class="taak-modal-content logout-modal-content">
            <h2 class="taak-modal-title">Uitloggen</h2>
            <p class="modal-text">Weet je zeker dat je wilt uitloggen?</p>
            <div class="form-actions logout-actions">
                <button type="button" class="cancel-logout-btn">Annuleer</button>
                <button type="button" class="confirm-logout-btn">Ja</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Profile icon click for logout modal
        const profileIcon = document.getElementById('profileBtn');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogoutBtn = document.querySelector('.cancel-logout-btn');
        const confirmLogoutBtn = document.querySelector('.confirm-logout-btn');

        if (profileIcon) {
            profileIcon.addEventListener('click', function(e) {
                e.preventDefault();
                logoutModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }

        function closeLogoutModal() {
            logoutModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (cancelLogoutBtn) {
            cancelLogoutBtn.addEventListener('click', closeLogoutModal);
        }

        if (logoutModal) {
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    closeLogoutModal();
                }
            });
        }

        if (confirmLogoutBtn) {
            confirmLogoutBtn.addEventListener('click', function() {
                window.location.href = 'views/login.php';
            });
        }
    </script>
</body>

</html>