<?php

namespace App\Controlleur;

class CartControlleur
{
    public function __construct()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function ajouter()
    {
        // Récupération des données POST
        $produitId = $_POST['id_produit'] ?? null;
        $quantite = $_POST['quantite'] ?? 1;
        $nom = $_POST['nom'] ?? '';
        $prix = $_POST['prix_unitaire'] ?? 0;
        $prixReduit = $_POST['prix_reduit'] ?? $prix;
        
        // Initialiser le panier s'il n'existe pas
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
        
        // Ajouter ou mettre à jour le produit dans le panier
        if ($produitId) {
            if (isset($_SESSION['panier'][$produitId])) {
                // Si le produit est déjà dans le panier, mettre à jour la quantité
                $_SESSION['panier'][$produitId]['quantite'] += $quantite;
            } else {
                // Sinon, ajouter le produit au panier
                $_SESSION['panier'][$produitId] = [
                    'id_produit' => $produitId,
                    'nom' => $nom,
                    'prix_unitaire' => $prix,
                    'prix_reduit' => $prixReduit,
                    'quantite' => $quantite
                ];
            }
            
            $_SESSION['message'] = "Le produit a été ajouté au panier.";
        } else {
            $_SESSION['erreur'] = "Impossible d'ajouter le produit au panier.";
        }
        
        // Rediriger vers la page précédente
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

   

    public function supprimerDuPanier($params)
    {
        $produitId = $params['id_produit'] ?? null;
        
        if ($produitId && isset($_SESSION['panier'][$produitId])) {
            unset($_SESSION['panier'][$produitId]);
            $_SESSION['message'] = "Le produit a été retiré du panier.";
        } else {
            $_SESSION['erreur'] = "Impossible de retirer le produit du panier.";
        }
        
        // Rediriger vers la page précédente
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    public function vider()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['id_utilisateur'])) {
            $_SESSION['erreur'] = "Veuillez vous connecter pour vider votre panier.";
            header('Location: /connexion');
            exit();
        }
        
        // Vider le panier
        if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
            $_SESSION['message'] = "Le panier a été vidé avec succès.";
        } else {
            $_SESSION['erreur'] = "Le panier est déjà vide.";
        }
        
        // Rediriger vers la page précédente ou la page d'accueil
        $redirectUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        header('Location: ' . $redirectUrl);
        exit();
    }
}
