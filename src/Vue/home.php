<?php
// Vérification de la connexion
$utilisateurEstConnecte = isset($_SESSION['utilisateur_id']) && !empty($_SESSION['utilisateur_id']);

$totalPanier = 0; // Initialisez le total du panier avant de l'utiliser

// Regroupement des produits par ID et addition des quantités
$panierRegroupe = [];
foreach ($panier as $id => $quantite) {
    if (isset($panierRegroupe[$id])) {
        $panierRegroupe[$id] += $quantite;  // Ajout de la quantité si le produit existe déjà
    } else {
        $panierRegroupe[$id] = $quantite;  // Sinon, ajout du produit avec sa quantité
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma boutique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-200 m-5">
    <div class="container mx-auto">
        <h2 class="text-center text-2xl text-blue-600 font-bold my-4">Ma boutique</h2>

        <!-- Section du Panier -->
        <div class="bg-white rounded shadow-md p-4 mb-8">
            <h3 class="text-2xl font-bold text-blue-600 text-center mb-4">Mon Panier</h3>

            <?php if (!empty($panierRegroupe)): ?>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-4 py-2 text-left">Nom</th>
                            <th class="border px-4 py-2 text-left">Quantité</th>
                            <th class="border px-4 py-2 text-left">Prix Unitaire</th>
                            <th class="border px-4 py-2 text-left">Prix Total</th>
                            <th class="border px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalPanier = 0;
                        $quantiteTotale = 0;
                        foreach ($panierRegroupe as $id => $quantite): 
                            if (isset($produits[$id])) {
                                $produit = $produits[$id];
                                $prixUnitaireProduit = $produit['prix_unitaire'] ?? 0;
                                $promoType = $produit['promo_type'] ?? null; 
                                $promoValeur = $produit['promo_valeur'] ?? null; 
                                $prixReduit = $prixUnitaireProduit;

                                // Calcul du prix réduit en fonction de la promotion
                                if ($promoType === 'pourcentage' && $promoValeur !== null) {
                                    $prixReduit = $prixUnitaireProduit - ($prixUnitaireProduit * $promoValeur / 100);
                                } elseif ($promoType === 'fixe' && $promoValeur !== null) {
                                    $prixReduit = max(0, $prixUnitaireProduit - $promoValeur);
                                }

                                $prixTotalProduit = $quantite * $prixReduit;
                                $totalPanier += $prixTotalProduit;
                                $quantiteTotale += $quantite;
                            }
                        ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($produit['nom'] ?? 'Nom indisponible') ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($quantite) ?></td>
                                <td class="border px-4 py-2"><?= number_format($prixReduit, 2) ?> €</td>
                                <td class="border px-4 py-2"><?= number_format($prixTotalProduit, 2) ?> €</td>
                                <td class="border px-4 py-2">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id_produit" value="<?= $id ?>">
                                        <input type="hidden" name="action" value="supprimer">
                                        <button type="submit" class="text-red-500 hover:text-red-700 focus:outline-none">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-200">
                            <th colspan="3" class="border px-4 py-2 text-left">Total</th>
                            <th colspan="2" class="border px-4 py-2"><?= number_format($totalPanier, 2) ?> €</th>
                        </tr>
                        <tr class="bg-gray-100">
                            <th colspan="3" class="border px-4 py-2 text-left">Quantité Totale</th>
                            <th colspan="2" class="border px-4 py-2"><?= $quantiteTotale ?> article(s)</th>
                        </tr>
                    </tfoot>
                </table>

                <!-- Bouton Vider le panier -->
                <form method="POST" action="" class="mt-4">
                    <input type="hidden" name="action" value="vider">
                    <button type="submit" class="py-2 px-4 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow-md w-full">Vider le panier</button>
                </form>

                <!-- Vérification de l'état de connexion -->
                <?php if ($utilisateurEstConnecte): ?>
                    <form method="POST" action="/commande" class="mt-4">
                        <button type="submit" class="py-2 px-4 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md w-full">Commander</button>
                    </form>
                <?php else: ?>
                    <form method="GET" action="/connexion" class="mt-4">
                        <button type="submit" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md w-full">Se connecter</button>
                    </form>
                <?php endif; ?>

            <?php else: ?>
                <p class="text-center text-gray-600">Votre panier est vide.</p>
            <?php endif; ?>
        </div>

        <!-- Section des Produits -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php
                if ($produits):
                    foreach ($produits as $id => $produit):
                        $nom = htmlspecialchars($produit['nom'] ?? 'Nom indisponible');
                        $prix = $produit['prix_unitaire'] ?? 0;
                        $cheminImage = htmlspecialchars($produit['chemin_image'] ?? 'public/uploads/default_image.png');
                        $promoType = $produit['promo_type'] ?? null;
                        $promoValeur = $produit['promo_valeur'] ?? null;

                        // Calcul du prix réduit
                        $prixReduit = $prix;
                        if ($promoType === 'pourcentage') {
                            $prixReduit = $prix - ($prix * $promoValeur / 100);
                        } elseif ($promoType === 'fixe') {
                            $prixReduit = max(0, $prix - $promoValeur);
                        }
            ?>
                <div class="border rounded shadow-lg p-4 bg-white">
                    <img src="/public/<?= $cheminImage ?>" class="w-full h-48 object-cover mb-4">
                    <h3 class="text-xl font-bold"><?= $nom ?></h3>
                    <p class="text-gray-600">
                        <?php if ($promoType): ?>
                            <span class="line-through text-red-500"><?= number_format($prix, 2) ?> €</span>
                            <span class="text-green-500"><?= number_format($prixReduit, 2) ?> €</span>
                        <?php else: ?>
                            <?= number_format($prix, 2) ?> €
                        <?php endif; ?>
                    </p>
                    <form method="POST" action="/produits/panier">
    <input type="hidden" name="id_produit" value="<?= $id ?>">
    <input type="hidden" name="action" value="ajouter">

    <!-- Champ de saisie pour la quantité -->
    <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
    <input type="number" name="quantite" id="quantite" value="1" min="1" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>

    <button type="submit" class="mt-4 w-full py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md">Ajouter au panier</button>
</form>

                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
