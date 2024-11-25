<?php
namespace App\Modele;
namespace App\Modele; // Assurez-vous que le namespace est correct


class PanierModel
{
    public function init()
    {
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
    }

    public function ajouter($idProduit, $quantite)
    {
        if (isset($_SESSION['panier'][$idProduit])) {
            $_SESSION['panier'][$idProduit] += $quantite;
        } else {
            $_SESSION['panier'][$idProduit] = $quantite;
        }
    }

    public function supprimer($idProduit)
    {
        unset($_SESSION['panier'][$idProduit]);
    }

    public function getContenu()
    {
        return $_SESSION['panier'];
    }
}

