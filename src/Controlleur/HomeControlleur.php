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
        $this->panier = new PanierModel(); 
    }

    public function index() {
        $this->panier->init();
        $produitModel = new ProduitModel($this->db);
        $produits = $produitModel->getTousLesProduitsAvecPromotions();
        // foreach ($produits as &$produit) {
        //     $produit['prix_reduit'] = $this->calculerPrixReduit(
        //         $produit['prix_unitaire'],
        //         $produit['promo_type'],
        //         $produit['promo_valeur']
        //     );
        // }
        $panier = $this->panier->getContenu();
        require_once '../src/vue/home.php';
    }

    public function ajouterProduit() {
        // Vérifier si les données ont été envoyées via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProduit = $_POST['id_produit'];
            
            $quantite = (int)$_POST['quantite'];
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['panier'])) {
                $_SESSION['panier'] = [];
            }
            if (isset($_SESSION['panier'][$idProduit])) {
                $_SESSION['panier'][$idProduit] += $quantite; 
            } else {
                $_SESSION['panier'][$idProduit] = $quantite; 
            }
            header('Location: /');  
            exit;
        }
    }
    

    public function gererPanier($idProduit = null) {
        // Vérifier si la méthode HTTP est POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($idProduit !== null) {
                unset($_SESSION['panier'][$idProduit]);
            } else {
                $_SESSION['panier'] = [];
            }
            header('Location: /'); 
            exit;
        }
    }
    
    function calculerPrixReduit($prixUnitaire, $promoType, $promoValeur) {
        if ($promoValeur === null || $promoValeur <= 0) {
            return $prixUnitaire; 
        }
        if ($promoType === 'pourcentage') {
            return $prixUnitaire - ($prixUnitaire * $promoValeur / 100);
        } elseif ($promoType === 'fixe') {
            return max(0, $prixUnitaire - $promoValeur);
        }
        return $prixUnitaire;
    }

    
}