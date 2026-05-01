<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du Code OTP</title>
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
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            color: #333;
            display: block;
        }
        input[type="text"],
        input[type="hidden"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.5);
        }
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        button:hover {
            background: #218838;
        }
        .resend-btn {
            background: #007bff;
            margin-top: 10px;
        }
        .resend-btn:hover {
            background: #0056b3;
        }
        .info-text {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 15px;
        }
        a {
            color: #667eea;
            text-decoration: none;
            text-align: center;
            display: block;
            margin-top: 20px;
        }
        a:hover {
            text-decoration: underline;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🔐 Vérification de Compte</h2>
    <p class="subtitle">Entrez le code OTP reçu par email pour activer votre compte</p>
    
    <?php
    session_start();
    $email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : (isset($_SESSION['email']) ? $_SESSION['email'] : '');
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
    
    if ($error) {
        echo '<div class="error" style="display: block;">❌ ' . htmlspecialchars($error) . '</div>';
        unset($_SESSION['error']);
    }
    
    if (isset($_GET['sent']) && $_GET['sent'] === '1') {
        echo '<div class="success" style="display: block;">✅ Nouveau code envoyé. Vérifiez votre boîte mail.</div>';
    }
    ?>
    
    <form action="../logique/verify.php" method="POST">
        <div class="form-group">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <label for="otp">Code OTP (6 chiffres) :</label>
            <input type="text" id="otp" name="otp" placeholder="Ex: 123456" required maxlength="6" pattern="[0-9]{6}">
        </div>
        <button type="submit" name="verify">✓ Vérifier le Code</button>
    </form>
    
    <form action="../page/sendmail.php" method="POST">
        <input type="hidden" name="email" value="<?php echo $email; ?>">
        <button type="submit" name="send" class="resend-btn">🔄 Renvoyer le Code</button>
    </form>
    
    <p class="info-text">⏱️ Le code expire dans 2 minutes</p>

</div>
</body>
</html>
