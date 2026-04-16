<?php
require_once('../config/db.php');
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

if (isset($_POST['register'])) {
    $nom       = trim($_POST['nom']);
    $prenom    = trim($_POST['prenom']);
    $telephone = trim($_POST['numero']);
    $email     = trim($_POST['email']);
    $password  = password_hash ($_POST['mdp'], PASSWORD_BCRYPT);

    // Générer OTP et expiration (2minutes)
    $otp = rand(100000, 999999);
    $expire_at = date("Y-m-d H:i:s", strtotime("+2 minutes"));

    $stmt = $pdo->prepare("INSERT INTO utilisateur 
        (nom, prenom, telephone, email, mdp, otp, expire_at, verified, created_at) 
        VALUES (:nom, :prenom, :telephone, :email, :mdp, :otp, :expire_at, :verified, :created_at)");

    $stmt->execute([
        ':nom'       => $nom,
        ':prenom'    => $prenom,
        ':telephone' => $telephone,
        ':email'     => $email,
        ':mdp'       => $password,
        ':otp'       => $otp,
        ':expire_at' => $expire_at,
        ':verified'  => 0,
        ':created_at'=> date("Y-m-d H:i:s")
    ]);

    if ($stmt) {
        // Stocker l'email en session pour la vérification
        $_SESSION['email'] = $email;

        // Envoyer l'email avec l'OTP
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'moukailatovo9@gmail.com';
            $mail->Password   = 'rwbhzklhqnjbxixw'; // mot de passe d'application Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('moukailatovo9@gmail.com', 'Moukaila TOVO');
            $mail->addAddress($email, 'Utilisateur');

            $mail->isHTML(true);
            $mail->Subject = 'Votre code OTP by Moukaila TOVO';
            $mail->Body    = "Bonjour,<br><br>
                              Votre code de vérification est : <b>$otp</b><br>
                              Il va expirer dans 2 minutes.<br><br>
                              Cordialement,<br>L'équipe";

            $mail->send();
            header("Location: ../page/correct.php?email=$email");
            exit();
        } catch (Exception $e) {
            header("Location: ../page/correct.php?error=2"); // Erreur envoi mail
            exit();
        }
    } else {
        header("Location: ../page/fail.php?error=1");
        exit();
    }
}