<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 400px;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .form-container button {
            width: 50%;
            padding: 10px;
            background: #29b954;
            border: none;
            color: #fff;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
             text-align: center;
             margin-left: 90px;
        }
        .form-container button:hover {
            background: #90e4aa;  
        }
        .resend-btn {
            width: 100%;
            margin-top: 10px;
              background: #29b954;
        }
        .resend-btn:hover {
            background: #90e4aa;  
        }
    </style>
</head>
<body>
<?php
require_once('../config/db.php');
session_start();
$email = $_SESSION['email'] ?? '';
$expirationMessage = '';

if ($email) {
    try {
        $stmt = $pdo->prepare("SELECT expire_at FROM utilisateur WHERE email = :email ORDER BY id_uti DESC LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && strtotime($user['expire_at']) <= time()) {
            $expirationMessage = '<p style="color:red;">Code expiré. Cliquez sur "Renvoyer le code" pour recevoir un nouveau OTP.</p>';
        }
    } catch (PDOException $e) {
        // Si la requête échoue, on ne bloque pas la page.
    }
}
?>
<div class="form-container">
    <h2>Vérification</h2>
    <span> veuillez entrer le code OTP envoyé à votre email</span>
    <?php
    if ($expirationMessage) {
        echo $expirationMessage;
    }
    if (isset($_GET['sent'])) {
        if ($_GET['sent'] === '1') {
            echo '<p style="color:green;">Nouveau code envoyé. Vérifiez votre boîte mail.</p>';
        } else {
            echo '<p style="color:red;">Impossible de renvoyer le code. Réessayez.</p>';
        }
    }
    ?>
    <form action="../logique/verify.php" method="POST">
        <input type="text" name="otp" placeholder="Code OTP" required>
        <button type="submit" name="verify">Vérifier</button>
    </form>
    <form action="../page/sendmail.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <button type="submit" name="send" class="resend-btn">Renvoyer le code</button>
    </form>
</div>

</body>
</html>
