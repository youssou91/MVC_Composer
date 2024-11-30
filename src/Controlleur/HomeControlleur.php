<?php

namespace App\Controlleur;

use App\Modele\ProduitModel;

require_once __DIR__ . '/../../config/db.php';

class HomeControlleur {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function index() {
        $produitModel = new ProduitModel($this->db);
        $produits = $produitModel->getTousLesProduitsAvecPromotions();
        $panier = $_SESSION['panier'] ?? [];
        require_once '../src/vue/home.php';
    }

    public function ajouterProduit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProduit = $_POST['id_produit'] ?? null;
            $quantite = (int)($_POST['quantite'] ?? 1);
            // Prix réduit envoyé depuis le formulaire
            $prixReduit = $_POST['prix_reduit'] ?? null; 
    
            if (!$idProduit || $quantite <= 0) {
                // Redirection en cas d'erreur
                header('Location: /'); 
                exit;
            }
    
            if (!isset($_SESSION['panier'])) {
                $_SESSION['panier'] = [];
            }
    
            if (isset($_SESSION['panier'][$idProduit])) {
                // Mise à jour de la quantité si le produit existe déjà dans le panier
                $_SESSION['panier'][$idProduit]['quantite'] += $quantite;
            } else {
                // Ajout du produit avec les informations nécessaires
                $_SESSION['panier'][$idProduit] = [
                    'quantite' => $quantite,
                    // Priorité au prix réduit
                    'prix_unitaire' => $prixReduit ? $prixReduit : $_POST['prix_unitaire'], 
                    'nom' => $_POST['nom'],
                    'promo_type' => $_POST['promo_type'] ?? null,
                    'promo_valeur' => $_POST['promo_valeur'] ?? null,
                ];
            }
    
            header('Location: /');
            exit;
        }
    }

    public function gererPanier() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProduit = $_POST['id_produit'] ?? null;
            $action = $_POST['action'] ?? null;

            if ($action === 'supprimer' && $idProduit !== null) {
                unset($_SESSION['panier'][$idProduit]);
            } elseif ($action === 'vider') {
                $_SESSION['panier'] = [];
            }

            header('Location: /');
            exit;
        }
    }
}
