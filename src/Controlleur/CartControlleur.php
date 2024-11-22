<?php

namespace App\Controlleur;

use App\Modele\CartModel;

class CartControlleur
{
    private $cartModel;

    public function __construct(CartModel $cartModel)
    {
        $this->cartModel = $cartModel;
    }

    public function ajouter()
    {
        // Récupération des données POST
        $produitId = $_POST['produit_id'] ?? null;
        $quantite = $_POST['quantite'] ?? 1;
        $userId = $_SESSION['user_id'] ?? null; // Assurez-vous que l'utilisateur est connecté

        if ($produitId && $userId) {
            $this->cartModel->ajouterAuPanier($produitId, $quantite, $userId);
            echo "Produit ajouté au panier.";
        } else {
            http_response_code(400);
            echo "Erreur : Produit ou utilisateur manquant.";
        }
    }

    public function afficher()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $panier = $this->cartModel->obtenirPanierParUtilisateur($userId);
            require '../src/vue/vue_panier.php'; 
        } else {
            http_response_code(403);
            echo "Veuillez vous connecter pour accéder à votre panier.";
        }
    }

    public function vider()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $this->cartModel->viderPanier($userId);
            echo "Panier vidé.";
        } else {
            http_response_code(403);
            echo "Veuillez vous connecter pour vider votre panier.";
        }
    }
}
