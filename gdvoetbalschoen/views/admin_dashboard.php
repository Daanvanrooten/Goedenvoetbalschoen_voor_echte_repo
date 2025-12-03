<?php
session_start();

// Check if user is logged in and is admin
// This is a placeholder - adjust according to your authentication system
$isAdmin = true; // Set this based on your session/auth logic

if (!$isAdmin) {
    header('Location: login.php');
    exit();
}

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
    <div class="container">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <div class="logo">
                    <img src="../images/football_dashboard.png" alt="">
                </div>
                <h1 class="dashboard-title">Admin Dashboard</h1>
            </div>
            <div class="header-right">
                <span class="welcome-text">Welkom, admin</span>
                <button class="logout-btn">Uitloggen</button>
            </div>
            <button class="mobile-menu-toggle" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </header>

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
                    <h2 class="stat-label">Afspraken</h2>
                    <p class="stat-value"><?php echo $aantalAfspraken; ?></p>
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
                
                <a href="#" class="action-card">
                    <h3 class="action-title">Taken deze week</h3>
                    <div class="action-icon">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="5" width="18" height="16" rx="1.5" stroke="#2c2c2c" stroke-width="1.5"/>
                            <path d="M3 9H21" stroke="#2c2c2c" stroke-width="1.5"/>
                            <path d="M7 3V7M17 3V7" stroke="#2c2c2c" stroke-width="1.5" stroke-linecap="round"/>
                            <rect x="7" y="12" width="2" height="2" rx="0.5" fill="#2c2c2c"/>
                            <rect x="11" y="12" width="2" height="2" rx="0.5" fill="#2c2c2c"/>
                            <rect x="15" y="12" width="2" height="2" rx="0.5" fill="#2c2c2c"/>
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

    <script>
        // Mobile menu toggle
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        // Logout buttons
        document.querySelectorAll('.logout-btn, .logout-btn-mobile').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Weet je zeker dat je wilt uitloggen?')) {
                    // Add your logout logic here
                    window.location.href = 'login.php';
                }
            });
        });
    </script>
</body>
</html>
