<?php
use App\Controlleur\ProduitControlleur;
use App\Modele\ProduitModel; 
use App\Modele\CategorieModel;

$db = getConnection(); 
$produitModel = new ProduitModel($db); 
$categorieModel = new CategorieModel($db);
$produitsController = new ProduitControlleur($produitModel, $categorieModel);
$produitsController->index();



$produits = $produitsController->afficherProduits();
$index = 1;

$db = null;  
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Liste des produits</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
        <script src="https://unpkg.com/heroicons@2.0.13/dist/24/outline.js"></script>
    </head>
    <body class="bg-gray-100">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Liste des produits</h1>
            <div class="flex justify-end mb-4">
                <a href="ajout_produit.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition">
                    <i class="inline-block h-5 w-5 mr-2"><svg><path fill="currentColor" d="M4 12h16M12 4v16"/></svg></i>
                    Ajouter un nouveau produit
                </a>
            </div>
            <div class="overflow-x-auto">
            <div class="overflow-x-auto">
    <table class="min-w-full table-auto border border-gray-200 shadow rounded-lg">
        <thead class="bg-blue-500 text-white">
            <tr>
                <th class="px-4 py-2 text-left border">#</th>
                <th class="px-4 py-2 text-left border">Image</th>
                <th class="px-4 py-2 text-left border">Nom</th>
                <th class="px-4 py-2 text-center border">Quantité</th>
                <th class="px-4 py-2 text-center border">Prix Unitaire (€)</th>
                <th class="px-4 py-2 text-center border">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white">
            <?php if (isset($produits) && is_array($produits) && count($produits) > 0): ?>
                <?php foreach ($produits as $produit): ?>
                    <tr class="hover:bg-gray-100 even:bg-gray-50">
                        <td class="px-4 py-2 text-left border"><?= $index++; ?></td>
                        <td class="px-4 py-2 text-center border">
                            <img src="<?= isset($produit['chemin_image']) && !empty($produit['chemin_image']) ? htmlspecialchars($produit['chemin_image']) : 'images/Image001.jpeg'; ?>" 
                                 alt="Image produit" class="w-12 h-12 object-cover rounded-full mx-auto">
                        </td>
                        <td class="px-4 py-2 text-left border"><?= htmlspecialchars($produit['nom']); ?></td>
                        <td class="px-4 py-2 text-center border"><?= htmlspecialchars($produit['quantite']); ?></td>
                        <td class="px-4 py-2 text-center border"><?= number_format(htmlspecialchars($produit['prix_unitaire']), 2); ?></td>
                        <td class="px-4 py-2 text-center border">
                            <div class="flex justify-center space-x-2">
                                <a href="modifier_produit.php?id=<?= $produit['id_produit']; ?>" 
                                   class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition"
                                   aria-label="Modifier">
                                    <i class="inline-block h-5 w-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M12 4.5v15M15 7.5l-6 6M12 16.5h8.25"/>
                                        </svg>
                                    </i>
                                </a>
                                <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition"
                                        data-bs-toggle="modal" data-bs-target="#modalSupprimerProduit<?= $produit['id_produit']; ?>" 
                                        aria-label="Supprimer">
                                    <i class="inline-block h-5 w-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M12 4.5v15m0-15H5.25m6.75 0H18"/>
                                        </svg>
                                    </i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">Aucun produit trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('.table').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    }
                });
            });
        </script>
    </body>
</html>
