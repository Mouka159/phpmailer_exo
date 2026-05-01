<?php
session_start();
include('../config/db.php'); // Connexion PDO

$message = trim((string) ($_GET['msg'] ?? ''));

try {
    // Récupération des produits
    $stmt = $pdo->query('SELECT id_produit, nom, description, prix, stock, image_url 
                         FROM Produits 
                         ORDER BY id_produit DESC');
    $produits = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Erreur de requête : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue Produits</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fb; margin: 0; padding: 0; }
        header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; }
        header a { color: #fff; text-decoration: none; margin-left: 18px; }
        .container { max-width: 1080px; margin: 24px auto; padding: 0 16px; }
        .message { margin-bottom: 20px; padding: 14px 18px; background: #e7f7e2; border: 1px solid #b8dfb4; color: #225927; border-radius: 8px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); padding: 20px; }
        .card img { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 12px; }
        .card h2 { margin: 0 0 10px; font-size: 1.3rem; color: #20335c; }
        .card p { margin: 10px 0; color: #555; line-height: 1.5; }
        .card strong { display: block; margin: 10px 0; font-size: 1rem; }
        .card form { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-top: 14px; }
        .card input[type="number"] { width: 70px; padding: 10px; border: 1px solid background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);; border-radius: 6px; }
        .card button { flex: 1; min-width: 120px; padding: 11px 14px; border: none; border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; cursor: pointer; }
        .card button:disabled { background: #8dabd6; cursor: not-allowed; }
    </style>
</head>
<body>
<header>
    <div><strong>Catalogue Produits</strong></div>
    <div><a href="panier.php">Voir mon panier</a></div>
</header>
<div class="container">
    <?php if ($message !== ''): ?>
        <div class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (empty($produits)): ?>
        <p>Aucun produit trouvé dans la base de données.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($produits as $produit): ?>
                <div class="card">
                    <?php if (!empty($produit['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($produit['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($produit['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <strong>Prix : <?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</strong>
                    <p>Stock : <?php echo (int) $produit['stock']; ?></p>
                    <form action="../logique/panier.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="produit_id" value="<?php echo (int) $produit['id_produit']; ?>">
                        <input type="number" name="quantite" min="1" max="<?php echo (int) $produit['stock']; ?>" value="1" required <?php echo ((int) $produit['stock'] === 0) ? 'disabled' : ''; ?>>
                        <button type="submit" <?php echo ((int) $produit['stock'] === 0) ? 'disabled' : ''; ?>>Ajouter au panier</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
