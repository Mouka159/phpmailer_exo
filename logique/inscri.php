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
    $mdp_plain = trim($_POST['mdp']); // Garder le mot de passe non hashé pour vérification
    $adminCode = trim($_POST['admin_code'] ?? '');
    $password  = password_hash($mdp_plain, PASSWORD_BCRYPT);
    
    // Déterminer le rôle
    $role = 'user';
    if ($adminCode !== '') {
        if ($adminCode === 'ADMIN2026') {
            $role = 'admin';
        } else {
            header("Location: ../page/inscri.php?error=".urlencode("❌ Code administrateur invalide"));
            exit();
        }
    }

    // Vérifier l'unicité du NOM
    $checkNom = $pdo->prepare("SELECT COUNT(*) as count FROM utilisateur WHERE nom = :nom");
    $checkNom->execute([':nom' => $nom]);
    $nomExists = $checkNom->fetch()['count'] > 0;

    // Vérifier l'unicité de l'EMAIL
    $checkEmail = $pdo->prepare("SELECT COUNT(*) as count FROM utilisateur WHERE email = :email");
    $checkEmail->execute([':email' => $email]);
    $emailExists = $checkEmail->fetch()['count'] > 0;

    // Vérifier l'unicité du MOT DE PASSE (hasher les mots de passe existants et comparer)
    $checkMdp = $pdo->prepare("SELECT mdp FROM utilisateur");
    $checkMdp->execute();
    $mdpExists = false;
    while ($row = $checkMdp->fetch()) {
        if (password_verify($mdp_plain, $row['mdp'])) {
            $mdpExists = true;
            break;
        }
    }

    // Vérifier les erreurs
    if ($nomExists) {
        header("Location: ../page/inscri.php?error=".urlencode("❌ Ce nom d'utilisateur est déjà pris"));
        exit();
    }

    if ($emailExists) {
        header("Location: ../page/inscri.php?error=".urlencode("❌ Cet email est déjà enregistré"));
        exit();
    }

    if ($mdpExists) {
        header("Location: ../page/inscri.php?error=".urlencode("❌ Ce mot de passe est déjà utilisé"));
        exit();
    }

    // Générer OTP et expiration (2 minutes)
    $otp = rand(100000, 999999);
    $expire_at = date("Y-m-d H:i:s", strtotime("+2 minutes"));

    $stmt = $pdo->prepare("INSERT INTO utilisateur 
        (nom, prenom, telephone, email, mdp, otp, expire_at, verified, created_at, role) 
        VALUES (:nom, :prenom, :telephone, :email, :mdp, :otp, :expire_at, :verified, :created_at, :role)");

    $stmt->execute([
        ':nom'       => $nom,
        ':prenom'    => $prenom,
        ':telephone' => $telephone,
        ':email'     => $email,
        ':mdp'       => $password,
        ':otp'       => $otp,
        ':expire_at' => $expire_at,
        ':verified'  => 0,
        ':created_at'=> date("Y-m-d H:i:s"),
        ':role'      => $role
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
            $mail->Subject = 'bienvenue sur ShopESA';
            $mail->Body    = "hello $prenom,<br><br>
                              Votre code de vérification est : <b>$otp</b><br>
                              Il va expirer dans 2 minutes. Cordialement,<br>L'équipe<br><br>
                              merci!!!";

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