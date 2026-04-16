<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>succes-form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    <style>
        body{
            padding: 0;
            margin: 0;
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
        background: #5bf088;  
    }
    h2{
        text-align:center;

    }
    i{
        font-size: 50px;
        text-align: center;
    }
    a{
        text-decoration:none;
        color:white;
    }
    </style>
</head>
<body>
    <form action="../page/verify.php" method="POST">
       <i class="fa-solid fa-check" style="color:green;"></i>
    <h2> Félicitations, inscription effectuer avec succes <br>
    veuillez maintenant confirmer l incription</h2>
   <button><a href="../page/verify.php">confirmer</a></button> 
    </form>
</body>
</html>