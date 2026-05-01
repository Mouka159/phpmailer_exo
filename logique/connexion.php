<?php
require('../config/db.php');
session_start();

// Vérifier si le formulaire est soumis
if(isset($_POST['login'])){
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $pwd = trim($_POST['password']);
    $message = '';
    
    // Vérifier si l'email existe dans la base de données
    $smt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
    $smt->execute([':email' => $email]);
    
    if($smt->rowCount() > 0){
        $user = $smt->fetch();
        
        // Vérifier le mot de passe
        if(password_verify($pwd, $user['mdp'])){
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = $user['role'] ?? 'user'; // Ajouter le rôle en session
            
            // Redirection basée sur le rôle
            if ($_SESSION['role'] === 'admin') {
                header('Location: ../page/admin.php');
                exit();
            } else {
                header('Location: ../page/affiche.php');
                exit();
            }
        } else {
            // Mot de passe incorrect
            header('Location: ../page/conexion.php?message='.urlencode('❌ Mot de passe incorrect'));
            exit();
        }
    } else {
        // Email non trouvé
        header('Location: ../page/conexion.php?message='.urlencode('❌ Email non trouvé'));
        exit();
    }
}
?>