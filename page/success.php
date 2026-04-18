<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte Activé avec Succès</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        .container {
            background: white;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0px 8px 24px rgba(0,0,0,0.2);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
            animation: bounce 1s;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        h1 {
            color: #333;
            margin: 15px 0;
            font-size: 28px;
        }
        .message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.8;
            font-size: 16px;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        a {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #28a745;
            color: white;
        }
        .btn-primary:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .btn-secondary {
            background: #667eea;
            color: white;
        }
        .btn-secondary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 25px;
            text-align: left;
            border-radius: 4px;
            color: #333;
        }
        .info-box strong {
            color: #2196F3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>🎉 Bienvenue!</h1>
        <p class="message">
            Votre compte a été activé avec succès.<br>
            Vous pouvez maintenant accéder à votre compte et commencer vos achats.
        </p>
        
        <div class="info-box">
            <strong>✓ Compte vérifié</strong><br>
            Votre email a été confirmé et votre profil est actif.
        </div>
        
        <div class="button-group">
            <a href="affiche.php" class="btn-primary">🛍️ Voir les Produits</a>
            <a href="conexion.php" class="btn-secondary">🔐 Se Connecter</a>
        </div>
    </div>
</body>
</html>
