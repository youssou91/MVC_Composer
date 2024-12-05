<?php

session_start();
// session_unset();
// session_destroy();

// require_once __DIR__ . '/../home.php'; // Exemple de chemin vers la vue


// Exemple de déconnexion
if (isset($_POST['action']) && $_POST['action'] === 'deconnexion') {
    unset($_SESSION['id_utilisateur']); // Supprimer les informations de connexion
    // Le panier reste dans la session
    header("Location: /connexion.php");
    session_destroy();
    exit;
    // header('Location: login.php'); // Rediriger vers la page de connexion
}
