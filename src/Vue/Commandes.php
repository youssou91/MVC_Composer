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
// Traitement des actions des commandes
if (isset($_POST['action'])) {
    $orderId = $_POST['order_id'];
    $action = $_POST['action'];
    switch ($action) {
        case 'traiter':
            $commandeController->updateCommande($orderId, null, null, null, 'En traitement');
            break;
        case 'expedier':
            $commandeController->updateCommande($orderId, null, null, null, 'En expedition');
            break;
        case 'annuler':
            $commandeController->updateCommande($orderId, null, null, null, 'Annulee');
            break;
    }
    echo '<script>window.location.href = "commandesAdmin.php";</script>';
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
    </head>
    <body class="bg-gray-50 font-sans">
        <div class="container mx-auto py-8 px-4">
            <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Liste des produits</h1>
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
                                <td class="px-4 py-2"><?php echo $index + 1; ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($order['prenom'] . ' ' . $order['nom_utilisateur']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($order['date_commande']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($order['prix_total']); ?> $</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded-full 
                                        <?php echo ($order['statut'] == 'En attente') ? 'bg-yellow-200 text-yellow-800' : ''; ?>
                                        <?php echo ($order['statut'] == 'Expédiée') ? 'bg-green-200 text-green-800' : ''; ?>
                                        <?php echo ($order['statut'] == 'Annulée') ? 'bg-red-200 text-red-800' : ''; ?>">
                                        <?php echo htmlspecialchars($order['statut']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 flex space-x-2">
                                    <form method="post" class="space-x-2">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id_commande']; ?>">
                                        <button type="submit" name="action" value="traiter" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                            Traiter
                                        </button>
                                        <button type="submit" name="action" value="expedier" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                            Expédier
                                        </button>
                                        <button type="submit" name="action" value="annuler" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                            Annuler
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Initialisation de DataTables
                $('#ordersTable').DataTable({
                    responsive: true, // Rend la table responsive
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    }
                });

                // Fonction de recherche
                $('#search').on('keyup', function() {
                    $('#ordersTable').DataTable().search(this.value).draw();
                });
            });
        </script>
    </body>
</html>


