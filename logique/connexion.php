<?php
 require('../config/db.php');
//verifier si le formulaire est soumis
if(isset($_POST['login'])){
    session_start();
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $pwd = trim($_POST['password']);
        //verifier si l'email existe dans la base de données
        $smt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
    $smt->execute([':email' => $email]);
    if($smt->rowCount() > 0){
        $user = $smt->fetch();
        //verifier le mot de passe
        if(password_verify($pwd, $user['mdp'])){
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            header('Location: ../page/affiche.php');
            exit();
        }else{
            echo "mot de passe incorrect";
            exit();
        }
    }else{
        echo "email non trouvé";
        exit();
    }
}

?>