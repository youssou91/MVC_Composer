<?php
namespace App\Controlleur;

class CartControlleur
{
    public function add($params)
    {
        session_start();
        $idProduit = $params['id']; // Récupération de l'ID du produit
        $nomProduit = $_POST['nom'];
        $prixUnitaire = (float) $_POST['prix_unitaire'];
        $quantite = (int) $_POST['quantite'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $itemIndex = array_search($idProduit, array_column($_SESSION['cart'], 'id_produit'));

        if ($itemIndex !== false) {
            // Mise à jour de la quantité
            $_SESSION['cart'][$itemIndex]['quantite'] += $quantite;
        } else {
            // Ajout d'un nouvel article
            $_SESSION['cart'][] = [
                'id_produit' => $idProduit,
                'nom' => $nomProduit,
                'prix_unitaire' => $prixUnitaire,
                'quantite' => $quantite,
            ];
        }

        header('Location: /cart'); // Redirection vers la page du panier
        exit;
    }
}

?>