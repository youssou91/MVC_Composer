<?php
namespace App\Controlleur;
use App\Modele\ProduitModel;
require_once '../modele/produitModel.php';

class ProduitControlleur {
    private $produitModel;
    private $categorieModel;

    public function __construct($produitModel, $categorieModel) {
        $this->produitModel = $produitModel;
        $this->categorieModel = $categorieModel;
    }
    public static function index() {
        $products = Product::getAllProducts();
        require '../views/produits/index.php';
    }

    public static function show($id) {
        $product = Product::getProductById($id);
        require '../src/views/produits/afficher.php';
    }

    

    // Méthode pour afficher tous les produits
    public function afficherProduits() {
        return $this->produitModel->getAllProduits();
    }

    // Méthode pour afficher toutes les catégories
    public function afficherCategories() {
        return $this->categorieModel->getAllCategories();
    }
    //methode pour ajouter les produits
    public function ajouterProduit($produit) {
        // Récupération de l'image via $_FILES
        $data = [
            'image' => $_FILES['image']  // Ajouter l'image provenant de l'upload
        ];
    
        // Ajout du produit
        $resultat = $this->produitModel->ajoutProduit($produit, $data);
    
        if ($resultat) {
            echo "Produit ajouté avec succès !";
        } else {
            echo "Échec de l'ajout du produit.";
        }
    }
    
}

?>
