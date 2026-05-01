<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function getProducts(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id_produit, nom, description, prix, stock, image_url FROM Produits ORDER BY id_produit');
    return $stmt->fetchAll();
}

function getCartId(PDO $pdo): int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $userId = (int)($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        die('User not authenticated. Please log in.');
    }

    $stmt = $pdo->prepare('SELECT id_panier FROM Panier WHERE utilisateur_id = :user_id LIMIT 1');
    $stmt->execute([':user_id' => $userId]);
    $cart = $stmt->fetch();

    if ($cart) {
        return (int) $cart['id_panier'];
    }

    $stmt = $pdo->prepare('INSERT INTO Panier (utilisateur_id) VALUES (:user_id)');
    $stmt->execute([':user_id' => $userId]);
    return (int) $pdo->lastInsertId();
}

function getCartItems(PDO $pdo): array
{
    $panierId = getCartId($pdo);
    $stmt = $pdo->prepare(
        'SELECT pp.id_panier_produit AS item_id, pp.quantite, p.id_produit AS produit_id, p.nom, p.description, p.prix, (p.prix * pp.quantite) AS total
         FROM Panier_Produits pp
         JOIN Produits p ON pp.produit_id = p.id_produit
         WHERE pp.panier_id = :panier_id'
    );
    $stmt->execute([':panier_id' => $panierId]);
    return $stmt->fetchAll();
}

function countCartItems(PDO $pdo): int
{
    $panierId = getCartId($pdo);
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantite), 0) AS total_qty FROM Panier_Produits WHERE panier_id = :panier_id');
    $stmt->execute([':panier_id' => $panierId]);
    $row = $stmt->fetch();
    return (int) $row['total_qty'];
}

function getCartTotal(PDO $pdo): float
{
    $panierId = getCartId($pdo);
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(p.prix * pp.quantite), 0) AS total_price
         FROM Panier_Produits pp
         JOIN Produits p ON pp.produit_id = p.id_produit
         WHERE pp.panier_id = :panier_id');
    $stmt->execute([':panier_id' => $panierId]);
    $row = $stmt->fetch();
    return (float) $row['total_price'];
}

function clearCart(PDO $pdo, int $userId): bool
{
    try {
        // Récupérer l'ID du panier de l'utilisateur
        $cartId = getCartId($pdo);

        // Supprimer tous les produits du panier
        $stmt = $pdo->prepare('DELETE FROM Panier_Produits WHERE panier_id = :panier_id');
        $stmt->execute([':panier_id' => $cartId]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

?>