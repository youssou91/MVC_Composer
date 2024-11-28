<?php
namespace App\Modele;

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
        // Vérification si le produit est déjà dans le panier
        if (isset($_SESSION['panier'][$idProduit])) {
            // Si le produit est déjà dans le panier, on ajoute la quantité
            $_SESSION['panier'][$idProduit]['quantite'] += $quantite;
        } else {
            // Si le produit n'est pas encore dans le panier, on l'ajoute avec la quantité
            $_SESSION['panier'][$idProduit] = [
                'quantite' => $quantite,
                'prix_unitaire' => $this->getPrixUnitaireProduit($idProduit), // Récupération du prix unitaire
                'promo_type' => $this->getPromoType($idProduit), // Type de réduction
                'promo_valeur' => $this->getPromoValeur($idProduit) // Valeur de la réduction
            ];
        }

        // Calcul du prix total du produit (avec réduction)
        $this->calculerPrixTotalPanier();
    }

    private function getPrixUnitaireProduit($idProduit)
    {
        // Vous devez récupérer le prix unitaire du produit depuis la base de données
        // Exemple d'accès à la base de données, adaptez selon votre architecture
        // return $this->produitModel->getProduit($idProduit)['prix_unitaire'];
        return 100; // Valeur fictive pour l'exemple
    }

    private function getPromoType($idProduit)
    {
        // Récupérer le type de promo depuis la base de données (par exemple, pourcentage ou fixe)
        // return $this->produitModel->getProduit($idProduit)['promo_type'];
        return 'pourcentage'; // Valeur fictive pour l'exemple
    }

    private function getPromoValeur($idProduit)
    {
        // Récupérer la valeur de la promo depuis la base de données
        // return $this->produitModel->getProduit($idProduit)['promo_valeur'];
        return 10; // Valeur fictive pour l'exemple
    }

    private function calculerPrixTotalPanier()
    {
        // Calcul du prix total du panier
        $totalPanier = 0;
        foreach ($_SESSION['panier'] as $idProduit => $produit) {
            // Calcul du prix réduit
            $prixUnitaire = $produit['prix_unitaire'];
            $promoType = $produit['promo_type'];
            $promoValeur = $produit['promo_valeur'];
            
            $prixReduit = $prixUnitaire; // Par défaut, sans réduction
            if ($promoType === 'pourcentage' && $promoValeur !== null) {
                $prixReduit = $prixUnitaire - ($prixUnitaire * $promoValeur / 100);
            } elseif ($promoType === 'fixe' && $promoValeur !== null) {
                $prixReduit = max(0, $prixUnitaire - $promoValeur);
            }

            // Calcul du prix total pour ce produit
            $quantite =  $_SESSION['quantite'];
            $prixTotalProduit = $quantite * $prixReduit;

            // Ajout au total général du panier
            $totalPanier += $prixTotalProduit;
        }

        // Vous pouvez maintenant retourner ou stocker ce total dans la session ou dans un autre endroit.
        $_SESSION['total_panier'] = $totalPanier;
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

