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

        // Ajoute le produit
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

        // Si une image est envoyée, l'upload
        if (isset($data['chemin_image'])) {
            $this->produitModel->uploadImage($data['chemin_image']);
        }

        // Retour à la vue pour afficher après ajout
        $message = "Produit ajouté avec succès!";
        require __DIR__ . '/../vue/ajout.php'; // Revenir au formulaire avec le message
    }
    
    public function recupererProduit($id) {
        $produit = $this->produitModel->getProduitById($id);
        require __DIR__. '/../vue/modifierProduit.php';

    }
    
    public function produitUpdate($id, array $data) {
        // Créez l'objet produit à partir des données
        $produit = new Produits(
            $data['nom'],
            $data['prix_unitaire'],
            $data['quantite'],
            $data['id_categorie'],
            $data['model'],
            $data['courte_description'],
            $data['longue_description']?? null,
            $data['couleurs']?? null,
            $data['chemin_image']?? null
        );
        
        // Mettre à jour le produit
        $this->produitModel->updateProduit(
            $id,
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
        
        // Si une image est envoyée, l'upload
        if (isset($data['chemin_image'])) {
            $this->produitModel->uploadImage($data['chemin_image']);
        }
        
        // Retour à la liste des produits
        header('Location: /produits');
        exit;
    }
    
    public function supprimerProduit($id) {
        if (isset($id) && is_numeric($id)) {
            $result = $this->produitModel->deleteProduit($id);
            if ($result) {
                $message = "Produit supprimé avec succès.";
            } else {
                $message = "Erreur lors de la suppression du produit.";
            }
        } else {
            throw new \Exception("ID invalide pour la suppression.");
        }
        // Retour à la liste des produits avec un message
        header('Location: /produits');
    }
}
