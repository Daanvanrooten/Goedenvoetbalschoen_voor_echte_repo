<?php

/**
 * EMAIL SENDER - Verstuurt verificatie emails naar gebruikers
 * 
 * Dit bestand zorgt ervoor dat nieuwe gebruikers een verificatiecode ontvangen
 * Lokaal (op je eigen PC) worden geen echte emails verstuurd, alleen gelogd
 * Online wordt PHPMailer gebruikt om echte emails te versturen via Gmail SMTP
 */

// Functie om verificatie email te versturen
function sendVerificationEmail($toEmail, $toName, $verificationCode)
{
    // Check of we online zijn of lokaal werken
    // Lokale hosts krijgen GEEN echte email, dat voorkomt errors tijdens development
    $isOnline = !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', 'webroot.local']);

    if (!$isOnline) {
        // Lokaal skippen we email versturen, anders krijgen we errors
        // Code wordt toch in verification_codes.txt gezet dus geen probleem
        return true;
    }

    // Online -> gebruik PHPMailer om echte emails te sturen
    require_once __DIR__ . '/../vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // SMTP setup voor Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bryvarooijen@gmail.com';
        $mail->Password = 'tpfyvtpbegpenfmb';  // app-specifiek wachtwoord
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Van en naar
        $mail->setFrom('noreply@fcteam.nl', 'FC Team zonder plan');
        $mail->addAddress($toEmail, $toName);

        // Email content
        $mail->isHTML(false);
        $mail->Subject = "Je verificatiecode: $verificationCode";
        $mail->Body = "Hallo $toName,\n\n";
        $mail->Body .= "Bedankt voor je registratie bij FC Team zonder plan!\n\n";
        $mail->Body .= "Je verificatiecode is: $verificationCode\n\n";
        $mail->Body .= "Deze code is 15 minuten geldig.\n\n";
        $mail->Body .= "Groetjes,\nFC Team zonder plan";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email fout: " . $mail->ErrorInfo);
        return false;
    }
}
