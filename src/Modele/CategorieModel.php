<?php
namespace App\Modele;

include '../Classes/Categorie.php';

class CategorieModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    // Méthode pour récupérer toutes les catégories
    public function getAllCategories() {
        $sql = "SELECT * FROM categorie";
        try {
            $query = $this->conn->prepare($sql);
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


    
