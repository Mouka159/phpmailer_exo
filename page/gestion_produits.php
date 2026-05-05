<?php
session_start();
include('../config/db.php');

// Admin only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'user') !== 'admin') {
    header('Location: conexion.php?message=' . urlencode('Accès administrateur requis.'));
    exit();
}

$message = '';
$error = '';

// Handle CRUD
if ($_POST) {
    if (isset($_POST['add_product'])) {
        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix = (float) ($_POST['prix'] ?? 0);
        $stock = (int) ($_POST['stock'] ?? 0);
        $image_url = trim($_POST['image_url'] ?? '');

        if ($nom && $prix > 0 && $stock >= 0) {
            try {
                $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, stock, image_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $description, $prix, $stock, $image_url]);
                $message = 'Produit ajouté avec succès !';
            } catch (PDOException $e) {
                $error = 'Erreur ajout : ' . $e->getMessage();
            }
        } else {
            $error = 'Champs invalides.';
        }
    } elseif (isset($_POST['update_product'])) {
        $id = (int) ($_POST['id_produit'] ?? 0);
        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix = (float) ($_POST['prix'] ?? 0);
        $stock = (int) ($_POST['stock'] ?? 0);
        $image_url = trim($_POST['image_url'] ?? '');

        if ($id > 0 && $nom && $prix > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE produits SET nom=?, description=?, prix=?, stock=?, image_url=? WHERE id_produit=?");
                $stmt->execute([$nom, $description, $prix, $stock, $image_url, $id]);
                $message = 'Produit mis à jour !';
            } catch (PDOException $e) {
                $error = 'Erreur mise à jour : ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['delete_product'])) {
        $id = (int) ($_POST['id_produit'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM produits WHERE id_produit=?");
                $stmt->execute([$id]);
                $message = 'Produit supprimé !';
            } catch (PDOException $e) {
                $error = 'Erreur suppression : ' . $e->getMessage();
            }
        }
    }
}

// Fetch all products
try {
    $stmt = $pdo->query("SELECT id_produit, nom, description, prix, stock, image_url FROM produits ORDER BY id_produit DESC");
    $produits = $stmt->fetchAll();
} catch (PDOException $e) {
    $produits = [];
    $error = 'Erreur chargement produits.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Produits - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admis.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
            <a class="btn btn-outline-light" href="admis.php"><i class="fas fa-arrow-left"></i> Retour Dashboard</a>
        </div>
    
    <div class="container mt-4">
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show"><?php echo htmlspecialchars($message); ?><button type="button" class="btn-close"></button></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?php echo htmlspecialchars($error); ?><button type="button" class="btn-close"></button></div>
        <?php endif; ?>

        <!-- Add Product Form -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-plus"></i> Ajouter un produit</div>
    <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Prix (FCFA)</label>
                            <input type="number" name="prix" step="0.01" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="url" name="image_url" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary"><i class="fas fa-save"></i> Ajouter</button>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="fas fa-boxes"></i> Liste des produits (<?php echo count($produits); ?>)</span>
            </div>
            <div class="card-body">
                <table id="produitsTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $p): ?>
                        <tr>
                            <td><?php if ($p['image_url']): ?><img src="<?php echo htmlspecialchars($p['image_url']); ?>" style="width:50px;height:50px;object-fit:cover;" alt="img"><?php endif; ?></td>
                            <td><?php echo htmlspecialchars($p['nom']); ?></td>
                            <td><?php echo number_format($p['prix'], 2, ',', ' '); ?> FCFA</td>
                            <td><span class="badge <?php echo $p['stock'] == 0 ? 'bg-danger' : 'bg-success'; ?>"><?php echo $p['stock']; ?></span></td>
                            <td><?php echo htmlspecialchars(substr($p['description'], 0, 50)) . '...'; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $p['id_produit']; ?>" data-nom="<?php echo htmlspecialchars($p['nom']); ?>" data-desc="<?php echo htmlspecialchars($p['description']); ?>" data-prix="<?php echo $p['prix']; ?>" data-stock="<?php echo $p['stock']; ?>" data-img="<?php echo htmlspecialchars($p['image_url']); ?>"><i class="fas fa-edit"></i></button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ?')">
                                    <input type="hidden" name="id_produit" value="<?php echo $p['id_produit']; ?>">
                                    <button type="submit" name="delete_product" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_produit" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" id="edit_nom" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prix</label>
                                <input type="number" name="prix" id="edit_prix" step="0.01" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" id="edit_stock" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="url" name="image_url" id="edit_img" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_desc" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="update_product" class="btn btn-primary">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#produitsTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
                order: [[1, 'asc']],
                pageLength: 25,
                responsive: true
            });

            $('.edit-btn').click(function() {
                const btn = $(this);
                $('#edit_id').val(btn.data('id'));
                $('#edit_nom').val(btn.data('nom'));
                $('#edit_desc').val(btn.data('desc'));
                $('#edit_prix').val(btn.data('prix'));
                $('#edit_stock').val(btn.data('stock'));
                $('#edit_img').val(btn.data('img'));
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
        });
    </script>
</body>
</html>
