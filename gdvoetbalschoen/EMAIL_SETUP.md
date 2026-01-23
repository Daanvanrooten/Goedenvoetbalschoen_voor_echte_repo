# Email Setup Instructies

## 📧 Stap 1: Installeer PHPMailer

Open PowerShell in de gdvoetbalschoen folder en run:

```powershell
cd C:\webroot.local\goudenvoetbalschoen\Goedenvoetbalschoen_voor_echte_repo\gdvoetbalschoen
composer install
```

Als je geen Composer hebt, download het van: https://getcomposer.org/download/

---

## 📧 Stap 2: Configureer je Email

Open: `phpcode/email_config.php`

### Voor Gmail:

1. Ga naar: https://myaccount.google.com/security
2. Schakel "2-Step Verification" in
3. Zoek naar "App passwords"
4. Genereer een app password voor "Mail"
5. Kopieer het 16-cijferige wachtwoord
6. Plak in `email_config.php`:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USERNAME', 'jouw-email@gmail.com');
define('SMTP_PASSWORD', 'jouw-16-cijferige-app-password');
define('MAIL_FROM_EMAIL', 'jouw-email@gmail.com');
```

### Voor Outlook/Hotmail:

```php
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USERNAME', 'jouw-email@outlook.com');
define('SMTP_PASSWORD', 'jouw-normale-wachtwoord');
define('MAIL_FROM_EMAIL', 'jouw-email@outlook.com');
```

---

## 📧 Stap 3: Test Email

Registreer een nieuwe gebruiker en check:
- Je inbox voor de verificatiecode
- `phpcode/email_log.txt` voor debug info
- `phpcode/verification_codes.txt` voor backup codes

---

## 🔧 Troubleshooting

**Email wordt niet verzonden?**
- Check `phpcode/email_log.txt` voor errors
- Verificatiecode staat ALTIJD in `phpcode/verification_codes.txt`
- Zet `MAIL_DEBUG` op `true` in `email_config.php`

**Gmail blokkeert emails?**
- Gebruik een App Password (NIET je normale wachtwoord)
- Check "Less secure app access" is AAN

**Composer werkt niet?**
- Download: https://getcomposer.org/Composer-Setup.exe
- Installeer en herstart terminal
- Run `composer install` opnieuw

---

## ✅ Het werkt als:

1. ✅ Composer install succesvol
2. ✅ Email config ingevuld
3. ✅ Test registratie verstuurt email
4. ✅ Verificatiecode komt aan in inbox
5. ✅ Code invoeren activeert account

DONE! 🎉
