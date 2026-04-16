<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inscription-form</title>
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
        background: #55735e;  
    }
    h2{
        text-align:center;

    }
    </style>
</head>
<body>
    <form action="../logique/inscri.php" method="POST">
    <input type="text" name="nom" placeholder="Nom" required><br>
    <input type="text" name="prenom" placeholder="Prénom" required><br>
    <input type="tel" name="numero"  pattern="^\+?[0-9]{8}$" placeholder="Téléphone" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="mdp" placeholder="Mot de passe" required><br>
    <button type="submit" name="register">S'inscrire</button>
</form>

</form>
</body>
</html> 