<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'user') !== 'admin') {
    header('Location: conexion.php?message=Accès refusé');
    exit();
}

$hasCategories = false;
$categories = [];
$totalVente = 0;

try {
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'categories'");
    if ($tableCheck->fetchColumn()) {
        $columnCheck = $pdo->query("SHOW COLUMNS FROM Produits LIKE 'id_categorie'");
        if ($columnCheck->fetchColumn()) {
            $hasCategories = true;
            $categories = $pdo->query('SELECT id_categorie, nom FROM categories ORDER BY nom')->fetchAll();
        }
    }
} catch (PDOException $e) {
    $hasCategories = false;
}

$sql = 'SELECT p.id_produit, p.nom, p.description, p.prix, p.stock, p.image_url' . ($hasCategories ? ', p.id_categorie, c.nom AS categorie_nom' : '') . ' FROM Produits p' . ($hasCategories ? ' LEFT JOIN categories c ON p.id_categorie = c.id_categorie' : '') . ' WHERE p.stock > 0 ORDER BY p.id_produit DESC';
$produits = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Produits Disponibles - Admin</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    /* Copie du CSS de admis.php pour cohérence */
    :root {
      --primary: #667eea;
      --primary-dark: #5568d3;
      --secondary: #764ba2;
      --success: #48bb78;
      --danger: #f56565;
      --light: #f7fafc;
      --dark: #1a202c;
      --text: #2d3748;
      --muted: #718096;
      --border: #e2e8f0;
      --shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
      --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    * { box-sizing: border-box; }
    body { 
      margin: 0; 
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: var(--text);
      min-height: 100vh;
    }

    .main { 
      padding: 40px;
      background: var(--light);
      overflow-y: auto;
    }

    .header { 
      text-align: center; 
      margin-bottom: 40px;
    }

    .header h1 { 
      margin: 0; 
      font-size: 2.5rem; 
      color: var(--dark);
      font-weight: 800;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 24px;
      background: white;
      color: var(--text);
      border: 2px solid var(--border);
      border-radius: 12px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-sm);
      margin-bottom: 20px;
    }

    .back-btn:hover {
      border-color: var(--primary);
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }

    .stats { 
      display: grid; 
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
      gap: 20px; 
      margin-bottom: 40px;
    }

    .stat-card { 
      background: white;
      border-radius: 18px; 
      padding: 28px;
      text-align: center;
      box-shadow: var(--shadow-sm);
      border-left: 5px solid var(--success);
    }

    .stat-card strong { 
      display: block; 
      font-size: 2.2rem; 
      color: var(--success);
      font-weight: 900;
    }

    .grid-products { 
      display: grid; 
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
      gap: 20px;
    }

    .produit-card { 
      background: white;
      border-radius: 16px; 
      overflow: hidden; 
      box-shadow: var(--shadow-sm);
      transition: all 0.3s ease;
    }

    .produit-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow);
    }

    .produit-card img { 
      width: 100%; 
      height: 160px; 
      object-fit: cover;
    }

    .produit-card-body { 
      padding: 18px; 
    }

    .produit-card h3 { 
      margin: 0 0 10px; 
      font-size: 1rem;
      color: var(--dark);
      font-weight: 700;
    }

    .produit-card p { 
      color: var(--muted); 
      font-size: .85rem; 
      margin-bottom: 10px;
    }

    .produit-meta { 
      color: var(--primary);
      font-weight: 700;
      margin-bottom: 5px;
    }

    .produit-actions { 
      display: flex; 
      gap: 8px;
      margin-top: 12px;
    }

    .produit-actions a, .produit-actions button { 
      flex: 1;
      padding: 8px 12px;
      border-radius: 8px;
      font-size: .8rem;
      font-weight: 600;
      text-align: center;
      border: none;
      cursor: pointer;
    }

    .produit-actions a { 
      background: linear-gradient(135deg, var(--success), #38a169);
      color: white; 
    }

    .produit-actions button { 
      background: linear-gradient(135deg, var(--danger), #e53e3e);
      color: white;
    }

    .no-products {
      text-align: center;
      padding: 60px 20px;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
      border-radius: 20px;
      border: 2px dashed var(--border);
    }

    @media (max-width: 768px) {
      .grid-products { 
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      }
    }

    @media (max-width: 480px) {
      .grid-products { 
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <main class="main">
    <div class="header">
      <h1>📦 Produits Disponibles</h1>
      <p><?php echo count($produits); ?> produits en stock</p>
    </div>

    <a href="admis.php" class="back-btn">
      <i class="fas fa-arrow-left"></i>
      Retour à la gestion
    </a>

    <div class="stats">
      <div class="stat-card">
        <strong><?php echo count($produits); ?></strong>
        <span>Produits disponibles</span>
      </div>
      <div class="stat-card">
        <strong><?php echo array_sum(array_column($produits, 'stock')); ?></strong>
        <span>Quantité totale</span>
      </div>
    </div>

    <div class="grid-products">
      <?php if (empty($produits)): ?>
        <div class="no-products">
          <h3>📭 Aucun produit disponible</h3>
          <p>Ajoutez des produits avec stock pour les voir ici.</p>
        </div>
      <?php else: ?>
        <?php foreach ($produits as $produit): ?>
          <div class="produit-card">
            <img src="<?php echo htmlspecialchars($produit['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="produit-card-body">
              <h3><?php echo htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <?php if ($hasCategories && !empty($produit['categorie_nom'])): ?>
                <div class="produit-meta"><?php echo htmlspecialchars($produit['categorie_nom'], ENT_QUOTES, 'UTF-8'); ?></div>
              <?php endif; ?>
              <p><?php echo htmlspecialchars(substr($produit['description'], 0, 50), ENT_QUOTES, 'UTF-8'); ?>...</p>
              <div class="produit-meta">💰 <?php echo number_format($produit['prix'], 2, ',', ' '); ?> FCFA</div>
              <div class="produit-meta">📦 <?php echo (int)$produit['stock']; ?> en stock</div>
              <div class="produit-actions">
                <a href="admis.php?edit=<?php echo (int)$produit['id_produit']; ?>">✏️ Modifier</a>
                <form method="post" action="admis.php" style="display: contents;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="product_id" value="<?php echo (int)$produit['id_produit']; ?>">
                  <button type="submit" onclick="return confirm('🗑️ Supprimer ce produit ?');">🗑️ Supprimer</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>

