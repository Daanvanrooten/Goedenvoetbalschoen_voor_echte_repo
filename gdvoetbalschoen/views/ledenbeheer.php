<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
$userInitial = isset($user['first_name']) ? strtoupper(substr($user['first_name'], 0, 1)) : '';

// Leden worden via AJAX opgehaald
$members = [];
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 7;
$totalPages = 1;
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leden Beheer - Gouden Schoen</title>
    <link rel="stylesheet" href="../css/ledenbeheer.css">
</head>

<body>


    <main>
        <div class="container">
            <h1 class="page-title">Leden Beheer</h1>

            <!-- Tabs -->
            <div class="tabs-container">
                <div class="tabs">

                    <a href="#" class="tab active">
                        Leden beheer
                        <span class="badge">5</span>
                    </a>
                </div>
                <div class="search-box desktop-search">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                    </svg>
                    <input type="text" placeholder="Search">
                </div>
            </div>

            <!-- Admin Link -->
            <div class="admin-link">
                <a href="admin_dashboard.php">Ga naar admin</a>
            </div>

            <!-- Mobile Search -->
            <div class="search-box mobile-search">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                </svg>
                <input type="text" placeholder="Search">
            </div>

            <!-- Members Table -->
            <div class="table-container">
                <table class="members-table">
                    <thead>
                        <tr>
                            <th class="checkbox-col">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="author-col">
                                Author
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                    <path d="M7 10l5 5 5-5z" />
                                </svg>
                            </th>
                            <th class="email-col desktop-only">Email</th>
                            <th class="phone-col desktop-only">Telefoon</th>
                            <th class="actions-col"></th>
                        </tr>
                    </thead>
                    <tbody id="ledenTableBody">
                        <tr><td colspan="5">Laden...</td></tr>
                    </tbody>
                </main>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    fetch('../phpcode/leden_lijst.php')
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const ledenBody = document.getElementById('ledenTableBody');
                                ledenBody.innerHTML = '';
                                data.leden.forEach(lid => {
                                    ledenBody.innerHTML += `
                                        <tr>
                                            <td class="checkbox-col"><input type="checkbox" name="member[]" value="${lid.user_id}"></td>
                                            <td class="author-col">
                                                <div class="member-info">
                                                    <div class="avatar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" /></svg>
                                                    </div>
                                                    <div class="member-details">
                                                        <div class="member-name">${lid.first_name} ${lid.last_name}</div>
                                                        <div class="member-role">${lid.role_id}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="email-col desktop-only"><span class="email-text">${lid.email}</span></td>
                                            <td class="phone-col desktop-only"><span class="phone-text">-</span></td>
                                            <td class="actions-col">
                                                <button class="icon-btn delete-btn" title="Verwijderen">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" /></svg>
                                                </button>
                                                <button class="icon-btn more-btn" data-member-id="${lid.user_id}" data-member-name="${lid.first_name} ${lid.last_name}" data-role="${lid.role_id}" title="Meer opties">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                });
                                if (data.leden.length === 0) ledenBody.innerHTML = '<tr><td colspan="5">Geen leden gevonden</td></tr>';
                            } else {
                                document.getElementById('ledenTableBody').innerHTML = '<tr><td colspan="5">Fout: ' + data.message + '</td></tr>';
                            }
                        })
                        .catch(err => {
                            document.getElementById('ledenTableBody').innerHTML = '<tr><td colspan="5">Fout bij ophalen</td></tr>';
                        });
                });
                // Modal admin beheer logica
                const adminModal = document.getElementById('adminModal');
                const closeModalBtn = document.getElementById('closeModal');
                const cancelBtn = document.getElementById('cancelBtn');
                const confirmBtn = document.getElementById('confirmBtn');
                let selectedUserId = null;
                let selectedRole = null;
                let selectedName = '';

                document.getElementById('ledenTableBody').addEventListener('click', function(e) {
                    if (e.target.classList.contains('more-btn')) {
                        selectedUserId = e.target.dataset.memberId;
                        selectedRole = parseInt(e.target.dataset.role);
                        selectedName = e.target.dataset.memberName;
                        // Pas modal tekst aan
                        document.querySelector('.modal-title').textContent = 'Admin beheer';
                        if (selectedRole === 2) {
                            document.querySelector('.modal-text').textContent = `Wil je ${selectedName} geen admin meer maken?`;
                        } else {
                            document.querySelector('.modal-text').textContent = `Wil je ${selectedName} admin maken?`;
                        }
                        adminModal.classList.add('active');
                    }
                });
                function closeAdminModal() {
                    adminModal.classList.remove('active');
                }
                closeModalBtn.addEventListener('click', closeAdminModal);
                cancelBtn.addEventListener('click', closeAdminModal);
                confirmBtn.addEventListener('click', function() {
                    if (!selectedUserId) return;
                    const newRole = selectedRole === 2 ? 1 : 2;
                    fetch('../phpcode/update_role.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `user_id=${selectedUserId}&role_id=${newRole}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        closeAdminModal();
                        window.location.reload();
                    });
                });
                </script>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="page-btn prev-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                    </svg>
                    Previous
                </button>
                <div class="page-numbers">
                    <button class="page-num">1</button>
                    <button class="page-num active">2</button>
                    <button class="page-num">3</button>
                    <button class="page-num">4</button>
                    <button class="page-num">5</button>
                    <span class="dots">...</span>
                    <button class="page-num">11</button>
                </div>
                <button class="page-btn next-btn">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z" />
                    </svg>
                </button>
            </div>
        </div>
    </main>

    <!-- Admin Modal -->
    <div id="adminModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" id="closeModal">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                </svg>
            </button>
            <h2 class="modal-title">Admin beheer</h2>
            <p class="modal-text">Wil je deze gebruiker admin maken?</p>
            <div class="modal-actions">
                <button class="modal-btn cancel-btn" id="cancelBtn">Annuleer</button>
                <button class="modal-btn confirm-btn" id="confirmBtn">Ja</button>
            </div>
        </div>
    </div>

    <script src="../js/ledenbeheer.js"></script>
</body>

</html>