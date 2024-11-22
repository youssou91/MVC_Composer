<?php

namespace App\Modele;

class CartModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ajouter un produit au panier
     */
    public function ajouterAuPanier($idUtilisateur, $idProduit, $quantite = 1)
    {
        $sql = "INSERT INTO panier (id_utilisateur, id_produit, quantite) 
                VALUES (:id_utilisateur, :id_produit, :quantite)
                ON DUPLICATE KEY UPDATE quantite = quantite + :quantite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur, \PDO::PARAM_INT);
        $stmt->bindValue(':id_produit', $idProduit, \PDO::PARAM_INT);
        $stmt->bindValue(':quantite', $quantite, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Modifier la quantité d'un produit dans le panier
     */
    public function modifierQuantite($idUtilisateur, $idProduit, $quantite)
    {
        $sql = "UPDATE panier SET quantite = :quantite 
                WHERE id_utilisateur = :id_utilisateur AND id_produit = :id_produit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':quantite', $quantite, \PDO::PARAM_INT);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur, \PDO::PARAM_INT);
        $stmt->bindValue(':id_produit', $idProduit, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprimer un produit du panier
     */
    public function supprimerDuPanier($idUtilisateur, $idProduit)
    {
        $sql = "DELETE FROM panier 
                WHERE id_utilisateur = :id_utilisateur AND id_produit = :id_produit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur, \PDO::PARAM_INT);
        $stmt->bindValue(':id_produit', $idProduit, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Récupérer les produits du panier pour un utilisateur
     */
    public function obtenirPanier($idUtilisateur)
    {
        $sql = "SELECT p.id_produit, p.nom AS nom_produit, p.prix, panier.quantite, 
                       (p.prix * panier.quantite) AS total
                FROM panier
                JOIN produits p ON panier.id_produit = p.id_produit
                WHERE panier.id_utilisateur = :id_utilisateur";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Vider le panier pour un utilisateur
     */
    public function viderPanier($idUtilisateur)
    {
        $sql = "DELETE FROM panier WHERE id_utilisateur = :id_utilisateur";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
