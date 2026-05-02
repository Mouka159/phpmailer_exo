<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopTogo - Accueil</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0; padding: 0;
      background: #fdfdfd;
      color: #333;
    }
    header {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      display: flex; justify-content: space-between;
      align-items: center; padding: 15px 30px;
    }
    header h1 { margin: 0; font-size: 1.8em;
   }
    nav a:hover { color:white; }
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
    nav {
      display: flex;
    }
    @media (max-width: 768px) {
      .hamburger {
        display: flex;
      }
      nav {
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
      nav.show {
        display: flex;
      }
      nav a {
        margin: 10px 0;
        padding: 10px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
      }
    }
    #hero {
      color: white; text-align: center;
      padding: 100px 20px;
    }
    #hero h2 { font-size: 2.8em; margin-top: 15px; font-weight: bold;color: black; }
    #hero p { font-size: 1.8em;margin-top: 10px;  color: black; }
    #hero  button{
      background-color:linear-gradient(135deg, #667eea 0%, #764ba2 100%)color: white;
      padding: 14px 28px; text-decoration: none;
      border-radius: 6px; font-weight: bold;
      transition: background 0.3s;
      margin-top: 35px;
       display: inline-block;
      margin-right:35px;
    }
    #hero button:hover {
      background-color: linear-gradient(135deg, #667eea 0%, #764ba2 100%) }
    #hero a{
      text-decoration:none;
    }
    #categories {
      padding: 50px 20px; text-align: center;
    }
    #categories h2 {
      font-size: 2em; 
      color: #0066cc;
      margin-top: -58px;
     
    }
    #categories ul {
      list-style: none; padding: 0;
      display: flex; justify-content: center;
    }
    #categories li {
      margin: 0 20px; font-size: 1.2em;
      background: #e3f2fd; padding: 15px 25px;
      border-radius: 8px; font-weight: 500;
      transition: transform 0.3s;
    }
    #categories li:hover { transform: scale(1.05); }
    #produits article {
      display: inline-block; margin: 20px;
      background: white; padding: 20px;
      border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: 220px; text-align: center;
      transition: transform 0.3s;
    }
    #produits article:hover { transform: translateY(-5px); }
    #produits img { width: 100%; border-radius: 8px; }
    #produits button {
      background: #0066cc; color: white;
      border: none; padding: 12px;
      margin-top: 10px; cursor: pointer;
      border-radius: 6px; font-weight: bold;
      transition: background 0.3s;
    }
    #produits button:hover { background: #004c99; }
    footer {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;
      text-align: center; padding: 20px;
      margin-top: 40px; font-size: 0.9em;
    }
  </style>
</head>
<body>

<header>
  <h1>ShopESA</h1>
  <span>Facile et Rapide!</span>
  <div class="hamburger" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
  </div>
  <nav id="nav-menu">
    <a href="acceuil.php"><i class="fas fa-home"></i> Accueil</a>
    <a href="affiche.php"><i class="fas fa-box"></i> Produits</a>
    <a href="conexion.php"><i class="fas fa-user"></i> Connexion</a>
  </nav>
</header>
<section id="hero">
  <h2>Bienvenue chez ShopESA</h2>
  <p>Votre destination en ligne pour les meilleures offres sur une large gamme de produits.</p>
  <button><a href="affiche.php"><i class="fas fa-box"></i>Voir nos produits</a></button>  <button><a href="conexion.php"><i class="fas fa-user"></i> Se connecter</a>
</section></button>


<section id="categories">
  <h2>Nos catégories phares</h2>
  <ul>
    <li><i class="fas fa-tshirt"></i> Mode</li>
    <li><i class="fas fa-laptop"></i> Électronique</li>
    <li><i class="fas fa-home"></i> Maison</li>
  </ul>
</section>

<footer>
  <p>&copy   2026 ShopTogo | Livraison rapide | Paiement sécurisé</p>
  <p>by Moukaila & Gloria</p>
</footer>

<script>
function toggleMenu() {
  const nav = document.getElementById('nav-menu');
  nav.classList.toggle('show');
}
</script>

</body>
</html>
