<?php
include('../config/db.php');
session_start();

// Récupérer l'email depuis le formulaire ou la session
$email = trim($_POST['email'] ?? ($_SESSION['email'] ?? ''));

if (isset($_POST['verify']) && $email) {
    $otp = trim($_POST['otp'] ?? '');

    // Validation de l'OTP : doit être numérique et de longueur 6
    if (!is_numeric($otp) || strlen($otp) !== 6) {
        $_SESSION['error'] = 'Code OTP invalide (doit être 6 chiffres).';
        header("Location: ../page/verify.php?email=" . urlencode($email));
        exit;
    }

    try {
        // Récupérer OTP et expiration depuis la base
        $stmt = $pdo->prepare("SELECT id, otp, expire_at FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vérifier le code OTP et l'expiration
            if ($otp == $user['otp'] && strtotime($user['expire_at']) > time()) {
                // Mise à jour du statut verified
                $update = $pdo->prepare("UPDATE utilisateur SET verified = 1 WHERE email = :email");
                $update->execute([':email' => $email]);

                // Nettoyer la session et définir user_id
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['email'] = $email;
                unset($_SESSION['error']);

                header("Location: ../page/success.php");
                exit();
            } else {
                $_SESSION['error'] = strtotime($user['expire_at']) <= time() 
                    ? 'Code expiré. Veuillez vous réinscrire.' 
                    : 'Code OTP incorrect.';
                header("Location: ../page/verify.php?email=" . urlencode($email));
                exit;
            }
        } else {
            $_SESSION['error'] = 'Utilisateur introuvable.';
            header("Location: ../page/verify.php?email=" . urlencode($email));
            exit;
        }
    } catch (PDOException $e) {
        error_log("Erreur de base de données : " . $e->getMessage());
        $_SESSION['error'] = 'Erreur interne. Veuillez réessayer.';
        header("Location: ../page/verify.php?email=" . urlencode($email));
        exit;
    }
}