<?php
session_start();
include('../config/db.php'); // Connexion PDO
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'user') !== 'admin') {
    header('Location: conexion.php?message=Accès refusé. Vous devez être administrateur.');
    exit();
}

$message = '';
if (isset($_POST['mark_delivered'])) {
    $orderId = (int) ($_POST['order_id'] ?? 0);

    try {
        $pdo->beginTransaction();

        $stmtOrder = $pdo->prepare("SELECT c.id_commande, c.utilisateur_id, c.montant_total, c.adresse_livraison, c.telephone, c.statut, u.nom, u.prenom, u.email
            FROM Commandes c
            JOIN utilisateur u ON c.utilisateur_id = u.id
            WHERE c.id_commande = ?");
        $stmtOrder->execute([$orderId]);
        $commande = $stmtOrder->fetch();

        if (!$commande) {
            throw new Exception('Commande introuvable.');
        }

        if ($commande['statut'] === 'Livrée') {
            throw new Exception('Cette commande est déjà marquée comme livrée.');
        }

        $update = $pdo->prepare("UPDATE Commandes SET statut = 'Livrée' WHERE id_commande = ?");
        $update->execute([$orderId]);

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'moukailatovo9@gmail.com';
        $mail->Password   = 'rwbhzklhqnjbxixw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('moukailatovo9@gmail.com', 'ShopESA');
        $mail->addAddress($commande['email'], $commande['prenom'] . ' ' . $commande['nom']);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de livraison de votre commande #' . $commande['id_commande'];
        $mail->Body    = "Bonjour " . htmlspecialchars($commande['prenom']) . ",<br><br>Votre commande <strong>#" . $commande['id_commande'] . "</strong> a été livrée.<br><br>Merci d'avoir choisi ShopESA.<br><br>Cordialement,<br>L'équipe ShopESA";

        $mail->send();

        $pdo->commit();
        $message = 'Commande #' . $orderId . ' marquée livrée et email envoyé au client.';
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = 'Erreur lors de la livraison : ' . $e->getMessage();
    }
    header('Location: commandes_jour.php?message=' . urlencode($message));
    exit();
}

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

// Récupérer les commandes du jour
try {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("
        SELECT c.*, u.nom, u.prenom, u.email, u.telephone
        FROM Commandes c
        JOIN utilisateur u ON c.utilisateur_id = u.id
        WHERE DATE(c.date_commande) = ?
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute([$today]);
    $commandes = $stmt->fetchAll();

    // Pour chaque commande, récupérer les produits
    foreach ($commandes as &$commande) {
        $stmtProduits = $pdo->prepare("
            SELECT cp.*, p.nom as nom_produit, p.prix
            FROM Commande_Produits cp
            JOIN Produits p ON cp.produit_id = p.id_produit
            WHERE cp.commande_id = ?
        ");
        $stmtProduits->execute([$commande['id_commande']]);
        $commande['produits'] = $stmtProduits->fetchAll();
    }
} catch (PDOException $e) {
    $message = 'Erreur lors de la récupération des commandes: ' . $e->getMessage();
    $commandes = [];
}

$totalCommandes = count($commandes);
$totalMontant = array_sum(array_column($commandes, 'montant_total'));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes du jour - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-strong: #f8fafc;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #4f46e5;
            --primary-soft: #eef2ff;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #f59e0b;
            --shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; }

        .layout { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
        .sidebar { background: #111827; color: #f9fafb; padding: 28px 22px; display: flex; flex-direction: column; }
        .brand { font-size: 1.4rem; font-weight: 800; letter-spacing: 0.03em; margin-bottom: 28px; }
        .nav-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
        .nav-item { border-radius: 14px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 14px 16px; color: #d1d5db; border-radius: 14px; transition: background .2s, color .2s; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.08); color: #fff; }
        .nav-link span { font-weight: 600; }
        .sidebar-footer { margin-top: auto; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.12); font-size: .92rem; color: #9ca3af; }

        .main { padding: 28px; }
        .header { display: flex; justify-content: space-between; align-items: center; gap: 18px; margin-bottom: 28px; }
        .header h1 { margin: 0; font-size: 2rem; letter-spacing: -0.03em; }
        .header .badge { background: var(--primary-soft); color: var(--primary); padding: 10px 16px; border-radius: 999px; font-weight: 700; }

        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px; }
        .stat-card { background: var(--surface); padding: 24px; border-radius: 16px; box-shadow: var(--shadow); text-align: center; }
        .stat-card strong { display: block; font-size: 2rem; font-weight: 800; color: var(--primary); margin-bottom: 4px; }
        .stat-card span { color: var(--muted); font-size: .9rem; }

        .card { background: var(--surface); border-radius: 24px; padding: 28px; box-shadow: var(--shadow); margin-bottom: 24px; }
        .card h2 { margin-top: 0; margin-bottom: 18px; font-size: 1.25rem; }

        .commande-item { border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 16px; background: var(--surface-strong); }
        .commande-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .commande-info { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 12px; }
        .commande-info div { font-size: .9rem; }
        .commande-info strong { color: var(--text); }
        .produits-list { background: #f9fafb; padding: 12px; border-radius: 8px; margin-top: 12px; }
        .produit-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .produit-item:last-child { border-bottom: none; }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-en-attente { background: var(--warning); color: #92400e; }
        .status-expediee { background: var(--primary-soft); color: var(--primary); }
        .status-livree { background: var(--success); color: #fff; }

        .message { border-radius: 20px; padding: 18px 22px; background: #ecfdf5; border: 1px solid #d1fae5; color: #065f46; margin-bottom: 24px; }
        .no-commands { text-align: center; padding: 40px; color: var(--muted); font-size: 1.1rem; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; text-decoration: none; transition: all .2s; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-secondary { background: #e5e7eb; color: var(--text); }

        /* RESPONSIVE */
        .hamburger {
          display: none;
          position: fixed;
          top: 20px;
          left: 20px;
          z-index: 1001;
          flex-direction: column;
          cursor: pointer;
          padding: 10px;
          background: rgba(0,0,0,0.5);
          border-radius: 5px;
        }
        .hamburger span {
          width: 25px;
          height: 3px;
          background: white;
          margin: 3px 0;
          transition: 0.3s;
        }
        @media (max-width: 768px) {
          .layout {
            grid-template-columns: 1fr;
          }
          .sidebar {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100vh;
            z-index: 1000;
            transition: left 0.3s;
          }
          .sidebar.show {
            left: 0;
          }
          .hamburger {
            display: flex;
          }
          .main {
            padding: 20px;
          }
        }
    </style>
</head>
<body>
    <div class="hamburger" onclick="toggleSidebar()">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">Admin Dashboard</div>
            <nav>
                <ul class="nav-list">
                    <li class="nav-item"><a class="nav-link" href="admis.php"><span>📦 Produits</span></a></li>
                    <li class="nav-item"><a class="nav-link active" href="commandes_jour.php"><span>📋 Commandes du jour</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="affiche.php"><span>🛍️ Catalogue client</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="page/panier.php"><span>🛒 Panier</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><span>⚙️ Paramètres</span></a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">Consultez et gérez les commandes de la journée.</div>
        </aside>

        <main class="main">
            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <div class="header">
                <div>
                    <h1>📋 Commandes du jour</h1>
                    <p style="margin: 8px 0 0; color: var(--muted);">Commandes passées aujourd'hui - <?php echo date('d/m/Y'); ?></p>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a class="btn btn-secondary" href="admis.php"><i class="fas fa-arrow-left"></i> Retour</a>
                </div>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <strong><?php echo $totalCommandes; ?></strong>
                    <span>Commandes aujourd'hui</span>
                </div>
                <div class="stat-card">
                    <strong><?php echo number_format($totalMontant, 2, ',', ' '); ?>FCFA</strong>
                    <span>Chiffre d'affaires du jour</span>
                </div>
                <div class="stat-card">
                    <strong><?php echo $totalCommandes > 0 ? number_format($totalMontant / $totalCommandes, 2, ',', ' ') : '0,00'; ?>FCFA</strong>
                    <span>Panier moyen</span>
                </div>
            </div>

            <div class="card">
                <h2>Détail des commandes</h2>

                <?php if (empty($commandes)): ?>
                    <div class="no-commands">
                        <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--muted); margin-bottom: 16px;"></i>
                        <p>Aucune commande passée aujourd'hui.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($commandes as $commande): ?>
                        <div class="commande-item">
                            <div class="commande-header">
                                <h3>Commande #<?php echo $commande['id_commande']; ?></h3>
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '', $commande['statut'])); ?>">
                                    <?php echo $commande['statut']; ?>
                                </span>
                            </div>

                            <div class="commande-info">
                                <div><strong>Client:</strong> <?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div><strong>Email:</strong> <?php echo htmlspecialchars($commande['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div><strong>Téléphone:</strong> <?php echo htmlspecialchars($commande['telephone'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div><strong>Date:</strong> <?php echo date('H:i', strtotime($commande['date_commande'])); ?></div>
                                <div><strong>Montant:</strong> <?php echo number_format($commande['montant_total'], 2, ',', ' '); ?> FCFA</div>
                            </div>

                            <div><strong>Adresse de livraison:</strong> <?php echo htmlspecialchars($commande['adresse_livraison'], ENT_QUOTES, 'UTF-8'); ?></div>

                            <?php if (!empty($commande['notes'])): ?>
                                <div style="margin-top: 8px;"><strong>Notes:</strong> <?php echo htmlspecialchars($commande['notes'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>

                            <div class="produits-list">
                                <strong>Produits commandés:</strong>
                                <?php foreach ($commande['produits'] as $produit): ?>
                                    <div class="produit-item">
                                        <span><?php echo htmlspecialchars($produit['nom_produit'], ENT_QUOTES, 'UTF-8'); ?> (x<?php echo $produit['quantite']; ?>)</span>
                                        <span><?php echo number_format($produit['prix_total'], 2, ',', ' '); ?> FCFA</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($commande['statut'] !== 'Livrée'): ?>
                                <form method="post" style="margin-top: 16px;">
                                    <input type="hidden" name="order_id" value="<?php echo $commande['id_commande']; ?>">
                                    <button type="submit" name="mark_delivered" class="btn btn-primary">Marquer comme livrée</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    function toggleSidebar() {
      const sidebar = document.querySelector('.sidebar');
      sidebar.classList.toggle('show');
    }
    </script>

</body>
</html>