<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Autoriser uniquement les administrateurs
if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? 'user') !== 'admin')) {
    header('Location: conexion.php?message=' . urlencode('Accès refusé. Vous devez être administrateur.'));
    exit();
}

// Pagination
$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

try {

    // Si la table a une colonne date_creation, on l'utilise.
    // Sinon, on se base sur l'id.
    $hasDateCreation = false;
    try {
        $colStmt = $pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'date_creation'");
        $hasDateCreation = (bool)$colStmt->fetchColumn();
    } catch (Throwable $e) {
        $hasDateCreation = false;
    }

    // Total pour pagination
    $totalClients = 0;
    try {
        $cntStmt = $pdo->query("SELECT COUNT(*) AS c FROM utilisateur");
        $totalClients = (int)($cntStmt->fetch()['c'] ?? 0);
    } catch (Throwable $e2) {
        $totalClients = 0;
    }
    $totalPages = max(1, (int)ceil($totalClients / $perPage));
    $page = min($page, $totalPages);

    if ($hasDateCreation) {
        $stmt = $pdo->prepare("SELECT id, nom, prenom, email, telephone FROM utilisateur ORDER BY date_creation DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT id, nom, prenom, email, telephone FROM utilisateur ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
    }

    $clients = $stmt->fetchAll();
} catch (Throwable $e) {

    $clients = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client récent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root{
            --primary:#667eea;--secondary:#764ba2;--bg:#f8fafc;--text:#1f2937;--muted:#6b7280;
            --card:#ffffff;--shadow:0 18px 50px rgba(15,23,42,.08);
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;background:var(--bg);color:var(--text)}
        .container{max-width:1000px;margin:0 auto;padding:26px}
        .title{display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:18px}
        h1{margin:0;font-size:1.8rem}
        .pill{background:rgba(102,126,234,.12);color:var(--primary);padding:10px 14px;border-radius:999px;font-weight:700}
        .grid{display:flex;flex-direction:row;gap:16px;align-items:flex-start}
        .grid > .card{flex:1;min-width:0}
        @media(max-width:860px){.grid{flex-direction:column}}

        .card{background:var(--card);border-radius:16px;box-shadow:var(--shadow);padding:18px}
        .client-item{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:10px}
        .client-item:last-child{margin-bottom:0}
        .client-name{font-weight:800}
        .btn{cursor:pointer;border:none;border-radius:10px;padding:10px 14px;font-weight:800;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}
        .btn-secondary{background:#e5e7eb;color:var(--text)}
        .details-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
        .detail{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:10px 12px;min-width:210px}
        .detail b{display:block;margin-bottom:4px}
        .muted{color:var(--muted)}
        .empty{padding:22px;color:var(--muted);text-align:center}
    </style>
</head>
<body>
    <div style="margin-top:14px;">
                <a class="btn" style="display:inline-block;text-decoration:none" href="admis.php"><i class="fas fa-arrow-left"></i> Retour</a>
            </div>
<div class="container">
    <div class="title">

        <h1><i class="fas fa-user-clock"></i> Client récent</h1>
        <div class="pill">Cliquez sur un client</div>
    </div>

    <div class="grid">
        <div class="card">
            <h2 style="margin:0 0 14px; font-size:1.2rem;">Liste des clients</h2>
            <?php if (empty($clients)): ?>
                <div class="empty">Aucun client trouvé.</div>
            <?php else: ?>
                <?php foreach ($clients as $c): ?>
                    <div class="client-item">
                        <div>
                            <div class="client-name">
                                <?php echo htmlspecialchars(trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="muted" style="font-size:.95rem;">#<?php echo (int)$c['id']; ?></div>
                        </div>
                        <button
                            class="btn btn-secondary client-btn"
                            type="button"
                            data-id="<?php echo (int)$c['id']; ?>"
                            data-nom="<?php echo htmlspecialchars($c['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            data-prenom="<?php echo htmlspecialchars($c['prenom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            data-email="<?php echo htmlspecialchars($c['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            data-telephone="<?php echo htmlspecialchars($c['telephone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        >Voir</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Pagination -->
            <div style="margin-top:14px; display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                <?php if ($page > 1): ?>
                    <a class="btn btn-secondary" href="client_recent.php?page=<?php echo $page-1; ?>" style="text-decoration:none; display:inline-block;">&larr; Précédent</a>
                <?php else: ?>
                    <span class="muted">&larr; Précédent</span>
                <?php endif; ?>

                <div class="muted" style="font-weight:700;">Page <?php echo (int)$page; ?> / <?php echo (int)$totalPages; ?></div>

                <?php if ($page < $totalPages): ?>
                    <a class="btn btn-secondary" href="client_recent.php?page=<?php echo $page+1; ?>" style="text-decoration:none; display:inline-block;">Suivant &rarr;</a>
                <?php else: ?>
                    <span class="muted">Suivant &rarr;</span>
                <?php endif; ?>
            </div>
        </div>


        <div class="card">
            <h2 style="margin:0 0 14px; font-size:1.2rem;">Informations client</h2>

            <div class="details-row" style="margin-top:0;">
                <div class="detail">
                    <b>Nom complet</b>
                    <div id="out-name" class="muted">—</div>
                </div>
                <div class="detail">
                    <b>Téléphone</b>
                    <div id="out-phone" class="muted">—</div>
                </div>
                <div class="detail">
                    <b>Email</b>
                    <div id="out-email" class="muted">—</div>
                </div>
            </div>

            <div style="margin-top:14px;">
                <div class="detail" style="min-width:100%">
                    <b>ID client</b>
                    <div id="out-id" class="muted">—</div>
                </div>
            </div>

            <div style="margin-top:14px;" class="muted">
                Astuce : pour afficher les données, cliquez sur le bouton <b>Voir</b> de chaque client.
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const buttons = document.querySelectorAll('.client-btn');
    const outName = document.getElementById('out-name');
    const outPhone = document.getElementById('out-phone');
    const outEmail = document.getElementById('out-email');
    const outId = document.getElementById('out-id');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const nom = btn.getAttribute('data-nom') || '';
            const prenom = btn.getAttribute('data-prenom') || '';
            const email = btn.getAttribute('data-email') || '';
            const tel = btn.getAttribute('data-telephone') || '';
            const id = btn.getAttribute('data-id') || '';

            const full = (prenom + ' ' + nom).trim();
            outName.textContent = full || '—';
            outPhone.textContent = tel || '—';
            outEmail.textContent = email || '—';
            outId.textContent = id || '—';
        });
    });
})();
</script>
</body>
</html>

