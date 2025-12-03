<?php
session_start();

// Check if user is logged in
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

// Sample members data
$members = [
    ['id' => 1, 'name' => 'Jane Doe', 'role' => 'Senior Designer', 'email' => 'jane@example.com', 'phone' => '+31 6 12345678'],
    ['id' => 2, 'name' => 'John Smith', 'role' => 'Senior Designer', 'email' => 'john@example.com', 'phone' => '+31 6 87654321'],
    ['id' => 3, 'name' => 'Alice Johnson', 'role' => 'Senior Designer', 'email' => 'alice@example.com', 'phone' => '+31 6 11223344'],
    ['id' => 4, 'name' => 'Bob Williams', 'role' => 'Senior Designer', 'email' => 'bob@example.com', 'phone' => '+31 6 55667788'],
    ['id' => 5, 'name' => 'Charlie Brown', 'role' => 'Senior Designer', 'email' => 'charlie@example.com', 'phone' => '+31 6 99887766'],
    ['id' => 6, 'name' => 'Diana Prince', 'role' => 'Senior Designer', 'email' => 'diana@example.com', 'phone' => '+31 6 44556677'],
    ['id' => 7, 'name' => 'Ethan Hunt', 'role' => 'Senior Designer', 'email' => 'ethan@example.com', 'phone' => '+31 6 22334455'],
];

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 7;
$totalPages = ceil(count($members) / $perPage);
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
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
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
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
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
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </th>
                            <th class="email-col desktop-only">Email</th>
                            <th class="phone-col desktop-only">Telefoon</th>
                            <th class="actions-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                        <tr>
                            <td class="checkbox-col">
                                <input type="checkbox" name="member[]" value="<?php echo $member['id']; ?>">
                            </td>
                            <td class="author-col">
                                <div class="member-info">
                                    <div class="avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                        </svg>
                                    </div>
                                    <div class="member-details">
                                        <div class="member-name"><?php echo htmlspecialchars($member['name']); ?></div>
                                        <div class="member-role"><?php echo htmlspecialchars($member['role']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="email-col desktop-only">
                                <span class="email-text">Email</span>
                            </td>
                            <td class="phone-col desktop-only">
                                <span class="phone-text">Nummer</span>
                            </td>
                            <td class="actions-col">
                                <button class="icon-btn delete-btn" title="Verwijderen">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                    </svg>
                                </button>
                                <button class="icon-btn more-btn" data-member-id="<?php echo $member['id']; ?>" data-member-name="<?php echo htmlspecialchars($member['name']); ?>" title="Meer opties">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="page-btn prev-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
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
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
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
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
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
