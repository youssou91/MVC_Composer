<?php
namespace App\Controlleur;

require_once __DIR__ . '/../../config/db.php';

use App\Modele\ProduitModel;
use App\Modele\CategorieModel;
use App\Classes\Produits;

class ProduitControlleur {
    private $produitModel;
    private $categorieModel;

    public function __construct(ProduitModel $produitModel, CategorieModel $categorieModel) {
        $this->produitModel = $produitModel;
        $this->categorieModel = $categorieModel;
    }

    public function index() {
        $produits = $this->produitModel->getAllProduits();
        require __DIR__ . '/../vue/Produits.php';
    }

    public function afficherProduits() {
        return $this->produitModel->getAllProduits();
    }

    public function afficherCategories() {
        return $this->categorieModel->getAllCategories();
    }
    
    public function afficheForm() {
        require __DIR__ . '/../vue/ajout.php';
    }

    public function ajouterProduit(array $data) {
        // Créez l'objet produit à partir des données
        $produit = new Produits(
            $data['nom'],
            $data['prix_unitaire'],
            $data['quantite'],
            $data['id_categorie'],
            $data['model'],
            $data['courte_description'],
            $data['longue_description'] ?? null,
            $data['couleurs'] ?? null,
            $data['chemin_image'] ?? null
        );
    
        // Ajoutez le produit sans image
        $idProduit = $this->produitModel->ajouterProduit(
            $produit->getNom(),
            $produit->getPrixUnitaire(),
            $produit->getQuantite(),
            $produit->getIdCategorie(),
            $produit->getModel(),
            $produit->getCourteDescription(),
            $produit->getDescription(),
            $produit->getCouleursProd(),
            $produit->getCheminImage()
        );
    
        // Vérifiez si une image a été envoyée et uploadée
        if (isset($data['image'])) {
            // Appelez la fonction pour uploader l'image et l'ajouter au produit
            $this->produitModel->uploadImage($data, $idProduit);
        }
    
        // Affichez la vue après l'ajout du produit
        require __DIR__ . '/../vue/ajout.php';
    }
    
    
}
