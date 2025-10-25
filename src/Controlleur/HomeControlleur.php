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
        $tousLesProduits = $produitModel->getTousLesProduitsAvecPromotions();
        
        // Pagination
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 8; // Nombre d'éléments par page
        $totalProduits = count($tousLesProduits);
        $totalPages = ceil($totalProduits / $perPage);
        $offset = ($page - 1) * $perPage;
        $produits = array_slice($tousLesProduits, $offset, $perPage);
        
        // Préparer les variables pour la vue
        $pagination = [
            'page' => $page,
            'totalPages' => $totalPages,
            'totalProduits' => $totalProduits,
            'perPage' => $perPage
        ];
        
        // Chargement de la vue avec les variables nécessaires
        require_once '../src/vue/home.php';
    }
    
    private function getProduitsDataTable() {
        $produitModel = new ProduitModel($this->db);
        $tousLesProduits = $produitModel->getTousLesProduitsAvecPromotions();
        
        // Préparer les données pour DataTables
        $data = [];
        foreach ($tousLesProduits as $produit) {
            $data[] = [
                'id' => $produit['id_produit'],
                'nom' => htmlspecialchars($produit['nom'] ?? ''),
                'prix' => number_format($produit['prix_unitaire'] ?? 0, 2) . ' $',
                'quantite' => $produit['quantite'] ?? 0,
                'actions' => $this->getBoutonAjouterPanier($produit)
            ];
        }
        
        // Réponse au format JSON pour DataTables
        header('Content-Type: application/json');
        echo json_encode([
            'draw' => intval($_GET['draw'] ?? 1),
            'recordsTotal' => count($tousLesProduits),
            'recordsFiltered' => count($tousLesProduits),
            'data' => $data
        ]);
        exit;
    }
    
    private function getBoutonAjouterPanier($produit) {
        $idProduit = $produit['id_produit'] ?? 0;
        $prix = $produit['prix_unitaire'] ?? 0;
        $prixReduit = $produit['prix_reduit'] ?? null;
        
        return '<form method="POST" action="/panier/ajouter" class="flex items-center">
            <input type="hidden" name="id_produit" value="' . $idProduit . '">
            <input type="hidden" name="prix_reduit" value="' . htmlspecialchars(json_encode($prixReduit)) . '">
            <input type="number" name="quantite" value="1" min="1" max="' . ($produit['quantite'] ?? 1) . '" class="w-16 px-2 py-1 border rounded-md mr-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md">
                <i class="fas fa-cart-plus"></i> Ajouter
            </button>
        </form>';
    }

    public function ajouterProduit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProduit = $_POST['id_produit'] ?? null;
            $quantite = (int)($_POST['quantite'] ?? 1);
            // Prix réduit envoyé depuis le formulaire
            $prixReduit = $_POST['prix_reduit'] ?? null; 
    
            if (!$idProduit || $quantite <= 0) {
                // Redirection en cas d'erreur
                header('Location: /'); 
                exit;
            }
    
            if (!isset($_SESSION['panier'])) {
                $_SESSION['panier'] = [];
            }
    
            if (isset($_SESSION['panier'][$idProduit])) {
                // Mise à jour de la quantité si le produit existe déjà dans le panier
                $_SESSION['panier'][$idProduit]['quantite'] += $quantite;
            } else {
                // Ajout du produit avec les informations nécessaires
                $_SESSION['panier'][$idProduit] = [
                    'quantite' => $quantite,
                    // Priorité au prix réduit
                    'prix_unitaire' => $prixReduit ? $prixReduit : $_POST['prix_unitaire'], 
                    'nom' => $_POST['nom'],
                    'promo_type' => $_POST['promo_type'] ?? null,
                    'promo_valeur' => $_POST['promo_valeur'] ?? null,
                ];
            }
    
            header('Location: /');
            exit;
        }
    }

    public function gererPanier() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProduit = $_POST['id_produit'] ?? null;
            $action = $_POST['action'] ?? null;

            if ($action === 'supprimer' && $idProduit !== null) {
                unset($_SESSION['panier'][$idProduit]);
            } elseif ($action === 'vider') {
                $_SESSION['panier'] = [];
            }

            header('Location: /');
            exit;
        }
    }
}
