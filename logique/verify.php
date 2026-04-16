<?php
include('../config/db.php');
session_start();

// Supposons que tu as stocké l'email en session après inscription
$email = $_SESSION['email'] ?? null;

if (isset($_POST['verify']) && $email) {
    $otp = trim($_POST['otp']);

    // Validation de l'OTP : doit être numérique et de longueur 6 (ajustez si nécessaire)
    if (!is_numeric($otp) || strlen($otp) !== 6) {
        echo "❌ Code OTP invalide (doit être 6 chiffres).";
        exit;
    }

    try {
        // Récupérer OTP et expiration depuis la base
        $stmt = $pdo->prepare("SELECT otp, expire_at FROM utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($otp == $user['otp'] && strtotime($user['expire_at']) > time()) {
                // Mise à jour du statut verified
                $update = $pdo->prepare("UPDATE utilisateur SET verified = 1 WHERE email = :email");
                $update->execute(['email' => $email]);

                // Nettoyer la session après vérification
                unset($_SESSION['email']);

                echo "✅ Vérification réussie, compte activé !";
                header("Location: ../page/conexion.php");
                exit();
            } else {
                echo "❌ Code expiré.";
            }
        } else {
            echo "❌ Utilisateur introuvable.";
        }
    } catch (PDOException $e) {
        // Gestion des erreurs de base de données
        error_log("Erreur de base de données : " . $e->getMessage());
        echo "❌ Erreur interne. Veuillez réessayer.";
    }
}