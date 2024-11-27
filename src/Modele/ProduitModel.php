<?php
namespace App\Modele;

use PDO;

class ProduitModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAllProduits() {
        $stmt = $this->pdo->prepare("SELECT * FROM produits;");
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

    public function ajouterProduit($nom, $prix, $quantite, $id_categorie, $model, $courteDescription, $longueDescription, $couleurs, $file) {
        try {
            if (isset($_FILES['chemin_image']) && $_FILES['chemin_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['chemin_image']; 
                $chemin_image = $this->uploadImage($file); 
            }            
            if (empty($chemin_image)) {
                $chemin_image = 'default_image_path.jpg';
            }
            $sql = "INSERT INTO produits (nom, prix_unitaire, quantite, id_categorie, model, courte_description, description, chemin_image";
            $params = [
                ':nom' => $nom,
                ':prix' => $prix,
                ':quantite' => $quantite,
                ':id_categorie' => $id_categorie,
                ':model' => $model,
                ':courteDescription' => $courteDescription,
                ':longueDescription' => $longueDescription,
                ':chemin_image' => $chemin_image,
            ];
            // Si des couleurs sont spécifiées, ajouter à la requête SQL
            if ($couleurs !== null) {
                $sql .= ", couleurs";
                $params[':couleurs'] = is_array($couleurs) ? json_encode($couleurs) : $couleurs;
            }
            $sql .= ") VALUES (:nom, :prix, :quantite, :id_categorie, :model, :courteDescription, :longueDescription, :chemin_image";
            if ($couleurs !== null) {
                $sql .= ", :couleurs";
            }
            $sql .= ")";
    
            // Exécution de la requête SQL
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
    
            return $this->pdo->lastInsertId(); 
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de l'ajout du produit : " . $e->getMessage());
        }
    }
    
    public function uploadImage($file, $uploadDir = 'public/uploads/') {
        $errors = [];
    
        // Vérification de l'existence du répertoire de destination
        if (!is_dir($uploadDir)) {
            // Essayer de créer le répertoire si il n'existe pas
            if (!mkdir($uploadDir, 0777, true)) {
                $errors[] = "Impossible de créer le répertoire 'uploads'.";
            }
        }
    
        // Vérification si le fichier a été téléchargé sans erreur
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors du téléchargement de l'image.";
        } else {
            // Générer un nom unique pour le fichier
            $fileName = time() . '_' . basename($file['name']);
            $filePath = $uploadDir . $fileName;
    
            // Récupérer l'extension du fichier et la convertir en minuscule
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
            // Extensions autorisées pour l'image
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
            // Vérification de l'extension du fichier
            if (!in_array($fileExtension, $allowedExtensions)) {
                $errors[] = "Extension non valide. Formats acceptés : jpg, jpeg, png, gif.";
            }
    
            // Vérification de la taille du fichier (limite 5 Mo)
            if ($file['size'] > 5 * 1024 * 1024) {
                $errors[] = "Fichier trop volumineux. Taille max : 5 Mo.";
            }
    
            // Si pas d'erreurs, déplacer le fichier téléchargé
            if (empty($errors)) {
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    // Retourner le chemin relatif pour l'utilisation en base de données
                    return 'uploads/' . $fileName;
                } else {
                    $errors[] = "Impossible de déplacer le fichier téléchargé. Vérifiez les permissions du répertoire.";
                }
            }
        }
    
        // Si des erreurs existent, afficher les erreurs et retourner null
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo $error . "<br>";
            }
        }
        return null; // Retourne null en cas d'échec
    }
    
    public  function getTousLesProduitsAvecPromotions() {
        global $connect; 
        $query = "
            SELECT 
            p.*, 
            pr.valeur AS promo_valeur, 
            pr.type AS promo_type
        FROM Produits p
        LEFT JOIN ProduitPromotion pp ON p.id_produit = pp.id_produit
        LEFT JOIN Promotions pr ON pp.id_promotion = pr.id_promotion
        WHERE p.quantite > 0
        AND (
            pr.id_promotion IS NULL OR 
            (pr.date_debut <= CURDATE() AND pr.date_fin >= CURDATE())
        )
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    }
    
}
