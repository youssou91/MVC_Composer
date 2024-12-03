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
                unset($_SESSION['panier']);
                echo "Commande ajoutée avec succès. ID de la commande : $id_commande";
                header('Location: /mon_profile'); 
            } else {
                echo "Erreur lors de l'ajout de la commande.";
            }
        } catch (\Exception $e) {
            echo "Erreur lors de l'ajout de la commande : " . $e->getMessage();
        }
    }
    
    public function afficherTotalCommande($id_commande) {
        try {
            // Appeler la méthode du modèle
            $total = $this->commandeModel->getOrderTotal($id_commande);

            // Retourner le total au lieu de l'afficher
            return $total;

        } catch (PDOException $e) {
            // Gérer les erreurs et retourner false ou null en cas d'erreur
            return false;
        }
    }

}
?>