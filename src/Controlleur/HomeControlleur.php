<?php
namespace App\Controlleur;
use App\Modele\ProduitModel;
require_once __DIR__ . '/../../config/db.php';

class HomeControlleur {
    private $db;

    public function __construct() {
        // Appeler la fonction getConnection() pour obtenir la connexion à la base de données
        $this->db = getConnection(); // Cette fonction provient de db.php
    }
    public function index() {
        // Instancier le modèle pour récupérer les produits
                // Exemple avec la connexion à la base de données
        // $db = new getConnection(); // Si vous avez une classe DBConnection
        $produitModel = new ProduitModel($db);
        $produitModel = new ProduitModel();
        $products = $produitModel->getProducts();

        // Charger la vue
        require_once '../src/vue/home.php';
    }
}
?>
