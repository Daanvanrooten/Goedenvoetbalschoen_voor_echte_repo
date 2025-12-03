<?php
session_start();

// Simuleer registratie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voornaam = $_POST['voornaam'] ?? '';
    $achternaam = $_POST['achternaam'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (!empty($voornaam) && !empty($achternaam) && !empty($email)) {
        $_SESSION['user'] = [
            'id' => rand(1, 1000),
            'voornaam' => $voornaam,
            'achternaam' => $achternaam,
            'email' => $email
        ];
        header('Location: agenda.php');
        exit;
    }
}

// Als gebruiker al ingelogd is, redirect naar agenda
if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gouden Schoen</title>
    <link rel="stylesheet" href="../css/login_register.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="../images/fc_team_zonder_plan.png" alt="FC Team zonder plan logo">
                <span class="logo-text">FC Team zonder plan</span>
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
                <a href="login.php" class="nav-icon profile-icon active" title="Profile">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <h1 class="auth-title">Register</h1>
            <div class="auth-card">
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <input type="text" id="voornaam" name="voornaam" required placeholder=" ">
                        <label for="voornaam">Voornaam</label>
                    </div>
                    <div class="form-group">
                        <input type="text" id="achternaam" name="achternaam" required placeholder=" ">
                        <label for="achternaam">Achternaam</label>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" required placeholder=" ">
                        <label for="email">Email</label>
                    </div>
                    <button type="submit" class="auth-btn">Register</button>
                </form>
            </div>
            <p class="auth-link">
                Al een account ga naar <a href="login.php">Login</a>
            </p>
        </div>
    </main>
</body>
</html>
