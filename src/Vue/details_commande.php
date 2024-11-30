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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto mt-10 bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold text-center mb-6">Détails de la Commande #<?= htmlspecialchars($commande['id_commande']) ?></h2>
        <div class="mb-4">
            <p><strong>Date de la commande:</strong> <?= htmlspecialchars($commande['date_commande']) ?></p>
            <p><strong>Prix total:</strong> $<?= number_format($commande['prix_total'], 2) ?></p>
            <p><strong>Nom utilisateur:</strong> <?= htmlspecialchars($commande['nom_utilisateur']) . ' ' . htmlspecialchars($commande['prenom']) ?></p>
        </div>
        <h3 class="text-xl font-semibold mb-4">Produits commandés</h3>
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="border border-gray-300 px-4 py-2">Nom produit</th>
                    <th class="border border-gray-300 px-4 py-2">Quantité</th>
                    <th class="border border-gray-300 px-4 py-2">Prix unitaire</th>
                    <th class="border border-gray-300 px-4 py-2">Prix total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($produits): ?>
                    <?php foreach ($produits as $produit): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($produit['nom']) ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-center"><?= $produit['quantite'] ?></td>
                            <td class="border border-gray-300 px-4 py-2">$<?= number_format($produit['prix_unitaire'], 2) ?></td>
                            <td class="border border-gray-300 px-4 py-2">$<?= number_format($produit['quantite'] * $produit['prix_unitaire'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">Aucun produit trouvé pour cette commande</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
