<?php
// require '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/autoload.php';


use App\Controlleur\ProfileControlleur;

// Connexion à la base de données
$pdo = getConnection();  

// Créer une instance du ProfileControlleur
$profileControlleur = new ProfileControlleur($pdo);

// Récupérer l'ID de l'utilisateur connecté
$userId = $_SESSION['id_utilisateur'];

// Récupérer les informations de l'utilisateur
$userInfo = $profileControlleur->getUserInfo($userId);

// Récupérer l'ID de la commande depuis l'URL
$request_uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($request_uri, '/'));

if (isset($parts[2]) && is_numeric($parts[2])) {
    $id_commande = intval($parts[2]);
} else {
    die("ID de commande manquant ou URL invalide.");
}

// Récupérer les détails de la commande
$sql_commande = "SELECT c.id_commande, c.date_commande, c.prix_total, u.nom_utilisateur, u.prenom
                 FROM commande c
                 JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur
                 WHERE c.id_commande = ?";
$stmt_commande = $pdo->prepare($sql_commande);
$stmt_commande->execute([$id_commande]);
$commande = $stmt_commande->fetch();

if (!$commande) {
    die("Commande non trouvée.");
}

// Récupérer les produits de la commande
$sql_produits = "SELECT pc.quantite, p.nom, p.prix_unitaire
                 FROM produit_commande pc
                 JOIN produits p ON pc.id_produit = p.id_produit
                 WHERE pc.id_commande = ?";
$stmt_produits = $pdo->prepare($sql_produits);
$stmt_produits->execute([$id_commande]);
$produits = $stmt_produits->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Commande</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-3xl text-center text-blue-600 font-semibold mb-5">Détails de la Commande #<?= htmlspecialchars($commande['id_commande']) ?></h1>
        <div class="mb-6">
            <p><strong class="font-semibold">Date de la commande:</strong> <?= htmlspecialchars($commande['date_commande']) ?></p>
            <p><strong class="font-semibold">Prix total:</strong> $<?= number_format($commande['prix_total'], 2) ?></p>
            <p><strong class="font-semibold">Nom utilisateur:</strong> <?= htmlspecialchars($commande['nom_utilisateur']) . ' ' . htmlspecialchars($commande['prenom']) ?></p>
        </div>
        <h3 class="text-3xl text-center text-blue-600 font-semibold mb-5">Produits commandés</h3>
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Nom produit</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Quantité</th>
                        <th class="border border-gray-300 px-4 py-2">Prix unitaire</th>
                        <th class="border border-gray-300 px-4 py-2">Prix total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($produits): ?>
                        <?php foreach ($produits as $produit): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($produit['nom']) ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-center"><?= $produit['quantite'] ?></td>
                                <td class="border border-gray-300 px-4 py-2">$<?= number_format($produit['prix_unitaire'], 2) ?></td>
                                <td class="border border-gray-300 px-4 py-2">$<?= number_format($produit['quantite'] * $produit['prix_unitaire'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="border border-gray-300 px-4 py-2 text-center text-gray-500">Aucun produit trouvé pour cette commande</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
