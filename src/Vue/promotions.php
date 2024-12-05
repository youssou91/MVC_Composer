<?php

use App\Controlleur\PromotionControlleur;
use App\Controlleur\ProduitControlleur;
use App\Modele\ProduitModel; 
use App\Modele\CategorieModel;
// Inclusion de l'autoloader de Composer
require_once __DIR__ . '/../../vendor/autoload.php';

// Connexion à la base de données
$pdo = getConnection(); 
// Initialisation du contrôleur
$produitModel = new ProduitModel($pdo); 
$categorieModel = new CategorieModel($pdo);
$produitsController = new ProduitControlleur($produitModel, $categorieModel);
$utilisateurEstConnecte = isset($_SESSION['id_utilisateur']) && !empty($_SESSION['id_utilisateur']);

// Initialisation du contrôleur
$controller = new PromotionControlleur($pdo);


// Récupérer les données depuis le contrôleur
$promotions = $controller->getPromotions(); // Promotions associées aux produits
$produits = $produitsController->afficherProduits(); // Produits pour le modal
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion des promotions</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100">
        <?php if ($utilisateurEstConnecte): 
            $utilisateurId = $_SESSION['id_utilisateur']; ?>
            <div class="container mx-auto p-6">
                <h2 class="text-2xl font-bold text-blue-600 mb-4 text-center"> Gestion des promotion </h2>
                <button 
                    class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition"
                    onclick="document.getElementById('addPromotionModal').classList.remove('hidden')">
                    Ajouter une promotion
                </button>
                <!-- Modal pour ajouter une promotion -->
                <div id="addPromotionModal" class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
                    <div class="bg-white p-6 rounded-lg w-96 shadow-lg">
                        <!-- <h3 class="text-lg font-semibold mb-4">Ajouter une promotion</h3> -->
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center"> Ajouter une promotion </h3>
                        <form action="/promotion/add" method="POST">
                            <div class="mb-4">
                                <label for="product" class="block text-sm font-medium text-gray-700">Produit</label>
                                <select name="id_produit" id="product" class="w-full border-gray-300 rounded mt-1" required>
                                    <?php foreach ($produits as $product): ?>
                                        <option value="<?= htmlspecialchars($product['id_produit']) ?>">
                                            <?= htmlspecialchars($product['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="valeur" class="block text-sm font-medium text-gray-700">Réduction (%)</label>
                                <input type="number" name="valeur" id="valeur" class="w-full border-gray-300 rounded mt-1" required>
                            </div>
                            <div class="mb-4">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                                <input type="date" name="date_debut" id="start_date" class="w-full border-gray-300 rounded mt-1" required>
                            </div>
                            <div class="mb-4">
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin</label>
                                <input type="date" name="date_fin" id="end_date" class="w-full border-gray-300 rounded mt-1" required>
                            </div>
                            <div class="flex justify-between">
                                <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Ajouter</button>
                                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600" onclick="document.getElementById('addPromotionModal').classList.add('hidden')">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                    if (isset($_GET['success']) && $_GET['success'] == 1) {
                        echo '<p class="text-green-500">La promotion a été ajoutée avec succès.</p>';
                    } elseif (isset($_GET['error'])) {
                        echo '<p class="text-red-500">Une erreur est survenue.</p>';
                    }
                ?>
                <!-- Tableau des promotions -->
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-2 px-4 border">Produit</th>
                                <th class="py-2 px-4 border">Réduction</th>
                                <th class="py-2 px-4 border">Date de début</th>
                                <th class="py-2 px-4 border">Date de fin</th>
                                <th class="py-2 px-4 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($promotions)): ?>
                                <?php foreach ($promotions as $promotion): ?>
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="py-2 px-4"><?= isset($promotion['nom']) ? htmlspecialchars($promotion['nom']) : 'Nom non disponible' ?></td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($promotion['valeur']) ?>%</td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($promotion['date_debut']) ?></td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($promotion['date_fin']) ?></td>
                                        <td class="py-2 px-4 space-x-2">
                                            <a href="admin_edit_promo.php?id=<?= htmlspecialchars($promotion['id_promotion']) ?>" 
                                            class="bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600 transition">
                                                Modifier
                                            </a>
                                            <a href="admin_delete_promo.php?id=<?= htmlspecialchars($promotion['id_promotion']) ?>" 
                                            class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 transition">
                                                Supprimer
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">Aucune promotion disponible.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: 
            header('Location: /login'); 
        endif; ?>
    </body>
</html>
