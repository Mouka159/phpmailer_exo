<?php
session_start();
include('../config/db.php'); // Connexion PDO

$message = trim((string) ($_GET['msg'] ?? ''));
$categorie = trim((string) ($_GET['categorie'] ?? 'all'));

try {
    // Vérifier si la table categories existe et si la colonne id_categorie existe dans Produits
    $hasCategories = false;
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'categories'");
    if ($tableCheck->fetchColumn()) {
        $columnCheck = $pdo->query("SHOW COLUMNS FROM Produits LIKE 'id_categorie'");
        if ($columnCheck->fetchColumn()) {
            $hasCategories = true;
        }
    }

    // Récupération des produits avec filtrage par catégorie si applicable
    $sql = 'SELECT id_produit, nom, description, prix, stock, image_url' . ($hasCategories ? ', id_categorie' : '') . ' FROM Produits';
    $params = [];

    if ($hasCategories && $categorie !== 'all') {
        // Pour les catégories dynamiques, rechercher par nom de catégorie
        $categoryStmt = $pdo->prepare('SELECT id_categorie FROM categories WHERE nom = ? LIMIT 1');
        $categoryStmt->execute([$categorie]);
        $categoryData = $categoryStmt->fetch();

        if ($categoryData) {
            $sql .= ' WHERE id_categorie = ?';
            $params[] = $categoryData['id_categorie'];
        } else {
            // Si la catégorie n'existe pas, ne filtrer rien (afficher tous)
            $categorie = 'all';
        }
    }

    $sql .= ' ORDER BY id_produit DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $produits = $stmt->fetchAll();

    // Récupération des catégories pour l'affichage dynamique si disponible
    $categoriesList = [];
    if ($hasCategories) {
        $categoriesList = $pdo->query('SELECT id_categorie, nom FROM categories ORDER BY nom')->fetchAll();
    }
} catch (PDOException $e) {
    die('Erreur de requête : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue Produits</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fb; margin: 0; padding: 0; }
        header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; }
        header a { color: #fff; text-decoration: none; margin-left: 18px; }
        .container { max-width: 1080px; margin: 24px auto; padding: 0 16px; }
        .message { margin-bottom: 20px; padding: 14px 18px; background: #e7f7e2; border: 1px solid #b8dfb4; color: #225927; border-radius: 8px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); padding: 20px; }
        .card h2 { margin: 0 0 10px; font-size: 1.3rem; color: #20335c; }
        .card p { margin: 10px 0; color: #555; line-height: 1.5; }
        .card strong { display: block; margin: 10px 0; font-size: 1rem; }
        .card form { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-top: 14px; }
        .card input[type="number"] { width: 70px; padding: 10px; border: 1px solidbackground: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; }
        .card button { flex: 1; min-width: 120px; padding: 11px 14px; border: none; border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);color: #fff; cursor: pointer; }
        .card button:disabled { background: #a3a8a5; cursor: not-allowed; }
        nav a { margin-right: 18px; }
        nav a:hover { text-decoration: underline; }
        a{ color: #fff; text-decoration: none; margin-left: 18px; }
        a:hover { text-decoration: underline; }
        input[type="search"] { padding: 8px 12px; border: none; border-radius: 6px; }
        .hamburger {
          display: none;
          flex-direction: column;
          cursor: pointer;
          padding: 10px;
        }
        .hamburger span {
          width: 25px;
          height: 3px;
          background: white;
          margin: 3px 0;
          transition: 0.3s;
        }
        .nav-container {
          display: flex;
          align-items: center;
        }
        @media (max-width: 768px) {
          .hamburger {
            display: flex;
          }
          .nav-container {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
          }
          .nav-container.show {
            display: flex;
          }
          .nav-container a,
          .nav-container input,
          .nav-container nav {
            width: 100%;
          }
          .nav-container nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
          }
          .nav-container a {
            margin: 10px 0;
          }
          nav a {
            margin: 0;
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
          }
        }
            .categorie { background: #fff; padding: 12px 16px; display: flex; gap: 18px; justify-content: center; margin-bottom: 24px; }
            .categorie a { color: #20335c; font-weight: bold; }
            .categorie a:hover, .categorie a.active { text-decoration: underline; color: #667eea; }
    </style>
</head>
<body>
    <header>
  <h1>ShopESA</h1>
  <div class="hamburger" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
  </div>
  <div class="nav-container" id="nav-menu">
    <nav>
      <a href="acceuil.php"><i class="fas fa-home"></i> Accueil</a>
      <a href="affiche.php"><i class="fas fa-box"></i> Produits</a>
      <a href="conexion.php"><i class="fas fa-user"></i> Connexion</a>
    </nav>
    <a href="compte.php"><i class="fas fa-user"></i> Mon compte</a>
    <a href="panier.php"><i class="fas fa-shopping-cart"></i> Panier </a>
  </div>
</header>
<div class="categorie">
    <a href="affiche.php?categorie=all" class="<?php echo $categorie === 'all' ? 'active' : ''; ?>">Tous les produits</a>
    <?php if ($hasCategories && !empty($categoriesList)): ?>
        <?php foreach ($categoriesList as $cat): ?>
            <a href="affiche.php?categorie=<?php echo htmlspecialchars($cat['nom'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $categorie === $cat['nom'] ? 'active' : ''; ?>">
                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($cat['nom'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Catégories hardcodées si la table categories n'existe pas -->
        <a href="affiche.php?categorie=Électronique" class="<?php echo $categorie === 'Électronique' ? 'active' : ''; ?>"><i class="fas fa-laptop"></i> Électronique</a>
        <a href="affiche.php?categorie=Mode" class="<?php echo $categorie === 'Mode' ? 'active' : ''; ?>"><i class="fas fa-tshirt"></i> Mode</a>
        <a href="affiche.php?categorie=Maison" class="<?php echo $categorie === 'Maison' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Maison</a>
    <?php endif; ?>
</div>

<div class="container">
    <?php if ($message !== ''): ?>
        <div class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (empty($produits)): ?>
        <p>Aucun produit trouvé dans la base de données.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($produits as $produit): ?>
                <div class="card">
                    <?php if (!empty($produit['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($produit['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($produit['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <strong>Prix : <?php echo number_format($produit['prix'], 2, ',', ' '); ?> FCFA</strong>
                    <p>Stock : <?php echo (int) $produit['stock']; ?></p>
                    <form action="../logique/panier.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="produit_id" value="<?php echo (int) $produit['id_produit']; ?>">
                        <input type="number" name="quantite" min="1" max="<?php echo (int) $produit['stock']; ?>" value="1" required <?php echo ((int) $produit['stock'] === 0) ? 'disabled' : ''; ?>>
                        <button type="submit" <?php echo ((int) $produit['stock'] === 0) ? 'disabled' : ''; ?>>Ajouter au panier</button>
                         <!--?php
                            if (!isset($_SESSION['user_id'])) {
                                header('Location: conexion.php');
                                exit();
                            }
                        ?-->
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleMenu() {
  const nav = document.getElementById('nav-menu');
  nav.classList.toggle('show');
}
</script>

</body>
</html>
