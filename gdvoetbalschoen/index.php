<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gouden Schoen - Club voor iedereen</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="images/fc_team_zonder_plan.png" alt="FC Team zonder plan logo">
                
            </div>
            <nav>
                <a href="#" class="nav-icon home-icon" title="Home">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </a>
                <a href="#" class="nav-icon calendar-icon" title="Kalender">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                    </svg>
                </a>
                <a href="#" class="nav-icon profile-icon" title="Profiel" id="profileBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-image">
                        <div class="circle-placeholder"></div>
                    </div>
                    <div class="hero-text">
                        <p class="subtitle">I AM</p>
                        <h1>Gouden schoen</h1>
                        <h2>Club voor iedereen</h2>
                        <p class="description">
                            Proin quis cras euismod sit et metus risus ut. Semper nam vel morbi sit 
                            cursus tincidunt massa et a. Dolor odio parturient cursus justo nunc enim, 
                            a, sit facilisi. Eleifend et ac lacus, ullamcorper mauris eget tortor mollis.
                        </p>
                        <a href="#" class="cta-button">Meld je aan</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="awards">
            <div class="container">
                <div class="awards-grid">
                    <div class="award-badge">
                        <img src="images/ultraclear.png" alt="Ultra Clean Award">
                    </div>
                    <div class="award-badge">
                        <img src="images/megastandard.png" alt="International Standard Award">
                    </div>
                    <div class="award-badge">
                        <img src="images/hyperbest.png" alt="Hyper Best Award">
                    </div>
                    <div class="award-badge">
                        <img src="images/ultimatewinner.png" alt="Award Badge">
                    </div>
                    <div class="award-badge">
                        <img src="images/ultrapres.png" alt="Ultra Performance Winner">
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
