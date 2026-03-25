<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>fail-form</title>
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
        background: #90e4aa;  
    }
    h2{
        text-align:center;

    }
    </style>
</head>
<body>
    <form action="">

    <h2> Erreur , votre compte n'est pas creer </h2>
    <button><a href="inscri.php">s' inscrire</a></button>
    </form>
</body>
</html>