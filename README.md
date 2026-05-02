# TP Classe 15 - Application E-commerce

## Description

Cette application web est un système de commerce électronique développé en PHP avec une base de données MySQL. Elle permet aux utilisateurs de s'inscrire, se connecter, parcourir les produits, ajouter des articles au panier, passer des commandes et aux administrateurs de gérer les commandes et les livraisons.

## Fonctionnalités

### Pour les utilisateurs :
- Inscription et connexion (avec vérification OTP pour les clients)
- Consultation des produits
- Gestion du panier (ajouter, supprimer, modifier quantités)
- Passage de commandes
- Suivi des commandes (actives et historiques)
- Gestion du compte utilisateur

### Pour les administrateurs :
- Connexion dédiée
- Gestion des commandes du jour
- Marquage des commandes comme livrées
- Envoi automatique d'emails de confirmation de livraison

## Technologies utilisées

- **Backend** : PHP 7+
- **Base de données** : MySQL
- **Email** : PHPMailer
- **Frontend** : HTML5, CSS3, JavaScript
- **Serveur** : XAMPP (Apache + MySQL)

## Prérequis

- XAMPP installé et configuré
- PHP 7.0 ou supérieur
- MySQL 5.7 ou supérieur
- Navigateur web moderne

## Installation

1. **Cloner ou télécharger le projet** dans le dossier `htdocs` de XAMPP :
   ```
   c:\xampp\htdocs\TP_classe15
   ```

2. **Démarrer XAMPP** :
   - Lancer Apache et MySQL depuis le panneau de contrôle XAMPP

3. **Configurer la base de données** :
   - Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
   - Créer une nouvelle base de données nommée `tp_classe15`
   - Importer les fichiers SQL dans l'ordre :
     - `config/create_orders_tables.sql`
     - `config/add_unique_constraints.sql`
     - `config/utilisateurusers (2).sql`
     - `config/setup_categories.sql` (si applicable)

4. **Configurer la connexion DB** :
   - Vérifier le fichier `config/db.php` pour les paramètres de connexion

5. **Accéder à l'application** :
   - Ouvrir un navigateur et aller à : `http://localhost/TP_classe15/page/acceuil.php`

## Structure du projet

```
TP_classe15/
├── config/           # Fichiers de configuration et SQL
│   ├── db.php
│   ├── create_orders_tables.sql
│   ├── add_unique_constraints.sql
│   └── utilisateurusers (2).sql
├── image/            # Images du projet
├── logique/          # Logique métier (backend)
│   ├── affiche.php
│   ├── compte.php
│   ├── connexion.php
│   ├── deconnexion.php
│   ├── inscri.php
│   ├── panier.php
│   ├── produit.php
│   ├── produit_panier.php
│   └── verify.php
├── page/             # Pages frontend
│   ├── acceuil.php
│   ├── admin.php
│   ├── admis.php
│   ├── affiche.php
│   ├── commande.php
│   ├── commandes_jour.php
│   ├── compte.php
│   ├── conexion.php
│   ├── correct.php
│   ├── fail.php
│   ├── inscri.php
│   ├── panier.php
│   ├── produit.php
│   ├── sendmail.php
│   ├── success.php
│   └── traitement_commande.php
└── PHPMailer-master/ # Bibliothèque pour l'envoi d'emails
```

## Utilisation

### Inscription :
- Aller sur la page d'inscription (`inscri.php`)
- Choisir le rôle (Client ou Administrateur)
- Remplir le formulaire
- Pour les admins, entrer le code d'accès
- Vérifier l'email (OTP pour les clients)

### Connexion :
- Utiliser la page de connexion (`conexion.php`)
- Les admins sont redirigés vers `admin.php`
- Les clients vers `affiche.php`

### Gestion des produits :
- Parcourir les produits sur `affiche.php`
- Ajouter au panier
- Voir le panier sur `panier.php`

### Passer une commande :
- Depuis le panier, procéder au checkout
- Remplir les détails de livraison
- Confirmer la commande

### Administration :
- Se connecter en tant qu'admin
- Voir les commandes du jour sur `commandes_jour.php`
- Marquer les commandes comme livrées (envoi automatique d'email)

## Configuration email

Pour l'envoi d'emails (confirmation de livraison), configurer PHPMailer dans `page/commandes_jour.php` :

```php
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com'; // ou autre fournisseur
$mail->SMTPAuth = true;
$mail->Username = 'votre-email@gmail.com';
$mail->Password = 'votre-mot-de-passe';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```

## Notes importantes

- Assurer que les permissions des dossiers permettent l'écriture (pour les logs si nécessaire)
- Vérifier que le port 587 (ou équivalent) n'est pas bloqué par le firewall
- Pour la production, utiliser des variables d'environnement pour les mots de passe

## Auteurs

TOVO Moukaïla & SODOGA Afi Gloria

## Licence

Ce projet est à usage éducatif.