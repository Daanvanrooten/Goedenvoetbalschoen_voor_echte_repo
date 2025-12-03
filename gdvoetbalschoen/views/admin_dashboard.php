<?php
session_start();

// Check if user is logged in and is admin
// This is a placeholder - adjust according to your authentication system
$isAdmin = true; // Set this based on your session/auth logic

if (!$isAdmin) {
    header('Location: login.php');
    exit();
}

// Set user info
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id' => 1,
        'voornaam' => 'Admin',
        'achternaam' => 'User',
        'email' => 'admin@example.com'
    ];
}

$user = $_SESSION['user'];
$userInitial = strtoupper(substr($user['voornaam'], 0, 1));

// Sample data - replace with actual database queries
$aantalLeden = 100;
$aantalAfspraken = 100;
$aantalTakenOpen = 100;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="../images/fc_team_zonder_plan.png" alt="FC Team zonder plan logo">
            </div>
            <nav>
                <a href="../index.html" class="nav-icon home-icon" title="Home">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </a>
                <a href="agenda.php" class="nav-icon calendar-icon" title="Kalender">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                    </svg>
                </a>
                <a href="profiel.php" class="nav-icon profile-icon" title="Profiel">
                    <div class="profile-circle" id="profileBtn"><?php echo $userInitial; ?></div>
                </a>
            </nav>
        </div>
    </header>

    <div class="container">

        <!-- Mobile Menu -->
        <div class="mobile-menu">
            <button class="logout-btn-mobile">Uitloggen</button>
        </div>

        <!-- Main Content -->
        <main class="dashboard-content">
            <!-- Statistics Cards -->
            <section class="stats-section">
                <div class="stat-card">
                    <h2 class="stat-label">Leden</h2>
                    <p class="stat-value"><?php echo $aantalLeden; ?></p>
                </div>
                <div class="stat-card">
                    <h2 class="stat-label">Taken open</h2>
                    <p class="stat-value"><?php echo $aantalTakenOpen; ?></p>
                </div>
            </section>

            <!-- Action Buttons -->
            <section class="actions-section">
                <a href="#" class="action-card">
                    <h3 class="action-title">Taken toevoegen</h3>
                    <div class="action-icon">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5" y="3" width="14" height="18" rx="1" stroke="#2c2c2c" stroke-width="1.5"/>
                            <path d="M9 7H15M9 11H15M9 15H12" stroke="#2c2c2c" stroke-width="1.5" stroke-linecap="round"/>
                            <rect x="8" y="1" width="8" height="3" rx="0.5" fill="#2c2c2c"/>
                        </svg>
                    </div>
                </a>
                
                
                <a href="ledenbeheer.php" class="action-card">
                    <h3 class="action-title">Leden beheer</h3>
                    <div class="action-icon">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="8" r="3.5" stroke="#2c2c2c" stroke-width="1.5"/>
                            <path d="M5 20C5 16.6863 7.68629 14 11 14H13C16.3137 14 19 16.6863 19 20" stroke="#2c2c2c" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                </a>
            </section>
        </main>
    </div>

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

    <script>
        // Mobile menu toggle
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', () => {
                menuToggle.classList.toggle('active');
                mobileMenu.classList.toggle('active');
            });
        }

        // Profile circle click for logout modal
        const profileCircle = document.querySelector('.profile-circle');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogoutBtn = document.querySelector('.cancel-logout-btn');
        const confirmLogoutBtn = document.querySelector('.confirm-logout-btn');

        if (profileCircle) {
            profileCircle.addEventListener('click', function(e) {
                e.preventDefault();
                logoutModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }

        // Close logout modal
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
                window.location.href = 'login.php';
            });
        }

        // Logout buttons (mobile)
        document.querySelectorAll('.logout-btn-mobile').forEach(btn => {
            btn.addEventListener('click', () => {
                logoutModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });
    </script>
</body>
</html>
