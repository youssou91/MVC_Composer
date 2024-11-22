<?php
namespace App\Modele;
use App\Classes\Produit; 
use \PDO;

class PromotionModel {
    private $db; // Propriété pour la connexion PDO

    public function __construct($pdo) {
        $this->db = $pdo; // Initialisation avec l'objet PDO
    }

    // Méthode pour récupérer les promotions
    public function getAllPromotions()
    {
        $query = "
            SELECT pp.id_promotion, p.nom AS nom_produit, pr.code_promotion, pr.valeur, pr.date_debut, pr.date_fin 
          FROM produitpromotion pp 
          JOIN produits p ON pp.id_produit = p.id_produit
          JOIN promotions pr ON pp.id_promotion = pr.id_promotion
        ";

        // Préparer et exécuter la requête
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour ajouter une promotion
    public function ajouterPromotion($valeur, $date_debut, $date_fin) {
        $query = "INSERT INTO promotions (valeur, date_debut, date_fin) VALUES (:valeur, :date_debut, :date_fin)";

        // Préparer la requête
        $stmt = $this->db->prepare($query);

        // Lier les paramètres
        $stmt->bindParam(':valeur', $valeur, PDO::PARAM_INT);
        $stmt->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
        $stmt->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);

        // Exécuter la requête
        $stmt->execute();

        // Retourner l'ID de la promotion insérée
        return $this->db->lastInsertId();
    }

    // Méthode pour associer un produit à une promotion
    public function associerProduitPromotion($id_produit, $id_promotion) {
        $query = "INSERT INTO produitpromotion (id_produit, id_promotion) VALUES (:id_produit, :id_promotion)";

        // Préparer la requête
        $stmt = $this->db->prepare($query);

        // Lier les paramètres
        $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
        $stmt->bindParam(':id_promotion', $id_promotion, PDO::PARAM_INT);

        // Exécuter la requête
        return $stmt->execute();
    }

    // Méthode pour récupérer les produits
    public function getProduits() {
        $query = "SELECT id_produit, nom FROM produits";

        // Préparer et exécuter la requête
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>