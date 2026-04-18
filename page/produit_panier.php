<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../logique/produit_panier.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$rows = getProduitPanierRows($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table produit_panier</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f7fb; }
        header { background: #1a4d80; color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; }
        header a { color: #fff; text-decoration: none; margin-left: 18px; }
        .container { max-width: 1080px; margin: 24px auto; padding: 0 16px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08); }
        th, td { padding: 14px 16px; border-bottom: 1px solid #e5e9ef; }
        th { background: #f1f5fb; text-align: left; }
    </style>
</head>
<body>
<header>
    <div><strong>Table produit_panier</strong></div>
    <div><a href="affiche.php">Retour au catalogue</a></div>
</header>
<div class="container">
    <?php if (empty($rows)): ?>
        <p>Aucun enregistrement dans produit_panier.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Panier</th>
                    <th>ID Produit</th>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo (int) $row['id']; ?></td>
                        <td><?php echo (int) $row['panier_id']; ?></td>
                        <td><?php echo (int) $row['produit_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['produit_nom'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo (int) $row['quantite']; ?></td>
                        <td><?php echo number_format($row['produit_prix'], 2, ',', ' '); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
     