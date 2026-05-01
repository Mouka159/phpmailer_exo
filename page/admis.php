<?php
session_start();
include('../config/db.php'); // Connexion PDO

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'user') !== 'admin') {
    header('Location: conexion.php?message=Accès refusé. Vous devez être administrateur.');
    exit();
}

$message = '';
$editing = false;
$editProduct = null;
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

if (isset($_GET['edit'])) {
    $editId = (int)($_GET['edit'] ?? 0);
    if ($editId > 0) {
        $sql = 'SELECT id_produit, nom, description, prix, stock, image_url' . ($hasCategories ? ', id_categorie' : '') . ' FROM Produits WHERE id_produit = ? LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$editId]);
        $editProduct = $stmt->fetch();
        if ($editProduct) {
            $editing = true;
        } else {
            $message = 'Produit introuvable pour modification.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = (float)($_POST['prix'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');
    $image_path = '';
    $uploadError = false;
    $productId = (int)($_POST['product_id'] ?? 0);
    $categorieId = $hasCategories ? (int)($_POST['id_categorie'] ?? 0) : null;

    if ($action === 'delete' && $productId > 0) {
        $delete = $pdo->prepare('DELETE FROM Produits WHERE id_produit = ?');
        $delete->execute([$productId]);
        $message = 'Produit supprimé avec succès.';
    } else {
        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
                $message = 'Erreur d\'upload de l\'image.';
                $uploadError = true;
            } else {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $tmpName = $_FILES['image_file']['tmp_name'];
                $originalName = basename($_FILES['image_file']['name']);
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (!in_array($extension, $allowed, true)) {
                    $message = 'Format d\'image non autorisé. Utilisez JPG, PNG, GIF ou WEBP.';
                    $uploadError = true;
                } else {
                    $uploadDir = __DIR__ . '/../image/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $safeName = 'produit_' . time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $originalName);
                    $targetFile = $uploadDir . $safeName;

                    if (move_uploaded_file($tmpName, $targetFile)) {
                        $image_path = '../image/' . $safeName;
                    } else {
                        $message = 'Impossible de déplacer l\'image uploadée.';
                        $uploadError = true;
                    }
                }
            }
        }

        if ($nom === '' || $prix <= 0) {
            $message = 'Le nom et le prix sont requis.';
            $uploadError = true;
        }

        if (!$uploadError) {
            $storedImage = $image_path ?: ($image_url ?: 'https://via.placeholder.com/300x200?text=Produit');
            if ($action === 'update' && $productId > 0) {
                $stmt = $pdo->prepare('SELECT image_url FROM Produits WHERE id_produit = ? LIMIT 1');
                $stmt->execute([$productId]);
                $currentProduct = $stmt->fetch();
                if (!$currentProduct) {
                    $message = 'Produit introuvable pour mise à jour.';
                } else {
                    $storedImage = $image_path ?: ($image_url ?: $currentProduct['image_url'] ?: $storedImage);
                    if ($hasCategories) {
                        $update = $pdo->prepare('UPDATE Produits SET nom = ?, description = ?, prix = ?, stock = ?, image_url = ?, id_categorie = ? WHERE id_produit = ?');
                        $update->execute([$nom, $description, $prix, $stock, $storedImage, $categorieId ?: null, $productId]);
                    } else {
                        $update = $pdo->prepare('UPDATE Produits SET nom = ?, description = ?, prix = ?, stock = ?, image_url = ? WHERE id_produit = ?');
                        $update->execute([$nom, $description, $prix, $stock, $storedImage, $productId]);
                    }
                    $message = 'Produit mis à jour avec succès.';
                    $editing = true;
                    $sql = 'SELECT id_produit, nom, description, prix, stock, image_url' . ($hasCategories ? ', id_categorie' : '') . ' FROM Produits WHERE id_produit = ? LIMIT 1';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$productId]);
                    $editProduct = $stmt->fetch();
                }
            } else {
                if ($hasCategories) {
                    $stmt = $pdo->prepare('INSERT INTO Produits (nom, description, prix, stock, image_url, id_categorie) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$nom, $description, $prix, $stock, $storedImage, $categorieId ?: null]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO Produits (nom, description, prix, stock, image_url) VALUES (?, ?, ?, ?, ?)');
                    $stmt->execute([$nom, $description, $prix, $stock, $storedImage]);
                }
                $message = 'Produit ajouté avec succès.';
                $editing = false;
                $editProduct = null;
            }
        }
    }
}

$sql = 'SELECT p.id_produit, p.nom, p.description, p.prix, p.stock, p.image_url' . ($hasCategories ? ', p.id_categorie, c.nom AS categorie_nom' : '') . ' FROM Produits p' . ($hasCategories ? ' LEFT JOIN categories c ON p.id_categorie = c.id_categorie' : '') . ' ORDER BY p.id_produit DESC';
$produits = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administration Produit</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #667eea;
      --primary-dark: #5568d3;
      --secondary: #764ba2;
      --success: #48bb78;
      --danger: #f56565;
      --warning: #ed8936;
      --info: #4299e1;
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
      font-family: 'Segoe UI', Trebuchet MS, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: var(--text);
      min-height: 100vh;
    }
    
    a { color: inherit; text-decoration: none; }
    button { font: inherit; cursor: pointer; }

    .layout { 
      display: grid; 
      grid-template-columns: 280px 1fr; 
      min-height: 100vh;
      gap: 0;
    }

    /* SIDEBAR */
    .sidebar { 
      background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
      color: #f7fafc;
      padding: 32px 20px;
      display: flex; 
      flex-direction: column;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
      position: relative;
      overflow-y: auto;
    }

    .brand { 
      font-size: 1.6rem; 
      font-weight: 900; 
      letter-spacing: -0.02em; 
      margin-bottom: 32px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .brand::before {
      content: "📦";
      font-size: 1.4rem;
      -webkit-text-fill-color: initial;
    }

    .nav-list { 
      list-style: none; 
      padding: 0; 
      margin: 0; 
      display: grid; 
      gap: 8px;
    }

    .nav-link { 
      display: flex; 
      align-items: center; 
      gap: 14px; 
      padding: 14px 16px; 
      color: #cbd5e0;
      border-radius: 14px; 
      transition: all 0.3s ease;
      font-weight: 600;
      position: relative;
    }

    .nav-link:hover, .nav-link.active { 
      background: rgba(102, 126, 234, 0.2);
      color: #fff;
      transform: translateX(4px);
      border-left: 4px solid var(--primary);
      padding-left: 12px;
    }

    .menu-section { 
      margin-top: 32px;
    }

    .menu-title { 
      margin: 0 0 14px; 
      color: #a0aec0; 
      font-size: .85rem; 
      letter-spacing: .06em; 
      text-transform: uppercase;
      font-weight: 700;
    }

    .product-menu { 
      list-style: none; 
      padding: 0; 
      margin: 0; 
      display: grid; 
      gap: 6px;
      max-height: 300px;
      overflow-y: auto;
    }

    .product-menu-item { 
      display: block; 
      padding: 10px 12px; 
      border-radius: 10px; 
      color: #cbd5e0;
      background: rgba(255, 255, 255, 0.05);
      transition: all 0.2s ease;
      font-size: .9rem;
      border-left: 3px solid transparent;
    }

    .product-menu-item:hover { 
      background: rgba(102, 126, 234, 0.15);
      color: #fff;
      border-left-color: var(--primary);
      padding-left: 15px;
    }

    .sidebar-footer { 
      margin-top: auto; 
      padding-top: 20px; 
      border-top: 1px solid rgba(255, 255, 255, 0.1); 
      font-size: .85rem; 
      color: #a0aec0;
      line-height: 1.5;
    }

    /* MAIN CONTENT */
    .main { 
      padding: 40px;
      background: var(--light);
      overflow-y: auto;
    }

    .header { 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
      gap: 20px; 
      margin-bottom: 40px;
      flex-wrap: wrap;
    }

    .header h1 { 
      margin: 0; 
      font-size: 2.5rem; 
      letter-spacing: -0.03em;
      color: var(--dark);
      font-weight: 800;
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .badge { 
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white; 
      padding: 12px 20px; 
      border-radius: 50px; 
      font-weight: 700;
      font-size: .9rem;
      box-shadow: var(--shadow-sm);
    }

    /* STATS */
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
      box-shadow: var(--shadow-sm);
      border-left: 5px solid var(--primary);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200px;
      height: 200px;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
      border-radius: 50%;
    }

    .stat-card:nth-child(2) { border-left-color: var(--secondary); }
    .stat-card:nth-child(3) { border-left-color: var(--success); }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow);
    }

    .stat-card strong { 
      display: block; 
      font-size: 2.2rem; 
      margin-bottom: 8px;
      color: var(--primary);
      font-weight: 900;
    }

    .stat-card:nth-child(2) strong { color: var(--secondary); }
    .stat-card:nth-child(3) strong { color: var(--success); }

    .stat-card span { 
      color: var(--muted);
      font-weight: 600;
    }

    /* GRID LAYOUT */
    .grid-2 { 
      display: grid; 
      grid-template-columns: 1fr 380px; 
      gap: 30px; 
      align-items: start;
    }

    /* CARDS */
    .card { 
      background: white;
      border-radius: 20px; 
      padding: 32px; 
      box-shadow: var(--shadow-sm);
      transition: all 0.3s ease;
    }

    .card:hover {
      box-shadow: var(--shadow);
    }

    .card h2 { 
      margin-top: 0; 
      margin-bottom: 24px; 
      font-size: 1.5rem;
      color: var(--dark);
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 800;
    }

    .card h2::before {
      content: "";
      display: inline-block;
      width: 4px;
      height: 28px;
      background: linear-gradient(180deg, var(--primary), var(--secondary));
      border-radius: 2px;
    }

    /* FORM FIELDS */
    .field { 
      margin-bottom: 22px; 
    }

    label { 
      display: block; 
      margin-bottom: 10px; 
      font-size: .95rem; 
      font-weight: 700; 
      color: var(--text);
    }

    input, textarea, select { 
      width: 100%; 
      border: 2px solid var(--border);
      border-radius: 12px; 
      padding: 14px 16px; 
      background: #f8f9fa;
      color: var(--text);
      transition: all 0.3s ease;
      font-size: 1rem;
    }

    input:hover, textarea:hover, select:hover {
      border-color: var(--primary);
      background: white;
    }

    input:focus, textarea:focus, select:focus { 
      outline: none; 
      border-color: var(--primary);
      background: white;
      box-shadow: 0 0 0 6px rgba(102, 126, 234, 0.1);
    }

    textarea { 
      min-height: 140px; 
      resize: vertical;
      font-family: inherit;
    }

    .small-note { 
      font-size: .85rem; 
      color: var(--muted); 
      margin-top: 8px;
      font-style: italic;
    }

    /* BUTTONS */
    .actions { 
      display: flex; 
      flex-wrap: wrap; 
      gap: 12px; 
      margin-top: 28px;
    }

    .btn { 
      display: inline-flex; 
      align-items: center; 
      justify-content: center; 
      gap: 10px; 
      padding: 14px 24px; 
      border-radius: 12px; 
      border: none; 
      cursor: pointer; 
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-sm);
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }

    .btn-primary { 
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
    }

    .btn-secondary { 
      background: white;
      color: var(--text);
      border: 2px solid var(--border);
    }

    .btn-secondary:hover {
      border-color: var(--primary);
      background: var(--light);
    }

    .btn-danger { 
      background: linear-gradient(135deg, var(--danger), #e53e3e);
      color: white;
    }

    /* MESSAGE */
    .message { 
      border-radius: 16px; 
      padding: 18px 22px; 
      background: linear-gradient(135deg, #c6f6d5, #b2f5ea);
      border-left: 5px solid var(--success);
      color: #22543d;
      margin-bottom: 28px;
      font-weight: 600;
      box-shadow: var(--shadow-sm);
    }

    /* PRODUCTS GRID */
    .grid-products { 
      display: grid; 
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
      gap: 16px;
    }

    .produit-card { 
      background: white;
      border-radius: 16px; 
      overflow: hidden; 
      box-shadow: var(--shadow-sm);
      display: flex; 
      flex-direction: column;
      transition: all 0.3s ease;
      border: 1px solid transparent;
    }

    .produit-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow);
      border-color: var(--primary);
    }

    .produit-card img { 
      width: 100%; 
      height: 160px; 
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .produit-card:hover img {
      transform: scale(1.05);
    }

    .produit-card-body { 
      padding: 18px; 
      flex: 1; 
      display: flex; 
      flex-direction: column; 
      gap: 10px;
    }

    .produit-card h3 { 
      margin: 0; 
      font-size: 1rem;
      color: var(--dark);
      font-weight: 700;
      line-height: 1.3;
    }

    .produit-card p { 
      color: var(--muted); 
      font-size: .85rem; 
      line-height: 1.4; 
      flex: 1;
      margin: 0;
    }

    .produit-meta { 
      color: var(--primary);
      font-size: .85rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .produit-meta::before {
      content: "📌";
      font-size: .9rem;
    }

    .produit-actions { 
      display: grid; 
      grid-template-columns: repeat(2, minmax(0, 1fr)); 
      gap: 8px;
      margin-top: 12px;
    }

    .produit-actions a, .produit-actions button { 
      border-radius: 10px; 
      padding: 10px 12px; 
      font-weight: 700;
      font-size: .85rem;
      transition: all 0.2s ease;
      border: none;
    }

    .produit-actions a { 
      background: linear-gradient(135deg, var(--success), #38a169);
      color: white; 
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .produit-actions a:hover {
      transform: scale(1.05);
    }

    .produit-actions button { 
      background: linear-gradient(135deg, var(--danger), #e53e3e);
      color: white;
    }

    .produit-actions button:hover {
      transform: scale(1.05);
    }

    .no-products {
      grid-column: 1 / -1;
      text-align: center;
      padding: 40px;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
      border-radius: 16px;
      border: 2px dashed var(--border);
    }

    .no-products h3 {
      color: var(--text);
      font-size: 1.2rem;
      margin-bottom: 10px;
    }

    .no-products p {
      color: var(--muted);
      margin: 0;
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
      .grid-2 { 
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .layout { 
        grid-template-columns: 1fr;
      }

      .sidebar { 
        padding: 20px;
        max-height: 200px;
        overflow-x: auto;
        flex-direction: row;
      }

      .nav-list {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      }

      .menu-section {
        display: none;
      }

      .main { 
        padding: 20px;
      }

      .header {
        flex-direction: column;
        align-items: flex-start;
      }

      .header h1 {
        font-size: 1.8rem;
      }

      .grid-products { 
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      }
    }

    @media (max-width: 480px) {
      .grid-products { 
        grid-template-columns: 1fr;
      }

      .stats {
        grid-template-columns: 1fr;
      }

      .header h1 {
        font-size: 1.5rem;
      }

      .card {
        padding: 20px;
      }
    }

    /* ANIMATIONS */
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card, .produit-card, .stat-card {
      animation: slideIn 0.5s ease-out;
    }

    /* SCROLLBAR */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background: rgba(102, 126, 234, 0.3);
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: rgba(102, 126, 234, 0.6);
    }
  </style>
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="brand">Admin Store</div>
      <nav>
        <ul class="nav-list">
          <li class="nav-item"><a class="nav-link active" href="admis.php"><i class="fas fa-cube"></i> <span>Produits</span></a></li>
          <li class="nav-item"><a class="nav-link" href="commandes_jour.php"><i class="fas fa-receipt"></i> <span>Commandes</span></a></li>
          <li class="nav-item"><a class="nav-link" href="affiche.php"><i class="fas fa-store"></i> <span>Catalogue</span></a></li>
          <li class="nav-item"><a class="nav-link" href="panier.php"><i class="fas fa-shopping-cart"></i> <span>Panier</span></a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-cog"></i> <span>Paramètres</span></a></li>
        </ul>
      </nav>
      <div class="menu-section">
        <p class="menu-title">⭐ Produits populaires</p>
        <ul class="product-menu">
          <?php if (empty($produits)): ?>
            <li class="product-menu-item">Aucun produit</li>
          <?php else: ?>
            <?php foreach ($produits as $produit): ?>
              <li><a class="product-menu-item" href="admis.php?edit=<?php echo (int)$produit['id_produit']; ?>"><?php echo htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </div>
      <div class="sidebar-footer">✨ Gérez facilement votre inventaire de produits et catégories.</div>
    </aside>

    <main class="main">
      <?php if ($message): ?>
        <div class="message">✅ <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>

      <div class="header">
        <div>
          <h1>🛒 Gestion des Produits</h1>
          <p style="margin: 8px 0 0; color: var(--muted); font-size: 1.1rem;">Administrez votre catalogue en un clin d'œil</p>
        </div>
        <div class="header-right">
          <?php if (!empty($produits)): ?>
            <a class="btn btn-secondary" href="#existingProducts">📊 Voir tous (<?php echo count($produits); ?>)</a>
          <?php endif; ?>
          <div class="badge"><?php echo $editing ? '✏️ Mode édition' : '➕ Ajouter'; ?></div>
        </div>
      </div>

      <div class="stats">
        <div class="stat-card">
          <strong><?php echo count($produits); ?></strong>
          <span>📦 Produits enregistrés</span>
        </div>
        <div class="stat-card">
          <strong><?php echo $hasCategories ? count($categories) : '–'; ?></strong>
          <span>🏷️ Catégories</span>
        </div>
        <div class="stat-card">
          <strong>💰</strong>
          <span>Ventes totales</span>
        </div>
      </div>

      <div class="grid-2">
        <section class="card">
          <h2><?php echo $editing ? '✏️ Modifier le produit' : '➕ Nouveau produit'; ?></h2>
          <form method="post" action="admis.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'add'; ?>">
            <?php if ($editing): ?>
              <input type="hidden" name="product_id" value="<?php echo (int)$editProduct['id_produit']; ?>">
            <?php endif; ?>

            <div class="field">
              <label for="nom">📝 Nom du produit</label>
              <input type="text" id="nom" name="nom" placeholder="Ex: T-shirt premium" required value="<?php echo $editing ? htmlspecialchars($editProduct['nom'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <div class="field">
              <label for="description">📄 Description</label>
              <textarea id="description" name="description" placeholder="Décrivez votre produit en détail..."><?php echo $editing ? htmlspecialchars($editProduct['description'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>

            <div class="field">
              <label for="prix">💵 Prix (FCFA)</label>
              <input type="number" id="prix" name="prix" step="0.01" placeholder="10000" required value="<?php echo $editing ? htmlspecialchars($editProduct['prix'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <div class="field">
              <label for="stock">📊 Quantité en stock</label>
              <input type="number" id="stock" name="stock" placeholder="50" required value="<?php echo $editing ? htmlspecialchars($editProduct['stock'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <?php if ($hasCategories): ?>
              <div class="field">
                <label for="id_categorie">🏷️ Catégorie</label>
                <select id="id_categorie" name="id_categorie">
                  <option value="">-- Sélectionner une catégorie --</option>
                  <?php foreach ($categories as $categorie): ?>
                    <option value="<?php echo (int)$categorie['id_categorie']; ?>" <?php echo ($editing && isset($editProduct['id_categorie']) && (int)$editProduct['id_categorie'] === (int)$categorie['id_categorie']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($categorie['nom'], ENT_QUOTES, 'UTF-8'); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endif; ?>

            <?php if ($editing && !empty($editProduct['image_url'])): ?>
              <div class="field">
                <label>🖼️ Image actuelle</label>
                <img src="<?php echo htmlspecialchars($editProduct['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="Image actuelle" style="width:100%; border-radius:16px; box-shadow: var(--shadow-sm);">
              </div>
            <?php endif; ?>

            <div class="field">
              <label for="image_file">📸 Uploader une image</label>
              <input type="file" id="image_file" name="image_file" accept="image/*">
              <div class="small-note">📎 JPG, PNG, GIF ou WEBP (max 5MB)</div>
            </div>

            <div class="field">
              <label for="image_url">🔗 URL de l'image (optionnel)</label>
              <input type="text" id="image_url" name="image_url" placeholder="https://example.com/image.jpg" value="<?php echo $editing ? htmlspecialchars($editProduct['image_url'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <div class="actions">
              <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i>
                <?php echo $editing ? 'Mettre à jour' : 'Ajouter le produit'; ?>
              </button>
              <?php if ($editing): ?>
                <a class="btn btn-secondary" href="admis.php">
                  <i class="fas fa-times"></i>
                  Annuler
                </a>
              <?php endif; ?>
            </div>
          </form>
        </section>

        <section class="card" id="existingProducts">
          <h2>🔥 Derniers produits</h2>
          <div class="grid-products">
            <?php if (empty($produits)): ?>
              <div class="no-products">
                <h3>📭 Aucun produit</h3>
                <p>Commencez par ajouter votre premier produit!</p>
              </div>
            <?php else: ?>
              <?php foreach (array_slice($produits, 0, 8) as $produit): ?>
                <div class="produit-card">
                  <img src="<?php echo htmlspecialchars($produit['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'); ?>">
                  <div class="produit-card-body">
                    <h3><?php echo htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <?php if ($hasCategories && !empty($produit['categorie_nom'])): ?>
                      <div class="produit-meta">🏷️ <?php echo htmlspecialchars($produit['categorie_nom'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                    <p><?php echo htmlspecialchars(substr($produit['description'], 0, 50), ENT_QUOTES, 'UTF-8'); ?>...</p>
                    <div class="produit-meta">💰 <?php echo number_format($produit['prix'], 2, ',', ' '); ?> FCFA</div>
                    <div class="produit-meta">📦 <?php echo (int)$produit['stock']; ?> pcs</div>
                    <div class="produit-actions">
                      <a href="admis.php?edit=<?php echo (int)$produit['id_produit']; ?>">✏️ Modifier</a>
                      <form method="post" action="admis.php" style="margin:0;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?php echo (int)$produit['id_produit']; ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('🗑️ Supprimer ce produit définitivement ?');">🗑️ Supprimer</button>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>
      </div>
    </main>
  </div>
</body>
</html>
