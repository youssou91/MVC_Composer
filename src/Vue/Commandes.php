<?php
use App\Modele\CommandeModel;
use App\Controlleur\CommandeControlleur;

$pdo = getConnection(); 
$commandeModel = new CommandeModel($pdo);
$commandeController = new CommandeControlleur($commandeModel);
$commandeController->index();
// Vérifiez si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}

$orders = $commandeController->listCommandes();
$index = 1;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des commandes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 font-sans">
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Liste des commandes</h1>
        <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
            <table id="ordersTable" class="min-w-full">
                <thead class="bg-blue-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Utilisateur</th>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Prix total</th>
                        <th class="px-4 py-2 text-left">Statut</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $index => $order): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?=  $index + 1; ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($order['prenom'] . ' ' . $order['nom_utilisateur']); ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($order['date_commande']); ?></td>
                            <td class="px-4 py-2"><?= number_format(htmlspecialchars($order['prix_total']), 2); ?> $</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full 
                                    <?php 
                                        // Application des couleurs en fonction du statut
                                        if ($order['statut'] == 'En attente') {
                                            echo 'bg-yellow-200 text-yellow-800'; 
                                        } elseif ($order['statut'] == 'En traitement') {
                                            echo 'bg-orange-200 text-orange-800'; 
                                        } elseif ($order['statut'] == 'En expédition') {
                                            echo 'bg-green-200 text-green-800'; 
                                        } elseif ($order['statut'] == 'Livrée') {
                                            echo 'bg-blue-200 text-blue-800'; 
                                        } elseif ($order['statut'] == 'Annulée') {
                                            echo 'bg-red-200 text-red-800'; 
                                        } elseif ($order['statut'] == 'Payée') {
                                            echo 'bg-purple-200 text-purple-800'; 
                                        }
                                    ?>">
                                    <?= htmlspecialchars($order['statut']); ?>
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <form method="post" class="flex space-x-2">
                                    <input type="hidden" name="id_commande" value="<?= $order['id_commande']; ?>">

                                    <!-- Bouton pour traiter la commande -->
                                    <a href="commande/editer/id_commande=<?= $order['id_commande']; ?>/action=traiter" 
                                        class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 flex items-center justify-center" 
                                        <?= ($order['statut'] == 'Annulée' || $order['statut'] == 'Livrée' || $order['statut'] == 'En expédition' || $order['statut'] == 'Payée') ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?>>
                                        <i class="bi bi-gear-fill"></i>
                                    </a>

                                    <!-- Bouton pour expédier la commande -->
                                    <a href="commande/editer/id_commande=<?= $order['id_commande']; ?>/action=expedier" 
                                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 flex items-center justify-center" 
                                        <?= ($order['statut'] == 'Annulée' || $order['statut'] == 'Livrée' || $order['statut'] == 'En expédition' || $order['statut'] == 'Payée' || $order['statut'] == 'En attente') ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?>>
                                        <i class="bi bi-truck"></i>
                                    </a>

                                    <!-- Bouton pour annuler la commande -->
                                    <a href="commande/editer/id_commande=<?= $order['id_commande']; ?>/action=annuler" 
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 flex items-center justify-center" 
                                        <?= ($order['statut'] == 'Annulée' || $order['statut'] == 'Livrée' || $order['statut'] == 'En expédition' || $order['statut'] == 'Payée') ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?>>
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function () {
        // Initialisation de DataTables
        $('#ordersTable').DataTable({
            responsive: true, // Rend la table responsive
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            }
        });
        });
    </script>
</body>
</html>


