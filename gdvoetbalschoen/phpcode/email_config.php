<?php

/**
 * Email Configuratie
 * 
 * Vul hier je SMTP gegevens in voor het versturen van emails
 */

// SMTP Configuratie
define('SMTP_HOST', 'smtp.gmail.com');           // Bijv: smtp.gmail.com, smtp.office365.com, smtp-mail.outlook.com
define('SMTP_PORT', 587);                         // 587 voor TLS, 465 voor SSL
define('SMTP_SECURE', 'tls');                     // 'tls' of 'ssl'
define('SMTP_USERNAME', 'jouw-email@gmail.com'); // Je volledige email adres
define('SMTP_PASSWORD', 'jouw-app-wachtwoord');  // Voor Gmail: App Password (niet je gewone wachtwoord!)

// Email Afzender
define('MAIL_FROM_EMAIL', 'jouw-email@gmail.com');
define('MAIL_FROM_NAME', 'FC Team zonder plan');

// Debug mode (zet op false in productie)
define('MAIL_DEBUG', true);

/**
 * GMAIL SETUP INSTRUCTIES:
 * 
 * 1. Ga naar je Google Account: https://myaccount.google.com/
 * 2. Ga naar "Security" (Beveiliging)
 * 3. Schakel "2-Step Verification" in als dit nog niet aan staat
 * 4. Zoek naar "App passwords" (App-wachtwoorden)
 * 5. Selecteer "Mail" en "Windows Computer"
 * 6. Kopieer het gegenereerde 16-cijferige wachtwoord
 * 7. Plak dat wachtwoord in SMTP_PASSWORD hierboven
 * 
 * OUTLOOK/HOTMAIL:
 * - SMTP_HOST: smtp-mail.outlook.com
 * - SMTP_PORT: 587
 * - Gebruik je normale email en wachtwoord
 * 
 * ANDERE PROVIDERS:
 * - Zoek naar "[provider] SMTP settings" in Google
 */
