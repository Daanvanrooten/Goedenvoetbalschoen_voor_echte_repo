<?php
session_start();
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
                <a href="login.php" class="nav-icon profile-icon active" title="Profile">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <h1 class="auth-title">Register</h1>
            <div class="auth-card">
                <div id="errorMessage" class="error-message" style="display: none;"></div>
                <form method="POST" class="auth-form" id="registerForm">
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
                    <div class="form-group">
                        <input type="text" id="telefoonnummer" name="telefoonnummer" required placeholder=" ">
                        <label for="telefoonnummer">Telefoonnummer</label>
                    </div>
                    <div class="form-group">
                        <input type="text" id="username" name="username" required placeholder=" ">
                        <label for="username">Gebruikersnaam</label>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" required placeholder=" ">
                        <label for="password">Wachtwoord</label>
                    </div>
                    <button type="submit" class="auth-btn">Register</button>
                </form>
            </div>
            <p class="auth-link">
                Al een account ga naar <a href="login.php">Login</a>
            </p>
        </div>
    </main>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const errorDiv = document.getElementById('errorMessage');
            const submitBtn = this.querySelector('.auth-btn');

            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Bezig...';
            errorDiv.style.display = 'none';

            const formData = new FormData(this);

            try {
                const response = await fetch('/goudenvoetbalschoen/Goedenvoetbalschoen_voor_echte_repo/gdvoetbalschoen/phpcode/registercode.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Toon de verificatiecode tijdelijk voor testing
                    if (data.verification_code) {
                        alert(data.message + '\n\nVERIFICATIECODE (voor testing): ' + data.verification_code);
                    } else {
                        alert(data.message);
                    }
                    window.location.href = data.redirect;
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                    errorDiv.style.color = 'red';
                    errorDiv.style.padding = '10px';
                    errorDiv.style.marginBottom = '15px';
                    errorDiv.style.backgroundColor = '#ffe6e6';
                    errorDiv.style.borderRadius = '5px';
                }
            } catch (error) {
                errorDiv.textContent = 'Er is een fout opgetreden. Probeer het opnieuw.';
                errorDiv.style.display = 'block';
                errorDiv.style.color = 'red';
                errorDiv.style.padding = '10px';
                errorDiv.style.marginBottom = '15px';
                errorDiv.style.backgroundColor = '#ffe6e6';
                errorDiv.style.borderRadius = '5px';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Register';
            }
        });
    </script>
</body>

</html>