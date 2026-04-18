<?php
session_start();
include('../config/db.php'); // Connexion PDO

$message = '';
$editing = false;
$editProduct = null;

if (isset($_GET['edit'])) {
    $editId = (int)($_GET['edit'] ?? 0);
    if ($editId > 0) {
        $stmt = $pdo->prepare('SELECT id_produit, nom, description, prix, stock, image_url FROM Produits WHERE id_produit = ? LIMIT 1');
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
        if ($action === 'update' && $productId > 0) {
            $stmt = $pdo->prepare('SELECT image_url FROM Produits WHERE id_produit = ? LIMIT 1');
            $stmt->execute([$productId]);
            $currentProduct = $stmt->fetch();
            if (!$currentProduct) {
                $message = 'Produit introuvable pour mise à jour.';
            } else {
                $storedImage = $image_path ?: ($image_url ?: $currentProduct['image_url'] ?: 'https://via.placeholder.com/300x200?text=Produit');
                $update = $pdo->prepare('UPDATE Produits SET nom = ?, description = ?, prix = ?, stock = ?, image_url = ? WHERE id_produit = ?');
                $update->execute([$nom, $description, $prix, $stock, $storedImage, $productId]);
                $message = 'Produit mis à jour avec succès.';
                $editing = true;
                $stmt = $pdo->prepare('SELECT id_produit, nom, description, prix, stock, image_url FROM Produits WHERE id_produit = ? LIMIT 1');
                $stmt->execute([$productId]);
                $editProduct = $stmt->fetch();
            }
        } else {
            $storedImage = $image_path ?: ($image_url ?: 'https://via.placeholder.com/300x200?text=Produit');
            $stmt = $pdo->prepare('INSERT INTO Produits (nom, description, prix, stock, image_url) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$nom, $description, $prix, $stock, $storedImage]);
            $message = 'Produit ajouté avec succès.';
            $editing = false;
            $editProduct = null;
        }
    }
}

// Récupération des produits existants
$stmt = $pdo->query('SELECT id_produit, nom, description, prix, stock, image_url FROM Produits ORDER BY id_produit DESC');
$produits = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Administration Produit</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
    .container { max-width: 900px; margin: 0 auto; }
    form, .produit-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input, textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
    button { margin-top: 15px; padding: 10px 15px; background: #16d857; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background: #00b36e; }
    .message { padding: 12px; background: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 8px; margin-bottom: 20px; }
    .liste-produits { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
    .produit-card img { max-width: 100%; height: 150px; object-fit: cover; border-radius: 6px; }
    .produit-card h3 { margin: 10px 0 5px; }
    .produit-card p { font-size: 14px; color: #555; min-height: 40px; }
  </style>
</head>
<body>
  <div class="container">
    <button><a href="affiche.php">Retour au catalogue</a></button>
    <h1>Administration des produits</h1>

    <?php if ($message): ?>
      <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <form method="post" action="ajout.php" enctype="multipart/form-data">
      <h2><?php echo $editing ? 'Modifier le produit' : 'Ajouter un produit'; ?></h2>
      <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'add'; ?>">
      <?php if ($editing): ?>
        <input type="hidden" name="product_id" value="<?php echo (int)$editProduct['id_produit']; ?>">
      <?php endif; ?>

      <label for="nom">Nom du produit :</label>
      <input type="text" id="nom" name="nom" required value="<?php echo $editing ? htmlspecialchars($editProduct['nom'], ENT_QUOTES, 'UTF-8') : ''; ?>">

      <label for="description">Description :</label>
      <textarea id="description" name="description" rows="4"><?php echo $editing ? htmlspecialchars($editProduct['description'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>

      <label for="prix">Prix :</label>
      <input type="number" id="prix" name="prix" step="0.01" required value="<?php echo $editing ? htmlspecialchars($editProduct['prix'], ENT_QUOTES, 'UTF-8') : ''; ?>">

      <label for="stock">Stock :</label>
      <input type="number" id="stock" name="stock" required value="<?php echo $editing ? htmlspecialchars($editProduct['stock'], ENT_QUOTES, 'UTF-8') : ''; ?>">

      <?php if ($editing && !empty($editProduct['image_url'])): ?>
        <label>Image actuelle :</label>
        <img src="<?php echo htmlspecialchars($editProduct['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="Image actuelle" style="max-width: 100%; height: auto; border-radius: 6px; margin-bottom: 12px;">
      <?php endif; ?>

      <label for="image_file">Image du produit :</label>
      <input type="file" id="image_file" name="image_file" accept="image/*">
      <small>Si vous uploadez une image, elle sera utilisée en priorité.</small>

      <label for="image_url">URL de l'image (optionnel) :</label>
      <input type="text" id="image_url" name="image_url" placeholder="https://example.com/image.jpg" value="<?php echo $editing ? htmlspecialchars($editProduct['image_url'], ENT_QUOTES, 'UTF-8') : ''; ?>">

      <button type="submit"><?php echo $editing ? 'Mettre à jour' : 'Enregistrer'; ?></button>
      <?php if ($editing): ?>
        <a href="ajout.php" style="display:inline-block;margin-left:12px;padding:10px 15px;background:#6c757d;color:#fff;border-radius:4px;text-decoration:none;">Annuler</a>
      <?php endif; ?>
    </form>

    <!-- Liste des produits existants -->
    <h2>Produits existants</h2>
    <div class="liste-produits">
      <?php if (empty($produits)): ?>
        <div>Aucun produit ajouté.</div>
      <?php else: ?>
        <?php foreach ($produits as $produit): ?>
          <div class="produit-card">
            <img src="<?php echo htmlspecialchars($produit['image_url']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
            <h3><?php echo htmlspecialchars($produit['nom']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
            <div class="prix"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</div>
            <p>Stock : <?php echo (int)$produit['stock']; ?></p>
            <a href="ajout.php?edit=<?php echo (int)$produit['id_produit']; ?>" style="display:inline-block;margin-top:12px;padding:8px 12px;background:#28a745;color:#fff;border-radius:4px;text-decoration:none;">Modifier</a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
