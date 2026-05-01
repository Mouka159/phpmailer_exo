<?php
session_start();

// Autoriser uniquement les administrateurs
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'user') !== 'admin') {
    header('Location: connexion.php?message=' . urlencode('Accès admin requis.'));
    exit();
}

// Redirection vers la page d'administration existante
header('Location: admis.php');
exit();
