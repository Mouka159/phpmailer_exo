<?php
session_start();
require('../config/db.php');
require('../logique/produit.php');

// Vérifier si l'utilisateur est connecté
//if (!isset($_SESSION['user_id'])) {
    //header('Location: conexion.php?message=Veuillez vous connecter pour passer une commande.');
   // exit();
//}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Récupérer les informations du panier
    $cartItems = getCartItems($pdo);
    $cartTotal = getCartTotal($pdo);

    // Validation des données
    if (empty($telephone) || empty($adresse) || empty($ville) || empty($code_postal)) {
        $message = "Tous les champs de livraison doivent être remplis.";
    } elseif (empty($cartItems)) {
        $message = "Votre panier est vide.";
    } else {
        try {
            // Commencer une transaction
            $pdo->beginTransaction();

            // Adresse de livraison complète
            $adresse_livraison = $adresse . ', ' . $ville . ' ' . $code_postal;

            // Insérer la commande
            $stmt = $pdo->prepare("
                INSERT INTO Commandes (utilisateur_id, montant_total, adresse_livraison, telephone, notes, statut)
                VALUES (?, ?, ?, ?, ?, 'En attente')
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $cartTotal,
                $adresse_livraison,
                $telephone,
                $notes
            ]);

            $commande_id = $pdo->lastInsertId();

            // Insérer les produits de la commande
            foreach ($cartItems as $item) {
                $stmtProduit = $pdo->prepare("
                    INSERT INTO Commande_Produits (commande_id, produit_id, quantite, prix_unitaire, prix_total)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtProduit->execute([
                    $commande_id,
                    $item['id_produit'],
                    $item['quantite'],
                    $item['prix'],
                    $item['total']
                ]);
            }

            // Vider le panier après la commande
            clearCart($pdo, $_SESSION['user_id']);

            // Valider la transaction
            $pdo->commit();

            $message = "Commande passée avec succès ! Vous recevrez un email de confirmation.";
            header('Location: success.php?message=' . urlencode($message) . '&type=order');
            exit();

        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            $message = "Erreur lors de la création de la commande : " . $e->getMessage();
        }
    }
}

// Rediriger vers la page de commande avec le message d'erreur
if (!empty($message)) {
    header('Location: commande.php?message=' . urlencode($message));
    exit();
}
?>
                $adresse_livraison,
                $telephone,
                $notes
            ]);

            $commande_id = $pdo->lastInsertId();

            // Insérer les produits de la commande
            $stmt_produit = $pdo->prepare("
                INSERT INTO Commande_Produits (commande_id, produit_id, quantite, prix_unitaire, prix_total)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt_produit->execute([
                $commande_id,
                1, // ID du produit (à adapter selon votre logique)
                $quantite,
                $prix_unitaire,
                $montant_total
            ]);

            // Valider la transaction
            $pdo->commit();

            $message = "Votre commande a été passée avec succès ! ID de commande : " . $commande_id;

        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            $message = "Erreur lors de la création de la commande : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traitement Commande - Ecommerce</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0px 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            text-align: center;
            padding: 40px;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        .back-link {
            margin-top: 30px;
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-check-circle success"></i> Traitement de la Commande</h2>

        <?php if (!empty($message)): ?>
            <p class="<?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php else: ?>
            <p>Une erreur inattendue s'est produite.</p>
        <?php endif; ?>

        <a href="affiche.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour aux produits
        </a>
    </div>
</body>
</html>