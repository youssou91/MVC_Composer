<?php
namespace App\Modele;

use PDO;

class ProduitModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProduits() {
        $stmt = $this->pdo->prepare("SELECT * FROM produits ;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCategories() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categorie");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de la récupération des catégories : " . $e->getMessage());
        }
    }

    public function ajouterProduit($nom, $prix, $quantite, $id_categorie, $model, $courteDescription, $longueDescription, $couleurs, $chemin_image) {
        try {
            $sql = "INSERT INTO produits (nom, prix_unitaire, quantite, id_categorie, model, courte_description, description";
            $params = [
                ':nom' => $nom,
                ':prix' => $prix,
                ':quantite' => $quantite,
                ':id_categorie' => $id_categorie,
                ':model' => $model,
                ':courteDescription' => $courteDescription,
                ':longueDescription' => $longueDescription,
            ];
    
            // Inclure `couleurs` si fourni
            if ($couleurs !== null) {
                $sql .= ", couleurs";
                $params[':couleurs'] = is_array($couleurs) ? json_encode($couleurs) : $couleurs;
            }
    
            // Ajouter `chemin_image` seulement si elle est fournie, sinon attribuer une valeur par défaut
            if ($chemin_image !== null) {
                $sql .= ", chemin_image";
                $params[':chemin_image'] = $chemin_image;
            } else {
                // Vous pouvez ajouter un chemin d'image par défaut si aucune image n'est fournie
                $sql .= ", chemin_image";
                $params[':chemin_image'] = 'default_image_path.jpg'; // ou une autre image par défaut
            }
    
            $sql .= ") VALUES (:nom, :prix, :quantite, :id_categorie, :model, :courteDescription, :longueDescription";
    
            if ($couleurs !== null) {
                $sql .= ", :couleurs";
            }
    
            $sql .= ", :chemin_image)";  // Fermeture de la requête SQL
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
    
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de l'ajout du produit : " . $e->getMessage());
        }
    }

    public function ajoutImage($imageData) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO images (chemin_image, nom_image, id_produit) VALUES (:chemin_image, :nom_image, :id_produit)");
            $stmt->execute([
                ':chemin_image' => $imageData['chemin_image'],
                ':nom_image' => $imageData['nom_image'],
                ':id_produit' => $imageData['id_produit'],
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de l'ajout de l'image : " . $e->getMessage());
        }
    }

    public function uploadImage($file) {
        // Vérifiez si l'image est bien présente
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            // Vérification du type d'image
            $imageInfo = getimagesize($file['tmp_name']);
            
            // Si getimagesize échoue, cela signifie que ce n'est pas une image
            if ($imageInfo === false) {
                throw new \Exception("Le fichier téléchargé n'est pas une image valide.");
            }
    
            // Vérification de l'extension de l'image
            $imageExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    
            if (!in_array($imageExtension, $validExtensions)) {
                throw new \Exception("Format d'image non supporté. Extensions autorisées : jpg, jpeg, png, gif, bmp, webp.");
            }
    
            // Créer un nom unique pour l'image
            $image_name = time() . '_' . basename($file['name']);
            
            // Nouveau chemin pour le dossier 'uploads' dans le répertoire public
            $image_destination = __DIR__ . '/../public/uploads/' . $image_name;  // Chemin relatif vers le dossier 'uploads' dans 'public'
    
            // Déplacer l'image téléchargée dans le dossier approprié
            if (move_uploaded_file($file['tmp_name'], $image_destination)) {
                // Retourner le chemin relatif de l'image dans le répertoire public
                return 'uploads/' . $image_name;
            } else {
                throw new \Exception("Erreur lors du téléchargement de l'image.");
            }
        } else {
            // Déboguer le problème d'erreur si l'image n'a pas été envoyée
            var_dump($file);
            throw new \Exception("Aucune image ou une erreur est survenue.");
        }
    }
    
    
    
}
