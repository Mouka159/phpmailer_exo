<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/produit.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function addToCart(PDO $pdo, int $produitId, int $quantite = 1): string
{
    $stmt = $pdo->prepare('SELECT * FROM Produits WHERE id_produit = :id LIMIT 1');
    $stmt->execute([':id' => $produitId]);
    $product = $stmt->fetch();

    if (!$product) {
        return 'Produit introuvable.';
    }

    if ($quantite < 1) {
        return 'La quantité doit être au moins 1.';
    }

    if ($quantite > (int) $product['stock']) {
        return 'Quantité supérieure au stock disponible.';
    }

    $panierId = getCartId($pdo);
    $stmt = $pdo->prepare('SELECT id_panier_produit, quantite FROM Panier_Produits WHERE panier_id = :panier_id AND produit_id = :produit_id');
    $stmt->execute([':panier_id' => $panierId, ':produit_id' => $produitId]);
    $item = $stmt->fetch();

    if ($item) {
        $newQuantity = $item['quantite'] + $quantite;
        if ($newQuantity > (int) $product['stock']) {
            return 'Quantité totale dépasse le stock disponible.';
        }
        $stmt = $pdo->prepare('UPDATE Panier_Produits SET quantite = :quantite WHERE id_panier_produit = :id');
        $stmt->execute([':quantite' => $newQuantity, ':id' => $item['id_panier_produit']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO Panier_Produits (panier_id, produit_id, quantite) VALUES (:panier_id, :produit_id, :quantite)');
        $stmt->execute([':panier_id' => $panierId, ':produit_id' => $produitId, ':quantite' => $quantite]);
    }

    return 'Produit ajouté au panier.';
}

function updateCartItem(PDO $pdo, int $itemId, int $quantite): string
{
    if ($quantite < 1) {
        return removeCartItem($pdo, $itemId);
    }

    $stmt = $pdo->prepare('SELECT pp.produit_id, p.stock FROM Panier_Produits pp JOIN Produits p ON pp.produit_id = p.id_produit WHERE pp.id_panier_produit = :id');
    $stmt->execute([':id' => $itemId]);
    $row = $stmt->fetch();

    if (!$row) {
        return 'Élément du panier introuvable.';
    }

    if ($quantite > (int) $row['stock']) {
        return 'Quantité supérieure au stock disponible.';
    }

    $stmt = $pdo->prepare('UPDATE Panier_Produits SET quantite = :quantite WHERE id_panier_produit = :id');
    $stmt->execute([':quantite' => $quantite, ':id' => $itemId]);
    return 'Quantité mise à jour.';
}

function removeCartItem(PDO $pdo, int $itemId): string
{
    $stmt = $pdo->prepare('DELETE FROM Panier_Produits WHERE id_panier_produit = :id');
    $stmt->execute([':id' => $itemId]);
    return 'Produit retiré du panier.';
}

function clearCart(PDO $pdo): string
{
    $panierId = getCartId($pdo);
    $stmt = $pdo->prepare('DELETE FROM Panier_Produits WHERE panier_id = :panier_id');
    $stmt->execute([':panier_id' => $panierId]);
    return 'Le panier a été vidé.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $message = '';
    $redirect = '../page/affiche.php';

    switch ($_POST['action']) {
        case 'add':
            $productId = (int) ($_POST['produit_id'] ?? 0);
            $quantity = max(1, (int) ($_POST['quantite'] ?? 1));
            $message = addToCart($pdo, $productId, $quantity);
            $redirect = '../page/affiche.php';
            break;
        case 'update':
            $itemId = (int) ($_POST['item_id'] ?? 0);
            $quantity = max(0, (int) ($_POST['quantite'] ?? 1));
            $message = updateCartItem($pdo, $itemId, $quantity);
            $redirect = '../page/panier.php';
            break;
        case 'remove':
            $itemId = (int) ($_POST['item_id'] ?? 0);
            $message = removeCartItem($pdo, $itemId);
            $redirect = '../page/panier.php';
            break;
        case 'clear':
            $message = clearCart($pdo);
            $redirect = '../page/panier.php';
            break;
    }

    header('Location: ' . $redirect . '?msg=' . urlencode($message));
    exit();
}
?>