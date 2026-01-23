<?php
/**
 * Email Functie - Verstuurt emails via SMTP
 * Gebruikt PHPMailer als beschikbaar, anders mail()
 */

require_once __DIR__ . '/email_config.php';

/**
 * Verstuur verificatie email
 * 
 * @param string $toEmail Ontvanger email
 * @param string $toName Ontvanger naam
 * @param string $verificationCode 6-cijferige code
 * @return bool Success
 */
function sendVerificationEmail($toEmail, $toName, $verificationCode) {
    // Check of PHPMailer beschikbaar is
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        return sendWithPHPMailer($toEmail, $toName, $verificationCode);
    } else {
        return sendWithBuiltIn($toEmail, $toName, $verificationCode);
    }
}

/**
 * Verstuur via PHPMailer (aanbevolen)
 */
function sendWithPHPMailer($toEmail, $toName, $verificationCode) {
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuratie
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        if (MAIL_DEBUG) {
            $mail->SMTPDebug = 2; // Debug output
        }
        
        // Afzender en ontvanger
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $toName);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Verificatiecode voor je account - ' . MAIL_FROM_NAME;
        $mail->Body = getEmailTemplate($toName, $verificationCode);
        $mail->AltBody = getEmailTextVersion($toName, $verificationCode);
        
        $mail->send();
        
        // Log success
        if (MAIL_DEBUG) {
            file_put_contents(__DIR__ . '/email_log.txt', 
                date('Y-m-d H:i:s') . " - Email verzonden naar: $toEmail\n", 
                FILE_APPEND
            );
        }
        
        return true;
        
    } catch (Exception $e) {
        // Log error
        error_log("Email fout: " . $mail->ErrorInfo);
        
        if (MAIL_DEBUG) {
            file_put_contents(__DIR__ . '/email_log.txt', 
                date('Y-m-d H:i:s') . " - FOUT: " . $mail->ErrorInfo . "\n", 
                FILE_APPEND
            );
        }
        
        return false;
    }
}

/**
 * Verstuur via PHP mail() functie (fallback)
 */
function sendWithBuiltIn($toEmail, $toName, $verificationCode) {
    $subject = 'Verificatiecode voor je account - ' . MAIL_FROM_NAME;
    $message = getEmailTextVersion($toName, $verificationCode);
    
    $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . MAIL_FROM_EMAIL . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    $success = mail($toEmail, $subject, $message, $headers);
    
    // Log
    if (MAIL_DEBUG) {
        $status = $success ? 'SUCCES' : 'FOUT';
        file_put_contents(__DIR__ . '/email_log.txt', 
            date('Y-m-d H:i:s') . " - $status - Email naar: $toEmail (via mail())\n", 
            FILE_APPEND
        );
    }
    
    return $success;
}

/**
 * HTML Email Template
 */
function getEmailTemplate($name, $code) {
    return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #6b5b95 0%, #8b7bb8 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .code-box { background: white; border: 3px dashed #6b5b95; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
        .code { font-size: 36px; font-weight: bold; color: #6b5b95; letter-spacing: 8px; font-family: 'Courier New', monospace; }
        .footer { text-align: center; margin-top: 20px; color: #888; font-size: 12px; }
        .button { display: inline-block; background: #6b5b95; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>⚽ Welkom bij FC Team zonder plan!</h1>
        </div>
        <div class='content'>
            <h2>Hallo $name! 👋</h2>
            <p>Bedankt voor je registratie! Om je account te activeren, hebben we een verificatiecode voor je gegenereerd.</p>
            
            <div class='code-box'>
                <p style='margin: 0; font-size: 14px; color: #666;'>Je verificatiecode:</p>
                <div class='code'>$code</div>
            </div>
            
            <p><strong>Voer deze code in op de verificatiepagina om je account te activeren.</strong></p>
            
            <p>⏱️ <em>Deze code is 15 minuten geldig.</em></p>
            
            <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
            
            <p style='font-size: 14px; color: #666;'>
                Als je geen account hebt aangemaakt, kun je deze email negeren.
            </p>
        </div>
        <div class='footer'>
            <p>© " . date('Y') . " FC Team zonder plan - Gouden Voetbalschoen</p>
            <p>Deze email is automatisch gegenereerd, reageer er niet op.</p>
        </div>
    </div>
</body>
</html>
";
}

/**
 * Plain text versie (voor email clients die geen HTML ondersteunen)
 */
function getEmailTextVersion($name, $code) {
    return "
==============================================
⚽ WELKOM BIJ FC TEAM ZONDER PLAN!
==============================================

Hallo $name!

Bedankt voor je registratie! Om je account te activeren, 
hebben we een verificatiecode voor je gegenereerd.

------------------------------------------
JE VERIFICATIECODE:

    $code

------------------------------------------

Voer deze code in op de verificatiepagina om je account 
te activeren.

⏱️ Deze code is 15 minuten geldig.

Als je geen account hebt aangemaakt, kun je deze email negeren.

© " . date('Y') . " FC Team zonder plan - Gouden Voetbalschoen
Deze email is automatisch gegenereerd, reageer er niet op.
";
}
?>
