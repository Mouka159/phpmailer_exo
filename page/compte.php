<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte - ShopTogo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        header h1 {
            margin: 0;
            font-size: 1.8em;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 600;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #ffd700;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .dashboard {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .sidebar {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .sidebar h3 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.3em;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .sidebar a {
            display: block;
            padding: 12px 15px;
            margin: 5px 0;
            background: #f0f0f0;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover, .sidebar a.active {
            background: #667eea;
            color: white;
            border-left-color: #764ba2;
        }

        .logout-btn {
            background: #e74c3c !important;
            color: white !important;
            text-align: center;
            margin-top: 20px !important;
        }

        .logout-btn:hover {
            background: #c0392b !important;
        }

        .content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        .section h2 {
            color: #667eea;
            font-size: 1.8em;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #764ba2;
        }

        button:active {
            transform: scale(0.98);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #f5c6cb;
        }

        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-box {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }

        .info-box label {
            font-size: 0.9em;
            color: #666;
        }

        .info-box p {
            font-size: 1.1em;
            color: #333;
            margin-top: 5px;
            font-weight: 500;
        }

        .orders-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .order-card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s;
        }

        .order-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .order-id {
            font-weight: 600;
            color: #667eea;
            font-size: 1.1em;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-align: center;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-shipped {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .order-info {
            font-size: 0.95em;
            color: #666;
            margin: 10px 0;
        }

        .order-amount {
            font-size: 1.3em;
            font-weight: 600;
            color: #667eea;
            margin-top: 15px;
        }

        .details-btn {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            margin-top: 10px;
            width: 100%;
            transition: background 0.3s;
        }

        .details-btn:hover {
            background: #764ba2;
        }

        .order-details {
            margin-top: 15px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-name {
            font-weight: 500;
            color: #333;
        }

        .product-qty {
            color: #666;
            font-size: 0.9em;
        }

        .product-price {
            font-weight: 600;
            color: #667eea;
        }

        .empty-message {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 1.1em;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: flex;
                gap: 10px;
            }

            .sidebar a {
                flex: 1;
                text-align: center;
            }

            .form-row,
            .info-row {
                grid-template-columns: 1fr;
            }

            .orders-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php 
    require_once '../logique/compte.php'; 
    ?>

    <header>
        <h1><i class="fas fa-shopping-bag"></i> ShopTogo</h1>
        <nav>
            <a href="affiche.php">Accueil</a>
            <a href="produit.php">Produits</a>
            <a href="panier.php">Panier</a>
            <a href="compte.php">Mon Compte</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Mon Compte</h1>

        <div class="dashboard">
            <div class="sidebar">
                <h3>Navigation</h3>
                <a href="#" onclick="showSection('info')" class="nav-link active">
                    <i class="fas fa-user"></i> Mes Informations
                </a>
                <a href="#" onclick="showSection('password')" class="nav-link">
                    <i class="fas fa-lock"></i> Changer le mot de passe
                </a>
                <a href="#" onclick="showSection('active-orders')" class="nav-link">
                    <i class="fas fa-truck"></i> Commandes en cours
                </a>
                <a href="#" onclick="showSection('history')" class="nav-link">
                    <i class="fas fa-history"></i> Historique
                </a>
                <a href="logique/deconnexion.php" class="nav-link logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>

            <div class="content">
                <!-- Section: Mes Informations -->
                <div id="info" class="section active">
                    <h2><i class="fas fa-user-circle"></i> Mes Informations Personnelles</h2>

                    <?php if (isset($updateMessage)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $updateMessage; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($updateError)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $updateError; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($userInfo): ?>
                        <!-- Affichage des informations -->
                        <div class="info-display" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #667eea;">
                            <h3 style="color: #667eea; margin-bottom: 15px;"><i class="fas fa-id-card"></i> Informations enregistrées</h3>
                            <div class="info-row">
                                <div class="info-box">
                                    <label>Nom complet</label>
                                    <p><?php echo htmlspecialchars($userInfo['prenom'] . ' ' . $userInfo['nom']); ?></p>
                                </div>
                                <div class="info-box">
                                    <label>Email</label>
                                    <p><?php echo htmlspecialchars($userInfo['email']); ?></p>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-box">
                                    <label>Téléphone</label>
                                    <p><?php echo htmlspecialchars($userInfo['telephone'] ?? 'Non renseigné'); ?></p>
                                </div>
                                <div class="info-box">
                                    <label>Adresse</label>
                                    <p><?php echo htmlspecialchars($userInfo['adresse'] ?? 'Non renseignée'); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Formulaire de modification -->
                        <div style="border-top: 1px solid #eee; padding-top: 20px;">
                            <h3 style="color: #667eea; margin-bottom: 15px;"><i class="fas fa-edit"></i> Modifier mes informations</h3>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="update_info">

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="nom"><i class="fas fa-user"></i> Nom</label>
                                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($userInfo['nom'] ?? ''); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="prenom"><i class="fas fa-user"></i> Prénom</label>
                                        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($userInfo['prenom'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email'] ?? ''); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="telephone"><i class="fas fa-phone"></i> Téléphone</label>
                                        <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($userInfo['telephone'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="adresse"><i class="fas fa-map-marker-alt"></i> Adresse</label>
                                    <textarea id="adresse" name="adresse" placeholder="Entrez votre adresse complète"><?php echo htmlspecialchars($userInfo['adresse'] ?? ''); ?></textarea>
                                </div>

                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> Impossible de charger vos informations.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Section: Changer le mot de passe -->
                <div id="password" class="section">
                    <h2><i class="fas fa-lock"></i> Changer le mot de passe</h2>

                    <?php if (isset($passwordMessage)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $passwordMessage; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($passwordError)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $passwordError; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="action" value="change_password">

                        <div class="form-group">
                            <label for="old_password"><i class="fas fa-lock"></i> Ancien mot de passe</label>
                            <input type="password" id="old_password" name="old_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password"><i class="fas fa-lock"></i> Nouveau mot de passe (minimum 8 caractères)</label>
                            <input type="password" id="new_password" name="new_password" minlength="8" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password"><i class="fas fa-lock"></i> Confirmer le nouveau mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password" minlength="8" required>
                        </div>

                        <button type="submit">
                            <i class="fas fa-key"></i> Changer le mot de passe
                        </button>
                    </form>

                    <div style="margin-top: 20px; padding: 15px; background: #f0f8ff; border-radius: 5px; border-left: 4px solid #667eea;">
                        <strong>Conseils de sécurité :</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <li>Utilisez au moins 8 caractères</li>
                            <li>Mélangez majuscules, minuscules et chiffres</li>
                            <li>Évitez les informations personnelles évidentes</li>
                            <li>Ne réutilisez pas d'anciens mots de passe</li>
                        </ul>
                    </div>
                </div>

                <!-- Section: Commandes en cours -->
                <div id="active-orders" class="section">
                    <h2><i class="fas fa-truck"></i> Statut de mes commandes en cours</h2>

                    <?php if ($activeOrders): ?>
                        <div class="orders-container">
                            <?php foreach ($activeOrders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <span class="order-id">#<?php echo $order['id_commande']; ?></span>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['statut'])); ?>">
                                            <?php echo $order['statut']; ?>
                                        </span>
                                    </div>
                                    <div class="order-info">
                                        <i class="fas fa-calendar"></i> <strong>Date :</strong> <?php echo date('d/m/Y', strtotime($order['date_commande'])); ?>
                                    </div>
                                    <div class="order-info">
                                        <i class="fas fa-clock"></i> <strong>Heure :</strong> <?php echo date('H:i', strtotime($order['date_commande'])); ?>
                                    </div>
                                    <div class="order-amount">
                                        <i class="fas fa-money-bill-wave"></i> <?php echo number_format($order['montant_total'], 2, ',', ' '); ?> FCFA
                                    </div>
                                    <button class="details-btn" onclick="toggleDetails(this)">
                                        <i class="fas fa-chevron-down"></i> Afficher les détails
                                    </button>
                                    <div class="order-details" style="display: none;">
                                        <?php 
                                        $details = getOrderDetails($pdo, $order['id_commande']);
                                        if ($details):
                                            foreach ($details as $item): ?>
                                                <div class="product-item">
                                                    <div>
                                                        <div class="product-name"><?php echo htmlspecialchars($item['nom_produit']); ?></div>
                                                        <div class="product-qty">Quantité : <?php echo $item['quantite']; ?></div>
                                                    </div>
                                                    <div class="product-price"><?php echo number_format($item['prix'] * $item['quantite'], 2, ',', ' '); ?> FCFA</div>
                                                </div>
                                            <?php endforeach;
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-message">
                            <i class="fas fa-inbox" style="font-size: 2em; margin-bottom: 10px; display: block; color: #ccc;"></i>
                            Vous n'avez pas de commandes en cours.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Section: Historique des commandes -->
                <div id="history" class="section">
                    <h2><i class="fas fa-history"></i> Historique de mes commandes</h2>

                    <?php if ($orderHistory): ?>
                        <div class="orders-container">
                            <?php foreach ($orderHistory as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <span class="order-id">#<?php echo $order['id_commande']; ?></span>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['statut'])); ?>">
                                            <?php echo $order['statut']; ?>
                                        </span>
                                    </div>
                                    <div class="order-info">
                                        <i class="fas fa-calendar"></i> <strong>Date :</strong> <?php echo date('d/m/Y', strtotime($order['date_commande'])); ?>
                                    </div>
                                    <div class="order-info">
                                        <i class="fas fa-clock"></i> <strong>Heure :</strong> <?php echo date('H:i', strtotime($order['date_commande'])); ?>
                                    </div>
                                    <div class="order-amount">
                                        <i class="fas fa-money-bill-wave"></i> <?php echo number_format($order['montant_total'], 2, ',', ' '); ?> FCFA
                                    </div>
                                    <button class="details-btn" onclick="toggleDetails(this)">
                                        <i class="fas fa-chevron-down"></i> Afficher les détails
                                    </button>
                                    <div class="order-details" style="display: none;">
                                        <?php 
                                        $details = getOrderDetails($pdo, $order['id_commande']);
                                        if ($details):
                                            foreach ($details as $item): ?>
                                                <div class="product-item">
                                                    <div>
                                                        <div class="product-name"><?php echo htmlspecialchars($item['nom_produit']); ?></div>
                                                        <div class="product-qty">Quantité : <?php echo $item['quantite']; ?></div>
                                                    </div>
                                                    <div class="product-price"><?php echo number_format($item['prix'] * $item['quantite'], 2, ',', ' '); ?> FCFA</div>
                                                </div>
                                            <?php endforeach;
                                        endif;
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-message">
                            <i class="fas fa-inbox" style="font-size: 2em; margin-bottom: 10px; display: block; color: #ccc;"></i>
                            Vous n'avez pas d'historique de commandes.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bouton de déconnexion en bas de page -->
        <div style="text-align: center; margin-top: 40px; padding: 20px; border-top: 1px solid #eee;">
            <a href="../logique/deconnexion.php" class="logout-btn" style="display: inline-block; padding: 12px 30px; background: #e74c3c; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s;">
                <i class="fas fa-sign-out-alt"></i> Se déconnecter
            </a>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Masquer toutes les sections
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));

            // Afficher la section sélectionnée
            document.getElementById(sectionId).classList.add('active');

            // Mettre à jour les liens actifs
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => link.classList.remove('active'));
            event.target.closest('.nav-link').classList.add('active');

            // Empêcher la navigation
            event.preventDefault();
        }

        function toggleDetails(button) {
            const details = button.nextElementSibling;
            if (details.style.display === 'none') {
                details.style.display = 'block';
                button.innerHTML = '<i class="fas fa-chevron-up"></i> Masquer les détails';
            } else {
                details.style.display = 'none';
                button.innerHTML = '<i class="fas fa-chevron-down"></i> Afficher les détails';
            }
        }

        // Initialiser la navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.href === 'logique/deconnexion.php') {
                    return; // Permettre la déconnexion
                }
                e.preventDefault();
                const sectionId = this.getAttribute('href').substring(1);
                showSection(sectionId);
                
                // Mettre à jour l'état actif
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
