<?php

namespace App\Controlleur;

use App\Modele\ProduitModel;
use App\Modele\PanierModel;

require_once __DIR__ . '/../../config/db.php';

class HomeControlleur {
    private $db;
    private $panier;

    public function __construct() {
        $this->db = getConnection();
        $this->panier = new PanierModel();  // Créer une instance de PanierModele
    }

    // src/Controlleur/HomeControlleur.php
    public function index() {
        $this->panier->init();
        $produitModel = new ProduitModel($this->db);
        $produits = $produitModel->getTousLesProduitsAvecPromotions();

        foreach ($produits as &$produit) {
            $produit['prix_reduit'] = $this->calculerPrixReduit(
                $produit['prix_unitaire'],
                $produit['promo_type'],
                $produit['promo_valeur']
            );
        }

        $panier = $this->panier->getContenu();
        require_once '../src/vue/home.php';
    }

    // public function index() {
    //     // Initialiser le panier
    //     $this->panier->init();  // Appeler la méthode non statique sur l'instance

    //     // Récupérer les produits
    //     $produitModel = new ProduitModel($this->db);
    //     $produits = $produitModel->getTousLesProduitsAvecPromotions(); 

    //     // Traiter les actions POST
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         if (isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    //             $idProduit = $_POST['id_produit'];
    //             $quantite = (int)$_POST['quantite'];
    //             $this->panier->ajouter($idProduit, $quantite);  // Appeler la méthode non statique
    //         }

    //         if (isset($_POST['action']) && $_POST['action'] === 'supprimer') {
    //             $idProduit = $_POST['id_produit'];
    //             $this->panier->supprimer($idProduit);  // Appeler la méthode non statique
    //         }
    //     }

    //     // Récupérer le contenu du panier
    //     $panier = $this->panier->getContenu();  // Appeler la méthode non statique
    //     require_once '../src/vue/home.php';
    // }

    // Méthode pour ajouter un produit au panier
    public function ajouterProduit() {
        // Vérifier si les données ont été envoyées via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les informations envoyées dans le formulaire
            $idProduit = $_POST['id_produit'];
            $quantite = (int)$_POST['quantite']; // La quantité d'article à ajouter

            // Ajouter le produit au panier en utilisant la méthode ajouter de PanierModele
            $this->panier->ajouter($idProduit, $quantite);

            // Rediriger l'utilisateur après l'ajout au panier (par exemple vers la page du panier)
            header('Location: /');  // Rediriger vers la page du panier ou autre action
            exit;
        }
    }

    public function gererPanier($idProduit = null) {
        // Vérifier si la méthode HTTP est POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($idProduit !== null) {
                // Si un ID de produit est fourni, supprimer ce produit spécifique
                unset($_SESSION['panier'][$idProduit]);
            } else {
                // Si aucun ID de produit, vider tout le panier
                $_SESSION['panier'] = [];
            }
    
            // Rediriger l'utilisateur après l'opération
            header('Location: /'); // Redirection vers la page du panier
            exit;
        }
    }
    
    // Méthode pour calculer le prix réduit d'un produit
    function calculerPrixReduit($prixUnitaire, $promoType, $promoValeur) {
    if ($promoValeur === null || $promoValeur <= 0) {
        return $prixUnitaire; // Pas de réduction
    }
    if ($promoType === 'pourcentage') {
        return $prixUnitaire - ($prixUnitaire * $promoValeur / 100);
    } elseif ($promoType === 'fixe') {
        return max(0, $prixUnitaire - $promoValeur);
    }
    return $prixUnitaire;
}

    
}
