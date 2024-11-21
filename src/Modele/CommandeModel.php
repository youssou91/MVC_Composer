<?php
namespace App\Modele;
use PDO;
use App\Classes\Commande;  

class CommandeModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllCommandes() {
        $stmt = $this->pdo->prepare("SELECT 
            commande.id_commande, 
            commande.id_utilisateur, 
            commande.date_commande, 
            commande.prix_total, 
            utilisateur.nom_utilisateur, 
            utilisateur.prenom,
            commande.statut
        FROM 
            commande
        INNER JOIN  utilisateur ON commande.id_utilisateur = utilisateur.id_utilisateur;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCommandeById($id_commande) {
        $stmt = $this->db->prepare("SELECT * FROM commandes WHERE id_commande = :id_commande");
        $stmt->bindParam(':id_commande', $id_commande, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCommande($id_utilisateur, $date_commande, $prix_total, $statut) {
        $stmt = $this->db->prepare("INSERT INTO commandes (id_utilisateur, date_commande, prix_total, statut) VALUES (:id_utilisateur, :date_commande, :prix_total, :statut)");
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':date_commande', $date_commande);
        $stmt->bindParam(':prix_total', $prix_total);
        $stmt->bindParam(':statut', $statut);
        return $stmt->execute();
    }

    public function updateCommande($id_commande, $id_utilisateur, $date_commande, $prix_total, $statut) {
        $stmt = $this->db->prepare("UPDATE commandes SET id_utilisateur = :id_utilisateur, date_commande = :date_commande, prix_total = :prix_total, statut = :statut WHERE id_commande = :id_commande");
        $stmt->bindParam(':id_commande', $id_commande, PDO::PARAM_INT);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':date_commande', $date_commande);
        $stmt->bindParam(':prix_total', $prix_total);
        $stmt->bindParam(':statut', $statut);
        return $stmt->execute();
    }

    public function deleteCommande($id_commande) {
        $stmt = $this->db->prepare("DELETE FROM commandes WHERE id_commande = :id_commande");
        $stmt->bindParam(':id_commande', $id_commande, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

?>