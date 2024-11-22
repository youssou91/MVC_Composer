<?php
namespace App\Controlleur;
require_once __DIR__ . '/../../config/db.php';
use App\Modele\ProduitModel;
use App\Modele\CategorieModel;

class ProduitControlleur {
    private $produitModel;
    private $categorieModel;

    public function __construct(ProduitModel $produitModel, CategorieModel $categorieModel) {
        $this->produitModel = $produitModel;
        $this->categorieModel = $categorieModel;
    }

    public function index() {
        require __DIR__ . '/../vue/Produits.php';
            
    }

    public function afficherProduits() {
        return $this->produitModel->getAllProduits();
    }

    public function afficherCategories() {
        return $this->categorieModel->getAllCategories();
    }

    public function ajouterProduit($produit) {
        require __DIR__ . '/../vue/ajout.php';
        $data = [
            'image' => $_FILES['image']
        ];
        $resultat = $this->produitModel->ajoutProduit($produit, $data);
        if ($resultat) {
            echo "Produit ajouté avec succès !";
        } else {
            echo "Échec de l'ajout du produit.";
        }
    }
}
?>