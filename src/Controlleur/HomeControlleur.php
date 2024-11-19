<?php
namespace App\Controlleur;
use App\Modele\ProduitModel;
require_once __DIR__ . '/../../config/db.php';

class HomeControlleur {
    private $db;
    public function __construct() {
        $this->db = getConnection(); 
    }

    public function index() {
        $produitModel = new ProduitModel($this->db);
        $products = $produitModel->getAllProduits(); 
        require_once '../src/vue/home.php';
    }
    
}
?>
