<?php
session_start();
include('../config/db.php');

try {
    // Top 6 produits (comme affiche.php)
    $sql = 'SELECT id_produit, nom, description, prix, stock, image_url FROM Produits ORDER BY id_produit DESC LIMIT 6';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $produitsPhares = $stmt->fetchAll();
} catch (PDOException $e) {
    $produitsPhares = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopESA - Accueil</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      line-height: 1.6;
      color: #333;
      overflow-x: hidden;
    }
    /* Header */
    header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 1rem 2rem;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    header h1 { margin: 0; }
    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
    }
    nav { display: flex; gap: 1.5rem; }
    nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    nav a:hover { color: #ffd700; transform: translateY(-2px); }
    .hamburger {
      display: none;
      flex-direction: column;
      cursor: pointer;
      gap: 4px;
    }
    .hamburger span {
      width: 25px;
      height: 3px;
      background: white;
      transition: 0.3s;
    }
    /* Hero */
    .hero {
      /*background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      position: relative;
      overflow: hidden;
    }
    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('../image/logo.png') no-repeat center/60%;
      
      z-index: 1;
    }
    .hero-content { position: relative; z-index: 2; animation: fadeInUp 1s ease; }
    .hero h1 {
      font-size: clamp(2.5rem, 5vw, 4rem);
      margin-bottom: 1.5rem;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
    }
    .hero p { font-size: 1.3rem; margin-bottom: 2rem; max-width: 600px; }
    .btn-container {
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
      justify-content: center;
    }
    .btn {
      background: #ffd700;
      color: #333;
      border: none;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: bold;
      font-size: 1.1rem;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255,215,0,0.4);
    }
    .btn:hover {
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 8px 25px rgba(255,215,0,0.6);
      background: #ffea70;
    }
    /* Produits Phares */
    .produits-section {
      padding: 5rem 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    .section-title {
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 3rem;
      color: #20335c;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
    }
    .card {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      transition: all 0.4s ease;
      animation: fadeIn 0.8s ease forwards;
      opacity: 0;
      transform: translateY(30px);
    }
    .card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .card-content {
      padding: 1.5rem;
    }
    .card h3 { font-size: 1.3rem; margin-bottom: 0.5rem; color: #20335c; }
    .card p { color: #666; margin-bottom: 1rem; }
    .prix { font-size: 1.5rem; font-weight: bold; color: #667eea; margin-bottom: 1rem; }
    .card form {
      display: flex;
      gap: 0.5rem;
    }
    .card input[type="number"] {
      width: 60px;
      padding: 0.5rem;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    .card button {
      flex: 1;
      padding: 0.7rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
    }
    .card button:hover { opacity: 0.9; }
    /* Footer */
    footer {
      background: #20335c;
      color: white;
      text-align: center;
      padding: 2rem;
    }
    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    /* Responsive */
    @media (max-width: 768px) {
      .hamburger { display: flex; }
      nav { display: none; position: absolute; top: 100%; left: 0; width: 100%; background: rgba(102,126,234,0.95); flex-direction: column; padding: 1rem; gap: 1rem; }
      nav.show { display: flex; }
      .btn-container { flex-direction: column; align-items: center; }
      .grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <div class="nav-container">
      <h1><i class="fas fa-store"></i> ShopESA</h1>
      <nav id="nav-menu">
        <a href="acceuil.php"><i class="fas fa-home"></i> Accueil</a>
        <a href="affiche.php"><i class="fas fa-box"></i> Produits</a>
        <a href="panier.php"><i class="fas fa-shopping-cart"></i> Panier (<?php echo isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0; ?>)</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="compte.php"><i class="fas fa-user"></i> Compte</a>
        <?php else: ?>
          <a href="conexion.php"><i class="fas fa-sign-in-alt"></i> Connexion</a>
        <?php endif; ?>
      </nav>
      <div class="hamburger" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="hero-content">
      <h1>Bienvenue chez ShopESA</h1>
      <p>Découvrez nos meilleures offres et produits de qualité. Livraison rapide et paiement sécurisé !</p>
      <div class="btn-container">
        <a href="affiche.php" class="btn"><i class="fas fa-box-open"></i> Découvrir les Produits</a>
        <a href="affiche.php?categorie=all" class="btn"><i class="fas fa-fire"></i> Nouveautés</a>
      </div>
    </div>
  </section>

  <!-- Produits Phares -->
  <section class="produits-section">
    <h2 class="section-title">Produits Phares</h2>
    <?php if (empty($produitsPhares)): ?>
      <p style="text-align: center; color: #666;">Aucun produit pour le moment. Revenez bientôt !</p>
    <?php else: ?>
      <div class="grid">
        <?php foreach ($produitsPhares as $index => $produit): ?>
          <div class="card" style="animation-delay: <?php echo $index * 0.1; ?>s;">
            <?php if (!empty($produit['image_url'])): ?>
              <img src="<?php echo htmlspecialchars($produit['image_url']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
            <?php endif; ?>
            <div class="card-content">
              <h3><?php echo htmlspecialchars($produit['nom']); ?></h3>
              <p><?php echo substr(htmlspecialchars($produit['description']), 0, 100); ?>...</p>
              <div class="prix"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> FCFA</div>
              <form action="../logique/panier.php" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="produit_id" value="<?php echo (int)$produit['id_produit']; ?>">
                <input type="number" name="quantite" min="1" max="<?php echo (int)$produit['stock']; ?>" value="1" required <?php echo $produit['stock'] == 0 ? 'disabled' : ''; ?>>
                <button type="submit" <?php echo $produit['stock'] == 0 ? 'disabled' : ''; ?>>
                  <i class="fas fa-cart-plus"></i> Ajouter
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2024 ShopESA — <i class="fas fa-truck"></i> Livraison rapide | <i class="fas fa-lock"></i> Paiement sécurisé | <i class="fas fa-headset"></i> Support 24/7</p>
  </footer>

  <script>
    function toggleMenu() {
      const nav = document.getElementById('nav-menu');
      nav.classList.toggle('show');
    }

    // Animations au scroll
    window.addEventListener('scroll', () => {
      document.querySelectorAll('.card').forEach((card, index) => {
        const rect = card.getBoundingClientRect();
        if (rect.top < window.innerHeight) {
          card.style.animationPlayState = 'running';
        }
      });
    });
  </script>
</body>
</html>

