<?php
use App\Controlleur\ProduitControlleur;
use App\Modele\ProduitModel; 
use App\Modele\CategorieModel;
use App\Classes\Produit;

require_once __DIR__ . '/../../vendor/autoload.php';

// Connexion à la base de données
$pdo = getConnection(); 

// Initialisation des modèles et contrôleurs
$produitModel = new ProduitModel($pdo); 
$categorieModel = new CategorieModel($pdo);
$produitsController = new ProduitControlleur($produitModel, $categorieModel);
$categories = $categorieModel->getAllCategories();

// Variables pour les messages
$message = '';
$messageType = '';

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ajouter un produit</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 min-h-screen flex items-center justify-center">

        <div class="container mx-auto max-w-3xl bg-white p-8 shadow-lg rounded-lg my-8">
            <?php if (!empty($message)): ?>
                <div class="p-4 mb-4 text-sm font-semibold 
                            <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> 
                            border 
                            <?php echo $messageType === 'success' ? 'border-green-200' : 'border-red-200'; ?> 
                            rounded-lg">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/produits/ajouterProduit"  enctype="multipart/form-data" class="space-y-8">
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4 text-blue-500">Détails du produit</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700">Nom produit</label>
                            <input type="text" id="nom" name="nom" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="prix" class="block text-sm font-medium text-gray-700">Prix unitaire</label>
                            <input type="number" id="prix" name="prix_unitaire" step="0.01" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                            <input type="number" id="quantite" name="quantite" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="id_categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                            <select id="id_categorie" name="id_categorie" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                                <option value="" selected>Choisir une catégorie</option>
                                <?php
                                foreach ($categories as $categorie) {
                                    echo "<option value='" . $categorie->getId_categorie() . "'>" . htmlspecialchars($categorie->getNom_categorie()) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4 text-blue-500">Description</h2>
                    <div>
                        <label for="courteDescription" class="block text-sm font-medium text-gray-700">Courte description</label>
                        <input type="text" id="courteDescription" name="courte_description" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label for="longueDescription" class="block text-sm font-medium text-gray-700">Longue description</label>
                        <textarea id="longueDescription" name="longue_description" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" rows="3" required></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4 text-blue-500">Autres informations</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                            <input type="text" id="model" name="model" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                            <input type="file" id="image" name="chemin_image" class="mt-1 p-2 block w-full border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Couleurs disponibles</label>
                        <div class="mt-2 grid grid-cols-3 gap-4">
                            <?php
                            $couleurs = [
                                'Rouge' => 'bg-red-500',
                                'Bleu' => 'bg-blue-500',
                                'Vert' => 'bg-green-500',
                                'Noir' => 'bg-black text-white',
                                'Blanc' => 'bg-white border-gray-300 text-gray-700',
                                'Gris' => 'bg-gray-500',
                                'Jaune' => 'bg-yellow-500',
                                'Rose' => 'bg-pink-500',
                                'Marron' => 'bg-amber-700'
                            ];

                            foreach ($couleurs as $couleur => $classeCouleur) {
                                echo "
                                <label class='flex items-center justify-center border border-gray-300 rounded-lg shadow-md cursor-pointer hover:opacity-75 transition p-2'>
                                    <input type='checkbox' name='couleurs[]' value='$couleur' class='hidden peer'>
                                    <span class='peer-checked:$classeCouleur peer-checked:text-white px-4 py-2'>
                                        $couleur
                                    </span>
                                </label>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit" name="ajouterProduit" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md">Ajouter le produit</button>
                </div>
            </form>
        </div>
    </body>
</html>