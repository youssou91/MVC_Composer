<?php
namespace App\Modele;
use App\Classes\Produit; 
use \PDO;


class ProduitModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Méthode pour récupérer tous les produits
    public function getAllProduits() {
        // $sql = "SELECT * FROM produits";
        $stmt = $this->pdo->prepare("SELECT p.*, i.chemin_image FROM produits p LEFT JOIN image i ON p.id_produit = i.id_produit;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // try {
        //     // Préparation de la requête
        //     $query = $this->pdo->prepare($sql);
        //     $query->execute();
        //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
            // $produits = [];
            // while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            //     // Traitement des couleurs
            //     $couleurs = !empty($row['couleurs_prod']) ? explode(',', $row['couleurs_prod']) : [];
                
            //     // Création d'un objet Produit
            //     $produits[] = new Produit(
            //         $row['nom'],
            //         $row['prix_unitaire'],
            //         $row['description'],
            //         $row['courte_description'],
            //         $row['quantite'],
            //         $row['id_categorie'],
            //         $couleurs,
            //         $row['model']
            //     );
            // }
        //     return $produits;
        // } catch (PDOException $e) {
        //     // Gestion des erreurs
        //     echo "Erreur : " . $e->getMessage();
        //     return [];
        // }
    }
    
    // public function getAllProduits() {
    //     $sql = "SELECT p.*, i.chemin_image FROM produits p LEFT JOIN image i ON p.id_produit = i.id_produit";
    //     try {
    //         $query = $this->pdo->prepare($sql);
    //         $query->execute();
    //         $produits = [];
    //         while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    //             $couleurs = !empty($row['couleurs_prod']) ? explode(',', $row['couleurs_prod']) : [];
    //             $produits[] = new Produit(
    //                 $row['nom'],
    //                 $row['prix_unitaire'],
    //                 $row['description'],
    //                 $row['courte_description'],
    //                 $row['quantite'],
    //                 $row['id_categorie'],
    //                 $couleurs, // Utilisation de la variable modifiée
    //                 $row['model']
    //             );
    //         }
    //         return $produits;
    //     } catch (PDOException $e) {
    //         echo "Erreur : " . $e->getMessage();
    //     }
    // }

    // Méthode pour récupérer toutes les catégories
    public function getAllCategories() {
        $sql = "SELECT * FROM categorie";
        try {
            $query = $this->pdo->prepare($sql);
            $query->execute();
            $categories = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $categories[] = new Categorie($row['id_categorie'], $row['nom_categorie']);
            }
            return $categories;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    // Méthode d'ajout de produit
    public function ajoutProduit($produit, $data) {
        try {
            $sql = "INSERT INTO produits (
                    nom, prix_unitaire, description, courte_description, quantite,
                    id_categorie, model, couleurs_prod) 
                    VALUES (:nom, :prix_unitaire, :description, :courte_description, :quantite, :id_categorie, :model, :couleurs_prod)";
            $query = $this->pdo->prepare($sql);

            $couleurs_prod = isset($produit['couleurs_prod']) ? implode(", ", $produit['couleurs_prod']) : '';

            $resultat = $query->execute([
                ':nom' => $produit['nom'],
                ':prix_unitaire' => $produit['prix_unitaire'],
                ':description' => $produit['description'],
                ':courte_description' => $produit['courte_description'],
                ':quantite' => $produit['quantite'],
                ':id_categorie' => $produit['id_categorie'],
                ':model' => $produit['model'],
                ':couleurs_prod' => $couleurs_prod,
            ]);
            
            if ($resultat) {
                $id_produit = $this->pdo->lastInsertId();
                echo "Produit ajouté avec succès, ID produit : $id_produit";

                // Appel à la méthode pour télécharger l'image
                return $this->uploadImage($data, $id_produit);
            } else {
                echo "Erreur lors de l'insertion du produit : " . implode(", ", $query->errorInfo());
            }
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Méthode d'upload d'image
    public function uploadImage($data, $id_produit) {
        if (isset($data['image']) && $data['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = time() . '_' . basename($data['image']['name']);
            $image_destination = '../images/' . $image_name;
            $from = $data['image']['tmp_name'];
            $image_type = strtolower(pathinfo($image_destination, PATHINFO_EXTENSION));

            // Vérification du type d'image
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($image_type, $allowed_types)) {
                if ($data['image']['size'] < 5 * 1024 * 1024) { // Taille limite de 5MB
                    if (move_uploaded_file($from, $image_destination)) {
                        echo "Image déplacée avec succès!";
                        // Appel à la méthode d'ajout de l'image dans la base de données
                        return $this->ajoutImage([
                            'chemin_image' => $image_destination,
                            'nom_image' => $image_name,
                            'id_produit' => $id_produit
                        ]);
                    } else {
                        echo "Erreur lors du déplacement de l'image.";
                        return false;
                    }
                } else {
                    echo "La taille de l'image dépasse la limite autorisée (5MB).";
                    return false;
                }
            } else {
                echo "Type de fichier non autorisé.";
                return false;
            }
        } else {
            echo "Erreur lors de l'upload de l'image. Code d'erreur : " . $data['image']['error'];
            return false;
        }
    }
    
    // Méthode d'ajout de l'image dans la base de données
    public function ajoutImage($imageData) {
        try {
            $sql = "INSERT INTO images (chemin_image, nom_image, id_produit) 
                    VALUES (:chemin_image, :nom_image, :id_produit)";
            
            // Préparer la requête avec PDO
            $stmt = $this->pdo->prepare($sql);
    
            // Liaison des valeurs
            $stmt->bindValue(':chemin_image', $imageData['chemin_image'], PDO::PARAM_STR);
            $stmt->bindValue(':nom_image', $imageData['nom_image'], PDO::PARAM_STR);
            $stmt->bindValue(':id_produit', $imageData['id_produit'], PDO::PARAM_INT);
            
            // Exécution de la requête
            if ($stmt->execute()) {
                return true; // Si l'insertion réussit
            } else {
                throw new \Exception("Erreur lors de l'insertion de l'image dans la base de données.");
            }
        } catch (\PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
            return false;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    } 
}
?>
