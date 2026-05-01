<?php
require_once '../config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../page/conexion.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
function getUserInfo($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT id, nom, prenom, email, telephone, adresse FROM utilisateur WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    return $stmt->fetch();
}

// Mettre à jour les informations personnelles
function updateUserInfo($pdo, $userId, $nom, $prenom, $email, $telephone, $adresse) {
    try {
        $stmt = $pdo->prepare("UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, adresse = :adresse WHERE id = :id");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':adresse' => $adresse,
            ':id' => $userId
        ]);
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['email'] = $email;
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Changer le mot de passe
function changePassword($pdo, $userId, $oldPassword, $newPassword, $confirmPassword) {
    // Vérifier que les nouveaux mots de passe correspondent
    if ($newPassword !== $confirmPassword) {
        return ['success' => false, 'message' => 'Les nouveaux mots de passe ne correspondent pas'];
    }

    // Vérifier la longueur du mot de passe
    if (strlen($newPassword) < 8) {
        return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères'];
    }

    // Récupérer le mot de passe actuel
    $stmt = $pdo->prepare("SELECT mdp FROM utilisateur WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Utilisateur non trouvé'];
    }

    // Vérifier l'ancien mot de passe
    if (!password_verify($oldPassword, $user['mdp'])) {
        return ['success' => false, 'message' => 'L\'ancien mot de passe est incorrect'];
    }

    // Hasher et mettre à jour le nouveau mot de passe
    try {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE utilisateur SET mdp = :mdp WHERE id = :id");
        $stmt->execute([
            ':mdp' => $hashedPassword,
            ':id' => $userId
        ]);
        return ['success' => true, 'message' => 'Mot de passe changé avec succès'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe'];
    }
}

// Récupérer l'historique des commandes
function getOrderHistory($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("
            SELECT id_commande, date_commande, montant_total, statut 
            FROM Commandes 
            WHERE utilisateur_id = :user_id 
            ORDER BY date_commande DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Récupérer les détails d'une commande
function getOrderDetails($pdo, $orderId) {
    try {
        $stmt = $pdo->prepare("
            SELECT cp.*, p.nom_produit, p.prix 
            FROM Commande_Produits cp
            JOIN Produits p ON cp.produit_id = p.id_produit
            WHERE cp.commande_id = :order_id
        ");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Récupérer les commandes en cours
function getActiveOrders($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("
            SELECT id_commande, date_commande, montant_total, statut 
            FROM Commandes 
            WHERE utilisateur_id = :user_id 
            AND statut IN ('En attente', 'Expédiée')
            ORDER BY date_commande DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_info') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $adresse = trim($_POST['adresse'] ?? '');

            if (updateUserInfo($pdo, $userId, $nom, $prenom, $email, $telephone, $adresse)) {
                $updateMessage = 'Informations mises à jour avec succès';
            } else {
                $updateError = 'Erreur lors de la mise à jour des informations';
            }
        } elseif ($_POST['action'] === 'change_password') {
            $oldPassword = $_POST['old_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $result = changePassword($pdo, $userId, $oldPassword, $newPassword, $confirmPassword);
            if ($result['success']) {
                $passwordMessage = $result['message'];
            } else {
                $passwordError = $result['message'];
            }
        }
    }
}

// Récupérer les informations actuelles
$userInfo = getUserInfo($pdo, $userId);
$activeOrders = getActiveOrders($pdo, $userId);
$orderHistory = getOrderHistory($pdo, $userId);

?>
