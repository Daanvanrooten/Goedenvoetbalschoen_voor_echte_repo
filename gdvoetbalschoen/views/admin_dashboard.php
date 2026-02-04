<?php
session_start();



// Controleer of user admin is via role_id == 2

// Controleer of user admin is via role_id == 2 (volgens database)

$isAdmin = isset($_SESSION['user']['role_id']) && $_SESSION['user']['role_id'] == 2;
if (!$isAdmin) {
    header('Location: ../index.php');
    exit();
}

// Data wordt via AJAX opgehaald
$aantalLeden = 0;
$aantalAfspraken = 0;
$aantalTakenOpen = 0;


$user = $_SESSION['user'];
$userInitial = isset($user['first_name']) ? strtoupper(substr($user['first_name'], 0, 1)) : '';

// TODO: Haal echte data op uit de database
$aantalLeden = 0;
$aantalAfspraken = 0;
$aantalTakenOpen = 0;
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="../assets/images/fc_team_zonder_plan.png" alt="FC Team zonder plan logo">
            </div>
            <nav>
                <a href="../index.php" class="nav-icon home-icon" title="Home">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                    </svg>
                </a>
                <a href="agenda.php" class="nav-icon calendar-icon" title="Kalender">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z" />
                    </svg>
                </a>
                <a href="UserPage.php" style="
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        background: linear-gradient(135deg, #6b7adb 0%, #8b9bef 100%);
                        color: white;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 18px;
                        font-weight: 600;
                        box-shadow: 0 2px 8px rgba(107, 122, 219, 0.3);
                        cursor: pointer;
                        transition: all 0.3s;
                    ">
                    <?php echo $userInitial;?>
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
                    <p class="stat-value" id="ledenCount">...</p>
                </div>
                <div class="stat-card">
                    <h2 class="stat-label">Taken open</h2>
                    <p class="stat-value" id="takenCount">...</p>
                </div>
            </section>

            <!-- Ledenlijst -->


            <!-- Action Buttons -->
            <section class="actions-section">
                <a href="categoriebeheer.php" class="action-card">
                    <h3 class="action-title">Categorie toevoegen</h3>
                    <div class="action-icon">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5" y="3" width="14" height="18" rx="1" stroke="#2c2c2c" stroke-width="1.5" />
                            <path d="M9 7H15M9 11H15M9 15H12" stroke="#2c2c2c" stroke-width="1.5" stroke-linecap="round" />
                            <rect x="8" y="1" width="8" height="3" rx="0.5" fill="#2c2c2c" />
                        </svg>
                    </div>
                </a>
                <a href="ledenbeheer.php" class="action-card">
                    <h3 class="action-title">Leden beheer</h3>
                    <div class="action-icon">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="8" r="3.5" stroke="#2c2c2c" stroke-width="1.5" />
                            <path d="M5 20C5 16.6863 7.68629 14 11 14H13C16.3137 14 19 16.6863 19 20" stroke="#2c2c2c" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </div>
                </a>
                <a href="TaskManager.php" class="action-card">
                    <h3 class="action-title">Taken Bewerken</h3>
                    <div class="action-icon">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5" y="3" width="14" height="18" rx="1" stroke="#2c2c2c" stroke-width="1.5" />
                            <path d="M9 7H15M9 11H15M9 15H12" stroke="#2c2c2c" stroke-width="1.5" stroke-linecap="round" />
                            <rect x="8" y="1" width="8" height="3" rx="0.5" fill="#2c2c2c" />
                        </svg>
                    </div>
                </a>
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

        <!-- Ledenlijst -->
        <section class="leden-section">
            <h2>Alle leden</h2>
            <table class="leden-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naam</th>
                        <th>Email</th>
                        <th>Gebruikersnaam</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody id="ledenTableBody">
                    <tr>
                        <td colspan="5">Laden...</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Open taken -->
        <section class="taken-section">
            <h2>Open taken</h2>
            <table class="taken-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titel</th>
                        <th>Beschrijving</th>
                        <th>Startdatum</th>
                        <th>Einddatum</th>
                    </tr>
                </thead>
                <tbody id="takenTableBody">
                    <tr>
                        <td colspan="5">Laden...</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <script>
            // Haal leden en open taken op via AJAX
            document.addEventListener('DOMContentLoaded', function() {
                fetch('../api/users/admin_data.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Leden
                            document.getElementById('ledenCount').textContent = data.aantalLeden;
                            const ledenBody = document.getElementById('ledenTableBody');
                            ledenBody.innerHTML = '';
                            data.leden.forEach(lid => {
                                ledenBody.innerHTML += `<tr><td>${lid.user_id}</td><td>${lid.first_name} ${lid.last_name}</td><td>${lid.email}</td><td>${lid.username}</td><td>${lid.role_id}</td></tr>`;
                            });
                            if (data.leden.length === 0) ledenBody.innerHTML = '<tr><td colspan="5">Geen leden gevonden</td></tr>';

                            // Taken
                            document.getElementById('takenCount').textContent = data.open_taken.length;
                            const takenBody = document.getElementById('takenTableBody');
                            takenBody.innerHTML = '';
                            data.open_taken.forEach(taak => {
                                takenBody.innerHTML += `<tr><td>${taak.task_id}</td><td>${taak.title}</td><td>${taak.description || ''}</td><td>${taak.start_date || ''}</td><td>${taak.end_date || ''}</td></tr>`;
                            });
                            if (data.open_taken.length === 0) takenBody.innerHTML = '<tr><td colspan="5">Geen open taken</td></tr>';
                        } else {
                            document.getElementById('ledenTableBody').innerHTML = '<tr><td colspan="5">Fout: ' + data.message + '</td></tr>';
                            document.getElementById('takenTableBody').innerHTML = '<tr><td colspan="5">Fout: ' + data.message + '</td></tr>';
                        }
                    })
                    .catch(err => {
                        document.getElementById('ledenTableBody').innerHTML = '<tr><td colspan="5">Fout bij ophalen</td></tr>';
                        document.getElementById('takenTableBody').innerHTML = '<tr><td colspan="5">Fout bij ophalen</td></tr>';
                    });
            });
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

            // if (profileCircle) {
            //     profileCircle.addEventListener('click', function(e) {
            //         e.preventDefault();
            //         // Haal leden en open taken op via AJAX
            //         logoutModal.classList.add('active');
            //         document.body.style.overflow = 'hidden';
            //     });
            // }

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