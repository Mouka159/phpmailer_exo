<?php
require_once('../config/db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require '../vendor/autoload.php';

// Créer l'instance PHPMailer une seule fois
$mail = new PHPMailer(true);

if (isset($_POST['send'])) {
    $email = $_POST['email'] ?? null;

    if ($email) {
        // Générer OTP
        $otp = rand(100000, 999999);
        $expire_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // Sauvegarder OTP et expiration dans la base
        $stmt = $pdo->prepare("UPDATE utilisateur SET otp = :otp, expire_at = :expire_at WHERE email = :email");
        $stmt->execute(['otp' => $otp, 'expire_at' => $expire_at, 'email' => $email]);

        // Envoyer le mail
        if (EnvoiMail($mail, $email, $otp)) {
            header("Location: ../page/verify.php?sent=1");
            exit();
        } else {
            header("Location: ../page/verify.php?sent=0&error=mail");
            exit();
        }
    } else {
        header("Location: ../page/verify.php?sent=0&error=invalid");
        exit();
    }
}

function EnvoiMail($mail, $mailToSend, $otp) {
    try {
        // Config SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'moukailatovo9@gmail.com';
        $mail->Password   = 'rwbhzklhqnjbxixw'; // mot de passe d'application Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Destinataires
        $mail->setFrom('moukailatovo9@gmail.com', 'Moukaila TOVO');
        $mail->addAddress($mailToSend, 'Utilisateur');

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'Votre code OTP';
        $mail->Body    = "Bonjour,<br><br>
                          Votre code de vérification est : <b>$otp</b><br>
                          Il expire dans 20 secondes.<br><br>
                          Cordiale,<br>L'équipe";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
