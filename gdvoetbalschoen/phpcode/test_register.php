<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registratie Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #6b5b95; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Registratie Test</h1>
    <p>Gebruik dit formulier om te testen wat er mis gaat bij registratie.</p>
    
    <form id="testForm">
        <div class="form-group">
            <label>Voornaam:</label>
            <input type="text" name="voornaam" value="Test" required>
        </div>
        <div class="form-group">
            <label>Achternaam:</label>
            <input type="text" name="achternaam" value="Gebruiker" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="test<?php echo time(); ?>@example.com" required>
        </div>
        <div class="form-group">
            <label>Telefoonnummer:</label>
            <input type="text" name="telefoonnummer" value="0612345678" required>
        </div>
        <div class="form-group">
            <label>Gebruikersnaam:</label>
            <input type="text" name="username" value="testuser<?php echo rand(1000, 9999); ?>" required>
        </div>
        <div class="form-group">
            <label>Wachtwoord:</label>
            <input type="password" name="password" value="test123456" required>
        </div>
        <button type="submit">Test Registratie</button>
    </form>
    
    <div id="result"></div>
    
    <script>
        document.getElementById('testForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const resultDiv = document.getElementById('result');
            const submitBtn = this.querySelector('button');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Bezig...';
            resultDiv.innerHTML = '<p>Laden...</p>';
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/Goedenvoetbalschoen_voor_echte_repo/gdvoetbalschoen/phpcode/registercode.php', {
                    method: 'POST',
                    body: formData
                });
                
                const responseText = await response.text();
                console.log('Response text:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    resultDiv.innerHTML = `
                        <div class="result error">
                            <h2>❌ JSON Parse Error</h2>
                            <p><strong>Error:</strong> ${parseError.message}</p>
                            <p><strong>Response status:</strong> ${response.status} ${response.statusText}</p>
                            <h3>Raw Response:</h3>
                            <pre>${responseText}</pre>
                        </div>
                    `;
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Test Registratie';
                    return;
                }
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="result success">
                            <h2>✅ Succes!</h2>
                            <p><strong>Message:</strong> ${data.message}</p>
                            <p><strong>Redirect:</strong> ${data.redirect}</p>
                            ${data.verification_code ? `<p><strong>Verificatiecode:</strong> <span style="font-size: 24px; font-weight: bold; color: #6b5b95;">${data.verification_code}</span></p>` : ''}
                            <h3>Volledige Response:</h3>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                            <p><a href="${data.redirect}" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background: #6b5b95; color: white; text-decoration: none; border-radius: 4px;">Ga naar Verificatie</a></p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="result error">
                            <h2>❌ Error</h2>
                            <p><strong>Message:</strong> ${data.message}</p>
                            <h3>Volledige Response:</h3>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="result error">
                        <h2>❌ Network/Fetch Error</h2>
                        <p><strong>Error:</strong> ${error.message}</p>
                        <p>Mogelijke oorzaken:</p>
                        <ul>
                            <li>Server is niet bereikbaar</li>
                            <li>PHP script geeft een fatal error</li>
                            <li>URL is verkeerd</li>
                        </ul>
                    </div>
                `;
                console.error('Fetch error:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Test Registratie';
            }
        });
    </script>
</body>
</html>
