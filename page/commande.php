<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../logique/produit.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: conexion.php');
    exit();
}

// Récupérer les informations du panier
$cartItems = getCartItems($pdo);
$cartTotal = getCartTotal($pdo);

// Récupérer les informations utilisateur
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT nom, prenom, email, telephone, adresse FROM utilisateur WHERE id = :id");
$stmt->execute([':id' => $userId]);
$userInfo = $stmt->fetch();

// Rediriger si panier vide
if (empty($cartItems)) {
    header('Location: panier.php?msg=Votre panier est vide. Ajoutez des produits avant de commander.');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande avec Livraison - Ecommerce</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0px 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h2 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .cart-summary {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .cart-summary h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-total {
            font-weight: bold;
            font-size: 18px;
            color: #28a745;
            text-align: right;
            margin-top: 15px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h2 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        form {
            padding: 40px;
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 20px;
            width: 100%;
        }
        label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }
        input:focus, select:focus, textarea:focus {
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
        button:active {
            transform: translateY(0);
        }
        .icon {
            display: inline-block;
            margin-right: 8px;
            color: #667eea;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-shopping-cart icon"></i>Passer une Commande</h2>
            <p>Informations de livraison et détails de commande</p>
        </div>

        <!-- Résumé du panier -->
        <div class="cart-summary">
            <h3><i class="fas fa-list icon"></i>Résumé de votre commande</h3>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <span><?php echo htmlspecialchars($item['nom'], ENT_QUOTES, 'UTF-8'); ?> (x<?php echo $item['quantite']; ?>)</span>
                    <span><?php echo number_format($item['total'], 2, ',', ' '); ?> FCFA</span>
                </div>
            <?php endforeach; ?>
            <div class="cart-total">
                Total : <?php echo number_format($cartTotal, 2, ',', ' '); ?> FCFA
            </div>
        </div>

        <form action="traitement_commande.php" method="post">
            <!-- Informations client (pré-remplies) -->
            <div class="form-group">
                <label for="nom"><i class="fas fa-user icon"></i>Nom complet *</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($userInfo['prenom'] . ' ' . $userInfo['nom'], ENT_QUOTES, 'UTF-8'); ?>" required readonly>
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope icon"></i>Email *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email'], ENT_QUOTES, 'UTF-8'); ?>" required readonly>
            </div>

            <div class="form-group">
                <label for="telephone"><i class="fas fa-phone icon"></i>Téléphone *</label>
                <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($userInfo['telephone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Entrez votre numéro de téléphone" required>
            </div>

            <h3 style="color: #667eea; margin: 30px 0 20px 0; font-size: 18px;"><i class="fas fa-truck icon"></i>Informations de Livraison</h3>

            <div class="form-group">
                <label for="adresse"><i class="fas fa-map-marker-alt icon"></i>Adresse de livraison *</label>
                <input type="text" id="adresse" name="adresse" placeholder="Entrez votre adresse complète" required>
            </div>

            <div class="form-group">
                <label for="ville"><i class="fas fa-city icon"></i>Ville *</label>
                <input type="text" id="ville" name="ville" placeholder="Entrez votre ville" required>
            </div>

            <div class="form-group">
                <label for="code_postal"><i class="fas fa-mailbox icon"></i>Code postal *</label>
                <input type="text" id="code_postal" name="code_postal" placeholder="Entrez votre code postal" required>
            </div>

            <div class="form-group">
                <label for="notes"><i class="fas fa-sticky-note icon"></i>Notes supplémentaires (optionnel)</label>
                <textarea id="notes" name="notes" placeholder="Instructions spéciales pour la livraison..." rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: all 0.3s; resize: vertical;"></textarea>
            </div>

            <button type="submit"><i class="fas fa-paper-plane icon"></i>Passer la Commande</button>
        </form>

        <div class="back-link">
            <a href="affiche.php"><i class="fas fa-arrow-left"></i> Retour aux produits</a>
        </div>
    </div>
</body>
</html>