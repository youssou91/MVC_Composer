<?php
namespace App\Modele;
use PDO;
use App\Classes\Commande;  
use Exception;

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
        $stmt = $this->pdo->prepare("SELECT * FROM commandes WHERE id_commande = :id_commande");
        $stmt->bindParam(':id_commande', $id_commande, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function addCommande($commande) {
        echo '<pre>';
        print_r($commande);
        echo '</pre>';
        if (!isset($commande['id_utilisateur']) || !isset($commande['prix_total'])) {
            throw new Exception("Les informations requises pour ajouter une commande sont manquantes.");
        }
    
        $id_utilisateur = $commande['id_utilisateur'];
        $prix_total = $commande['prix_total'];
        $statut = 'En attente';
    
        try {
            // Début de la transaction
            $this->pdo->beginTransaction();
    
            // Insertion dans la table commande
            $sql = "INSERT INTO commande (id_utilisateur, statut, date_commande, prix_total) VALUES (:id_utilisateur, :statut, NOW(), :prix_total)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_utilisateur' => $id_utilisateur,
                ':statut' => $statut,
                ':prix_total' => $prix_total
            ]);
            // Récupération de l'ID de la commande créée
            $id_commande = $this->pdo->lastInsertId();
    
            // Insertion des produits de la commande
            foreach ($commande['produits'] as $produit) {
                if (!$this->addProduitCommande($id_commande, $produit)) {
                    throw new Exception("Erreur lors de l'ajout d'un produit à la commande.");
                }
            }
    
            // Validation de la transaction
            $this->pdo->commit();
            return $id_commande;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erreur lors de l'ajout de la commande : " . $e->getMessage());
        }
    }
    
    function addProduitCommande($id_commande, $produit) {
        $id_produit = $produit['id_produit'];
        $quantite = $produit['quantite'];
    
        try {
            // Vérifiez si le produit existe dans la table produits
            $stmtCheckProduit = $this->pdo->prepare("SELECT id_produit FROM produits WHERE id_produit = :id_produit");
            $stmtCheckProduit->execute([':id_produit' => $id_produit]);
    
            if ($stmtCheckProduit->rowCount() > 0) {
                // Insertion dans produit_commande
                $stmtProduitCommande = $this->pdo->prepare(
                    "INSERT INTO produit_commande (id_commande, id_produit, quantite) VALUES (:id_commande, :id_produit, :quantite)"
                );
                $stmtProduitCommande->execute([
                    ':id_commande' => $id_commande,
                    ':id_produit' => $id_produit,
                    ':quantite' => $quantite
                ]);
    
                // Mise à jour de la quantité
                if (!$this->miseAJourQuantiteProduit($id_produit, $quantite)) {
                    throw new Exception("Erreur lors de la mise à jour de la quantité du produit.");
                }
            } else {
                throw new Exception("Le produit avec l'ID $id_produit n'existe pas.");
            }
    
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur dans addProduitCommande : " . $e->getMessage());
        }
    }
    
    function miseAJourQuantiteProduit($id_produit, $quantite) {
        try {
            $sql = "UPDATE produits SET quantite = quantite - :quantite WHERE id_produit = :id_produit";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':quantite' => $quantite,
                ':id_produit' => $id_produit
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur dans miseAJourQuantiteProduit : " . $e->getMessage());
        }
    }
    
    
    
    
}

?>