    <?php
    // Vérification de la connexion
    $utilisateurEstConnecte = isset($_SESSION['id_utilisateur']) && !empty($_SESSION['id_utilisateur']);
    $produits = $produits ?? []; 
    $totalPanier = 0; 
    $quantiteTotale = 0;
    // Regroupement des produits par ID et addition des quantités
    $panierRegroupe = $_SESSION['panier'] ?? [];

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
            <!-- panier -->
            <div class="bg-white rounded shadow-md p-4 mb-8">
                <h3 class="text-2xl font-bold text-blue-600 text-center mb-4">Mon Panier</h3>
                <?php if (!empty($_SESSION['panier'])): ?>
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
                            <?php if (!empty($_SESSION['panier'])): 
                                foreach ($_SESSION['panier'] as $id => $produit): 
                                    if (is_array($produit) && isset($produit['prix_unitaire'], $produit['quantite'])):
                                        $prixUnitaireProduit = $produit['prix_unitaire'];
                                        $quantite = $produit['quantite'];
                                        $prixTotalProduit = $quantite * $prixUnitaireProduit;
                                        $totalPanier += $prixTotalProduit;
                                        $quantiteTotale += $quantite;
                            ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($produit['nom']) ?></td>
                                <td class="border px-4 py-2"><?= $quantite ?></td>
                                <td class="border px-4 py-2"><?= number_format($prixUnitaireProduit, 2) ?> $</td>
                                <td class="border px-4 py-2"><?= number_format($prixTotalProduit, 2) ?> $</td>
                                <td class="border px-4 py-2">
                                    <form method="POST" action="/produits/panier">
                                        <input type="hidden" name="id_produit" value="<?= $id ?>">
                                        <input type="hidden" name="action" value="supprimer">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; endforeach; endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-200">
                                <th colspan="3" class="border px-4 py-2 text-left">Total</th>
                                <th colspan="2" class="border px-4 py-2"><?= number_format($totalPanier, 2) ?> $</th>
                            </tr>
                            <tr class="bg-gray-100">
                                <th colspan="3" class="border px-4 py-2 text-left">Quantité Totale</th>
                                <th colspan="2" class="border px-4 py-2"><?= $quantiteTotale ?> article(s)</th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="flex justify-between space-x-4 mt-4">
                        <form method="POST" action="/produits/supprimer" class="w-1/4">
                            <input type="hidden" name="action" value="vider">
                            <button type="submit" class="py-2 px-4 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow-md w-full h-12 flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i> 
                            </button>
                        </form>
                        <?php if ($utilisateurEstConnecte): 
                            $utilisateurId = $_SESSION['id_utilisateur']; 
                        ?>
                            <form method="POST" action="/commande" class="w-1/4">
                                <input type="hidden" name="id_utilisateur" value="<?= $utilisateurId ?? '' ?>">
                                <input type="hidden" name="prix_total" value="<?= $totalPanier ?>">
                                <?php foreach ($_SESSION['panier'] as $id => $produit): ?>
                                    <input type="hidden" name="produits[<?= $id ?>][id_produit]" value="<?= $id ?>">
                                    <input type="hidden" name="produits[<?= $id ?>][quantite]" value="<?= $produit['quantite'] ?>">
                                <?php endforeach; ?>
                                <button type="submit" class="py-2 px-4 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md w-full h-12 flex items-center justify-center">
                                    <i class="fas fa-shopping-cart mr-2"></i> 
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="GET" action="/login" class="w-1/4">
                                <button type="submit" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md w-full h-12 flex items-center justify-center">
                                    <i class="fas fa-sign-in-alt mr-2"></i> 
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-600">Votre panier est vide.</p>
                <?php endif; ?>
            </div>
            <!-- Section des Produits -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php
                if (!empty($produits)): ?>
                    <?php foreach ($produits as $produit): ?>
                        <?php
                            $idProduit = $produit['id_produit'];
                            $nom = htmlspecialchars($produit['nom'] ?? 'Nom indisponible');
                            $prix = $produit['prix_unitaire'] ?? 0;
                            $cheminImage = htmlspecialchars($produit['chemin_image'] ?? 'public/uploads/default_image.png');
                            $promoType = $produit['promo_type'] ?? null;
                            $promoValeur = $produit['promo_valeur'] ?? null;
                            $quantiteStock = $produit['quantite'] ?? 0;
                            $descripton = $produit['description'] ?? null;
                            $couleurs = $produit['couleurs'] ?? null;
                            $prixReduit = $prix;
            
                            if ($promoType === 'pourcentage') {
                                $prixReduit = $prix - ($prix * $promoValeur / 100);
                            } elseif ($promoType === 'fixe') {
                                $prixReduit = max(0, $prix - $promoValeur);
                            }
                        ?>
                        <div class="border rounded shadow-lg p-4 bg-white">
                            <img src="/public/<?= $cheminImage ?>" 
                            class="w-full h-48 object-cover mb-4 rounded-[10px] shadow-md transition-transform transform hover:scale-105 hover:shadow-lg cursor-pointer" 
                            onclick="openModal('modal-<?= $idProduit ?>')">
                            <h3 class="text-xl font-bold"><?= $nom ?></h3>
                            <p class="text-gray-600">
                                <?php if ($promoType): ?>
                                    <span class="line-through text-red-500"><?= number_format($prix, 2) ?> $</span>
                                    <span class="text-green-500"><?= number_format($prixReduit, 2) ?> $</span>
                                <?php else: ?>
                                    <?= number_format($prix, 2) ?> $
                                <?php endif; ?>
                            </p>
                            <form method="POST" action="/produits/panier">
                                <input type="hidden" name="id_produit" value="<?= $idProduit ?>">
                                <input type="hidden" name="nom" value="<?= $nom ?>">
                                <input type="hidden" name="prix_unitaire" value="<?= $prix ?>">
                                <input type="hidden" name="prix_reduit" value="<?= $prixReduit ?>">
                                <input type="hidden" name="promo_type" value="<?= $promoType ?>">
                                <input type="hidden" name="promo_valeur" value="<?= $promoValeur ?>">
                                <input type="hidden" name="action" value="ajouter">
                                <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                                <input type="number" name="quantite" id="quantite" value="1" min="1" max="<?= $quantiteStock ?>" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <button type="submit" class="mt-4 w-full py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md flex items-center justify-center">
                                    <i class="fas fa-cart-plus mr-2"></i> 
                                </button>
                            </form>
                        </div>
                        <!-- Modal -->
                        <div id="modal-<?= $idProduit ?>" 
                            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
                            <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-8 relative">
                                <!-- Bouton de fermeture -->
                                <button onclick="closeModal('modal-<?= $idProduit ?>')" 
                                        class="absolute top-4 right-4 text-gray-600 hover:text-red-600 text-2xl">
                                    ✕
                                </button>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <img src="/public/<?= $cheminImage ?>" class="w-full h-auto rounded-lg shadow-md">
                                    <div>
                                        <h2 class="text-2xl font-bold mb-4"><?= $nom ?></h2>
                                        <p class="text-gray-700 mb-2">
                                            <strong>Prix :</strong> <?= number_format($prix, 2) ?> $
                                        </p>
                                        <?php if ($promoType): ?>
                                            <p class="text-green-500 mb-4">
                                                <strong>Prix réduit :</strong> <?= number_format($prixReduit, 2) ?> $
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-gray-700 mb-2">
                                            <strong>Stock Disponible :</strong> <?= $quantiteStock ?> 
                                        </p>
                                        <div>
                                            <p class="text-gray-600 mb-4"><strong>Couleurs disponibles</strong></p>
                                            <div class="mt-2 grid grid-cols-3 gap-4">
                                                <?php
                                                // Tableau des couleurs avec leurs classes Tailwind
                                                $couleurs = [
                                                    'Rouge' => 'bg-red-500',
                                                    'Bleu' => 'bg-blue-500',
                                                    'Vert' => 'bg-green-500',
                                                    'Noir' => 'bg-black text-white',
                                                    'Blanc' => 'bg-white border-gray-300 text-gray-700',
                                                    'Gris' => 'bg-gray-500 text-white',
                                                    'Jaune' => 'bg-yellow-500',
                                                    'Rose' => 'bg-pink-500',
                                                    'Marron' => 'bg-amber-700'
                                                ];

                                                // Vérifier si la chaîne des couleurs est bien définie et la décoder
                                                if (isset($produit['couleurs']) && !empty($produit['couleurs'])) {
                                                    $couleursProduit = json_decode($produit['couleurs'], true);  // Décode en tableau

                                                    // Vérifier si le résultat du json_decode est bien un tableau
                                                    if (is_array($couleursProduit)) {
                                                        foreach ($couleursProduit as $couleur) {
                                                            // Vérifier que la couleur existe dans le tableau $couleurs
                                                            if (array_key_exists($couleur, $couleurs)) {
                                                                $classeCouleur = $couleurs[$couleur]; // Récupérer la classe Tailwind correspondante
                                                                echo "
                                                                <span class='inline-block px-4 py-2 $classeCouleur text-white rounded-md shadow-md'>
                                                                    $couleur
                                                                </span>";
                                                            }
                                                        }
                                                    } else {
                                                        // Si json_decode échoue, afficher un message d'erreur ou ignorer
                                                        echo "<p>Erreur de format des couleurs disponibles.</p>";
                                                    }
                                                } else {
                                                    echo "<p>Aucune couleur disponible.</p>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <p class="text-gray-600 mb-4">
                                            <strong>Description :</strong> <?= $descripton ?> 
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-gray-600">Aucun produit disponible.</p>
                <?php endif; ?>
                
            </div>
        </div>
        
    </body>
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    </script>

    </html>