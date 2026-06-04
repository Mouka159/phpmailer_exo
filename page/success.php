<?php
$type = $_GET['type'] ?? 'account';
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte Activé avec Succès</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .success-section, .login-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0px 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .success-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .success-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .success-content {
            padding: 40px;
            text-align: center;
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
        .message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.8;
            font-size: 16px;
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
        .login-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .login-form {
            padding: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 40px;
        }
        .btn-primary, .btn-secondary {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s;
        }
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            .success-content, .login-form, .button-group {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($type === 'order'): ?>
            <!-- Success Order -->
            <div class="success-section">
                <div class="success-header">
                    <h1>✅ Commande Réussie !</h1>
                </div>
                <div class="success-content">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="message">
                        <?php echo htmlspecialchars($message ?: 'Votre commande a été enregistrée avec succès.'); ?><br>
                        Vous recevrez un email de confirmation dans les plus brefs délais.
                    </p>
                    <div class="info-box">
                        <strong>📦 Statut de votre commande</strong><br>
                        Votre commande est en cours de traitement. Vous serez informé de son évolution.
                    </div>
                    <div class="button-group">
                        <a href="compte.php" class="btn-primary">Voir mes commandes</a>
                        <a href="affiche.php" class="btn-secondary">Continuer mes achats</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Success Account + Login Form -->
            <div class="success-section">
                <div class="success-header">
                    <h1>✅ Bienvenue !</h1>
                </div>
                <div class="success-content">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="message">
                        <?php echo htmlspecialchars($message ?: 'Votre compte a été activé avec succès.'); ?><br>
                        Connectez-vous dès maintenant pour commencer vos achats.
                    </p>
                    <div class="info-box">
                        <strong>✓ Compte vérifié</strong><br>
                        Votre email a été confirmé et votre profil est actif.
                    </div>
                    <div class="login">
                        <button><a href="conexion.php">Connectez-vous</a></button> 
                    </div>
                </div>
                </form>
            </div>
            
            <!-- Login Form Integrated -->
            <!--div class="login-section">
                <div class="login-header">
                    <h2><i class="fas fa-sign-in-alt"></i> Connexion Rapide</h2>
                </div>
                <form class="login-form" action="../logique/connexion.php" method="POST">
                    <div class="form-group">
                        <label for="nom"><i class="fas fa-user icon"></i> Nom *</label>
                        <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope icon"></i> Email *</label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock icon"></i> Mot de Passe *</label>
                        <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                    </div>
                    <button type="submit" name="login">Se Connecter</button>
                    <div class="signup-link">
                        Pas encore inscrit ? <a href="inscri.php">Créer un compte</a>
                    </div>
                </form>
            </div-->
        <?php endif; ?>
    </div>
</body>
</html>
