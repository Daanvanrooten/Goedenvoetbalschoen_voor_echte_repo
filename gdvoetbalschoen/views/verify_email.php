<?php
session_start();
require_once('../phpcode/config.php');

// Check of er een pending verification is
if (!isset($_SESSION['pending_verification'])) {
    header('Location: login.php');
    exit();
}

$userInfo = $_SESSION['pending_verification'];
$email = $userInfo['email'];
$firstName = $userInfo['first_name'];

// Maskeer email voor privacy (show first 3 chars + domain)
$emailParts = explode('@', $email);
$maskedEmail = substr($emailParts[0], 0, 3) . '***@' . $emailParts[1];
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verificatie - Gouden Schoen</title>
    <link rel="stylesheet" href="../css/login_register.css">
    <style>
        .verification-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .verification-info h2 {
            color: #6b5b95;
            margin-bottom: 10px;
        }

        .verification-info p {
            color: #666;
            line-height: 1.6;
        }

        .masked-email {
            font-weight: 600;
            color: #6b5b95;
        }

        .code-input {
            text-align: center;
            font-size: 24px;
            letter-spacing: 8px;
            font-weight: 600;
        }

        .resend-link {
            text-align: center;
            margin-top: 15px;
        }

        .resend-link a {
            color: #6b5b95;
            text-decoration: none;
            font-size: 14px;
        }

        .resend-link a:hover {
            text-decoration: underline;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
        }

        .timer-info {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: #888;
        }
    </style>
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
            </nav>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <div class="verification-info">
                <h2>👋 Hallo <?php echo htmlspecialchars($firstName); ?>!</h2>
                <p>We hebben een verificatiecode gestuurd naar:<br>
                    <span class="masked-email"><?php echo htmlspecialchars($maskedEmail); ?></span>
                </p>
                <p>Voer de 6-cijferige code hieronder in om je account te activeren.</p>
                <div class="timer-info">
                    ⏱️ De code is 15 minuten geldig
                </div>
            </div>

            <div class="auth-card">
                <div id="errorMessage" class="error-message" style="display: none;"></div>
                <div id="successMessage" class="success-message"></div>

                <form method="POST" class="auth-form" id="verifyForm">
                    <div class="form-group">
                        <input type="text"
                            id="verification_code"
                            name="verification_code"
                            required
                            placeholder=" "
                            maxlength="6"
                            pattern="[0-9]{6}"
                            class="code-input"
                            autocomplete="off">
                        <label for="verification_code">Verificatiecode</label>
                    </div>
                    <button type="submit" class="auth-btn">Verifieer Account</button>
                </form>

                <div class="resend-link">
                    <a href="#" id="resendLink">Code niet ontvangen? Stuur opnieuw</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // detecteer of we lokaal of online zijn
        const isLocal = window.location.hostname.includes('localhost') ||
            window.location.hostname.includes('127.0.0.1') ||
            window.location.hostname.includes('webroot.local');

        // Automatische path detectie - werkt op elke PC
        const currentPath = window.location.pathname;
        const viewsIndex = currentPath.lastIndexOf('/views/');
        const baseUrl = viewsIndex !== -1 ? currentPath.substring(0, viewsIndex) : '';

        // Auto-focus op code input
        document.getElementById('verification_code').focus();

        // Alleen cijfers toestaan
        document.getElementById('verification_code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Verify form submit
        document.getElementById('verifyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const errorDiv = document.getElementById('errorMessage');
            const successDiv = document.getElementById('successMessage');
            const submitBtn = this.querySelector('.auth-btn');

            submitBtn.disabled = true;
            submitBtn.textContent = 'Verifiëren...';
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';

            const formData = new FormData(this);

            try {
                const response = await fetch(baseUrl + '/phpcode/verify_email_code.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    successDiv.textContent = '✓ ' + data.message;
                    successDiv.style.display = 'block';

                    // Redirect na 1 seconde
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
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
                submitBtn.textContent = 'Verifieer Account';
            }
        });

        // Resend code
        document.getElementById('resendLink').addEventListener('click', async function(e) {
            e.preventDefault();

            const errorDiv = document.getElementById('errorMessage');
            const successDiv = document.getElementById('successMessage');

            this.textContent = 'Bezig met versturen...';
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';

            try {
                const response = await fetch(baseUrl + '/phpcode/resend_verification.php', {
                    method: 'POST'
                });

                const data = await response.json();

                if (data.success) {
                    successDiv.textContent = '✓ ' + data.message;
                    successDiv.style.display = 'block';
                    this.textContent = 'Code opnieuw verzonden!';

                    // Reset link text na 3 seconden
                    setTimeout(() => {
                        this.textContent = 'Code niet ontvangen? Stuur opnieuw';
                    }, 3000);
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                    errorDiv.style.color = 'red';
                    errorDiv.style.padding = '10px';
                    errorDiv.style.marginBottom = '15px';
                    errorDiv.style.backgroundColor = '#ffe6e6';
                    errorDiv.style.borderRadius = '5px';
                    this.textContent = 'Code niet ontvangen? Stuur opnieuw';
                }
            } catch (error) {
                errorDiv.textContent = 'Er is een fout opgetreden bij het opnieuw versturen.';
                errorDiv.style.display = 'block';
                this.textContent = 'Code niet ontvangen? Stuur opnieuw';
            }
        });
    </script>
</body>

</html>