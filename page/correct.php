<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Réussie</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            padding: 0;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            display: flex;
            flex-direction: column;
            width: 450px;
            background: white;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            margin: 15px 0;
            font-size: 24px;
        }
        .message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            text-align: left;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        input {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.5);
        }
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover {
            background: #218838;
        }
        .info-text {
            font-size: 12px;
            color: #999;
            margin-top: 15px;
        }
        .error {
            color: #dc3545;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        a {
            color: #667eea;
            text-decoration: none;
            margin-top: 20px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <i class="fa-solid fa-check-circle"></i>
        </div>
        <h2> Inscription Réussie!</h2>
        <p class="message">
            Bienvenue! Vous êtes maintenant inscrit.<br>
            Un code de vérification a été envoyé à votre email.
        </p>
        
        <?php
        $error = isset($_GET['error']) ? (int)$_GET['error'] : 0;
        $email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
        
        if ($error === 2) {
            echo '<div class="error"> Erreur lors de l\'envoi du email. Veuillez réessayer.</div>';
        }
        ?>
        
        <form action="../logique/verify.php" method="POST">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <label for="otp">Entrez le code OTP reçu par email :</label>
            <input type="text" id="otp" name="otp" placeholder="Ex: 123456" required maxlength="6" pattern="[0-9]{6}">
            <button type="submit" name="verify">Vérifier & Activer mon compte</button>
        </form>
        
        <p class="info-text">⏱ Le code expire dans 2 minutes</p>
        
        <p style="margin-top: 20px; color: #666;">
            Vous avez un compte? 
            <a href="conexion.php">Se connecter</a>
        </p>
    </div>
</body>
</html>