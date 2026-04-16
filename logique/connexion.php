<?php
 require('../config/db.php');
 require '../vendor/autoload.php';
//verifier si le formulaire est soumis
if(isset($_POST['login'])){
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $pwd = trim($_POST['password']);
        //verifier si l'email existe dans la base de données
        $smt = $pd->prepare("SELECT * FROM utilisateur WHERE email = :email
    ");
    $smt->execute([':email' => $email]);
    if($smt->rowCount() > 0){
        $user = $smt->fetch();
        //verifier le mot de passe
        if(password_verify($pwd, $user['pwd'])){
            echo" biennvenue $nom  $prenom sur notre site";
            exit();
        }else{
            header("Location: ../page/fail.php?error=invalid_password");
            exit();
        }
    }else{
        header("Location: ../page/fail.php?error=email_not_found");
        exit();
    }
}

?>