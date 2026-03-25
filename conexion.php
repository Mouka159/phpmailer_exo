<?php
$servername="localhost";
$username="root";
$password="";
$dbname="mailer";
if (isset($_POST["login"])){
    $nom=$_POST['nom'];
    $email=$_POST['email'];
    $mdp=$_POST['password'];
    $req=" INSERT INTO `mailer`(`nom`,`email`,`mdp`) VALUES ($nom,$email,$mdp)";
    $con=mysqli_connect("localhost","root","","mailer");
    mysqli_connect($con,$req);
    echo "utilisateur ajouté avec succes";

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>connexion-form</title>
    <style>
    body{
    margin: 0;
    padding: 0;
    }
    form{
        display:flex;
        flex-direction: column;
        width: 400px;
        margin: 20px auto;
        box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        padding: 20px;
        margin-top: 10%;
    }
    input{
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        position: relative;
        width: 75%;
        margin-left: 35px;
    }
    button{
      width: 75%;
      margin-left: 50px;
      padding: 10px;
      background: #29b954;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover{
        background: #6bef93;  
    }
    h2{
        text-align:center;

    }

    </style>
</head>
<body>
    <form action="POST">
        <h2>connexion</h2>
        <input type="text" name="nom" id="nom" placeholder="nom">
        <input type="email" name="email" id="email" placeholder="email">
        <input type="password" name="password" id="password" placeholder="password">
        <button name="login">CONNECTER</button>
    </form>
</body>
</html>