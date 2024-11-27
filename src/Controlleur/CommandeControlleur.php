<?php
namespace App\Controlleur;
require_once __DIR__ . '/../../config/db.php';

use App\Modele\CommandeModel;

class CommandeControlleur {
    private $commandeModel; 

    public function __construct(CommandeModel $commandeModel) {
        $this->commandeModel = $commandeModel; 
    }
    

    public function index() {
        require_once '../src/vue/Commandes.php';
    }
    // Méthode pour lister les commandes
    public function listCommandes() {
        return $this->commandeModel->getAllCommandes(); // Appel au modèle
    }
    // Afficher une commande spécifique
    public function afficherCommande($id_commande) {
        try {
            $commande = $this->commandeModel->getCommandeById($id_commande);
            if ($commande) {
                header('Content-Type: application/json');
                echo json_encode($commande);
            } else {
                echo "Commande introuvable.";
            }
        } catch (\Exception $e) {
            echo "Erreur lors de la récupération de la commande : " . $e->getMessage();
        }
    }
    // Ajouter une nouvelle commande
    public function ajouterCommande($data) {
        try {
            $id_commande = $this->commandeModel->addCommande($data);
            if ($id_commande) {
                echo "Commande ajoutée avec succès. ID de la commande : $id_commande";
            } else {
                echo "Erreur lors de l'ajout de la commande.";
            }
        } catch (\Exception $e) {
            echo "Erreur lors de l'ajout de la commande : " . $e->getMessage();
        }
    }
    // Exemple d'une méthode pour traiter un formulaire (par exemple en POST)
    public function traiterAjoutCommande() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true); // Pour les requêtes JSON
            if ($data && isset($data['id_utilisateur'], $data['prix_total'], $data['produits'])) {
                $this->ajouterCommande($data);
            } else {
                echo "Données invalides pour ajouter une commande.";
            }
        } else {
            echo "Méthode non autorisée.";
        }
    }

    
}
?>