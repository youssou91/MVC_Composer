<?php
use App\Controlleur\ProduitControlleur;
use App\Modele\ProduitModel; 
use App\Modele\CategorieModel;
use AltoRouter\Router;
$db = getConnection(); 
$produitModel = new ProduitModel($db); 
$categorieModel = new CategorieModel($db);
$produitsController = new ProduitControlleur($produitModel, $categorieModel);
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
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Liste des produits</h1>
        <div class="flex justify-end mb-4">
            <a href="/produits/ajout" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition">
                <i class="fas fa-plus mr-2"></i> Ajouter un nouveau produit
            </a>
        </div>
        <div class="overflow-x-auto">
            <table id="dataTable" class="min-w-full table-auto border border-gray-200 shadow rounded-lg">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left border">#</th>
                        <th class="px-4 py-2 text-left border">Image</th>
                        <th class="px-4 py-2 text-left border">Nom</th>
                        <th class="px-4 py-2 text-center border">Quantité</th>
                        <th class="px-4 py-2 text-center border">Prix Unitaire ($)</th>
                        <th class="px-4 py-2 text-center border">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php if (isset($produits) && is_array($produits) && count($produits) > 0): ?>
                        <?php foreach ($produits as $produit): ?>
                            <tr class="hover:bg-gray-100 even:bg-gray-50">
                                <td class="px-4 py-2 text-left border"><?= $index++; ?></td>
                                <td class="px-4 py-2 text-center border">
                                    <img src="/public/<?= htmlspecialchars($produit['chemin_image']); ?>" alt="Image produit" class="w-12 h-12 object-cover rounded-full mx-auto">
                                </td>
                                <td class="px-4 py-2 text-left border"><?= htmlspecialchars($produit['nom']); ?></td>
                                <td class="px-4 py-2 text-center border"><?= htmlspecialchars($produit['quantite']); ?></td>
                                <td class="px-4 py-2 text-center border"><?= number_format(htmlspecialchars($produit['prix_unitaire']), 2); ?></td>
                                <td class="px-4 py-2 text-center border">
                                    <div class="flex justify-center space-x-2">
                                        <a href="/produits/modifierProduit=<?= $produit['id_produit']; ?>" 
                                           class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition"
                                           aria-label="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition"
                                            onclick="ouvrirModal('modalSupprimerProduit<?= $produit['id_produit']; ?>')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Modal -->
                            <div id="modalSupprimerProduit<?= $produit['id_produit']; ?>" 
                                class="fixed inset-0 hidden z-50 bg-black bg-opacity-50 flex items-center justify-center">
                                <div class="bg-white rounded-lg shadow-lg w-1/3">
                                    <div class="p-4 border-b flex justify-between items-center">
                                        <h5 class="text-lg font-bold">Confirmation de suppression</h5>
                                        <button class="text-gray-400 hover:text-gray-600" 
                                                onclick="fermerModal('modalSupprimerProduit<?= $produit['id_produit']; ?>')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="p-4">
                                        <p>Voulez-vous vraiment supprimer ce produit ?</p>
                                    </div>
                                    <div class="p-4 flex justify-end space-x-2">
                                        <button class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300"
                                                onclick="fermerModal('modalSupprimerProduit<?= $produit['id_produit']; ?>')">
                                            Annuler
                                        </button>
                                        <a href="produits/supprimer=<?= $produit['id_produit']; ?>" 
                                           class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
                                            Supprimer
                                        </a>
                                    </div>
                                </div>
                            </div>
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
    <script>
        function ouvrirModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        function fermerModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        $(document).ready(function() {
            $('#dataTable').DataTable({
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
