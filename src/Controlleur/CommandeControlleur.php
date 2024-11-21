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

    // Méthode pour mettre à jour une commande
    public function updateCommande($id, $date, $prix, $utilisateur, $statut) {
        return $this->commandeModel->updateCommande($id, $date, $prix, $utilisateur, $statut);
    }
}
?>