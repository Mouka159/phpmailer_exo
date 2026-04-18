<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function getProduitPanierRows(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT pp.id_panier_produit AS id, pp.panier_id, pp.produit_id, pp.quantite, p.nom AS produit_nom, p.prix AS produit_prix
         FROM Panier_Produits pp
         JOIN Produits p ON pp.produit_id = p.id_produit
         ORDER BY pp.id_panier_produit'
    );
    return $stmt->fetchAll();
}
?>