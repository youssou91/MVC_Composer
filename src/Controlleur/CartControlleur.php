<?php
namespace App\Controlleur;

use App\Modele\ProduitModel;

class CartControlleur {
    // Afficher le panier
    public static function index() {
        $panier = $_SESSION['panier'] ?? [];
        require 'vue/home.php'; // Afficher une vue spécifique pour le panier
    }

    // Ajouter un produit au panier
    public function ajouter()
    {
        session_start();

        $productId = $_POST['id'] ?? null;
        $price = $_POST['price'] ?? null;

        if ($productId && $price) {
            if (!isset($_SESSION['panier'])) {
                $_SESSION['panier'] = [];
            }

            // Ajouter ou mettre à jour le produit dans le panier
            if (isset($_SESSION['panier'][$productId])) {
                $_SESSION['panier'][$productId]['quantity'] += 1;
            } else {
                $_SESSION['panier'][$productId] = [
                    'id' => $productId,
                    'price' => $price,
                    'quantity' => 1,
                ];
            }

            $total = 0;
            $cartHtml = '';
            foreach ($_SESSION['panier'] as $item) {
                $cartHtml .= '<li>Produit ' . $item['id'] . ' - ' . $item['quantity'] . ' x ' . $item['price'] . ' €</li>';
                $total += $item['price'] * $item['quantity'];
            }

            echo json_encode([
                'success' => true,
                'cartHtml' => $cartHtml,
                'total' => $total,
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Données invalides',
            ]);
        }
    }

    // Mettre à jour la quantité d'un produit dans le panier
    public function mettreAJour() {
        if (isset($_POST['idProduit'], $_POST['quantite'])) {
            $idProduit = $_POST['idProduit'];
            $quantite = $_POST['quantite'];

            if ($quantite > 0) {
                $_SESSION['cart'][$idProduit]['quantite'] = $quantite;
            }
        }

        // Rediriger vers la page du panier
        header('Location: ' . $router->generate('panier'));
        exit;
    }

    // Supprimer un produit du panier
    public function supprimer() {
        if (isset($_POST['idProduit'])) {
            $idProduit = $_POST['idProduit'];
            unset($_SESSION['cart'][$idProduit]);
        }

        // Rediriger vers la page du panier
        header('Location: ' . $router->generate('panier'));
        exit;
    }

    // Vider le panier
    public function vider() {
        unset($_SESSION['cart']);

        // Rediriger vers la page du panier
        header('Location: ' . $router->generate('panier'));
        exit;
    }
}

?>