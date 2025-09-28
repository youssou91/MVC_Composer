<?php
// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit();
}

// Les variables $userName et $commandes sont passées par le contrôleur
$userName = $userName ?? 'Utilisateur inconnu';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes de <?= htmlspecialchars($userName) ?> - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="container mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                <a href="/admin/utilisateurs" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                </a>
                Commandes de <?= htmlspecialchars($userName) ?>
            </h1>
        </div>

        <?php if (empty($commandes)): ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <i class="fas fa-shopping-cart text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune commande trouvée</h3>
                <p class="text-gray-500">Cet utilisateur n'a pas encore passé de commande.</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table id="commandesTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Commande</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($commandes as $commande): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#<?= $commande['id_commande'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $statusClass = [
                                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                                        'payee' => 'bg-green-100 text-green-800',
                                        'expediee' => 'bg-blue-100 text-blue-800',
                                        'annulee' => 'bg-red-100 text-red-800'
                                    ][$commande['statut']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $commande['statut'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <?= number_format($commande['prix_total'], 2) ?> $
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="/admin/commande/<?= $commande['id_commande'] ?>" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            $('#commandesTable').DataTable({
                order: [[0, 'desc']], // Trier par numéro de commande décroissant
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json',
                    search: 'Rechercher :',
                    lengthMenu: 'Afficher _MENU_ commandes par page',
                    zeroRecords: 'Aucune commande trouvée',
                    info: 'Affichage de _START_ à _END_ sur _TOTAL_ commandes',
                    infoEmpty: 'Aucune commande disponible',
                    infoFiltered: '(filtré à partir de _MAX_ commandes au total)',
                    paginate: {
                        first: 'Premier',
                        last: 'Dernier',
                        next: 'Suivant',
                        previous: 'Précédent'
                    }
                },
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100]
            });
        });
    </script>
</body>
</html>
