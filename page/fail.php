<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur d'Inscription</title>
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
        .error-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
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
        .error-details {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 25px;
            color: #721c24;
            text-align: left;
        }
        .error-details strong {
            display: block;
            margin-bottom: 8px;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        a, button {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #dc3545;
            color: white;
        }
        .btn-primary:hover {
            background: #c82333;
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
        .help-text {
            margin-top: 20px;
            font-size: 13px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        
        <h1>❌ Erreur d'Inscription</h1>
        <p class="message">
            Nous avons rencontré un problème lors de la création de votre compte.
        </p>
        
        <div class="error-details">
            <strong>⚠️ Raison possible:</strong>
            <?php
            $error = isset($_GET['error']) ? (int)$_GET['error'] : 0;
            switch($error) {
                case 1:
                    echo "Email déjà utilisé ou données invalides.";
                    break;
                case 2:
                    echo "Impossible d'envoyer l'email de confirmation.";
                    break;
                default:
                    echo "Erreur interne du serveur. Veuillez réessayer.";
            }
            ?>
        </div>
        
        <div class="button-group">
            <a href="inscri.php" class="btn-primary">🔄 Recommencer l'Inscription</a>
        </div>

        <p class="help-text">
            Vous avez des questions? Contactez notre support.
        </p>
    </div>
</body>
</html>