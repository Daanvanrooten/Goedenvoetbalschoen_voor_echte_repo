<?php
// email config voor online server
// lokaal wordt geen email verstuurd, alleen gelogd

function sendVerificationEmail($toEmail, $toName, $verificationCode)
{
    // check of we online zijn
    $isOnline = !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', 'webroot.local']);

    if (!$isOnline) {
        // lokaal - skip email
        return true;
    }

    // PHPMailer gebruiken voor online
    require_once __DIR__ . '/../vendor/autoload.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // SMTP instellingen - vul jouw gegevens in
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bryvarooijen@gmail.com';
        $mail->Password = 'tpfyvtpbegpenfmb';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Van en naar
        $mail->setFrom('noreply@fcteam.nl', 'FC Team zonder plan');
        $mail->addAddress($toEmail, $toName);

        // Content
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
