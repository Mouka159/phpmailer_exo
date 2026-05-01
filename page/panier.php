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

$items = getCartItems($pdo);
$total = getCartTotal($pdo);
$message = trim((string) ($_GET['msg'] ?? ''));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f7fb; }
        header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; }
        header a { color: #fff; text-decoration: none; margin-left: 18px; }
        .container { max-width: 1080px; margin: 24px auto; padding: 0 16px; }
        .message { margin-bottom: 20px; padding: 14px 18px; background: #e7f7e2; border: 1px solid #b8dfb4; color: #225927; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08); }
        th, td { padding: 14px 16px; border-bottom: 1px solid #e5e9ef; }
        th { background: #f1f5fb; text-align: left; }
        .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .actions input[type="number"] { width: 72px; padding: 8px; border: 1px solid #d3d8de; border-radius: 6px; }
        .actions button { padding: 9px 12px; border: none; border-radius: 8px; background: #1a73e8; color: #fff; cursor: pointer; }
        .actions button.remove { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .summary { margin-top: 20px; padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08); display: flex; justify-content: space-between; align-items: center; }
        .summary button { padding: 12px 18px; border: none; border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; cursor: pointer; }
    </style>
</head>
<body>
<header>
    <div><strong>Panier</strong></div>
    <div><a href="affiche.php">Retour au catalogue</a></div>
</header>
<div class="container">
    <?php if ($message !== ''): ?>
        <div class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <p>Votre panier est vide.<br><a href="affiche.php">Retourner au catalogue</a></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nom'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo number_format($item['prix'], 2, ',', ' '); ?> FCFA</td>
                        <td>
                            <form class="actions" action="../logique/panier.php" method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="item_id" value="<?php echo (int)$item['item_id']; ?>">
                                <input type="number" name="quantite" min="1" value="<?php echo (int)$item['quantite']; ?>" required>
                                <button type="submit">Mettre à jour</button>
                            </form>
                        </td>
                        <td><?php echo number_format($item['total'], 2, ',', ' '); ?> FCFA</td>
                        <td>
                            <form action="../logique/panier.php" method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="item_id" value="<?php echo (int)$item['item_id']; ?>">
                                <button class="remove" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="summary">
            <div><strong>Total panier :</strong> <?php echo number_format($total, 2, ',', ' '); ?> FCFA</div>
            <div style="display: flex; gap: 10px;">
                <form action="../logique/panier.php" method="POST">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" style="background: #dc3545;">Vider le panier</button>
                </form>
                <a href="commande.php" style="padding: 12px 18px; border: none; border-radius: 8px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: #fff; text-decoration: none; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-shopping-cart"></i> Passer la commande
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
