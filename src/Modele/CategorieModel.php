<?php
namespace App\Modele;
use PDO;
use App\Classes\Categorie;

class CategorieModel {
    private $pdo;

    public function __construct( PDO $pdo) {
        $this->pdo = $pdo;
    }
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


    
}


    
