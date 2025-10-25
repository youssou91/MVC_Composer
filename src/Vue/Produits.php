<?php
use App\Controlleur\ProduitControlleur;
use App\Modele\ProduitModel; 
use App\Modele\CategorieModel;

$db = getConnection(); 
$produitModel = new ProduitModel($db); 
$categorieModel = new CategorieModel($db);
$produitsController = new ProduitControlleur($produitModel, $categorieModel);

// Récupération des variables de pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10; // Doit correspondre à la valeur dans le contrôleur
$totalProduits = count($produitModel->getAllProduits());
$totalPages = ceil($totalProduits / $perPage);

// Récupération des produits pour la page courante
$offset = ($page - 1) * $perPage;
$produits = $produitModel->getProduitsPagination($offset, $perPage);

$utilisateurEstConnecte = isset($_SESSION['id_utilisateur']) && !empty($_SESSION['id_utilisateur']);
$index = ($page - 1) * $perPage + 1; // Pour la numérotation continue des lignes

// Fermeture de la connexion
$db = null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php if ($utilisateurEstConnecte): 
        $utilisateurId = $_SESSION['id_utilisateur']; ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
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
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="flex justify-center mt-8">
                    <nav class="flex items-center space-x-2">
                        <!-- Bouton Précédent -->
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="px-4 py-2 border rounded-l-lg bg-white text-blue-600 hover:bg-blue-50">
                                <i class="fas fa-chevron-left"></i> Précédent
                            </a>
                        <?php else: ?>
                            <span class="px-4 py-2 border rounded-l-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i> Précédent
                            </span>
                        <?php endif; ?>

                        <!-- Numéros de page -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="px-4 py-2 border-t border-b border-blue-500 bg-blue-100 text-blue-600 font-medium">
                                    <?= $i ?>
                                </span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?>" class="px-4 py-2 border-t border-b border-gray-200 bg-white text-gray-600 hover:bg-gray-50">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <!-- Bouton Suivant -->
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="px-4 py-2 border rounded-r-lg bg-white text-blue-600 hover:bg-blue-50">
                                Suivant <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="px-4 py-2 border rounded-r-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                Suivant <i class="fas fa-chevron-right"></i>
                            </span>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>
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
        <?php else: 
        header('Location: /'); 
    endif; ?>   
    <?php else: 
        header('Location: /login'); 
    endif; ?>
    
</html>



